<?php

namespace App\Notifications;

use App\Models\PendingProfileUpdate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProfileUpdateRequested extends Notification
{
    use Queueable;

    public $updateRequest;

    public function __construct(PendingProfileUpdate $updateRequest)
    {
        $this->updateRequest = $updateRequest;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'profile_update',
            'title' => 'Pengajuan Perubahan Profil',
            'message' => "Mitra {$this->updateRequest->user->name} mengajukan perubahan profil.",
            'url' => route('admin.mitra'),
            'request_id' => $this->updateRequest->id,
        ];
    }
}
