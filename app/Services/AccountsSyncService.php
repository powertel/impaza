<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Link;
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
            'customers_upserted' => 0,
            'links_created' => 0,
            'links_updated' => 0,
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

                $customer = Customer::query()->updateOrCreate(
                    ['account_number' => $accountNumber],
                    Arr::only($normalizedAccount, [
                        'account_number',
                        'contract_number',
                        'customer',
                        'address',
                        'contact_number',
                    ])
                );
                $result['customers_upserted']++;

                $links = $this->extractLinks($account);
                foreach ($links as $linkPayload) {
                    $normalizedLink = $this->normalizeLink($linkPayload);
                    $linkName = $normalizedLink['link'];
                    if ($linkName === null) {
                        $result['links_skipped_missing_link_name']++;
                        continue;
                    }

                    $attributes = [
                        'customer_id' => $customer->id,
                        'link' => $linkName,
                    ];

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

                    $existing = Link::query()
                        ->where('customer_id', $customer->id)
                        ->where('link', $linkName)
                        ->first();

                    if ($existing) {
                        $existing->fill(Arr::only($values, $existing->getFillable()));
                        if ($existing->isDirty()) {
                            $existing->save();
                            $result['links_updated']++;
                        }
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
            'address' => $this->stringOrNull($account['address'] ?? null),
            'contact_number' => $this->stringOrNull($account['contact_number'] ?? $account['contactNumber'] ?? null),
        ];
    }

    private function extractLinks(array $account): array
    {
        $links = $account['links'] ?? $account['Links'] ?? $account['services'] ?? null;
        if (is_array($links)) {
            return array_values($links);
        }

        if (is_string($links) && trim($links) !== '') {
            return [['link' => $links]];
        }

        return [];
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
            'service_type' => $this->stringOrNull($linkPayload['service_type'] ?? $linkPayload['serviceType'] ?? null),
            'capacity' => $this->stringOrNull($linkPayload['capacity'] ?? null),
            'city_id' => $this->intOrNull($linkPayload['city_id'] ?? $linkPayload['cityId'] ?? null),
            'suburb_id' => $this->intOrNull($linkPayload['suburb_id'] ?? $linkPayload['suburbId'] ?? null),
            'pop_id' => $this->intOrNull($linkPayload['pop_id'] ?? $linkPayload['popId'] ?? null),
            'linkType_id' => $this->intOrNull($linkPayload['linkType_id'] ?? $linkPayload['linkTypeId'] ?? null),
            'link_status' => $this->intOrNull($linkPayload['link_status'] ?? $linkPayload['linkStatus'] ?? null),
            'contract_number' => $this->stringOrNull($linkPayload['contract_number'] ?? $linkPayload['contractNumber'] ?? null),
        ];
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
