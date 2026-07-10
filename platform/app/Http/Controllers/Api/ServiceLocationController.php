<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\HealthApplication;
use App\Models\ServiceLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceLocationController extends Controller
{
    public function sync(Request $request): JsonResponse
    {
        $data = $request->validate([
            'app_code' => ['required', 'string'], 'professional_id' => ['required', 'integer'], 'device_id' => ['required', 'string'],
            'locations' => ['required', 'array', 'max:100'], 'locations.*.local_id' => ['required', 'integer'], 'locations.*.deleted' => ['nullable', 'boolean'],
            'locations.*.name' => ['nullable', 'string', 'max:255'], 'locations.*.address' => ['nullable', 'string', 'max:255'],
            'locations.*.neighborhood' => ['nullable', 'string', 'max:120'], 'locations.*.city' => ['nullable', 'string', 'max:120'],
            'locations.*.state' => ['nullable', 'string', 'max:2'], 'locations.*.zip_code' => ['nullable', 'string', 'max:20'],
            'locations.*.latitude' => ['nullable', 'numeric', 'between:-90,90'], 'locations.*.longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'locations.*.type' => ['nullable', 'string', 'max:40'], 'locations.*.notes' => ['nullable', 'string'], 'locations.*.is_public' => ['nullable', 'boolean'],
        ]);
        $app = HealthApplication::where('code', $data['app_code'])->where('is_active', true)->firstOrFail();
        $device = Device::where('health_application_id', $app->id)->where('professional_id', $data['professional_id'])->where('device_id', $data['device_id'])->firstOrFail();
        $synced = DB::transaction(function () use ($data, $app, $device): array {
            $ids = [];
            foreach ($data['locations'] as $location) {
                $key = ['health_application_id' => $app->id, 'professional_id' => $device->professional_id, 'local_id' => $location['local_id']];
                if ($location['deleted'] ?? false) { ServiceLocation::where($key)->delete(); $ids[] = $location['local_id']; continue; }
                ServiceLocation::updateOrCreate($key, [
                    'device_id' => $device->id, 'name' => $location['name'] ?? 'Local', 'address' => $location['address'] ?? null,
                    'neighborhood' => $location['neighborhood'] ?? null, 'city' => $location['city'] ?? null,
                    'state' => isset($location['state']) ? strtoupper($location['state']) : null, 'zip_code' => $location['zip_code'] ?? null,
                    'latitude' => $location['latitude'] ?? null, 'longitude' => $location['longitude'] ?? null, 'type' => $location['type'] ?? 'OUTRO',
                    'notes' => $location['notes'] ?? null, 'is_public' => $location['is_public'] ?? false,
                ]); $ids[] = $location['local_id'];
            }
            return $ids;
        });
        return response()->json(['success' => true, 'synced_local_ids' => $synced]);
    }
}
