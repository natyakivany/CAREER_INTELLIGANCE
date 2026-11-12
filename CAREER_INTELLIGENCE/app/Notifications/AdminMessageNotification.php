<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminMessageNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $subject,
        public string $content,
        public ?string $senderName = 'Administrator'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $targetUrl = ($notifiable->role === 'mitra') ? route('mitra.dashboard') : route('dashboard');

        return [
            'type' => 'admin_message',
            'subject' => $this->subject,
            'message' => "Pesan dari {$this->senderName}: {$this->subject}",
            'content' => $this->content,
            'sender' => $this->senderName,
            'url' => $targetUrl,
            'sent_at' => now()->toIso8601String(),
        ];
    }
}
