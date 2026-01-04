<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('loyalty_segments')) {
            Schema::create('loyalty_segments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('type')->default('rule');
                $table->string('status')->default('active');
                $table->json('rules')->nullable();
                $table->timestamp('last_built_at')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable('loyalty_customer_segments')) {
            Schema::create('loyalty_customer_segments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('segment_id')->constrained('loyalty_segments')->cascadeOnDelete();
                $table->foreignId('customer_id')->constrained('loyalty_customers')->cascadeOnDelete();
                $table->string('source')->default('rule');
                $table->timestamp('assigned_at')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'segment_id', 'customer_id'], 'loy_cust_seg_unique');
                $table->index(['tenant_id', 'customer_id']);
            });
        }

        if (! Schema::hasTable('loyalty_campaigns')) {
            Schema::create('loyalty_campaigns', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('status')->default('draft');
                $table->json('channels')->nullable();
                $table->string('segment_strategy')->default('all');
                $table->timestamp('schedule_start_at')->nullable();
                $table->timestamp('schedule_end_at')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'schedule_start_at']);
            });
        }

        if (! Schema::hasTable('loyalty_campaign_segments')) {
            Schema::create('loyalty_campaign_segments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('campaign_id')->constrained('loyalty_campaigns')->cascadeOnDelete();
                $table->foreignId('segment_id')->constrained('loyalty_segments')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['tenant_id', 'campaign_id', 'segment_id'], 'loy_campaign_segment_unique');
            });
        }

        if (! Schema::hasTable('loyalty_campaign_variants')) {
            Schema::create('loyalty_campaign_variants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('campaign_id')->constrained('loyalty_campaigns')->cascadeOnDelete();
                $table->string('name');
                $table->string('channel');
                $table->unsignedInteger('weight')->default(100);
                $table->string('status')->default('active');
                $table->json('content')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'campaign_id']);
            });
        }

        if (! Schema::hasTable('loyalty_campaign_dispatches')) {
            Schema::create('loyalty_campaign_dispatches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('campaign_id')->constrained('loyalty_campaigns')->cascadeOnDelete();
                $table->foreignId('variant_id')->nullable()->constrained('loyalty_campaign_variants')->nullOnDelete();
                $table->foreignId('customer_id')->constrained('loyalty_customers')->cascadeOnDelete();
                $table->string('channel');
                $table->string('status')->default('pending');
                $table->timestamp('dispatched_at')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->timestamp('opened_at')->nullable();
                $table->timestamp('clicked_at')->nullable();
                $table->timestamp('converted_at')->nullable();
                $table->foreignId('conversion_event_id')->nullable()->constrained('loyalty_events')->nullOnDelete();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'campaign_id']);
                $table->index(['tenant_id', 'customer_id']);
                $table->index(['tenant_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_campaign_dispatches');
        Schema::dropIfExists('loyalty_campaign_variants');
        Schema::dropIfExists('loyalty_campaign_segments');
        Schema::dropIfExists('loyalty_campaigns');
        Schema::dropIfExists('loyalty_customer_segments');
        Schema::dropIfExists('loyalty_segments');
    }
};
