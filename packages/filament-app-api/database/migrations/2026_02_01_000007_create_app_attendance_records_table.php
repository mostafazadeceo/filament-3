<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('filament-app-api.tables.attendance_records', 'app_attendance_records'), function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 16);
            $table->string('status', 32)->default('pending');
            $table->timestamp('clocked_at');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'clocked_at']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('filament-app-api.tables.attendance_records', 'app_attendance_records'));
    }
};
