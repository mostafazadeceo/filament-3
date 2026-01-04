<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('feature-gates.tables', []);
        $tenantOverridesTable = $tables['tenant_feature_overrides'] ?? 'tenant_feature_overrides';

        if (Schema::hasTable($tenantOverridesTable)) {
            return;
        }

        Schema::create($tenantOverridesTable, function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('feature_key');
            $table->boolean('allowed')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->json('limits')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'feature_key']);
            $table->index(['feature_key', 'allowed']);
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        $tables = config('feature-gates.tables', []);
        $tenantOverridesTable = $tables['tenant_feature_overrides'] ?? 'tenant_feature_overrides';

        Schema::dropIfExists($tenantOverridesTable);
    }
};
