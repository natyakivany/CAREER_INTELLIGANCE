<?php

namespace App\Notifications;

use App\Models\JobListing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JobApprovalRequested extends Notification
{
    use Queueable;

    public $job;

    public function __construct(JobListing $job)
    {
        $this->job = $job;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'job_approval',
            'title' => 'Pengajuan Lowongan Baru',
            'message' => "Mitra {$this->job->user->name} membuat lowongan baru: {$this->job->title}.",
            'url' => route('admin.dashboard'),
            'job_id' => $this->job->id,
        ];
    }
}
