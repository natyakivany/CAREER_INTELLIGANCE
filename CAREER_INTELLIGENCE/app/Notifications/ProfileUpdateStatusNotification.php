<?php

namespace App\Notifications;

use App\Models\PendingProfileUpdate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProfileUpdateStatusNotification extends Notification
{
    use Queueable;

    public function __construct(public PendingProfileUpdate $updateRequest, public string $statusAction) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $isApproved = $this->statusAction === 'approved';
        $message = $isApproved
            ? 'Pengajuan perubahan profil perusahaan Anda telah disetujui oleh Administrator.'
            : 'Pengajuan perubahan profil perusahaan Anda ditolak oleh Administrator.';

        return [
            'type' => 'profile_update_status',
            'request_id' => $this->updateRequest->id,
            'status' => $this->statusAction,
            'message' => $message,
        ];
    }
}
