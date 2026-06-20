<?php

declare(strict_types=1);

namespace App\Modules\Notifications\Libraries;

/**
 * NotificationService
 *
 * Orchestrates notification delivery across channels (email, SMS, in-app).
 * Currently provides a placeholder for future notification expansion.
 *
 * @package App\Modules\Notifications\Libraries
 * @author Senior Developer
 * @since 1.0.0
 */
class NotificationService
{
    /**
     * Send a notification to a user.
     *
     * @param int    $userId
     * @param string $title
     * @param string $message
     * @param string $channel 'email', 'sms', or 'in_app'
     *
     * @return bool
     */
    public function send(int $userId, string $title, string $message, string $channel = 'in_app'): bool
    {
        // Future implementation: dispatch to channel-specific handlers
        log_message('info', 'Notification queued', [
            'user_id' => $userId,
            'title'   => $title,
            'channel' => $channel,
        ]);

        return true;
    }
}
