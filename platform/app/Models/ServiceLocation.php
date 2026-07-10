<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceLocation extends Model
{
    protected $fillable = ['health_application_id', 'professional_id', 'device_id', 'local_id', 'name', 'address', 'neighborhood', 'city', 'state', 'zip_code', 'latitude', 'longitude', 'type', 'notes', 'is_public'];
    protected function casts(): array { return ['latitude' => 'float', 'longitude' => 'float', 'is_public' => 'boolean']; }
}
