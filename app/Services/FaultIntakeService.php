<?php

namespace App\Services;

use App\Models\AccountManager;
use App\Models\Customer;
use App\Models\Fault;
use App\Models\FaultSection;
use App\Models\Link;
use App\Models\Remark;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Logs and reads faults on behalf of external channels (WhatsApp, USSD, web, mobile, call centre).
 *
 * Creation mirrors FaultController@store (the NOC "Log fault" form): same reference format,
 * account-manager snapshot, ON LOGGING remark, fault section row, POP-aggregator child faults,
 * lifecycle notifications and receipt e-mail. The difference is that the fault is attributed to
 * a per-channel system user and stamped with channel / channel_reference.
 */
class FaultIntakeService
{
    public const RESULT_CREATED = 'created';
    public const RESULT_ALREADY_LOGGED = 'already_logged';
    public const RESULT_OPEN_FAULT_EXISTS = 'open_fault_exists';

    /* ------------------------------------------------------------------
     | Phone numbers
     * ------------------------------------------------------------------ */

    /**
     * Normalise a Zimbabwean mobile number to 2637XXXXXXXX (the format the fault form enforces).
     * Accepts 0773..., 773..., +263773..., 263773..., 00263773..., with spaces/dashes.
     */
    public static function normalizePhone(?string $raw): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $raw);
        if ($digits === '') {
            return null;
        }
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }
        if (str_starts_with($digits, '263')) {
            $digits = substr($digits, 3);
        }
        $digits = ltrim($digits, '0');

        if (!preg_match('/^7\d{8}$/', $digits)) {
            return null;
        }

        return '263' . $digits;
    }

    /* ------------------------------------------------------------------
     | Customer resolution
     * ------------------------------------------------------------------ */

    /**
     * Find the customer behind an account number. Customers can quote either the customer-level
     * account/contract number or a link-level account number (links.account_number); in the second
     * case only links on that account are in scope.
     *
     * @return array{customer: Customer, link_account: ?string}|null
     */
    public function findByAccount(string $account): ?array
    {
        $account = trim($account);
        if ($account === '') {
            return null;
        }

        $customer = Customer::query()
            ->where('account_number', $account)
            ->orWhere('contract_number', $account)
            ->orderBy('id')
            ->first();

        if ($customer) {
            return ['customer' => $customer, 'link_account' => null];
        }

        $link = Link::query()->where('account_number', $account)->orderBy('id')->first();
        if ($link && $link->customer) {
            return ['customer' => $link->customer, 'link_account' => $account];
        }

        return null;
    }

    /**
     * Customers associated with a phone number: their registered contact number, or the phone used
     * on faults previously logged for them. Most recent association first.
     */
    public function findByPhone(string $phone): Collection
    {
        $normalized = self::normalizePhone($phone);
        if (!$normalized) {
            return collect();
        }
        $last9 = substr($normalized, -9);

        $byContact = Customer::query()
            ->whereNotNull('contact_number')
            ->whereRaw(
                "REPLACE(REPLACE(REPLACE(REPLACE(contact_number, ' ', ''), '-', ''), '+', ''), '.', '') LIKE ?",
                ['%' . $last9]
            )
            ->pluck('id');

        $byFaults = DB::table('faults')
            ->where('phoneNumber', $normalized)
            ->whereNotNull('customer_id')
            ->orderByDesc('created_at')
            ->pluck('customer_id');

        $ids = $byFaults->merge($byContact)->unique()->values();
        if ($ids->isEmpty()) {
            return collect();
        }

        $customers = Customer::query()->whereIn('id', $ids)->get()->keyBy('id');

        return $ids->map(fn ($id) => $customers->get($id))->filter()->values();
    }

    /**
     * Resolve the caller to exactly one customer scope from account_number and/or phone.
     *
     * @return array{status: string, customer?: Customer, link_account?: ?string, customers?: Collection}
     *         status = found | not_found | multiple | invalid_phone | missing
     */
    public function resolveCaller(?string $account, ?string $phone): array
    {
        if ($account !== null && trim($account) !== '') {
            $found = $this->findByAccount($account);
            return $found ? ['status' => 'found'] + $found : ['status' => 'not_found'];
        }

        if ($phone !== null && trim($phone) !== '') {
            if (!self::normalizePhone($phone)) {
                return ['status' => 'invalid_phone'];
            }
            $customers = $this->findByPhone($phone);
            if ($customers->isEmpty()) {
                return ['status' => 'not_found'];
            }
            if ($customers->count() > 1) {
                return ['status' => 'multiple', 'customers' => $customers];
            }
            return ['status' => 'found', 'customer' => $customers->first(), 'link_account' => null];
        }

        return ['status' => 'missing'];
    }

    public function presentCustomer(Customer $customer, ?string $linkAccount = null): array
    {
        $linkQuery = Link::query()
            ->where('customer_id', $customer->id)
            ->whereNotIn('link_status', $this->hiddenLinkStatuses());
        if ($linkAccount) {
            $linkQuery->where('account_number', $linkAccount);
        }

        return [
            'id' => $customer->id,
            'name' => $customer->customer,
            'account_number' => $linkAccount ?: $customer->account_number,
            'contract_number' => $customer->contract_number,
            'address' => $this->clean($customer->address),
            'links_count' => $linkQuery->count(),
            'open_faults_count' => $this->openFaultsQuery()->where('faults.customer_id', $customer->id)->count(),
        ];
    }

    /* ------------------------------------------------------------------
     | Links
     * ------------------------------------------------------------------ */

    protected function linksBaseQuery()
    {
        return DB::table('links')
            ->join('customers', 'links.customer_id', '=', 'customers.id')
            ->leftJoin('cities', 'links.city_id', '=', 'cities.id')
            ->leftJoin('suburbs', 'links.suburb_id', '=', 'suburbs.id')
            ->leftJoin('pops', 'links.pop_id', '=', 'pops.id')
            ->leftJoin('link_statuses', 'links.link_status', '=', 'link_statuses.id')
            ->select([
                'links.id',
                'links.link',
                'links.customer_id',
                'links.account_number',
                'links.jcc_number',
                'links.sapcodes',
                'links.service_type',
                'links.capacity',
                'links.comment',
                'links.link_status',
                'links.city_id',
                'links.suburb_id',
                'links.pop_id',
                'link_statuses.link_status as link_status_name',
                'cities.city',
                'cities.region',
                'suburbs.suburb',
                'pops.pop',
                'customers.customer',
                'customers.address as customer_address',
                'customers.account_number as customer_account_number',
            ]);
    }

    /**
     * Search a customer's links. Every word in $q must match one of: link name, city, suburb,
     * POP, JCC number, SAP code, comment, service type or the customer address.
     *
     * @return array{links: array, total: int, truncated: bool}
     */
    public function searchLinks(Customer $customer, ?string $linkAccount, ?string $q = null, ?string $city = null): array
    {
        $query = $this->linksBaseQuery()
            ->where('links.customer_id', $customer->id)
            ->whereNotIn('links.link_status', $this->hiddenLinkStatuses());

        if ($linkAccount) {
            $query->where('links.account_number', $linkAccount);
        }

        if ($city !== null && trim($city) !== '') {
            $query->where('cities.city', 'like', '%' . trim($city) . '%');
        }

        $tokens = array_values(array_filter(preg_split('/\s+/', trim((string) $q)) ?: [], fn ($t) => $t !== ''));
        foreach (array_slice($tokens, 0, 6) as $token) {
            $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $token) . '%';
            $query->where(function ($w) use ($like) {
                $w->where('links.link', 'like', $like)
                    ->orWhere('cities.city', 'like', $like)
                    ->orWhere('suburbs.suburb', 'like', $like)
                    ->orWhere('pops.pop', 'like', $like)
                    ->orWhere('links.jcc_number', 'like', $like)
                    ->orWhere('links.sapcodes', 'like', $like)
                    ->orWhere('links.comment', 'like', $like)
                    ->orWhere('links.service_type', 'like', $like)
                    ->orWhere('customers.address', 'like', $like);
            });
        }

        $total = (clone $query)->count();
        $max = max(1, (int) config('fault_intake.max_link_results', 20));
        $rows = $query->orderBy('cities.city')->orderBy('links.link')->limit($max)->get();

        $openFaults = $this->openFaultsByLink($rows->pluck('id')->all());

        $links = [];
        foreach ($rows->values() as $i => $row) {
            $links[] = $this->presentLink($row, $openFaults[$row->id] ?? null, $i + 1);
        }

        return ['links' => $links, 'total' => $total, 'truncated' => $total > count($links)];
    }

    /** A single link, only if it belongs to the caller's customer scope. */
    public function findLinkForCustomer(int $linkId, Customer $customer, ?string $linkAccount): ?object
    {
        $query = $this->linksBaseQuery()
            ->where('links.id', $linkId)
            ->where('links.customer_id', $customer->id);
        if ($linkAccount) {
            $query->where('links.account_number', $linkAccount);
        }

        return $query->first();
    }

    public function presentLink(object $row, ?object $openFault = null, ?int $option = null): array
    {
        $city = $this->clean($row->city ?? null);
        $suburb = $this->clean($row->suburb ?? null);
        $place = implode(', ', array_filter([$suburb, $city]));
        $name = (string) $row->link;

        $data = [
            'id' => (int) $row->id,
            'name' => $name,
            // Human-friendly line to read back to the customer for confirmation.
            'display' => $place !== '' ? "{$name} — {$place}" : $name,
            'location' => [
                'city' => $city,
                'suburb' => $suburb,
                'region' => $this->clean($row->region ?? null),
                'pop' => $this->clean($row->pop ?? null),
                'address' => $this->clean($row->customer_address ?? null),
            ],
            'service_type' => $this->clean($row->service_type ?? null),
            'capacity' => $this->clean($row->capacity ?? null),
            'account_number' => $this->clean($row->account_number ?? null) ?? $this->clean($row->customer_account_number ?? null),
            'jcc_number' => $this->clean($row->jcc_number ?? null),
            'link_status' => $this->clean($row->link_status_name ?? null),
            'open_fault' => $openFault ? [
                'reference' => $openFault->fault_ref_number,
                'status' => $this->customerStatus($openFault->status_code ?? null, $openFault->status_description ?? null),
                'logged_at' => optional($openFault->created_at ? \Carbon\Carbon::parse($openFault->created_at) : null)->toIso8601String(),
            ] : null,
        ];

        if ($option !== null) {
            $data = ['option' => $option] + $data;
        }

        return $data;
    }

    /** WhatsApp/USSD-ready numbered menu built from presented links. */
    public function linksMenu(array $links): string
    {
        return implode("\n", array_map(function ($l) {
            $line = "{$l['option']}. {$l['display']}";
            if (!empty($l['open_fault'])) {
                $line .= " (open fault {$l['open_fault']['reference']})";
            }
            return $line;
        }, $links));
    }

    /* ------------------------------------------------------------------
     | Categories / RFOs
     * ------------------------------------------------------------------ */

    public function categories(): array
    {
        $out = [];
        $i = 1;
        foreach ((array) config('fault_intake.categories', []) as $key => $cat) {
            $out[] = [
                'option' => $i++,
                'key' => $key,
                'label' => $cat['label'] ?? $key,
                'suspected_rfo' => $cat['rfo'] ?? null,
            ];
        }
        return $out;
    }

    public function resolveRfoId(?string $categoryKey, $suspectedRfoId): array
    {
        if ($suspectedRfoId) {
            $id = (int) DB::table('reasons_for_outages')->where('id', (int) $suspectedRfoId)->value('id');
            if ($id) {
                return [$id, null];
            }
        }

        $categories = (array) config('fault_intake.categories', []);
        $key = $categoryKey ?: config('fault_intake.default_category');
        $cat = $categories[$key] ?? null;

        $id = 0;
        if ($cat && !empty($cat['rfo'])) {
            $id = (int) DB::table('reasons_for_outages')->whereRaw('LOWER(RFO) = ?', [strtolower($cat['rfo'])])->value('id');
        }
        if (!$id) {
            Log::warning('FaultIntake: category RFO not found, falling back to first RFO', ['category' => $key]);
            $id = (int) DB::table('reasons_for_outages')->orderBy('id')->value('id');
        }

        return [$id, $cat];
    }

    /* ------------------------------------------------------------------
     | Faults
     * ------------------------------------------------------------------ */

    protected function closedStatusIds(): array
    {
        $codes = (array) config('fault_intake.closed_status_codes', ['CLN', 'RVK']);
        $ids = DB::table('statuses')->whereIn('status_code', $codes)->pluck('id')->map(fn ($v) => (int) $v)->all();
        return $ids ?: [6];
    }

    protected function hiddenLinkStatuses(): array
    {
        return array_map('intval', (array) config('fault_intake.hidden_link_statuses', [4]));
    }

    public function openFaultsQuery()
    {
        return DB::table('faults')
            ->leftJoin('statuses', 'faults.status_id', '=', 'statuses.id')
            ->whereNotIn('faults.status_id', $this->closedStatusIds());
    }

    /** Latest open fault per link id. */
    public function openFaultsByLink(array $linkIds): array
    {
        if (empty($linkIds)) {
            return [];
        }

        $rows = $this->openFaultsQuery()
            ->whereIn('faults.link_id', $linkIds)
            ->orderByDesc('faults.created_at')
            ->get(['faults.id', 'faults.link_id', 'faults.fault_ref_number', 'faults.created_at', 'statuses.status_code', 'statuses.description as status_description']);

        $map = [];
        foreach ($rows as $row) {
            if (!isset($map[$row->link_id])) {
                $map[$row->link_id] = $row;
            }
        }
        return $map;
    }

    /** One system user per channel so "Reported By" in Impaza shows the channel. */
    public function channelUser(string $channel): User
    {
        $channels = (array) config('fault_intake.channels', []);
        $name = $channels[$channel] ?? (ucfirst($channel) . ' (API)');
        $email = $channel . '@' . config('fault_intake.system_user_email_domain', 'channels.impaza.local');

        $user = User::where('email', $email)->first();
        if ($user) {
            return $user;
        }

        $user = new User();
        $user->forceFill([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make(Str::random(48)),
            'department_id' => 0,
            'section_id' => 0,
            'position_id' => 0,
            'user_status' => 2, // Unassignable — never picked for work or NOC alerts
            'is_access' => 0,
        ])->save();

        return $user;
    }

    /**
     * Log a fault from an external channel.
     *
     * @param array $data validated input (see FaultIntakeController@store)
     * @return array{result: string, fault: Fault}
     */
    public function logFault(array $data, Customer $customer, object $link, string $channel): array
    {
        $channelRef = isset($data['channel_reference']) && trim((string) $data['channel_reference']) !== ''
            ? trim((string) $data['channel_reference'])
            : null;

        // 1. Idempotency: the same channel reference never creates a second fault.
        if ($channelRef) {
            $existing = Fault::where('channel', $channel)->where('channel_reference', $channelRef)->first();
            if ($existing) {
                return ['result' => self::RESULT_ALREADY_LOGGED, 'fault' => $existing];
            }
        }

        // 2. One open fault per link (unless an agent explicitly overrides).
        if (empty($data['allow_duplicate'])) {
            $open = $this->openFaultsByLink([(int) $link->id])[(int) $link->id] ?? null;
            if ($open) {
                return ['result' => self::RESULT_OPEN_FAULT_EXISTS, 'fault' => Fault::find($open->id)];
            }
        }

        [$rfoId, $category] = $this->resolveRfoId($data['category'] ?? null, $data['suspected_rfo_id'] ?? null);
        $user = $this->channelUser($channel);
        $phone = self::normalizePhone($data['contact_phone']);
        $channelLabel = config("fault_intake.channels.$channel", $channel);

        $meta = array_filter([
            'category' => $data['category'] ?? null,
            'category_label' => $category['label'] ?? null,
            'reported_by' => $data['reported_by'] ?? null,
            'extra' => $data['meta'] ?? null,
        ], fn ($v) => $v !== null && $v !== '');

        $remarkText = $this->buildRemark($data, $channelLabel, $phone, $category);

        try {
            $fault = DB::transaction(function () use ($data, $customer, $link, $channel, $channelRef, $rfoId, $category, $user, $phone, $meta, $remarkText) {
                [$prefix, $next] = $this->nextReference();

                $fault = Fault::create([
                    'fault_ref_number' => $prefix . sprintf('%03d', $next),
                    'channel' => $channel,
                    'channel_reference' => $channelRef,
                    'channel_meta' => $meta ?: null,
                    'customer_id' => $customer->id,
                    'contactName' => trim($data['contact_name']),
                    'phoneNumber' => $phone,
                    'contactEmail' => !empty($data['contact_email']) ? trim($data['contact_email']) : null,
                    'address' => !empty($data['address']) ? trim($data['address']) : ($this->clean($customer->address) ?? null),
                    'accountManager_id' => $this->accountManagerIdFor($customer),
                    'city_id' => $this->nullIfZero($link->city_id),
                    'suburb_id' => $this->nullIfZero($link->suburb_id),
                    'pop_id' => $this->nullIfZero($link->pop_id),
                    'link_id' => $link->id,
                    'suspectedRfo_id' => $rfoId,
                    'serviceType' => $this->clean($link->service_type) ?? 'N/A',
                    'serviceAttribute' => $this->clean($link->capacity) ?? 'N/A',
                    'faultType' => $category['fault_type'] ?? null,
                    'status_id' => 1,
                    'user_id' => $user->id,
                ]);

                FaultLifecycle::recordStatusChange($fault, 1, $user->id);

                $activityId = $this->loggingActivityId();
                Remark::create([
                    'fault_id' => $fault->id,
                    'user_id' => $user->id,
                    'remark' => $remarkText,
                    'remarkActivity_id' => $activityId,
                    'file_path' => null,
                ]);

                FaultSection::create(['fault_id' => $fault->id]);

                $this->createPopImpactChildren($fault, $customer, $user->id, $remarkText, $activityId, $prefix, $next + 1);

                return $fault;
            });
        } catch (QueryException $e) {
            // Two retries raced on the same channel_reference: return the winner.
            if ($channelRef) {
                $existing = Fault::where('channel', $channel)->where('channel_reference', $channelRef)->first();
                if ($existing) {
                    return ['result' => self::RESULT_ALREADY_LOGGED, 'fault' => $existing];
                }
            }
            throw $e;
        }

        FaultLifecycle::sendReceiptEmail($fault);

        Log::info('FaultIntake: fault logged', [
            'fault' => $fault->fault_ref_number,
            'channel' => $channel,
            'channel_reference' => $channelRef,
            'link_id' => $link->id,
        ]);

        return ['result' => self::RESULT_CREATED, 'fault' => $fault];
    }

    protected function buildRemark(array $data, string $channelLabel, ?string $phone, ?array $category): string
    {
        $lines = [];
        $who = trim($data['contact_name']) . ($phone ? " ({$phone})" : '');
        $lines[] = "Logged via {$channelLabel} by {$who}.";
        if (!empty($data['reported_by'])) {
            $lines[] = 'Captured by: ' . trim($data['reported_by']);
        }
        if ($category) {
            $lines[] = 'Problem: ' . ($category['label'] ?? '');
        }
        if (!empty($data['address'])) {
            $lines[] = 'Site / address: ' . trim($data['address']);
        }
        $lines[] = 'Customer description: ' . trim($data['description']);

        return implode("\n", $lines);
    }

    /**
     * Same daily sequence as the web form (PWTyymmddNNN), but taken under a row lock so
     * concurrent channel requests cannot get the same number.
     *
     * @return array{0: string, 1: int}
     */
    protected function nextReference(): array
    {
        $prefix = 'PWT' . date('ymd');

        $last = Fault::where('fault_ref_number', 'like', $prefix . '%')
            ->orderByRaw('LENGTH(fault_ref_number) DESC')
            ->orderByDesc('fault_ref_number')
            ->lockForUpdate()
            ->value('fault_ref_number');

        $next = $last ? ((int) substr($last, strlen($prefix)) + 1) : 1;

        return [$prefix, $next];
    }

    protected function accountManagerIdFor(?Customer $customer): int
    {
        $amUserId = $customer ? $customer->account_manager_id : null;
        if ($amUserId) {
            $amUser = User::find($amUserId);
            return AccountManager::firstOrCreate(
                ['user_id' => $amUserId],
                ['accountManager' => $amUser ? $amUser->name : 'Account Manager']
            )->id;
        }

        $fallback = AccountManager::whereNull('user_id')->where('accountManager', 'Unassigned')->first();
        if (!$fallback) {
            $fallback = AccountManager::create(['accountManager' => 'Unassigned', 'user_id' => null]);
        }
        return $fallback->id;
    }

    protected function loggingActivityId(): int
    {
        $id = DB::table('remark_activities')->where('activity', 'ON LOGGING')->value('id');
        if ($id) {
            return (int) $id;
        }
        return (int) DB::table('remark_activities')->insertGetId([
            'activity' => 'ON LOGGING',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** Mirrors FaultController@store: a POP aggregator's fault opens child faults for every customer on that POP. */
    protected function createPopImpactChildren(Fault $fault, Customer $customer, int $userId, string $remarkText, int $activityId, string $prefix, int $seq): void
    {
        if (!(bool) $customer->is_pop_aggregator || empty($fault->pop_id)) {
            return;
        }

        $poiStatusId = (int) (DB::table('statuses')->where('status_code', 'POI')->value('id') ?? 0);
        if ($poiStatusId <= 0) {
            $poiStatusId = 1;
        }

        $impactedLinks = Link::query()
            ->join('customers', 'links.customer_id', '=', 'customers.id')
            ->where('links.pop_id', (int) $fault->pop_id)
            ->where('links.customer_id', '!=', (int) $customer->id)
            ->where(function ($q) {
                $q->whereNull('customers.is_pop_aggregator')->orWhere('customers.is_pop_aggregator', '!=', 1);
            })
            ->select('links.*')
            ->get();

        foreach ($impactedLinks as $impactedLink) {
            $impactedCustomer = Customer::find($impactedLink->customer_id);

            $child = Fault::create([
                'root_fault_id' => $fault->id,
                'fault_ref_number' => $prefix . sprintf('%03d', $seq++),
                'channel' => $fault->channel,
                'customer_id' => $impactedLink->customer_id,
                'contactName' => $impactedCustomer ? (string) $impactedCustomer->customer : (string) $fault->contactName,
                'phoneNumber' => $impactedCustomer && !empty($impactedCustomer->contact_number)
                    ? preg_replace('/\s+/', '', (string) $impactedCustomer->contact_number)
                    : (string) $fault->phoneNumber,
                'contactEmail' => $fault->contactEmail,
                'address' => $impactedCustomer && !empty($impactedCustomer->address) ? (string) $impactedCustomer->address : (string) $fault->address,
                'accountManager_id' => $this->accountManagerIdFor($impactedCustomer),
                'city_id' => $impactedLink->city_id ?: $fault->city_id,
                'suburb_id' => $impactedLink->suburb_id ?: $fault->suburb_id,
                'pop_id' => $impactedLink->pop_id ?: $fault->pop_id,
                'link_id' => $impactedLink->id,
                'suspectedRfo_id' => $fault->suspectedRfo_id,
                'serviceType' => $impactedLink->service_type ?? $fault->serviceType,
                'serviceAttribute' => $fault->serviceAttribute,
                'status_id' => $poiStatusId,
                'faultType' => $fault->faultType ?? 'POP OUTAGE',
                'priorityLevel' => $fault->priorityLevel,
                'user_id' => $userId,
            ]);
            FaultLifecycle::recordStatusChange($child, $poiStatusId, $userId);

            Remark::create([
                'fault_id' => $child->id,
                'user_id' => $userId,
                'remark' => $remarkText . " (Auto-linked to POP fault {$fault->fault_ref_number})",
                'remarkActivity_id' => $activityId,
                'file_path' => null,
            ]);
            FaultSection::create(['fault_id' => $child->id]);
        }
    }

    /* ------------------------------------------------------------------
     | Fault presentation
     * ------------------------------------------------------------------ */

    public function findFaultByReference(string $reference): ?object
    {
        return $this->faultQuery()->where('faults.fault_ref_number', strtoupper(trim($reference)))->first();
    }

    public function faultQuery()
    {
        return DB::table('faults')
            ->leftJoin('customers', 'faults.customer_id', '=', 'customers.id')
            ->leftJoin('links', 'faults.link_id', '=', 'links.id')
            ->leftJoin('cities', 'faults.city_id', '=', 'cities.id')
            ->leftJoin('suburbs', 'faults.suburb_id', '=', 'suburbs.id')
            ->leftJoin('pops', 'faults.pop_id', '=', 'pops.id')
            ->leftJoin('statuses', 'faults.status_id', '=', 'statuses.id')
            ->leftJoin('reasons_for_outages as srfo', 'faults.suspectedRfo_id', '=', 'srfo.id')
            ->leftJoin('reasons_for_outages as crfo', 'faults.confirmedRfo_id', '=', 'crfo.id')
            ->leftJoin('faults as parent', 'faults.root_fault_id', '=', 'parent.id')
            ->select([
                'faults.*',
                'customers.customer',
                'customers.account_number as customer_account_number',
                'customers.contact_number as customer_contact_number',
                'customers.address as customer_address',
                'links.link',
                'links.service_type as link_service_type',
                'links.capacity as link_capacity',
                'links.account_number as link_account_number',
                'cities.city',
                'cities.region',
                'suburbs.suburb',
                'pops.pop',
                'statuses.status_code',
                'statuses.description as status_description',
                'srfo.RFO as suspected_rfo',
                'crfo.RFO as confirmed_rfo',
                'parent.fault_ref_number as parent_reference',
            ]);
    }

    /** Can the caller (by account scope or phone) see this fault? */
    public function callerOwnsFault(object $fault, ?array $caller, ?string $phone): bool
    {
        if ($caller && ($caller['status'] ?? null) === 'found') {
            if ((int) $fault->customer_id === (int) $caller['customer']->id) {
                if (empty($caller['link_account'])) {
                    return true;
                }
                return (string) $fault->link_account_number === (string) $caller['link_account'];
            }
        }

        $normalized = $phone ? self::normalizePhone($phone) : null;
        if ($normalized) {
            if (self::normalizePhone((string) $fault->phoneNumber) === $normalized) {
                return true;
            }
            if ($fault->customer_contact_number && self::normalizePhone((string) $fault->customer_contact_number) === $normalized) {
                return true;
            }
        }

        return false;
    }

    public function presentFault(object $f, bool $withTimeline = true): array
    {
        $city = $this->clean($f->city ?? null);
        $suburb = $this->clean($f->suburb ?? null);
        $place = implode(', ', array_filter([$suburb, $city]));
        $linkName = (string) ($f->link ?? '');
        $meta = $f->channel_meta ? json_decode($f->channel_meta, true) : null;
        $status = $this->customerStatus($f->status_code ?? null, $f->status_description ?? null);

        $data = [
            'reference' => $f->fault_ref_number,
            'channel' => $f->channel ?? 'internal',
            'channel_reference' => $f->channel_reference ?? null,
            'status' => $status,
            'problem' => $meta['category_label'] ?? null,
            'suspected_rfo' => $f->suspected_rfo ?? null,
            'confirmed_rfo' => in_array($f->status_code ?? null, ['CLT', 'CLC', 'CLN'], true) ? ($f->confirmed_rfo ?? null) : null,
            'customer' => [
                'name' => $f->customer ?? null,
                'account_number' => $f->customer_account_number ?? null,
            ],
            'link' => [
                'id' => $f->link_id ? (int) $f->link_id : null,
                'name' => $linkName ?: null,
                'display' => $linkName !== '' ? ($place !== '' ? "{$linkName} — {$place}" : $linkName) : null,
                'service_type' => $this->clean($f->link_service_type ?? null),
                'capacity' => $this->clean($f->link_capacity ?? null),
                'location' => [
                    'city' => $city,
                    'suburb' => $suburb,
                    'region' => $this->clean($f->region ?? null),
                    'pop' => $this->clean($f->pop ?? null),
                    'address' => $this->clean($f->address ?? null) ?? $this->clean($f->customer_address ?? null),
                ],
            ],
            'contact' => [
                'name' => $f->contactName ?? null,
                'phone' => $f->phoneNumber ?? null,
                'email' => $f->contactEmail ?? null,
            ],
            'description' => $this->firstRemark((int) $f->id),
            'is_part_of_network_outage' => !empty($f->root_fault_id),
            'parent_reference' => $f->parent_reference ?? null,
            'logged_at' => $f->created_at ? \Carbon\Carbon::parse($f->created_at)->toIso8601String() : null,
            'updated_at' => $f->updated_at ? \Carbon\Carbon::parse($f->updated_at)->toIso8601String() : null,
        ];

        $data['summary'] = $this->faultSummaryText($data);

        if ($withTimeline) {
            $data['timeline'] = DB::table('fault_stage_logs')
                ->leftJoin('statuses', 'fault_stage_logs.status_id', '=', 'statuses.id')
                ->where('fault_stage_logs.fault_id', $f->id)
                ->orderBy('fault_stage_logs.started_at')
                ->get(['statuses.status_code', 'statuses.description', 'fault_stage_logs.started_at', 'fault_stage_logs.ended_at'])
                ->map(fn ($s) => [
                    'status' => $this->customerStatus($s->status_code, $s->description)['label'],
                    'started_at' => $s->started_at ? \Carbon\Carbon::parse($s->started_at)->toIso8601String() : null,
                    'ended_at' => $s->ended_at ? \Carbon\Carbon::parse($s->ended_at)->toIso8601String() : null,
                ])->values()->all();
        }

        return $data;
    }

    /** Short text a bot can send as-is. */
    public function faultSummaryText(array $f): string
    {
        $lines = ["Fault {$f['reference']}"];
        if (!empty($f['link']['display'])) {
            $lines[] = "Link: {$f['link']['display']}";
        }
        $lines[] = "Status: {$f['status']['label']}";
        if (!empty($f['logged_at'])) {
            $lines[] = 'Logged: ' . \Carbon\Carbon::parse($f['logged_at'])->format('d M Y H:i');
        }
        if (!empty($f['parent_reference'])) {
            $lines[] = "Part of network outage {$f['parent_reference']}";
        }
        return implode("\n", $lines);
    }

    /** Map internal statuses to customer-friendly wording + a coarse stage. */
    public function customerStatus(?string $code, ?string $description): array
    {
        $map = [
            'WAS' => ['received', 'Received – waiting for assessment'],
            'ASD' => ['in_progress', 'Assessed – a technician is being assigned'],
            'RTN' => ['in_progress', 'A technician is working on it'],
            'CLT' => ['in_progress', 'Fixed – awaiting final confirmation'],
            'CLC' => ['in_progress', 'Fixed – awaiting final confirmation'],
            'CLN' => ['resolved', 'Resolved'],
            'REF' => ['in_progress', 'Referred to a specialist team'],
            'PRK' => ['in_progress', 'On hold'],
            'RVK' => ['closed', 'Closed'],
            'ESC' => ['in_progress', 'Escalated'],
            'MES' => ['in_progress', 'Escalated'],
            'POI' => ['in_progress', 'Affected by a network outage – being worked on'],
        ];

        [$stage, $label] = $map[$code] ?? ['in_progress', $description ?: 'In progress'];

        return [
            'code' => $code,
            'stage' => $stage,
            'label' => $label,
            'internal' => $description,
        ];
    }

    protected function firstRemark(int $faultId): ?string
    {
        $text = DB::table('remarks')->where('fault_id', $faultId)->orderBy('id')->value('remark');
        if (!$text) {
            return null;
        }
        // For channel-logged faults return only what the customer typed.
        if (preg_match('/Customer description:\s*(.*)$/s', $text, $m)) {
            return trim($m[1]);
        }
        return $text;
    }

    protected function clean($value): ?string
    {
        $v = trim((string) $value);
        return ($v === '' || strtoupper($v) === 'N/A') ? null : $v;
    }

    protected function nullIfZero($value): ?int
    {
        return empty($value) ? null : (int) $value;
    }
}
