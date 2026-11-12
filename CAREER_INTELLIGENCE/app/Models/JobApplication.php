<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'job_listing_id', 'match_score', 'status', 'is_invited', 'notes', 'interview_date', 'interview_type', 'interview_link', 'interview_location', 'interview_status'])]
class JobApplication extends Model
{
    protected function casts(): array
    {
        return [
            'interview_date' => 'datetime',
            'match_score' => 'float',
            'is_invited' => 'boolean',
        ];
    }

    public function isOnlineInterview(): bool
    {
        if ($this->interview_type === 'offline' || empty(trim((string) $this->interview_link))) {
            return false;
        }

        return true;
    }

    public function isOfflineInterview(): bool
    {
        return ! $this->isOnlineInterview();
    }

    public function getInterviewTypeLabelAttribute(): string
    {
        return $this->isOnlineInterview() ? 'Online (Daring)' : 'Offline (Tatap Muka)';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobListing(): BelongsTo
    {
        return $this->belongsTo(JobListing::class);
    }
}
