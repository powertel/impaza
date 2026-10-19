<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FaultIntakeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Channel-agnostic fault intake for WhatsApp, USSD, web, mobile and call-centre clients.
 * All routes: /api/v1/intake/*, protected by the api_key middleware.
 *
 * Every response carries a ready-to-send `message` so thin clients (bots, USSD menus)
 * can relay it to the customer without building their own wording.
 */
class FaultIntakeController extends Controller
{
    public function __construct(protected FaultIntakeService $intake)
    {
    }

    /* ----------------------------------------------------------------
     | GET /fault-categories
     * ---------------------------------------------------------------- */
    public function categories(): JsonResponse
    {
        $categories = $this->intake->categories();

        return response()->json([
            'categories' => $categories,
            'menu' => implode("\n", array_map(fn ($c) => "{$c['option']}. {$c['label']}", $categories)),
            'channels' => array_keys((array) config('fault_intake.channels', [])),
            // For agent tools that want to pick the exact RFO instead of a category.
            'rfos' => DB::table('reasons_for_outages')->orderBy('RFO')->get(['id', 'RFO as name']),
        ]);
    }

    /* ----------------------------------------------------------------
     | GET /customers/lookup?account_number=… | ?phone=…
     * ---------------------------------------------------------------- */
    public function lookupCustomer(Request $request): JsonResponse
    {
        $request->validate([
            'account_number' => ['nullable', 'string', 'max:64'],
            'phone' => ['nullable', 'string', 'max:32'],
        ]);

        $caller = $this->intake->resolveCaller($request->query('account_number'), $request->query('phone'));

        if ($error = $this->callerError($caller)) {
            return $error;
        }

        $customer = $this->intake->presentCustomer($caller['customer'], $caller['link_account']);

        return response()->json([
            'result' => 'found',
            'customer' => $customer,
            'message' => "We found {$customer['name']} (account {$customer['account_number']}) with {$customer['links_count']} link(s).",
        ]);
    }

    /* ----------------------------------------------------------------
     | GET /links?account_number=…|phone=…&q=…&city=…
     * ---------------------------------------------------------------- */
    public function links(Request $request): JsonResponse
    {
        $request->validate([
            'account_number' => ['nullable', 'string', 'max:64'],
            'phone' => ['nullable', 'string', 'max:32'],
            'q' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:80'],
        ]);

        $caller = $this->intake->resolveCaller($request->query('account_number'), $request->query('phone'));
        if ($error = $this->callerError($caller)) {
            return $error;
        }

        $result = $this->intake->searchLinks($caller['customer'], $caller['link_account'], $request->query('q'), $request->query('city'));
        $customer = $this->intake->presentCustomer($caller['customer'], $caller['link_account']);

        if (empty($result['links'])) {
            $msg = $request->filled('q') || $request->filled('city')
                ? 'No link matched that search. Try the town, suburb or part of the link name.'
                : 'We could not find any active links on this account.';

            return response()->json([
                'result' => 'no_links',
                'customer' => $customer,
                'links' => [],
                'total' => 0,
                'truncated' => false,
                'menu' => '',
                'message' => $msg,
            ]);
        }

        $count = count($result['links']);
        if ($count === 1) {
            $only = $result['links'][0];
            $message = "Is this the affected link?\n{$only['display']}";
            if (!empty($only['open_fault'])) {
                $message .= "\nNote: open fault {$only['open_fault']['reference']} – {$only['open_fault']['status']['label']}";
            }
        } else {
            $message = "Which link is affected? Reply with the number:\n" . $this->intake->linksMenu($result['links']);
            if ($result['truncated']) {
                $message .= "\n\nShowing {$count} of {$result['total']}. Reply with a town or part of the link name to narrow down.";
            }
        }

        return response()->json([
            'result' => $count === 1 ? 'single_match' : 'multiple_matches',
            'customer' => $customer,
            'links' => $result['links'],
            'total' => $result['total'],
            'truncated' => $result['truncated'],
            'menu' => $this->intake->linksMenu($result['links']),
            'message' => $message,
        ]);
    }

    /* ----------------------------------------------------------------
     | GET /links/{link}?account_number=…|phone=…
     * ---------------------------------------------------------------- */
    public function link(Request $request, int $link): JsonResponse
    {
        $caller = $this->intake->resolveCaller($request->query('account_number'), $request->query('phone'));
        if ($error = $this->callerError($caller)) {
            return $error;
        }

        $row = $this->intake->findLinkForCustomer($link, $caller['customer'], $caller['link_account']);
        if (!$row) {
            return response()->json([
                'result' => 'link_not_found',
                'message' => 'That link was not found on this account.',
            ], 404);
        }

        $open = $this->intake->openFaultsByLink([(int) $row->id])[(int) $row->id] ?? null;
        $presented = $this->intake->presentLink($row, $open);

        $recent = $this->intake->faultQuery()
            ->where('faults.link_id', $row->id)
            ->orderByDesc('faults.created_at')
            ->limit(5)
            ->get()
            ->map(fn ($f) => $this->intake->presentFault($f, false))
            ->all();

        $message = $presented['display'];
        if ($presented['open_fault']) {
            $message .= "\nOpen fault: {$presented['open_fault']['reference']} – {$presented['open_fault']['status']['label']}";
        }

        return response()->json([
            'result' => 'found',
            'link' => $presented,
            'recent_faults' => $recent,
            'message' => $message,
        ]);
    }

