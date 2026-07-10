<?php

namespace Tests\Feature;

use App\Models\HealthApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    public function test_admin_sections_have_their_own_pages(): void
    {
        $pages = [
            '/admin/applications' => 'Aplicativos',
            '/admin/professionals' => 'Profissionais',
            '/admin/devices' => 'Dispositivos',
            '/admin/versions' => 'Versoes dos aplicativos',
            '/admin/flags' => 'Feature flags',
            '/admin/notices' => 'Avisos internos',
            '/admin/notifications' => 'Push notifications',
            '/admin/support' => 'Suporte',
        ];

        foreach ($pages as $path => $title) {
            $this->get($path)
                ->assertOk()
                ->assertSee($title);
        }
    }

    public function test_admin_can_create_an_application(): void
    {
        $this->post('/admin/applications', [
            'code' => 'cardiocheck',
            'name' => 'CardioCheck',
            'primary_color' => '#dc2626',
            'secondary_color' => '#18181b',
            'is_active' => '1',
            'settings' => '{"segment":"cardio"}',
        ])->assertRedirect();

        $this->assertDatabaseHas('health_applications', [
            'code' => 'cardiocheck',
            'name' => 'CardioCheck',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_create_version_flag_notice_and_notification(): void
    {
        $application = HealthApplication::create([
            'code' => 'fitcheck',
            'name' => 'FitCheck',
            'is_active' => true,
        ]);

        $this->post('/admin/versions', [
            'health_application_id' => $application->id,
            'platform' => 'android',
            'version' => '1.1.0',
            'is_active' => '1',
            'release_notes' => 'Atualizacao administrativa.',
        ])->assertRedirect();

        $this->post('/admin/flags', [
            'health_application_id' => $application->id,
            'key' => 'new_dashboard',
            'name' => 'Novo dashboard',
            'enabled' => '1',
            'rules' => '{"percent":50}',
        ])->assertRedirect();

        $this->post('/admin/notices', [
            'health_application_id' => $application->id,
            'title' => 'Manutencao',
            'body' => 'Janela programada.',
            'severity' => 'warning',
            'is_active' => '1',
        ])->assertRedirect();

        $this->post('/admin/notifications', [
            'health_application_id' => $application->id,
            'title' => 'Novidade',
            'body' => 'Confira a nova versao.',
            'data' => '{"screen":"home"}',
            'status' => 'draft',
        ])->assertRedirect();

        $this->assertDatabaseHas('app_versions', ['version' => '1.1.0']);
        $this->assertDatabaseHas('feature_flags', ['key' => 'new_dashboard']);
        $this->assertDatabaseHas('in_app_notices', ['title' => 'Manutencao']);
        $this->assertDatabaseHas('push_notifications', ['title' => 'Novidade']);
    }
}
