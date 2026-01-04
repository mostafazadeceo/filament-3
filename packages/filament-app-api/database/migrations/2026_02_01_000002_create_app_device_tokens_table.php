<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('filament-app-api.tables.device_tokens', 'app_device_tokens'), function (Blueprint $table): void {
            $table->id();
            $table->foreignId('device_id')->constrained(config('filament-app-api.tables.devices', 'app_devices'))->cascadeOnDelete();
            $table->string('provider', 32);
            $table->string('token', 512)->unique();
            $table->string('status', 32)->default('active');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->index(['device_id', 'provider']);
            $table->index(['device_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('filament-app-api.tables.device_tokens', 'app_device_tokens'));
    }
};
