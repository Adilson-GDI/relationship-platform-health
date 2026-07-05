<?php

namespace Tests\Feature;

use App\Models\AppVersion;
use App\Models\FeatureFlag;
use App\Models\HealthApplication;
use App\Models\InAppNotice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppClientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_flutter_app_can_register_status_token_and_support_message(): void
    {
        $application = HealthApplication::create([
            'code' => 'fitcheck',
            'name' => 'FitCheck',
            'is_active' => true,
        ]);

        AppVersion::create([
            'health_application_id' => $application->id,
            'platform' => 'android',
            'version' => '1.0.0',
            'is_required' => false,
            'is_active' => true,
        ]);

        FeatureFlag::create([
            'health_application_id' => $application->id,
            'key' => 'agenda_enabled',
            'name' => 'Agenda',
            'enabled' => true,
        ]);

        InAppNotice::create([
            'health_application_id' => $application->id,
            'title' => 'Aviso',
            'body' => 'Mensagem administrativa.',
            'severity' => 'info',
            'is_active' => true,
        ]);

        $register = $this->postJson('/api/v1/app_users/register', [
            'nome' => 'Ana Silva',
            'email' => 'ana@example.com',
            'telefone' => '11999999999',
            'profissao' => 'Personal Trainer',
            'cidade' => 'Sao Paulo',
            'estado' => 'SP',
            'device_id' => 'android-device',
            'platform' => 'android',
            'app_version' => '1.0.0',
            'fcm_token' => 'token-a',
        ]);

        $register
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('application.code', 'fitcheck');

        $userId = $register->json('user_id');

        $this->postJson('/api/v1/fcm_tokens/register', [
            'user_id' => (string) $userId,
            'device_id' => 'android-device',
            'fcm_token' => 'token-b',
            'platform' => 'android',
            'app_version' => '1.0.0',
        ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->postJson('/api/v1/app/status', [
            'user_id' => (string) $userId,
            'device_id' => 'android-device',
            'platform' => 'android',
            'app_version' => '1.0.0',
            'fcm_token' => 'token-b',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('app_enabled', true)
            ->assertJsonPath('user_blocked', false)
            ->assertJsonPath('force_update', false)
            ->assertJsonPath('notice.active', true)
            ->assertJsonPath('features.agenda_enabled', true);

        $this->postJson('/api/v1/support/messages', [
            'user_id' => (string) $userId,
            'local_user_id' => '1',
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
            'phone' => '11999999999',
            'message' => 'Preciso de ajuda.',
            'local_created_at' => now()->toIso8601String(),
            'platform' => 'android',
            'app_version' => '1.0.0',
        ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('devices', [
            'device_id' => 'android-device',
            'push_token' => 'token-b',
        ]);
        $this->assertDatabaseHas('support_messages', [
            'message' => 'Preciso de ajuda.',
            'status' => 'open',
        ]);
    }
}
