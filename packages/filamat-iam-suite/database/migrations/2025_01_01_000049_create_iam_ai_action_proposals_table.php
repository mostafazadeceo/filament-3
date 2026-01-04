<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('iam_ai_action_proposals')) {
            return;
        }

        Schema::create('iam_ai_action_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('report_id')->nullable()->constrained('iam_ai_reports')->nullOnDelete();
            $table->string('action_type');
            $table->json('target')->nullable();
            $table->text('reason')->nullable();
            $table->boolean('requires_approval')->default(true);
            $table->string('status', 32)->default('pending');
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('executed_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('executed_at')->nullable();
            $table->json('result')->nullable();
            $table->string('idempotency_key', 64)->nullable();
            $table->string('correlation_id', 64)->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->unique(['tenant_id', 'idempotency_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iam_ai_action_proposals');
    }
};
