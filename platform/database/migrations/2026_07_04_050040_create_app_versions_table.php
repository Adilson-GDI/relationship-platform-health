<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('health_application_id')->constrained()->cascadeOnDelete();
            $table->string('platform');
            $table->string('version');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('release_notes')->nullable();
            $table->timestamps();

            $table->unique(['health_application_id', 'platform', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_versions');
    }
};
