<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
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

    public function unreadCount(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'unread' => $user ? $user->unreadNotifications()->count() : 0,
        ]);
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
}
