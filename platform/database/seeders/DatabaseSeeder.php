<?php

namespace Database\Seeders;

use App\Models\AppVersion;
use App\Models\FeatureFlag;
use App\Models\HealthApplication;
use App\Models\InAppNotice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $applications = [
            ['code' => 'fitcheck', 'name' => 'FitCheck', 'primary_color' => '#16a34a', 'secondary_color' => '#0f172a'],
            ['code' => 'physiocheck', 'name' => 'PhysioCheck', 'primary_color' => '#0284c7', 'secondary_color' => '#164e63'],
            ['code' => 'massagecheck', 'name' => 'MassageCheck', 'primary_color' => '#db2777', 'secondary_color' => '#831843'],
            ['code' => 'pilatescheck', 'name' => 'PilatesCheck', 'primary_color' => '#7c3aed', 'secondary_color' => '#312e81'],
            ['code' => 'yogacheck', 'name' => 'YogaCheck', 'primary_color' => '#0d9488', 'secondary_color' => '#134e4a'],
            ['code' => 'nutricheck', 'name' => 'NutriCheck', 'primary_color' => '#65a30d', 'secondary_color' => '#365314'],
            ['code' => 'therapycheck', 'name' => 'TherapyCheck', 'primary_color' => '#2563eb', 'secondary_color' => '#1e3a8a'],
        ];

        foreach ($applications as $applicationData) {
            $application = HealthApplication::updateOrCreate(
                ['code' => $applicationData['code']],
                $applicationData + ['is_active' => true]
            );

            foreach (['android', 'ios'] as $platform) {
                AppVersion::updateOrCreate(
                    [
                        'health_application_id' => $application->id,
                        'platform' => $platform,
                        'version' => '1.0.0',
                    ],
                    [
                        'is_required' => false,
                        'is_active' => true,
                        'release_notes' => 'Versao inicial cadastrada na Relationship Platform Health.',
                    ]
                );
            }
        }

        FeatureFlag::updateOrCreate(
            ['health_application_id' => null, 'key' => 'offline_first_sync'],
            ['name' => 'Sincronizacao Offline First', 'enabled' => false]
        );

        InAppNotice::updateOrCreate(
            ['title' => 'Bem-vindo a Relationship Platform Health'],
            [
                'body' => 'Plataforma administrativa ativa para controle dos aplicativos do ecossistema.',
                'severity' => 'info',
                'is_active' => true,
            ]
        );
    }
}
