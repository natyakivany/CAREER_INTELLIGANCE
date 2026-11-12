<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Occupation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function skills(): HasMany
    {
        return $this->hasMany(OccupationSkill::class);
    }
}
