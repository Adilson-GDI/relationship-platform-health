<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HealthApplication extends Model
{
    protected $fillable = [
        'code',
        'name',
        'primary_color',
        'secondary_color',
        'is_active',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function professionals(): HasMany
    {
        return $this->hasMany(Professional::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(AppVersion::class);
    }

    public function notices(): HasMany
    {
        return $this->hasMany(InAppNotice::class);
    }

    public function featureFlags(): HasMany
    {
        return $this->hasMany(FeatureFlag::class);
    }
}
