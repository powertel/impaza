<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPushToken;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpoPushService
{
    private function pushSendDebug(string $message, array $context = []): void
    {
        try {
            $line = json_encode([
                'ts' => now()->toIso8601String(),
                'message' => $message,
                'context' => $context,
            ], JSON_UNESCAPED_SLASHES);
            @file_put_contents(storage_path('logs/expo_push.log'), $line . PHP_EOL, FILE_APPEND);
        } catch (\Throwable $e) {
        }
    }

    public function sendToUsers($users, string $title, string $body, array $data = []): void
    {
        $userIds = collect($users)->map(function ($u) {
            if ($u instanceof User) {
                return $u->id;
            }
            return (int) $u;
        })->filter()->unique()->values();

        if ($userIds->isEmpty()) {
            return;
        }

        $tokens = UserPushToken::query()
            ->whereIn('user_id', $userIds->all())
            ->pluck('expo_push_token')
            ->filter()
            ->unique()
            ->values();

        if ($tokens->isEmpty()) {
            $this->pushSendDebug('sendToUsers no tokens', [
                'user_ids_count' => $userIds->count(),
                'event' => $data['event'] ?? null,
            ]);
            return;
        }

        $this->sendToTokens($tokens, $title, $body, $data);
    }

    public function sendToTokens(Collection $tokens, string $title, string $body, array $data = []): void
    {
        $tokens = $tokens->filter()->unique()->values();
        if ($tokens->isEmpty()) {
            return;
        }

        $endpoint = 'https://exp.host/--/api/v2/push/send';

        foreach ($tokens->chunk(100) as $chunk) {
            $messages = $chunk->map(function ($token) use ($title, $body, $data) {
                return [
                    'to' => $token,
                    'title' => $title,
                    'body' => $body,
                    'data' => $data,
                    'sound' => 'default',
                    'priority' => 'high',
                    'channelId' => 'impazamon_alerts',
                ];
            })->values()->all();

            try {
                $response = Http::timeout(15)->post($endpoint, $messages);
                $this->pushSendDebug('sendToTokens response', [
                    'status' => $response->status(),
                    'ok' => $response->successful(),
                    'tokens_count' => $chunk->count(),
                    'event' => $data['event'] ?? null,
                    'body' => $response->json(),
                ]);
                if (!$response->successful()) {
                    Log::warning('Expo push: request failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            } catch (\Throwable $e) {
                $this->pushSendDebug('sendToTokens exception', [
                    'tokens_count' => $chunk->count(),
                    'event' => $data['event'] ?? null,
                    'error' => $e->getMessage(),
                ]);
                report($e);
            }
        }
    }
}
