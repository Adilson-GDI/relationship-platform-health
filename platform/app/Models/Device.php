<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Device extends Model
{
    protected $fillable = [
        'professional_id',
        'health_application_id',
        'device_id',
        'platform',
        'app_version',
        'push_token',
        'is_blocked',
        'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'is_blocked' => 'boolean',
            'last_seen_at' => 'datetime',
        ];
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function healthApplication(): BelongsTo
    {
        return $this->belongsTo(HealthApplication::class);
    }
}
