<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Paginated list of all notifications for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $paginator = Auth::user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return $this->paginated($paginator, 'Notifications retrieved successfully.');
    }

    /**
     * Latest 10 notifications + unread count.
     */
    public function recent(): JsonResponse
    {
        $user = Auth::user();

        $notifications = $user
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return $this->success([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ], 'Recent notifications retrieved successfully.');
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(string $id): JsonResponse
    {
        $notification = Auth::user()
            ->notifications()
            ->find($id);

        if (! $notification) {
            return $this->notFound('Notification not found.');
        }

        $notification->markAsRead();

        return $this->success($notification, 'Notification marked as read.');
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllRead(): JsonResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return $this->success(null, 'All notifications marked as read.');
    }
}
