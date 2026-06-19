<?php

namespace App\Console\Commands;

use App\Services\AccountsSyncService;
use Illuminate\Console\Command;
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

            $accounts = $this->fetchAccounts($url, $timeout);

            $defaults = [
                'city_id' => $this->envInt('ACCOUNTS_SYNC_DEFAULT_CITY_ID'),
                'suburb_id' => $this->envInt('ACCOUNTS_SYNC_DEFAULT_SUBURB_ID'),
                'pop_id' => $this->envInt('ACCOUNTS_SYNC_DEFAULT_POP_ID'),
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

    private function fetchAccounts(string $url, int $timeout): array
    {
        $request = $this->makeAccountsRequest($timeout);
        $payload = $this->fetchAccountsPayload($request, $url);
        $accounts = $this->extractAccountsArray($payload);
        $lastPage = $this->extractLastPage($payload);

        for ($page = 2; $page <= $lastPage; $page++) {
            $pagePayload = $this->fetchAccountsPayload($request, $this->urlWithPage($url, $page));
            $accounts = array_merge($accounts, $this->extractAccountsArray($pagePayload));
        }

        return $accounts;
    }

    private function makeAccountsRequest(int $timeout)
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

        return Http::retry(3, 500)
            ->timeout(max(1, $timeout))
            ->acceptJson()
            ->withHeaders($headers);
    }

    private function fetchAccountsPayload($request, string $url): array
    {
        $response = $request->get($url);

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

    private function extractLastPage(array $payload): int
    {
        $candidates = [
            $payload['pagination']['last_page'] ?? null,
            $payload['pagination']['lastPage'] ?? null,
            $payload['meta']['last_page'] ?? null,
            $payload['last_page'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (is_int($candidate) && $candidate >= 1) {
                return $candidate;
            }

            if (is_string($candidate) && preg_match('/^\d+$/', trim($candidate))) {
                return max(1, (int) trim($candidate));
            }
        }

        return 1;
    }

    private function urlWithPage(string $url, int $page): string
    {
        $parts = parse_url($url);
        if ($parts === false) {
            return $url;
        }

        $query = [];
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }
        $query['page'] = $page;

        $rebuilt = '';
        if (isset($parts['scheme'])) {
            $rebuilt .= $parts['scheme'] . '://';
        }
        if (isset($parts['user'])) {
            $rebuilt .= $parts['user'];
            if (isset($parts['pass'])) {
                $rebuilt .= ':' . $parts['pass'];
            }
            $rebuilt .= '@';
        }
        if (isset($parts['host'])) {
            $rebuilt .= $parts['host'];
        }
        if (isset($parts['port'])) {
            $rebuilt .= ':' . $parts['port'];
        }
        $rebuilt .= $parts['path'] ?? '';

        $queryString = http_build_query($query);
        if ($queryString !== '') {
            $rebuilt .= '?' . $queryString;
        }

        if (isset($parts['fragment'])) {
            $rebuilt .= '#' . $parts['fragment'];
        }

        return $rebuilt;
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
}
