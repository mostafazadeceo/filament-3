<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_ir_fixed_assets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('accounting_ir_branches')->nullOnDelete();
            $table->string('name');
            $table->string('asset_code')->nullable();
            $table->string('category')->nullable();
            $table->date('acquisition_date')->nullable();
            $table->decimal('cost', 18, 2)->default(0);
            $table->decimal('salvage_value', 18, 2)->default(0);
            $table->string('depreciation_method')->default('straight_line');
            $table->unsignedSmallInteger('useful_life_months')->default(12);
            $table->string('status')->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id', 'status'], 'air_fixed_assets_tenant_company_status_idx');
        });

        Schema::create('accounting_ir_depreciation_schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('fixed_asset_id')->constrained('accounting_ir_fixed_assets')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('amount', 18, 2)->default(0);
            $table->boolean('is_posted')->default(false);
            $table->timestamps();

            $table->index(['fixed_asset_id', 'period_start'], 'air_depr_sched_asset_period_idx');
        });

        Schema::create('accounting_ir_depreciation_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('fixed_asset_id')->constrained('accounting_ir_fixed_assets')->cascadeOnDelete();
            $table->foreignId('journal_entry_id')->nullable()->constrained('accounting_ir_journal_entries')->nullOnDelete();
            $table->date('posted_at')->nullable();
            $table->decimal('amount', 18, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['fixed_asset_id', 'posted_at'], 'air_depr_entries_asset_posted_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_ir_depreciation_entries');
        Schema::dropIfExists('accounting_ir_depreciation_schedules');
        Schema::dropIfExists('accounting_ir_fixed_assets');
    }
};
