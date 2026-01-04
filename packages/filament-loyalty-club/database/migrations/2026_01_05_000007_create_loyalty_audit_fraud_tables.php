<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('loyalty_audit_events')) {
            Schema::create('loyalty_audit_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('actor_type')->nullable();
                $table->string('action');
                $table->string('subject_type')->nullable();
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->string('ip_hash')->nullable();
                $table->json('meta')->nullable();
                $table->timestamp('occurred_at')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'action']);
                $table->index(['tenant_id', 'subject_type', 'subject_id']);
                $table->index(['tenant_id', 'occurred_at']);
            });
        }

        if (! Schema::hasTable('loyalty_fraud_signals')) {
            Schema::create('loyalty_fraud_signals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('customer_id')->nullable()->constrained('loyalty_customers')->nullOnDelete();
                $table->string('type');
                $table->string('severity')->default('medium');
                $table->string('status')->default('open');
                $table->string('subject_type')->nullable();
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->unsignedInteger('score')->default(0);
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('detected_at')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->string('resolution')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'severity']);
                $table->index(['tenant_id', 'customer_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_fraud_signals');
        Schema::dropIfExists('loyalty_audit_events');
    }
};
