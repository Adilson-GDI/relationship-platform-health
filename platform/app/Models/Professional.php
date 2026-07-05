<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Professional extends Model
{
    protected $fillable = [
        'health_application_id',
        'name',
        'email',
        'phone',
        'city',
        'state',
        'profession',
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

    public function healthApplication(): BelongsTo
    {
        return $this->belongsTo(HealthApplication::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }
}
