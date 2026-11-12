<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'nim', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function skillGapResults(): HasMany
    {
        return $this->hasMany(SkillGapResult::class);
    }

    public function careerRoadmaps(): HasMany
    {
        return $this->hasMany(CareerRoadmap::class);
    }

    public function jobListings(): HasMany
    {
        return $this->hasMany(JobListing::class);
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->studentProfile?->avatar_url) {
            return $this->studentProfile->avatar_url;
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name ?? 'Mahasiswa').'&background=0f172a&color=00c99f&size=150';
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->studentProfile?->phone;
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        return $this->studentProfile?->whatsapp_url;
    }

    /**
     * Check if the student user has filled their CV.
     */
    public function isCvFilled(): bool
    {
        return (bool) ($this->studentProfile?->isCvFilled());
    }
}
