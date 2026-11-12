<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

#[Fillable([
    'user_id',
    'role',
    'action',
    'title',
    'description',
    'subject_type',
    'subject_id',
    'properties',
    'ip_address',
    'user_agent',
])]
class ActivityLog extends Model
{
    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Helper to record an activity log seamlessly.
     */
    public static function record(
        string $action,
        string $title,
        string $description,
        ?Model $subject = null,
        ?array $properties = null,
        ?User $user = null
    ): self {
        $currentUser = $user ?? Auth::user();

        return static::create([
            'user_id' => $currentUser?->id,
            'role' => $currentUser?->role ?? 'system',
            'action' => $action,
            'title' => $title,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'properties' => $properties,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    /**
     * Badge styles and icons for UI display.
     */
    public function getActionBadgeAttribute(): array
    {
        return match (true) {
            str_contains($this->action, 'interview') => [
                'bg' => 'bg-purple-50 text-purple-700 border-purple-200',
                'icon' => 'calendar',
                'label' => 'Wawancara',
            ],
            str_contains($this->action, 'accepted') || str_contains($this->action, 'approved') => [
                'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'icon' => 'check-circle',
                'label' => 'Disetujui',
            ],
            str_contains($this->action, 'rejected') || str_contains($this->action, 'deleted') => [
                'bg' => 'bg-rose-50 text-rose-700 border-rose-200',
                'icon' => 'x-circle',
                'label' => 'Penolakan',
            ],
            str_contains($this->action, 'job') => [
                'bg' => 'bg-blue-50 text-blue-700 border-blue-200',
                'icon' => 'briefcase',
                'label' => 'Lowongan',
            ],
            str_contains($this->action, 'student') || str_contains($this->action, 'candidate') => [
                'bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                'icon' => 'user',
                'label' => 'Mahasiswa',
            ],
            str_contains($this->action, 'email') => [
                'bg' => 'bg-sky-50 text-sky-700 border-sky-200',
                'icon' => 'mail',
                'label' => 'Email',
            ],
            default => [
                'bg' => 'bg-slate-50 text-slate-700 border-slate-200',
                'icon' => 'activity',
                'label' => 'Aktivitas',
            ],
        };
    }
}
