<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApplicationStatusUpdated extends Notification
{
    use Queueable;

    protected $application;

    /**
     * Create a new notification instance.
     */
    public function __construct($application)
    {
        $this->application = $application;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $typeLabel = $this->application->isOfflineInterview() ? 'Offline (Tatap Muka)' : 'Online';
        $statusMsg = match ($this->application->status) {
            'Wawancara' => $this->application->interview_date
                ? "Undangan Wawancara {$typeLabel}: ".Carbon::parse($this->application->interview_date)->translatedFormat('l, d F Y - H:i').' WIB'
                : "Anda mendapat undangan wawancara ({$typeLabel})!",
            'Diterima' => 'Selamat, Anda diterima magang/bekerja!',
            'Ditolak' => 'Maaf, lamaran Anda belum lolos.',
            'Proses Seleksi' => 'Lamaran Anda masuk ke tahap seleksi.',
            default => 'Status lamaran diperbarui: '.$this->application->status,
        };

        return [
            'application_id' => $this->application->id,
            'job_title' => $this->application->jobListing->title,
            'mitra_name' => $this->application->jobListing->user->name,
            'status' => $this->application->status,
            'message' => $statusMsg,
            'interview_date' => $this->application->interview_date ? Carbon::parse($this->application->interview_date)->toIso8601String() : null,
            'interview_type' => $this->application->isOfflineInterview() ? 'offline' : 'online',
            'interview_link' => $this->application->interview_link,
            'interview_location' => $this->application->interview_location,
            'interview_status' => $this->application->interview_status,
        ];
    }
}
