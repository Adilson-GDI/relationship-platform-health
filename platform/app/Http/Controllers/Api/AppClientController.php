<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use App\Models\Device;
use App\Models\FeatureFlag;
use App\Models\HealthApplication;
use App\Models\InAppNotice;
use App\Models\Professional;
use App\Models\SupportMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppClientController extends Controller
{
    public function registerUser(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:30'],
            'profissao' => ['nullable', 'string', 'max:120'],
            'cidade' => ['nullable', 'string', 'max:120'],
            'estado' => ['nullable', 'string', 'max:2'],
            'device_id' => ['required', 'string', 'max:255'],
            'platform' => ['required', 'string', 'max:40'],
            'app_version' => ['required', 'string', 'max:40'],
            'fcm_token' => ['nullable', 'string', 'max:500'],
            'app_code' => ['nullable', 'string', Rule::exists('health_applications', 'code')],
        ]);

        [$application, $professional, $device] = $this->upsertProfessionalAndDevice($data);

        return response()->json([
            'success' => true,
            'message' => 'Profissional cadastrado com sucesso.',
            'user_id' => $professional->id,
            'remote_user_id' => $professional->id,
            'device_id' => $device->id,
            'application' => [
                'id' => $application->id,
                'code' => $application->code,
                'name' => $application->name,
            ],
        ], 201);
    }

    public function registerFcmToken(Request $request): JsonResponse
    {
        $data = $request->validate([
            'user_id' => ['nullable', 'string', 'max:80'],
            'local_user_id' => ['nullable', 'string', 'max:80'],
            'device_id' => ['required', 'string', 'max:255'],
            'fcm_token' => ['required', 'string', 'max:500'],
            'platform' => ['nullable', 'string', 'max:40'],
            'app_version' => ['nullable', 'string', 'max:40'],
            'app_code' => ['nullable', 'string', Rule::exists('health_applications', 'code')],
        ]);

        $application = $this->application($data['app_code'] ?? 'fitcheck');
        $device = Device::where('health_application_id', $application->id)
            ->where('device_id', $data['device_id'])
            ->first();

        if (! $device && filled($data['user_id'] ?? null)) {
            $professional = Professional::whereKey($data['user_id'])->first();
            if ($professional) {
                $device = Device::create([
                    'professional_id' => $professional->id,
                    'health_application_id' => $application->id,
                    'device_id' => $data['device_id'],
                    'platform' => strtolower($data['platform'] ?? 'unknown'),
                    'app_version' => $data['app_version'] ?? 'unknown',
                    'push_token' => $data['fcm_token'],
                    'last_seen_at' => now(),
                ]);
            }
        }

        if ($device) {
            $device->update([
                'push_token' => $data['fcm_token'],
                'platform' => strtolower($data['platform'] ?? $device->platform),
                'app_version' => $data['app_version'] ?? $device->app_version,
                'last_seen_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token atualizado.',
            'device_id' => $device?->id,
        ]);
    }

    public function status(Request $request): JsonResponse
    {
        $data = $request->validate([
            'user_id' => ['nullable', 'string', 'max:80'],
            'local_user_id' => ['nullable', 'string', 'max:80'],
            'device_id' => ['required', 'string', 'max:255'],
            'platform' => ['required', 'string', 'max:40'],
            'app_version' => ['required', 'string', 'max:40'],
            'fcm_token' => ['nullable', 'string', 'max:500'],
            'app_code' => ['nullable', 'string', Rule::exists('health_applications', 'code')],
        ]);

        $application = $this->application($data['app_code'] ?? 'fitcheck', false);
        $professional = filled($data['user_id'] ?? null)
            ? Professional::whereKey($data['user_id'])->first()
            : null;
        $device = Device::where('health_application_id', $application?->id)
            ->where('device_id', $data['device_id'])
            ->first();

        if ($device) {
            $device->update([
                'push_token' => $data['fcm_token'] ?? $device->push_token,
                'app_version' => $data['app_version'],
                'platform' => strtolower($data['platform']),
                'last_seen_at' => now(),
            ]);
            $professional ??= $device->professional;
        }

        $requiredVersion = $application
            ? AppVersion::where('health_application_id', $application->id)
                ->where('platform', strtolower($data['platform']))
                ->where('is_active', true)
                ->where('is_required', true)
                ->latest()
                ->first()
            : null;

        $notice = $application ? $this->activeNotice($application) : null;
        $features = $application ? $this->featureMap($application) : [];

        return response()->json([
            'success' => true,
            'app_enabled' => (bool) ($application?->is_active ?? false),
            'user_blocked' => (bool) ($professional?->is_blocked || $device?->is_blocked),
            'force_update' => $requiredVersion !== null && $requiredVersion->version !== $data['app_version'],
            'min_version' => $requiredVersion?->version ?? '',
            'notice' => [
                'active' => $notice !== null,
                'title' => $notice?->title ?? '',
                'message' => $notice?->body ?? '',
                'type' => $notice?->severity ?? 'info',
            ],
            'features' => [
                'agenda_enabled' => $features['agenda_enabled'] ?? true,
                'payments_enabled' => $features['payments_enabled'] ?? true,
                'backup_enabled' => $features['backup_enabled'] ?? true,
            ],
        ]);
    }

    public function supportMessage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'user_id' => ['nullable', 'string', 'max:80'],
            'local_user_id' => ['nullable', 'string', 'max:80'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:5000'],
            'local_created_at' => ['nullable', 'date'],
            'platform' => ['nullable', 'string', 'max:40'],
            'app_version' => ['nullable', 'string', 'max:40'],
            'app_code' => ['nullable', 'string', Rule::exists('health_applications', 'code')],
        ]);

        $application = $this->application($data['app_code'] ?? 'fitcheck');
        $professional = filled($data['user_id'] ?? null)
            ? Professional::whereKey($data['user_id'])->first()
            : null;

        $message = SupportMessage::create([
            'health_application_id' => $application->id,
            'professional_id' => $professional?->id,
            'local_user_id' => $data['local_user_id'] ?? null,
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'],
            'local_created_at' => $data['local_created_at'] ?? null,
            'platform' => $data['platform'] ?? null,
            'app_version' => $data['app_version'] ?? null,
            'status' => 'open',
        ]);

        return response()->json([
            'success' => true,
            'message_id' => $message->id,
            'id' => $message->id,
        ], 201);
    }

    private function upsertProfessionalAndDevice(array $data): array
    {
        $application = $this->application($data['app_code'] ?? 'fitcheck');

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
                'name' => $data['nome'],
                'email' => $data['email'] ?? null,
                'phone' => $data['telefone'] ?? null,
                'city' => $data['cidade'] ?? null,
                'state' => isset($data['estado']) ? strtoupper($data['estado']) : null,
                'profession' => $data['profissao'] ?? null,
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
                'app_version' => $data['app_version'],
                'push_token' => $data['fcm_token'] ?? null,
                'last_seen_at' => now(),
            ]
        );

        return [$application, $professional, $device];
    }

    private function application(string $code, bool $activeOnly = true): ?HealthApplication
    {
        return HealthApplication::where('code', $code)
            ->when($activeOnly, fn ($query) => $query->where('is_active', true))
            ->firstOrFail();
    }

    private function activeNotice(HealthApplication $application): ?InAppNotice
    {
        return InAppNotice::where(function ($query) use ($application): void {
            $query->whereNull('health_application_id')->orWhere('health_application_id', $application->id);
        })
            ->where('is_active', true)
            ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->latest()
            ->first();
    }

    private function featureMap(HealthApplication $application): array
    {
        return FeatureFlag::where(function ($query) use ($application): void {
            $query->whereNull('health_application_id')->orWhere('health_application_id', $application->id);
        })
            ->get()
            ->mapWithKeys(fn (FeatureFlag $flag) => [$flag->key => $flag->enabled])
            ->all();
    }
}
