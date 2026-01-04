<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $digestTable = config('filament-commerce-core.tables.compliance_digests', 'commerce_compliance_digests');

        if (! Schema::hasTable($digestTable)) {
            Schema::create($digestTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->timestamp('period_start');
                $table->timestamp('period_end');
                $table->string('status')->default('generated');
                $table->json('summary')->nullable();
                $table->json('meta')->nullable();
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['tenant_id', 'period_start', 'period_end'], 'commerce_compliance_digest_unique');
                $table->index(['tenant_id', 'period_start']);
            });
        }
    }

    public function down(): void
    {
        $digestTable = config('filament-commerce-core.tables.compliance_digests', 'commerce_compliance_digests');

        Schema::dropIfExists($digestTable);
    }
};