    /* ----------------------------------------------------------------
     | POST /faults
     * ---------------------------------------------------------------- */
    public function store(Request $request): JsonResponse
    {
        $channels = array_keys((array) config('fault_intake.channels', []));
        $categories = array_keys((array) config('fault_intake.categories', []));

        // Channel may also come from the X-Channel header.
        if (!$request->filled('channel') && $request->headers->has('X-Channel')) {
            $request->merge(['channel' => strtolower((string) $request->header('X-Channel'))]);
        }
        if ($request->filled('channel')) {
            $request->merge(['channel' => strtolower(trim((string) $request->input('channel')))]);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'channel' => ['required', 'string', Rule::in($channels)],
            'channel_reference' => ['nullable', 'string', 'max:120'],
            'account_number' => ['nullable', 'required_without:phone', 'string', 'max:64'],
            'phone' => ['nullable', 'string', 'max:32'],
            'link_id' => ['required', 'integer'],
            'category' => ['nullable', 'string', Rule::in($categories)],
            'suspected_rfo_id' => ['nullable', 'integer', 'exists:reasons_for_outages,id'],
            'description' => ['required', 'string', 'min:3', 'max:2000'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:32'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'reported_by' => ['nullable', 'string', 'max:120'],
            'allow_duplicate' => ['nullable', 'boolean'],
            'meta' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result' => 'validation_error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }
        $data = $validator->validated();

        if ($bound = $this->boundChannel($request)) {
            if ($bound !== $data['channel']) {
                return response()->json([
                    'result' => 'channel_not_allowed',
                    'message' => "This API key may only log faults for channel '{$bound}'.",
                ], 403);
            }
        }

        if (!FaultIntakeService::normalizePhone($data['contact_phone'])) {
            return response()->json([
                'result' => 'invalid_phone',
                'message' => 'Please provide a valid Zimbabwean mobile number, e.g. 0773 123 456.',
                'errors' => ['contact_phone' => ['Must be a Zimbabwean mobile number (07XXXXXXXX or 2637XXXXXXXX).']],
            ], 422);
        }

        $caller = $this->intake->resolveCaller($data['account_number'] ?? null, $data['phone'] ?? null);
        if ($error = $this->callerError($caller)) {
            return $error;
        }

        $link = $this->intake->findLinkForCustomer((int) $data['link_id'], $caller['customer'], $caller['link_account']);
        if (!$link) {
            return response()->json([
                'result' => 'link_not_found',
                'message' => 'That link was not found on this account. Please choose the link again.',
            ], 404);
        }

        $outcome = $this->intake->logFault($data, $caller['customer'], $link, $data['channel']);
        $fault = $this->intake->findFaultByReference($outcome['fault']->fault_ref_number);
        $presented = $this->intake->presentFault($fault);
        $ref = $presented['reference'];
        $display = $presented['link']['display'] ?? 'your link';

        switch ($outcome['result']) {
            case FaultIntakeService::RESULT_CREATED:
                $name = trim(strtok($data['contact_name'], ' ')) ?: 'there';
                return response()->json([
                    'result' => 'created',
                    'fault' => $presented,
                    'message' => "Thank you {$name}. Your fault has been logged.\nReference: {$ref}\nLink: {$display}\nOur NOC team will assess it shortly and keep you updated.",
                ], 201);

            case FaultIntakeService::RESULT_ALREADY_LOGGED:
                return response()->json([
                    'result' => 'already_logged',
                    'fault' => $presented,
                    'message' => "This report was already received.\nReference: {$ref}\nStatus: {$presented['status']['label']}",
                ], 200);

            default: // open fault on that link
                return response()->json([
                    'result' => 'open_fault_exists',
                    'fault' => $presented,
                    'message' => "There is already an open fault on {$display}.\nReference: {$ref}\nStatus: {$presented['status']['label']}\nWe are working on it – no need to log it again.",
                ], 409);
        }
    }

    /* ----------------------------------------------------------------
     | GET /faults/{reference}?account_number=…|phone=…
     * ---------------------------------------------------------------- */
    public function show(Request $request, string $reference): JsonResponse
    {
        $fault = $this->intake->findFaultByReference($reference);

        $account = $request->query('account_number');
        $phone = $request->query('phone');
        if (!$account && !$phone) {
            return response()->json([
                'result' => 'caller_required',
                'message' => 'Provide account_number or phone to view a fault.',
            ], 422);
        }

        $caller = $account ? $this->intake->resolveCaller($account, null) : null;

        // Same 404 whether the fault doesn't exist or belongs to someone else.
        if (!$fault || !$this->intake->callerOwnsFault($fault, $caller, $phone)) {
            return response()->json([
                'result' => 'fault_not_found',
                'message' => "We couldn't find fault " . strtoupper(trim($reference)) . ' for your account. Please check the reference.',
            ], 404);
        }

        $presented = $this->intake->presentFault($fault);

        return response()->json([
            'result' => 'found',
            'fault' => $presented,
            'message' => $presented['summary'],
        ]);
    }

    /* ----------------------------------------------------------------
     | GET /faults?account_number=…|phone=…&status=open|all&limit=…
     * ---------------------------------------------------------------- */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'account_number' => ['nullable', 'string', 'max:64'],
            'phone' => ['nullable', 'string', 'max:32'],
            'status' => ['nullable', Rule::in(['open', 'all'])],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $account = $request->query('account_number');
        $phone = $request->query('phone');
        $limit = (int) $request->query('limit', 10);
        $onlyOpen = $request->query('status', 'open') === 'open';

        $query = $onlyOpen
            ? $this->intake->faultQuery()->whereNotIn('faults.status_id', DB::table('statuses')->whereIn('status_code', (array) config('fault_intake.closed_status_codes'))->pluck('id'))
            : $this->intake->faultQuery();

        if ($account) {
            $caller = $this->intake->resolveCaller($account, null);
            if ($error = $this->callerError($caller)) {
                return $error;
            }
            $query->where('faults.customer_id', $caller['customer']->id);
            if ($caller['link_account']) {
                $query->where('links.account_number', $caller['link_account']);
            }
        } elseif ($phone) {
            $normalized = FaultIntakeService::normalizePhone($phone);
            if (!$normalized) {
                return $this->callerError(['status' => 'invalid_phone']);
            }
            $query->where('faults.phoneNumber', $normalized);
        } else {
            return $this->callerError(['status' => 'missing']);
        }

        $faults = $query->orderByDesc('faults.created_at')->limit($limit)->get()
            ->map(fn ($f) => $this->intake->presentFault($f, false))
            ->values()->all();

        if (empty($faults)) {
            $message = $onlyOpen ? 'You have no open faults.' : 'No faults found.';
        } else {
            $message = ($onlyOpen ? 'Your open faults:' : 'Your faults:') . "\n" . implode("\n", array_map(
                fn ($f, $i) => ($i + 1) . ". {$f['reference']} – " . ($f['link']['name'] ?? 'link') . " – {$f['status']['label']}",
                $faults,
                array_keys($faults)
            ));
        }

        return response()->json([
            'result' => empty($faults) ? 'none' : 'found',
            'faults' => $faults,
            'message' => $message,
        ]);
    }

