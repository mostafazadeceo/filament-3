<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('iam_impersonation_sessions')) {
            return;
        }

        Schema::create('iam_impersonation_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('impersonator_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('impersonated_id')->constrained('users')->cascadeOnDelete();
            $table->string('session_id')->nullable();
            $table->string('token_hash');
            $table->string('ticket_id')->nullable();
            $table->text('reason')->nullable();
            $table->boolean('restricted')->default(true);
            $table->boolean('can_write')->default(false);
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->foreignId('ended_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('end_reason')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'impersonator_id']);
            $table->index(['tenant_id', 'impersonated_id']);
            $table->index('expires_at');
            $table->unique('token_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iam_impersonation_sessions');
    }
};
