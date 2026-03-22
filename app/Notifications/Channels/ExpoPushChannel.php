<?php

namespace App\Notifications\Channels;

use App\Services\ExpoPushService;
use Illuminate\Notifications\Notification;

class ExpoPushChannel
{
    /**
     * Send the given notification via Expo Push.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toArray')) {
            return;
        }

        $data = $notification->toArray($notifiable);
        $title = $data['title'] ?? ($data['type'] ?? 'Notification');
        $body = $data['message'] ?? '';

        if (empty($body)) {
            return;
        }

        // Format title from type: 'group_expense_added' -> 'New Expense'
        $titleMap = [
            'group_expense_added' => 'New Expense',
            'added_to_group' => 'Added to Group',
            'settlement_requested' => 'Settlement Request',
            'settlement_completed' => 'Payment Received',
            'todo_assigned' => 'Task Assigned',
            'todo_reminder' => 'Task Reminder',
            'group_join_request' => 'Join Request',
            'removed_from_group' => 'Group Update',
            'join_request_rejected' => 'Join Request Update',
        ];

        $pushTitle = $titleMap[$data['type'] ?? ''] ?? 'JodTod';

        ExpoPushService::sendToUser($notifiable, $pushTitle, $body, [
            'type' => $data['type'] ?? null,
            'group_id' => $data['group_id'] ?? null,
            'todo_id' => $data['todo_id'] ?? null,
        ]);
    }
}
