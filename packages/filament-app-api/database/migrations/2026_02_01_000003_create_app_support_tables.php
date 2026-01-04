<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('filament-app-api.tables.support_tickets', 'app_support_tickets'), function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject');
            $table->string('status', 32)->default('open');
            $table->string('priority', 32)->default('normal');
            $table->timestamp('latest_message_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'updated_at']);
        });

        Schema::create(config('filament-app-api.tables.support_messages', 'app_support_messages'), function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('ticket_id')->constrained(config('filament-app-api.tables.support_tickets', 'app_support_tickets'))->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('body')->nullable();
            $table->string('type', 32)->default('text');
            $table->string('attachment_path')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['ticket_id', 'created_at']);
            $table->index(['tenant_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('filament-app-api.tables.support_messages', 'app_support_messages'));
        Schema::dropIfExists(config('filament-app-api.tables.support_tickets', 'app_support_tickets'));
    }
};
