<?php

namespace Tests\Feature;

use App\Models\HealthApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_registers_a_professional_and_device_for_an_application(): void
    {
        HealthApplication::create([
            'code' => 'fitcheck',
            'name' => 'FitCheck',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/professionals/register', [
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
            'phone' => '11999999999',
            'city' => 'Sao Paulo',
            'state' => 'SP',
            'profession' => 'Personal Trainer',
            'app_code' => 'fitcheck',
            'version' => '1.0.0',
            'platform' => 'android',
            'device_id' => 'device-123',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('professional.email', 'ana@example.com')
            ->assertJsonPath('device.device_id', 'device-123')
            ->assertJsonPath('application.code', 'fitcheck');

        $this->assertDatabaseHas('professionals', ['email' => 'ana@example.com']);
        $this->assertDatabaseHas('devices', ['device_id' => 'device-123']);
    }

    public function test_returns_bootstrap_data_for_an_application(): void
    {
        $this->seed();

        $response = $this->getJson('/api/v1/apps/fitcheck/bootstrap?platform=android');

        $response
            ->assertOk()
            ->assertJsonPath('application.code', 'fitcheck')
            ->assertJsonCount(1, 'versions')
            ->assertJsonPath('features.0.key', 'offline_first_sync');
    }
}
