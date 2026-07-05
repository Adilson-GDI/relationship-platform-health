<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use App\Models\Device;
use App\Models\FeatureFlag;
use App\Models\HealthApplication;
use App\Models\InAppNotice;
use App\Models\Professional;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfessionalRegistrationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'size:2'],
            'profession' => ['nullable', 'string', 'max:120'],
            'app_code' => ['required', 'string', Rule::exists('health_applications', 'code')->where('is_active', true)],
            'version' => ['required', 'string', 'max:40'],
            'platform' => ['required', 'string', 'max:40'],
            'device_id' => ['required', 'string', 'max:255'],
            'push_token' => ['nullable', 'string', 'max:500'],
        ]);

        $application = HealthApplication::where('code', $data['app_code'])->firstOrFail();

        $professional = Professional::query()
            ->when($data['email'] ?? null, fn ($query) => $query->where('email', $data['email']))
            ->where('health_application_id', $application->id)
            ->first();

        if (! $professional) {
            $professional = Device::where('health_application_id', $application->id)
                ->where('device_id', $data['device_id'])
                ->first()
                ?->professional;
        }

        $professional = Professional::updateOrCreate(
            ['id' => $professional?->id],
            [
                'health_application_id' => $application->id,
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => isset($data['state']) ? strtoupper($data['state']) : null,
                'profession' => $data['profession'] ?? null,
                'last_seen_at' => now(),
            ]
        );

        $device = Device::updateOrCreate(
            [
                'health_application_id' => $application->id,
                'device_id' => $data['device_id'],
            ],
            [
                'professional_id' => $professional->id,
                'platform' => strtolower($data['platform']),
                'app_version' => $data['version'],
                'push_token' => $data['push_token'] ?? null,
                'last_seen_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Profissional cadastrado com sucesso.',
            'professional' => [
                'id' => $professional->id,
                'name' => $professional->name,
                'email' => $professional->email,
                'is_blocked' => $professional->is_blocked,
            ],
            'device' => [
                'id' => $device->id,
                'device_id' => $device->device_id,
                'is_blocked' => $device->is_blocked,
            ],
            'application' => [
                'code' => $application->code,
                'name' => $application->name,
            ],
        ], 201);
    }

    public function bootstrap(string $appCode, Request $request): JsonResponse
    {
        $data = $request->validate([
            'platform' => ['nullable', 'string', 'max:40'],
        ]);

        $application = HealthApplication::where('code', $appCode)
            ->where('is_active', true)
            ->firstOrFail();

        $versions = AppVersion::where('health_application_id', $application->id)
            ->where('is_active', true)
            ->when($data['platform'] ?? null, fn ($query, $platform) => $query->where('platform', strtolower($platform)))
            ->latest('created_at')
            ->get(['platform', 'version', 'is_required', 'release_notes']);

        $notices = InAppNotice::where(function ($query) use ($application): void {
            $query->whereNull('health_application_id')
                ->orWhere('health_application_id', $application->id);
        })
            ->where('is_active', true)
            ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->latest()
            ->get(['title', 'body', 'severity']);

        $features = FeatureFlag::where(function ($query) use ($application): void {
            $query->whereNull('health_application_id')
                ->orWhere('health_application_id', $application->id);
        })->get(['key', 'name', 'enabled', 'rules']);

        return response()->json([
            'application' => [
                'code' => $application->code,
                'name' => $application->name,
                'theme' => [
                    'primary_color' => $application->primary_color,
                    'secondary_color' => $application->secondary_color,
                ],
                'settings' => $application->settings ?? [],
            ],
            'versions' => $versions,
            'notices' => $notices,
            'features' => $features,
        ]);
    }
}
