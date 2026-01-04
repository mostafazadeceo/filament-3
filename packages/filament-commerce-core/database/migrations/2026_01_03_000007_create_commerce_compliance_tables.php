<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $exceptionsTable = config('filament-commerce-core.tables.exceptions', 'commerce_exceptions');
        $rulesTable = config('filament-commerce-core.tables.fraud_rules', 'commerce_fraud_rules');

        if (! Schema::hasTable($rulesTable)) {
            Schema::create($rulesTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('key')->unique();
                $table->string('name');
                $table->string('status')->default('active');
                $table->json('thresholds')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable($exceptionsTable)) {
            Schema::create($exceptionsTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('type');
                $table->string('severity')->default('medium');
                $table->string('status')->default('open');
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('entity_type')->nullable();
                $table->unsignedBigInteger('entity_id')->nullable();
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('resolved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('resolved_at')->nullable();
                $table->text('resolution_note')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'type']);
            });
        }
    }

    public function down(): void
    {
        $exceptionsTable = config('filament-commerce-core.tables.exceptions', 'commerce_exceptions');
        $rulesTable = config('filament-commerce-core.tables.fraud_rules', 'commerce_fraud_rules');

        Schema::dropIfExists($exceptionsTable);
        Schema::dropIfExists($rulesTable);
    }
};
