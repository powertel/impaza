<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\UserPushToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    private function pushTokenDebug(string $message, array $context = []): void
    {
        try {
            $line = json_encode([
                'ts' => now()->toIso8601String(),
                'message' => $message,
                'context' => $context,
            ], JSON_UNESCAPED_SLASHES);
            @file_put_contents(storage_path('logs/push_tokens.log'), $line . PHP_EOL, FILE_APPEND);
        } catch (\Throwable $e) {
        }
    }

    public function registerPushToken(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            Log::warning('Mobile push token register: unauthenticated', [
                'ip' => $request->ip(),
                'ua' => (string) $request->userAgent(),
            ]);
            $this->pushTokenDebug('register unauthenticated', [
                'ip' => $request->ip(),
                'ua' => (string) $request->userAgent(),
                'host' => (string) $request->getHost(),
            ]);
            return response()->json(['success' => false], 401);
        }

        try {
            $validated = $request->validate([
                'token' => 'required|string|max:255',
                'platform' => 'nullable|string|max:32',
                'device_id' => 'nullable|string|max:255',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Mobile push token register: validation failed', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'ua' => (string) $request->userAgent(),
                'has_token' => $request->filled('token'),
                'platform' => $request->input('platform'),
                'device_id' => $request->input('device_id'),
                'error' => $e->getMessage(),
            ]);
            $this->pushTokenDebug('register validation failed', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'host' => (string) $request->getHost(),
                'has_token' => (bool) $request->filled('token'),
                'platform' => $request->input('platform'),
                'device_id' => $request->input('device_id'),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        $tokenHash = substr(hash('sha256', $validated['token']), 0, 12);
        Log::info('Mobile push token register: received', [
            'user_id' => $user->id,
            'token_hash' => $tokenHash,
            'platform' => $validated['platform'] ?? null,
            'device_id' => $validated['device_id'] ?? null,
            'db_connection' => (string) config('database.default'),
            'db_name' => (string) (DB::connection()->getDatabaseName() ?? ''),
            'ip' => $request->ip(),
        ]);
        $this->pushTokenDebug('register received', [
            'user_id' => $user->id,
            'token_hash' => $tokenHash,
            'platform' => $validated['platform'] ?? null,
            'device_id' => $validated['device_id'] ?? null,
            'db_connection' => (string) config('database.default'),
            'db_name' => (string) (DB::connection()->getDatabaseName() ?? ''),
            'host' => (string) $request->getHost(),
            'ip' => $request->ip(),
        ]);

        try {
            UserPushToken::updateOrCreate(
                ['expo_push_token' => $validated['token']],
                [
                    'user_id' => $user->id,
                    'platform' => $validated['platform'] ?? null,
                    'device_id' => $validated['device_id'] ?? null,
                    'last_seen_at' => now(),
                ]
            );
        } catch (\Throwable $e) {
            Log::error('Mobile push token register: DB write failed', [
                'user_id' => $user->id,
                'token_hash' => $tokenHash,
                'platform' => $validated['platform'] ?? null,
                'device_id' => $validated['device_id'] ?? null,
                'db_connection' => (string) config('database.default'),
                'db_name' => (string) (DB::connection()->getDatabaseName() ?? ''),
                'error' => $e->getMessage(),
            ]);
            $this->pushTokenDebug('register db write failed', [
                'user_id' => $user->id,
                'token_hash' => $tokenHash,
                'platform' => $validated['platform'] ?? null,
                'device_id' => $validated['device_id'] ?? null,
                'db_connection' => (string) config('database.default'),
                'db_name' => (string) (DB::connection()->getDatabaseName() ?? ''),
                'host' => (string) $request->getHost(),
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Token registration failed'], 500);
        }

        $count = UserPushToken::query()->where('user_id', $user->id)->count();
        Log::info('Mobile push token register: saved', [
            'user_id' => $user->id,
            'token_hash' => $tokenHash,
            'token_count' => $count,
        ]);
        $this->pushTokenDebug('register saved', [
            'user_id' => $user->id,
            'token_hash' => $tokenHash,
            'token_count' => $count,
            'db_name' => (string) (DB::connection()->getDatabaseName() ?? ''),
            'host' => (string) $request->getHost(),
        ]);
        $payload = ['success' => true, 'token_hash' => $tokenHash, 'token_count' => $count];
        if ((bool) config('app.debug')) {
            $payload['debug'] = [
                'db_connection' => (string) config('database.default'),
                'db_name' => (string) (DB::connection()->getDatabaseName() ?? ''),
                'host' => (string) $request->getHost(),
            ];
        }
        return response()->json($payload);
    }

    public function unregisterPushToken(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            Log::warning('Mobile push token unregister: unauthenticated', [
                'ip' => $request->ip(),
                'ua' => (string) $request->userAgent(),
            ]);
            return response()->json(['success' => false], 401);
        }

        $validated = $request->validate([
            'token' => 'required|string|max:255',
        ]);

        $tokenHash = substr(hash('sha256', $validated['token']), 0, 12);
        UserPushToken::query()
            ->where('user_id', $user->id)
            ->where('expo_push_token', $validated['token'])
            ->delete();

        Log::info('Mobile push token unregister: deleted', [
            'user_id' => $user->id,
            'token_hash' => $tokenHash,
        ]);
        return response()->json(['success' => true]);
    }

    public function pushTokenStatus(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        $tokens = UserPushToken::query()
            ->where('user_id', $user->id)
            ->orderByDesc('last_seen_at')
            ->get(['platform', 'device_id', 'last_seen_at', 'created_at', 'updated_at']);

        return response()->json([
            'success' => true,
            'token_count' => $tokens->count(),
            'tokens' => $tokens,
        ]);
    }

    public function testPush(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        $data = $request->validate([
            'title' => 'nullable|string|max:120',
            'body' => 'nullable|string|max:240',
        ]);

        $title = $data['title'] ?? 'iMpazamon';
        $body = $data['body'] ?? 'Test push notification';

        $tokens = UserPushToken::query()
            ->where('user_id', $user->id)
            ->pluck('expo_push_token')
            ->filter()
            ->unique()
            ->values();

        if ($tokens->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No push tokens registered', 'token_count' => 0], 422);
        }

        $endpoint = 'https://exp.host/--/api/v2/push/send';
        $messages = $tokens->map(function ($token) use ($title, $body) {
            return [
                'to' => $token,
                'title' => $title,
                'body' => $body,
                'data' => ['event' => 'test'],
                'sound' => 'default',
                'priority' => 'high',
                'channelId' => 'impazamon_alerts',
            ];
        })->values()->all();

        try {
            $response = Http::timeout(15)->post($endpoint, $messages);
            $json = $response->json();
            $items = is_array($json) ? ($json['data'] ?? null) : null;
            $items = is_array($items) ? $items : [];

            $ids = collect($items)
                ->map(function ($item) {
                    if (!is_array($item)) return null;
                    if (($item['status'] ?? null) !== 'ok') return null;
                    return $item['id'] ?? null;
                })
                ->filter()
                ->values();

            $receipts = null;
            if ($ids->isNotEmpty()) {
                try {
                    $receiptsRes = Http::timeout(15)->post('https://exp.host/--/api/v2/push/getReceipts', [
                        'ids' => $ids->all(),
                    ]);
                    $receiptsJson = $receiptsRes->json();
                    $receipts = [
                        'status' => $receiptsRes->status(),
                        'ok' => $receiptsRes->successful(),
                        'body' => $receiptsJson,
                    ];
                } catch (\Throwable $e) {
                    $receipts = [
                        'status' => null,
                        'ok' => false,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            return response()->json([
                'success' => $response->successful(),
                'token_count' => $tokens->count(),
                'status' => $response->status(),
                'body' => $json,
                'receipt_ids' => $ids,
                'receipts' => $receipts,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function unreadCount(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'unread' => $user ? $user->unreadNotifications()->count() : 0,
        ]);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['notifications' => []], 401);
        }

        $onlyUnread = (string) $request->query('unread', '0') === '1';
        $limit = (int) $request->query('limit', 20);
        $limit = max(1, min($limit, 50));

        $query = $onlyUnread ? $user->unreadNotifications() : $user->notifications();

        $items = $query->orderByDesc('created_at')->limit($limit)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'type' => $n->type,
                'data' => $n->data,
                'read_at' => $n->read_at,
                'created_at' => $n->created_at,
            ];
        })->values();

        return response()->json(['notifications' => $items]);
    }

    public function markRead(Request $request, string $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        $notification = $user->notifications()->where('id', $id)->first();
        if (!$notification) {
            return response()->json(['success' => false], 404);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        $user->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
