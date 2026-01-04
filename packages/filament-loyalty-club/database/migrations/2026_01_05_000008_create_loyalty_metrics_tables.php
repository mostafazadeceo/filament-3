<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('loyalty_customer_metrics')) {
            Schema::create('loyalty_customer_metrics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('customer_id')->constrained('loyalty_customers')->cascadeOnDelete();
                $table->timestamp('last_purchase_at')->nullable();
                $table->unsignedInteger('purchase_count')->default(0);
                $table->decimal('monetary_total', 16, 4)->default(0);
                $table->unsignedInteger('recency_days')->nullable();
                $table->unsignedInteger('frequency_score')->nullable();
                $table->unsignedInteger('monetary_score')->nullable();
                $table->unsignedInteger('rfm_score')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'customer_id']);
                $table->index(['tenant_id', 'rfm_score']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_customer_metrics');
    }
};
