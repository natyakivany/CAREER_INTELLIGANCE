<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CandidateResponseNotification extends Notification
{
    use Queueable;

    public function __construct(
        public JobApplication $application,
        public string $responseType, // 'Bersedia' or 'Tidak Bersedia'
        public ?string $stage = null // 'Tes / Seleksi' or 'Wawancara'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $applicantName = $this->application->user->name ?? 'Mahasiswa';
        $jobTitle = $this->application->jobListing->title ?? 'Lowongan';
        $stage = $this->stage ?? ($this->application->status === 'Proses Seleksi' ? 'Tes / Seleksi Khusus' : 'Wawancara');
        $isWilling = ($this->responseType === 'Bersedia');

        if ($this->application->status === 'Proses Seleksi') {
            $message = $isWilling
                ? "{$applicantName} bersedia mengikuti {$stage} untuk posisi {$jobTitle}."
                : "{$applicantName} menyatakan tidak bersedia mengikuti {$stage} untuk posisi {$jobTitle}.";
        } else {
            $message = $isWilling
                ? "{$applicantName} mengonfirmasi kehadiran sesi {$stage} untuk posisi {$jobTitle}."
                : "{$applicantName} tidak dapat hadir / menolak jadwal {$stage} untuk posisi {$jobTitle}.";
        }

        return [
            'type' => 'candidate_response',
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_listing_id,
            'job_title' => $jobTitle,
            'applicant_name' => $applicantName,
            'stage' => $stage,
            'response' => $this->responseType,
            'status' => $this->application->status,
            'message' => $message,
            'url' => route('mitra.applications.show', $this->application->job_listing_id),
        ];
    }
}
