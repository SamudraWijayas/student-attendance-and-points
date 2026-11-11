<?php

namespace App\Models;

use App\Enums\PointType;
use App\Enums\ViolationSeverity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PointCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'severity',
        'category_name',
        'action_name',
        'min_points',
        'max_points',
        'default_points',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => PointType::class,
            'severity' => ViolationSeverity::class,
            'min_points' => 'integer',
            'max_points' => 'integer',
            'default_points' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }
}

