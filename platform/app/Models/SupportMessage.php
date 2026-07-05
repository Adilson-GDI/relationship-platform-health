<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportMessage extends Model
{
    protected $fillable = [
        'health_application_id',
        'professional_id',
        'local_user_id',
        'name',
        'email',
        'phone',
        'message',
        'platform',
        'app_version',
        'local_created_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'local_created_at' => 'datetime',
        ];
    }

    public function healthApplication(): BelongsTo
    {
        return $this->belongsTo(HealthApplication::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }
}
