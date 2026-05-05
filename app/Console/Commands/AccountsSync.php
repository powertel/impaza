<?php

namespace App\Console\Commands;

use App\Services\AccountsSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AccountsSync extends Command
{
    protected $signature = 'accounts:sync
        {--url= : Accounts endpoint URL}
        {--timeout=15 : HTTP timeout seconds}
        {--interval=300 : Loop sleep seconds}
        {--loop : Run continuously}
    ';

    protected $description = 'Sync customers and links from external accounts endpoint';

    public function handle(AccountsSyncService $syncService): int
    {
        $url = $this->option('url') ?: env('ACCOUNTS_SYNC_URL', 'http://192.168.15.246:8080/api/v1/accounts');
        $timeout = (int) $this->option('timeout');
        $interval = max(1, (int) $this->option('interval'));
        $loop = (bool) $this->option('loop');

        do {
            $startedAt = microtime(true);

            $payload = $this->fetchAccountsPayload($url, $timeout);
            $accounts = $this->extractAccountsArray($payload);

            $defaults = [
                'city_id' => $this->envInt('ACCOUNTS_SYNC_DEFAULT_CITY_ID') ?? $this->minId('cities'),
                'suburb_id' => $this->envInt('ACCOUNTS_SYNC_DEFAULT_SUBURB_ID') ?? $this->minId('suburbs'),
                'pop_id' => $this->envInt('ACCOUNTS_SYNC_DEFAULT_POP_ID') ?? $this->minId('pops'),
                'linkType_id' => $this->envInt('ACCOUNTS_SYNC_DEFAULT_LINK_TYPE_ID') ?? 2,
                'link_status' => $this->envInt('ACCOUNTS_SYNC_DEFAULT_LINK_STATUS') ?? 2,
            ];

            $result = $syncService->sync($accounts, $defaults);

            $this->line(json_encode($result, JSON_UNESCAPED_SLASHES));

            if (!$loop) {
                break;
            }

            $elapsed = microtime(true) - $startedAt;
            $sleep = (int) max(0, $interval - $elapsed);
            if ($sleep > 0) {
                sleep($sleep);
            }
        } while (true);

        return Command::SUCCESS;
    }

    private function fetchAccountsPayload(string $url, int $timeout): array
    {
        $headers = [];

        $apiKey = env('ACCOUNTS_SYNC_API_KEY');
        if (is_string($apiKey) && trim($apiKey) !== '') {
            $headers['X-API-Key'] = trim($apiKey);
        }

        $bearer = env('ACCOUNTS_SYNC_BEARER_TOKEN');
        if (is_string($bearer) && trim($bearer) !== '') {
            $headers['Authorization'] = 'Bearer ' . trim($bearer);
        }

        $response = Http::retry(3, 500)
            ->timeout(max(1, $timeout))
            ->acceptJson()
            ->withHeaders($headers)
            ->get($url);

        $response->throw();

        $json = $response->json();
        return is_array($json) ? $json : [];
    }

    private function extractAccountsArray(array $payload): array
    {
        if (array_is_list($payload)) {
            return $payload;
        }

        $candidates = [
            $payload['data'] ?? null,
            $payload['accounts'] ?? null,
            $payload['results'] ?? null,
        ];

        foreach ($candidates as $c) {
            if (is_array($c)) {
                return array_values($c);
            }
        }

        return [];
    }

    private function envInt(string $key): ?int
    {
        $v = env($key);
        if ($v === null) {
            return null;
        }
        $s = trim((string) $v);
        if ($s === '' || !preg_match('/^-?\d+$/', $s)) {
            return null;
        }
        return (int) $s;
    }

    private function minId(string $table): ?int
    {
        try {
            $id = DB::table($table)->min('id');
            return $id ? (int) $id : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}

