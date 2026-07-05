<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureFlag extends Model
{
    protected $fillable = [
        'health_application_id',
        'key',
        'name',
        'enabled',
        'rules',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'rules' => 'array',
        ];
    }

    public function healthApplication(): BelongsTo
    {
        return $this->belongsTo(HealthApplication::class);
    }
}
