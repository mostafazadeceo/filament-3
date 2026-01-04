<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('filament-app-api.tables.tasks', 'app_tasks'), function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('client_id', 128)->nullable();
            $table->string('title');
            $table->string('status', 32)->default('open');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'updated_at']);
            $table->unique(['tenant_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('filament-app-api.tables.tasks', 'app_tasks'));
    }
};
