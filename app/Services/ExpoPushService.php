<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPushToken;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpoPushService
{
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
                if (!$response->successful()) {
                    Log::warning('Expo push: request failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
}