    /* ---------------------------------------------------------------- */

    protected function callerError(array $caller): ?JsonResponse
    {
        switch ($caller['status']) {
            case 'found':
                return null;
            case 'missing':
                return response()->json([
                    'result' => 'caller_required',
                    'message' => 'Provide account_number or phone.',
                ], 422);
            case 'invalid_phone':
                return response()->json([
                    'result' => 'invalid_phone',
                    'message' => 'Please provide a valid Zimbabwean mobile number, e.g. 0773 123 456.',
                ], 422);
            case 'multiple':
                $customers = $caller['customers']->map(fn ($c) => $this->intake->presentCustomer($c))->values()->all();
                return response()->json([
                    'result' => 'multiple_customers',
                    'customers' => $customers,
                    'message' => "This number is linked to more than one account. Please reply with your account number:\n"
                        . implode("\n", array_map(fn ($c, $i) => ($i + 1) . ". {$c['name']} ({$c['account_number']})", $customers, array_keys($customers))),
                ], 409);
            default:
                return response()->json([
                    'result' => 'customer_not_found',
                    'message' => "We couldn't find an account with those details. Please check your account number.",
                ], 404);
        }
    }

    /** Channel an API key is locked to (FAULT_INTAKE_KEY_CHANNELS="key:whatsapp,key2:ussd"). */
    protected function boundChannel(Request $request): ?string
    {
        $key = (string) $request->attributes->get('api_key', '');
        $raw = (string) config('fault_intake.key_channels', '');
        if ($key === '' || trim($raw) === '') {
            return null;
        }
        foreach (preg_split('/[,\s]+/', $raw) as $pair) {
            $parts = explode(':', $pair, 2);
            if (count($parts) === 2 && hash_equals(trim($parts[0]), $key)) {
                return strtolower(trim($parts[1]));
            }
        }
        return null;
    }
}
