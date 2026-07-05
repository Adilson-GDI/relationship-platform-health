<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppVersion extends Model
{
    protected $fillable = [
        'health_application_id',
        'platform',
        'version',
        'is_required',
        'is_active',
        'release_notes',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function healthApplication(): BelongsTo
    {
        return $this->belongsTo(HealthApplication::class);
    }
}
