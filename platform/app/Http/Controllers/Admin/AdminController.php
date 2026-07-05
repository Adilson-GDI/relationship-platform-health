<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use App\Models\Device;
use App\Models\FeatureFlag;
use App\Models\HealthApplication;
use App\Models\InAppNotice;
use App\Models\Professional;
use App\Models\PushNotification;
use App\Models\SupportMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index(Request $request, string $section = 'dashboard')
    {
        $section = in_array($section, $this->sections(), true) ? $section : 'dashboard';
        $selectedApplication = $request->integer('app') ?: null;
        $search = trim((string) $request->query('search', ''));

        $applications = HealthApplication::query()
            ->withCount(['professionals', 'devices', 'versions', 'featureFlags', 'notices'])
            ->orderBy('name')
            ->get();

        $professionals = Professional::query()
            ->with(['healthApplication', 'devices'])
            ->when($selectedApplication, fn ($query) => $query->where('health_application_id', $selectedApplication))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('profession', 'like', "%{$search}%");
                });
            })
            ->latest('last_seen_at')
            ->limit(80)
            ->get();

        return view('admin.dashboard', [
            'section' => $section,
            'pageTitle' => $this->pageTitle($section),
            'applications' => $applications,
            'selectedApplication' => $selectedApplication,
            'search' => $search,
            'stats' => [
                'applications' => HealthApplication::count(),
                'professionals' => Professional::count(),
                'devices' => Device::count(),
                'blocked' => Professional::where('is_blocked', true)->count() + Device::where('is_blocked', true)->count(),
                'notices' => InAppNotice::where('is_active', true)->count(),
                'notifications' => PushNotification::count(),
                'support' => SupportMessage::where('status', 'open')->count(),
            ],
            'professionals' => $professionals,
            'devices' => Device::with(['healthApplication', 'professional'])
                ->when($selectedApplication, fn ($query) => $query->where('health_application_id', $selectedApplication))
                ->latest('last_seen_at')
                ->limit(80)
                ->get(),
            'versions' => AppVersion::with('healthApplication')
                ->when($selectedApplication, fn ($query) => $query->where('health_application_id', $selectedApplication))
                ->latest()
                ->get(),
            'flags' => FeatureFlag::with('healthApplication')
                ->when($selectedApplication, fn ($query) => $query->where(function ($query) use ($selectedApplication): void {
                    $query->whereNull('health_application_id')->orWhere('health_application_id', $selectedApplication);
                }))
                ->orderBy('key')
                ->get(),
            'notices' => InAppNotice::with('healthApplication')
                ->when($selectedApplication, fn ($query) => $query->where(function ($query) use ($selectedApplication): void {
                    $query->whereNull('health_application_id')->orWhere('health_application_id', $selectedApplication);
                }))
                ->latest()
                ->get(),
            'notifications' => PushNotification::with(['healthApplication', 'professional'])
                ->when($selectedApplication, fn ($query) => $query->where('health_application_id', $selectedApplication))
                ->latest()
                ->limit(80)
                ->get(),
            'supportMessages' => SupportMessage::with(['healthApplication', 'professional'])
                ->when($selectedApplication, fn ($query) => $query->where('health_application_id', $selectedApplication))
                ->latest()
                ->limit(120)
                ->get(),
        ]);
    }

    private function sections(): array
    {
        return ['dashboard', 'applications', 'professionals', 'devices', 'versions', 'flags', 'notices', 'notifications', 'support'];
    }

    private function pageTitle(string $section): string
    {
        return [
            'dashboard' => 'Dashboard',
            'applications' => 'Aplicativos',
            'professionals' => 'Profissionais',
            'devices' => 'Dispositivos',
            'versions' => 'Versoes dos aplicativos',
            'flags' => 'Feature flags',
            'notices' => 'Avisos internos',
            'notifications' => 'Push notifications',
            'support' => 'Suporte',
        ][$section] ?? 'Dashboard';
    }

    public function storeApplication(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'alpha_dash', 'max:80', 'unique:health_applications,code'],
            'name' => ['required', 'string', 'max:160'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
            'settings' => ['nullable', 'json'],
        ]);

        HealthApplication::create($this->withJsonAndBoolean($data, 'settings', 'is_active'));

        return back()->with('status', 'Aplicativo criado.');
    }

    public function updateApplication(Request $request, HealthApplication $application): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'alpha_dash', 'max:80', Rule::unique('health_applications', 'code')->ignore($application)],
            'name' => ['required', 'string', 'max:160'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
            'settings' => ['nullable', 'json'],
        ]);

        $application->update($this->withJsonAndBoolean($data, 'settings', 'is_active'));

        return back()->with('status', 'Aplicativo atualizado.');
    }

    public function destroyApplication(HealthApplication $application): RedirectResponse
    {
        $application->delete();

        return back()->with('status', 'Aplicativo removido.');
    }

    public function storeVersion(Request $request): RedirectResponse
    {
        $data = $this->validateVersion($request);
        AppVersion::create($this->checkboxes($data, ['is_required', 'is_active']));

        return back()->with('status', 'Versao cadastrada.');
    }

    public function updateVersion(Request $request, AppVersion $version): RedirectResponse
    {
        $data = $this->validateVersion($request, $version);
        $version->update($this->checkboxes($data, ['is_required', 'is_active']));

        return back()->with('status', 'Versao atualizada.');
    }

    public function destroyVersion(AppVersion $version): RedirectResponse
    {
        $version->delete();

        return back()->with('status', 'Versao removida.');
    }

    public function storeFlag(Request $request): RedirectResponse
    {
        $data = $this->validateFlag($request);
        FeatureFlag::create($this->withJsonAndBoolean($data, 'rules', 'enabled'));

        return back()->with('status', 'Flag criada.');
    }

    public function updateFlag(Request $request, FeatureFlag $flag): RedirectResponse
    {
        $data = $this->validateFlag($request, $flag);
        $flag->update($this->withJsonAndBoolean($data, 'rules', 'enabled'));

        return back()->with('status', 'Flag atualizada.');
    }

    public function destroyFlag(FeatureFlag $flag): RedirectResponse
    {
        $flag->delete();

        return back()->with('status', 'Flag removida.');
    }

    public function storeNotice(Request $request): RedirectResponse
    {
        InAppNotice::create($this->validateNotice($request));

        return back()->with('status', 'Aviso criado.');
    }

    public function updateNotice(Request $request, InAppNotice $notice): RedirectResponse
    {
        $notice->update($this->validateNotice($request));

        return back()->with('status', 'Aviso atualizado.');
    }

    public function destroyNotice(InAppNotice $notice): RedirectResponse
    {
        $notice->delete();

        return back()->with('status', 'Aviso removido.');
    }

    public function storeNotification(Request $request): RedirectResponse
    {
        $data = $this->validateNotification($request);
        PushNotification::create($this->withJsonAndBoolean($data, 'data'));

        return back()->with('status', 'Notificacao criada.');
    }

    public function updateNotification(Request $request, PushNotification $notification): RedirectResponse
    {
        $data = $this->validateNotification($request);
        if (($data['status'] ?? null) === 'sent' && empty($data['sent_at'])) {
            $data['sent_at'] = now();
        }

        $notification->update($this->withJsonAndBoolean($data, 'data'));

        return back()->with('status', 'Notificacao atualizada.');
    }

    public function destroyNotification(PushNotification $notification): RedirectResponse
    {
        $notification->delete();

        return back()->with('status', 'Notificacao removida.');
    }

    public function toggleProfessional(Professional $professional): RedirectResponse
    {
        $professional->update(['is_blocked' => ! $professional->is_blocked]);

        return back()->with('status', 'Profissional atualizado.');
    }

    public function toggleDevice(Device $device): RedirectResponse
    {
        $device->update(['is_blocked' => ! $device->is_blocked]);

        return back()->with('status', 'Dispositivo atualizado.');
    }

    private function validateVersion(Request $request, ?AppVersion $version = null): array
    {
        $data = $request->validate([
            'health_application_id' => ['required', 'exists:health_applications,id'],
            'platform' => ['required', 'string', 'max:40'],
            'version' => ['required', 'string', 'max:40'],
            'is_required' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'release_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $exists = AppVersion::where('health_application_id', $data['health_application_id'])
            ->where('platform', strtolower($data['platform']))
            ->where('version', $data['version'])
            ->when($version, fn ($query) => $query->whereKeyNot($version->id))
            ->exists();

        abort_if($exists, 422, 'Esta versao ja existe para o aplicativo e plataforma.');

        $data['platform'] = strtolower($data['platform']);

        return $data;
    }

    private function validateFlag(Request $request, ?FeatureFlag $flag = null): array
    {
        $data = $request->validate([
            'health_application_id' => ['nullable', 'exists:health_applications,id'],
            'key' => ['required', 'alpha_dash', 'max:120'],
            'name' => ['required', 'string', 'max:160'],
            'enabled' => ['nullable', 'boolean'],
            'rules' => ['nullable', 'json'],
        ]);

        $appId = $data['health_application_id'] ?? null;
        $exists = FeatureFlag::where('key', $data['key'])
            ->where(function ($query) use ($appId): void {
                $appId ? $query->where('health_application_id', $appId) : $query->whereNull('health_application_id');
            })
            ->when($flag, fn ($query) => $query->whereKeyNot($flag->id))
            ->exists();

        abort_if($exists, 422, 'Esta flag ja existe neste escopo.');

        return $data;
    }

    private function validateNotice(Request $request): array
    {
        $data = $request->validate([
            'health_application_id' => ['nullable', 'exists:health_applications,id'],
            'title' => ['required', 'string', 'max:160'],
            'body' => ['required', 'string', 'max:3000'],
            'severity' => ['required', Rule::in(['info', 'success', 'warning', 'danger'])],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        return $this->checkboxes($data, ['is_active']);
    }

    private function validateNotification(Request $request): array
    {
        return $request->validate([
            'health_application_id' => ['nullable', 'exists:health_applications,id'],
            'professional_id' => ['nullable', 'exists:professionals,id'],
            'title' => ['required', 'string', 'max:160'],
            'body' => ['required', 'string', 'max:3000'],
            'data' => ['nullable', 'json'],
            'status' => ['required', Rule::in(['draft', 'scheduled', 'sent', 'canceled'])],
            'scheduled_at' => ['nullable', 'date'],
            'sent_at' => ['nullable', 'date'],
        ]);
    }

    private function withJsonAndBoolean(array $data, ?string $jsonField = null, ?string $booleanField = null): array
    {
        if ($jsonField) {
            $data[$jsonField] = filled($data[$jsonField] ?? null) ? json_decode($data[$jsonField], true) : null;
        }

        if ($booleanField) {
            $data = $this->checkboxes($data, [$booleanField]);
        }

        return $data;
    }

    private function checkboxes(array $data, array $fields): array
    {
        foreach ($fields as $field) {
            $data[$field] = (bool) ($data[$field] ?? false);
        }

        return $data;
    }
}
