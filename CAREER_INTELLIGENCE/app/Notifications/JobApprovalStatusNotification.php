<?php

namespace App\Notifications;

use App\Models\JobListing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JobApprovalStatusNotification extends Notification
{
    use Queueable;

    public function __construct(public JobListing $job, public string $statusAction) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $isApproved = $this->statusAction === 'approved';
        $message = $isApproved
            ? "Lowongan '{$this->job->title}' telah disetujui oleh Administrator dan kini dipublikasikan."
            : "Pengajuan lowongan '{$this->job->title}' ditolak oleh Administrator.";

        return [
            'type' => 'job_approval_status',
            'job_id' => $this->job->id,
            'job_title' => $this->job->title,
            'status' => $this->statusAction,
            'message' => $message,
        ];
    }
}
