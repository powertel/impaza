<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\UserPushToken;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function registerPushToken(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        $validated = $request->validate([
            'token' => 'required|string|max:255',
            'platform' => 'nullable|string|max:32',
            'device_id' => 'nullable|string|max:255',
        ]);

        UserPushToken::updateOrCreate(
            ['expo_push_token' => $validated['token']],
            [
                'user_id' => $user->id,
                'platform' => $validated['platform'] ?? null,
                'device_id' => $validated['device_id'] ?? null,
                'last_seen_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }

    public function unregisterPushToken(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        $validated = $request->validate([
            'token' => 'required|string|max:255',
        ]);

        UserPushToken::query()
            ->where('user_id', $user->id)
            ->where('expo_push_token', $validated['token'])
            ->delete();

        return response()->json(['success' => true]);
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
