<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_locations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('health_application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('professional_id')->constrained()->cascadeOnDelete();
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('local_id');
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city', 120)->nullable();
            $table->string('state', 2)->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('type', 40);
            $table->text('notes')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            $table->unique(['health_application_id', 'professional_id', 'local_id']);
            $table->index(['is_public', 'latitude', 'longitude']);
        });
    }

    public function down(): void { Schema::dropIfExists('service_locations'); }
};
