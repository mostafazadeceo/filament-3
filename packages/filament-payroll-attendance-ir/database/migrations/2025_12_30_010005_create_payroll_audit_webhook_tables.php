<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_audit_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_audit_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_audit_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'payroll_audit_branch_fk')
                ->nullOnDelete();
            $table->foreignId('actor_id')
                ->nullable()
                ->constrained('users', 'id', 'payroll_audit_actor_fk')
                ->nullOnDelete();
            $table->string('event');
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTime('created_at');

            $table->index(['tenant_id', 'company_id', 'branch_id', 'event'], 'payroll_audit_tenant_company_branch_event_idx');
            $table->index(['subject_type', 'subject_id'], 'payroll_audit_subject_idx');
        });

        Schema::create('payroll_webhook_subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_webhook_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_webhook_company_fk')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('url');
            $table->json('events');
            $table->string('secret')->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('last_delivery_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'is_active'], 'payroll_webhook_tenant_company_active_idx');
        });

        Schema::create('payroll_webhook_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_webhook_delivery_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_webhook_delivery_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('subscription_id')
                ->constrained('payroll_webhook_subscriptions', 'id', 'payroll_webhook_delivery_sub_fk')
                ->cascadeOnDelete();
            $table->string('event');
            $table->json('payload');
            $table->string('status')->default('pending');
            $table->unsignedInteger('response_code')->nullable();
            $table->text('response_body')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'status'], 'payroll_webhook_delivery_tenant_company_status_idx');
            $table->index(['subscription_id', 'event'], 'payroll_webhook_delivery_sub_event_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_webhook_deliveries');
        Schema::dropIfExists('payroll_webhook_subscriptions');
        Schema::dropIfExists('payroll_audit_events');
    }
};
