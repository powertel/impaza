<?php

namespace App\Services;

use App\Models\AccountManager;
use App\Models\Customer;
use App\Models\Link;
use App\Models\LinkStatus;
use App\Models\LinkType;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AccountsSyncService
{
    public function sync(array $accounts, array $defaults = []): array
    {
        $defaultCityId = $defaults['city_id'] ?? null;
        $defaultSuburbId = $defaults['suburb_id'] ?? null;
        $defaultPopId = $defaults['pop_id'] ?? null;
        $defaultLinkTypeId = $defaults['linkType_id'] ?? 2;
        $defaultLinkStatus = $defaults['link_status'] ?? 2;

        $result = [
            'accounts_processed' => 0,
            'customers_created' => 0,
            'customers_skipped_existing' => 0,
            'links_created' => 0,
            'links_skipped_existing' => 0,
            'accounts_skipped_missing_account_number' => 0,
            'links_skipped_missing_link_name' => 0,
        ];

        DB::transaction(function () use (
            $accounts,
            $defaultCityId,
            $defaultSuburbId,
            $defaultPopId,
            $defaultLinkTypeId,
            $defaultLinkStatus,
            &$result
        ) {
            foreach ($accounts as $account) {
                if (!is_array($account)) {
                    continue;
                }

                $normalizedAccount = $this->normalizeAccount($account);
                $accountNumber = $normalizedAccount['account_number'];
                if ($accountNumber === null) {
                    $result['accounts_skipped_missing_account_number']++;
                    continue;
                }

                $result['accounts_processed']++;

                $customer = Customer::query()
                    ->where('account_number', $accountNumber)
                    ->first();

                if ($customer) {
                    $result['customers_skipped_existing']++;
                } else {
                    $customer = Customer::query()->create(Arr::only($normalizedAccount, [
                        'account_number',
                        'contract_number',
                        'customer',
                        'account_manager_id',
                        'customer_status',
                        'address',
                        'contact_number',
                    ]));
                    $result['customers_created']++;
                }

                $links = $this->extractLinks($account);
                foreach ($links as $linkPayload) {
                    $normalizedLink = $this->normalizeLink($linkPayload);
                    $linkName = $normalizedLink['link'];
                    if ($linkName === null) {
                        $result['links_skipped_missing_link_name']++;
                        continue;
                    }

                    $values = array_merge($normalizedLink, [
                        'customer_id' => $customer->id,
                        'account_number' => $accountNumber,
                        'contract_number' => $normalizedLink['contract_number'] ?? $customer->contract_number,
                        'city_id' => $normalizedLink['city_id'] ?? $defaultCityId,
                        'suburb_id' => $normalizedLink['suburb_id'] ?? $defaultSuburbId,
                        'pop_id' => $normalizedLink['pop_id'] ?? $defaultPopId,
                        'linkType_id' => $normalizedLink['linkType_id'] ?? $defaultLinkTypeId,
                        'link_status' => $normalizedLink['link_status'] ?? $defaultLinkStatus,
                    ]);

                    $existing = $this->findExistingLink($customer->id, $normalizedLink);

                    if ($existing) {
                        $result['links_skipped_existing']++;
                    } else {
                        Link::query()->create(Arr::only($values, (new Link())->getFillable()));
                        $result['links_created']++;
                    }
                }
            }
        });

        return $result;
    }

    private function normalizeAccount(array $account): array
    {
        $accountNumber = $this->stringOrNull(
            $account['account_number'] ?? $account['accountNumber'] ?? $account['account'] ?? null
        );

        $contractNumber = $this->stringOrNull(
            $account['contract_number'] ?? $account['contractNumber'] ?? null
        );

        return [
            'account_number' => $accountNumber,
            'contract_number' => $contractNumber,
            'customer' => $this->stringOrNull($account['customer'] ?? $account['name'] ?? $account['customer_name'] ?? null),
            'account_manager_id' => $this->resolveAccountManagerId(
                $account['account_manager_id'] ?? $account['accountManagerId'] ?? null,
                $account['manager_name'] ?? $account['managerName'] ?? $account['account_manager'] ?? null
            ),
            'customer_status' => $this->resolveCustomerStatus(
                $account['customer_status'] ?? $account['customerStatus'] ?? $account['status'] ?? null
            ),
            'address' => $this->stringOrNull($account['address'] ?? null),
            'contact_number' => $this->stringOrNull(
                $account['contact_number'] ?? $account['contactNumber'] ?? $account['phone'] ?? $account['phone_number'] ?? null
            ),
        ];
    }

    private function extractLinks(array $account): array
    {
        $links = $account['links'] ?? $account['Links'] ?? $account['services'] ?? null;
        if (is_string($links) && trim($links) !== '') {
            return [['link' => $links]];
        }

        if (!is_array($links)) {
            return [];
        }

        $normalized = [];

        foreach (array_values($links) as $linkPayload) {
            if (is_string($linkPayload) && trim($linkPayload) !== '') {
                $normalized[] = ['link' => $linkPayload];
                continue;
            }

            if (!is_array($linkPayload)) {
                continue;
            }

            $serviceNames = $this->normalizeServiceNames($linkPayload['services'] ?? null);
            $explicitLinkName = $this->stringOrNull($linkPayload['link'] ?? $linkPayload['name'] ?? $linkPayload['service'] ?? null);

            if ($explicitLinkName !== null || $serviceNames === []) {
                if ($explicitLinkName === null && count($serviceNames) === 1) {
                    $linkPayload['link'] = $serviceNames[0];
                }

                if (
                    !array_key_exists('service_type', $linkPayload)
                    && !array_key_exists('serviceType', $linkPayload)
                    && $serviceNames !== []
                ) {
                    $linkPayload['service_type'] = implode(', ', $serviceNames);
                }

                $normalized[] = $linkPayload;
                continue;
            }

            foreach ($serviceNames as $serviceName) {
                $expandedPayload = $linkPayload;
                $expandedPayload['link'] = $serviceName;
                $expandedPayload['service_type'] = $expandedPayload['service_type'] ?? $serviceName;
                $normalized[] = $expandedPayload;
            }
        }

        return $normalized;
    }

    private function normalizeLink(array $linkPayload): array
    {
        $linkName = $this->stringOrNull($linkPayload['link'] ?? $linkPayload['name'] ?? $linkPayload['service'] ?? null);

        return [
            'link' => $linkName,
            'jcc_number' => $this->stringOrNull($linkPayload['jcc_number'] ?? $linkPayload['jccNumber'] ?? null),
            'sapcodes' => $this->stringOrNull($linkPayload['sapcodes'] ?? $linkPayload['sapCodes'] ?? $linkPayload['sap'] ?? null),
            'comment' => $this->stringOrNull($linkPayload['comment'] ?? null),
            'quantity' => $this->intOrNull($linkPayload['quantity'] ?? null),
            'service_type' => $this->stringOrNull(
                $linkPayload['service_type'] ?? $linkPayload['serviceType'] ?? $this->stringifyServices($linkPayload['services'] ?? null)
            ),
            'capacity' => $this->stringOrNull($linkPayload['capacity'] ?? null),
            'city_id' => $this->intOrNull($linkPayload['city_id'] ?? $linkPayload['cityId'] ?? null),
            'suburb_id' => $this->intOrNull($linkPayload['suburb_id'] ?? $linkPayload['suburbId'] ?? null),
            'pop_id' => $this->intOrNull($linkPayload['pop_id'] ?? $linkPayload['popId'] ?? null),
            'linkType_id' => $this->resolveLinkTypeId(
                $linkPayload['linkType_id'] ?? $linkPayload['linkTypeId'] ?? $linkPayload['type'] ?? null
            ),
            'link_status' => $this->resolveLinkStatusId(
                $linkPayload['link_status'] ?? $linkPayload['linkStatus'] ?? $linkPayload['status'] ?? null
            ),
            'contract_number' => $this->stringOrNull($linkPayload['contract_number'] ?? $linkPayload['contractNumber'] ?? null),
        ];
    }

    private function findExistingLink(int $customerId, array $normalizedLink): ?Link
    {
        $linkName = $normalizedLink['link'] ?? null;
        if ($linkName !== null) {
            $exact = Link::query()
                ->where('customer_id', $customerId)
                ->where('link', $linkName)
                ->first();

            if ($exact) {
                return $exact;
            }
        }

        foreach (['jcc_number', 'sapcodes'] as $field) {
            $value = $normalizedLink[$field] ?? null;
            if ($value === null) {
                continue;
            }

            $match = Link::query()
                ->where('customer_id', $customerId)
                ->where($field, $value)
                ->first();

            if ($match) {
                return $match;
            }
        }

        if ($linkName === null) {
            return null;
        }

        $incomingFingerprint = $this->linkNameFingerprint($linkName);
        if ($incomingFingerprint === null) {
            return null;
        }

        $incomingServiceType = $this->linkNameFingerprint($normalizedLink['service_type'] ?? null);

        $candidates = Link::query()
            ->where('customer_id', $customerId)
            ->get(['id', 'link', 'service_type']);

        foreach ($candidates as $candidate) {
            $candidateFingerprint = $this->linkNameFingerprint($candidate->link);
            if ($candidateFingerprint === null) {
                continue;
            }

            $candidateServiceType = $this->linkNameFingerprint($candidate->service_type);
            if ($this->looksLikeSameLink($incomingFingerprint, $candidateFingerprint, $incomingServiceType, $candidateServiceType)) {
                return $candidate;
            }
        }

        return null;
    }

    private function looksLikeSameLink(
        string $incomingFingerprint,
        string $existingFingerprint,
        ?string $incomingServiceType,
        ?string $existingServiceType
    ): bool {
        if ($incomingFingerprint === $existingFingerprint) {
            return true;
        }

        if (
            str_contains($incomingFingerprint, $existingFingerprint)
            || str_contains($existingFingerprint, $incomingFingerprint)
        ) {
            return true;
        }

        if (
            $incomingServiceType !== null
            && $existingServiceType !== null
            && $incomingServiceType !== $existingServiceType
        ) {
            return false;
        }

        return false;
    }

    private function linkNameFingerprint(mixed $value): ?string
    {
        $name = $this->stringOrNull($value);
        if ($name === null) {
            return null;
        }

        $name = strtoupper($name);
        $name = preg_replace('/^\d+\s*[- ]\s*/', '', $name);
        $name = preg_replace('/[^A-Z0-9]+/', ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        $name = $this->applyLinkAliases($name);
        $name = trim($name);

        return $name === '' ? null : $name;
    }

    private function applyLinkAliases(string $value): string
    {
        $aliases = [
            '/\bPWT ?INT\b/' => 'INTERNET',
        ];

        $normalized = preg_replace(array_keys($aliases), array_values($aliases), $value);
        if (!is_string($normalized)) {
            return $value;
        }

        $normalized = preg_replace('/\s+/', ' ', $normalized);

        return is_string($normalized) ? trim($normalized) : trim($value);
    }

    private function normalizeServiceNames(mixed $services): array
    {
        if (is_string($services)) {
            $service = $this->stringOrNull($services);
            return $service === null ? [] : [$service];
        }

        if (!is_array($services)) {
            return [];
        }

        $normalized = [];

        foreach ($services as $service) {
            $name = $this->stringOrNull($service);
            if ($name !== null) {
                $normalized[] = $name;
            }
        }

        return array_values(array_unique($normalized));
    }

    private function stringifyServices(mixed $services): ?string
    {
        $serviceNames = $this->normalizeServiceNames($services);
        if ($serviceNames === []) {
            return null;
        }

        return implode(', ', $serviceNames);
    }

    private function resolveAccountManagerId(mixed $value, mixed $managerName = null): ?int
    {
        $directId = $this->intOrNull($value);
        if ($directId !== null) {
            return $directId;
        }

        $name = $this->stringOrNull($managerName);
        if ($name === null) {
            return null;
        }

        try {
            $id = AccountManager::query()
                ->whereRaw('LOWER(accountManager) = ?', [strtolower($name)])
                ->value('id');

            return $id ? (int) $id : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function resolveCustomerStatus(mixed $value): ?int
    {
        $directId = $this->intOrNull($value);
        if ($directId !== null) {
            return $directId;
        }

        return $this->statusIdFromName($value);
    }

    private function resolveLinkStatusId(mixed $value): ?int
    {
        $directId = $this->intOrNull($value);
        if ($directId !== null) {
            return $directId;
        }

        $mappedId = $this->statusIdFromName($value);
        if ($mappedId !== null) {
            return $mappedId;
        }

        $name = $this->stringOrNull($value);
        if ($name === null) {
            return null;
        }

        try {
            $id = LinkStatus::query()
                ->whereRaw('LOWER(link_status) = ?', [strtolower($name)])
                ->value('id');

            return $id ? (int) $id : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function resolveLinkTypeId(mixed $value): ?int
    {
        $directId = $this->intOrNull($value);
        if ($directId !== null) {
            return $directId;
        }

        $name = $this->stringOrNull($value);
        if ($name === null) {
            return null;
        }

        $aliases = [
            'internal' => 1,
            'external' => 2,
        ];

        $normalized = strtolower($name);
        if (array_key_exists($normalized, $aliases)) {
            return $aliases[$normalized];
        }

        try {
            $id = LinkType::query()
                ->whereRaw('LOWER(linkType) = ?', [$normalized])
                ->value('id');

            return $id ? (int) $id : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function statusIdFromName(mixed $value): ?int
    {
        $name = $this->stringOrNull($value);
        if ($name === null) {
            return null;
        }

        $aliases = [
            'pending' => 1,
            'active' => 2,
            'connected' => 2,
            'inactive' => 3,
            'disconnected' => 3,
            'suspended' => 3,
            'decommissioned' => 4,
            'terminated' => 4,
        ];

        return $aliases[strtolower($name)] ?? null;
    }

    private function stringOrNull(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $s = trim((string) $value);
        return $s === '' ? null : $s;
    }

    private function intOrNull(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        $s = trim((string) $value);
        if ($s === '') {
            return null;
        }

        if (!preg_match('/^-?\d+$/', $s)) {
            return null;
        }

        return (int) $s;
    }
}
