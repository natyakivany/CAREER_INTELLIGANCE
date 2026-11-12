<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OccupationSkill extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'benchmark_score' => 'integer',
            'is_core' => 'boolean',
        ];
    }

    public function occupation(): BelongsTo
    {
        return $this->belongsTo(Occupation::class);
    }
}
