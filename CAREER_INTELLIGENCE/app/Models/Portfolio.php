<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Portfolio extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'completion_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get completeness percentage of this single portfolio item.
     */
    public function getCompletenessAttribute(): int
    {
        $score = 40; // Title & field (required)

        if (! empty($this->description)) {
            $score += 20;
        }

        if (! empty($this->tech_stack)) {
            $score += 15;
        }

        if (! empty($this->project_url)) {
            $score += 15;
        }

        if (! empty($this->repository_url)) {
            $score += 10;
        }

        return min(100, $score);
    }
}
