<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_ir_company_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'air_company_settings_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'air_company_settings_company_fk')
                ->cascadeOnDelete();
            $table->json('posting_accounts')->nullable();
            $table->boolean('posting_requires_approval')->default(true);
            $table->boolean('allow_negative_inventory')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'company_id'], 'air_company_settings_tenant_company_uniq');
            $table->index(['tenant_id', 'company_id'], 'air_company_settings_tenant_company_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_ir_company_settings');
    }
};
