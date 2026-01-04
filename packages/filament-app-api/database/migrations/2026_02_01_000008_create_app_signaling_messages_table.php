<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('filament-app-api.tables.signaling_messages', 'app_signaling_messages'), function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('from_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('channel', 128)->nullable();
            $table->json('payload');
            $table->timestamps();

            $table->index(['tenant_id', 'channel']);
            $table->index(['tenant_id', 'to_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('filament-app-api.tables.signaling_messages', 'app_signaling_messages'));
    }
};
