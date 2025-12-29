<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_ir_account_types', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('normal_balance')->default('debit');
            $table->boolean('is_system')->default(true);
            $table->timestamps();
        });

        Schema::create('accounting_ir_account_plans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('industry')->nullable();
            $table->boolean('is_default')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'name'], 'air_account_plans_company_name_uniq');
            $table->index(['tenant_id', 'company_id'], 'air_account_plans_tenant_company_idx');
        });

        Schema::create('accounting_ir_chart_accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('accounting_ir_account_plans')->cascadeOnDelete();
            $table->foreignId('type_id')->constrained('accounting_ir_account_types')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('accounting_ir_chart_accounts')->nullOnDelete();
            $table->string('code');
            $table->string('name');
            $table->unsignedSmallInteger('level')->default(1);
            $table->boolean('is_postable')->default(false);
            $table->json('requires_dimensions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'code'], 'air_chart_accounts_company_code_uniq');
            $table->index(['tenant_id', 'company_id', 'plan_id'], 'air_chart_accounts_tenant_company_plan_idx');
            $table->index(['tenant_id', 'type_id'], 'air_chart_accounts_tenant_type_idx');
            $table->index(['tenant_id', 'is_active'], 'air_chart_accounts_tenant_active_idx');
        });

        Schema::create('accounting_ir_dimensions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'code'], 'air_dimensions_company_code_uniq');
            $table->index(['tenant_id', 'company_id'], 'air_dimensions_tenant_company_idx');
        });

        Schema::create('accounting_ir_dimension_values', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('dimension_id')->constrained('accounting_ir_dimensions')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['dimension_id', 'code'], 'air_dimension_values_dimension_code_uniq');
            $table->index(['tenant_id', 'company_id', 'dimension_id'], 'air_dimension_values_tenant_company_dim_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_ir_dimension_values');
        Schema::dropIfExists('accounting_ir_dimensions');
        Schema::dropIfExists('accounting_ir_chart_accounts');
        Schema::dropIfExists('accounting_ir_account_plans');
        Schema::dropIfExists('accounting_ir_account_types');
    }
};
