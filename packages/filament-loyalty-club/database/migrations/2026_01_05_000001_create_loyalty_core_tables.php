<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('loyalty_tiers')) {
            Schema::create('loyalty_tiers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('slug')->nullable();
                $table->unsignedInteger('rank')->default(1);
                $table->unsignedBigInteger('threshold_points')->default(0);
                $table->decimal('threshold_spend', 16, 4)->default(0);
                $table->json('benefits')->nullable();
                $table->boolean('is_default')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['tenant_id', 'slug']);
                $table->index(['tenant_id', 'rank']);
                $table->index(['tenant_id', 'is_active']);
            });
        }

        if (! Schema::hasTable('loyalty_customers')) {
            Schema::create('loyalty_customers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('tier_id')->nullable()->constrained('loyalty_tiers')->nullOnDelete();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->json('external_refs')->nullable();
                $table->string('status')->default('active');
                $table->date('birth_date')->nullable();
                $table->timestamp('joined_at')->nullable();
                $table->boolean('marketing_opt_in')->default(false);
                $table->timestamp('marketing_opt_in_at')->nullable();
                $table->string('marketing_opt_in_source')->nullable();
                $table->boolean('sms_opt_in')->default(false);
                $table->timestamp('sms_opt_in_at')->nullable();
                $table->boolean('whatsapp_opt_in')->default(false);
                $table->timestamp('whatsapp_opt_in_at')->nullable();
                $table->boolean('telegram_opt_in')->default(false);
                $table->timestamp('telegram_opt_in_at')->nullable();
                $table->boolean('bale_opt_in')->default(false);
                $table->timestamp('bale_opt_in_at')->nullable();
                $table->boolean('webpush_opt_in')->default(false);
                $table->timestamp('webpush_opt_in_at')->nullable();
                $table->boolean('email_opt_in')->default(false);
                $table->timestamp('email_opt_in_at')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'phone']);
                $table->unique(['tenant_id', 'email']);
                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'tier_id']);
                $table->index(['tenant_id', 'updated_at']);
            });
        }

        if (! Schema::hasTable('loyalty_customer_tiers')) {
            Schema::create('loyalty_customer_tiers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('customer_id')->constrained('loyalty_customers')->cascadeOnDelete();
                $table->foreignId('tier_id')->constrained('loyalty_tiers')->cascadeOnDelete();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('ended_at')->nullable();
                $table->string('reason')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'customer_id']);
                $table->index(['tenant_id', 'tier_id']);
            });
        }

        if (! Schema::hasTable('loyalty_points_rules')) {
            Schema::create('loyalty_points_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('event_type');
                $table->string('status')->default('active');
                $table->unsignedInteger('priority')->default(100);
                $table->string('scope_type')->default('global');
                $table->string('scope_ref')->nullable();
                $table->string('points_type')->default('fixed');
                $table->unsignedBigInteger('points_value')->default(0);
                $table->decimal('percent_rate', 8, 4)->nullable();
                $table->decimal('min_amount', 16, 4)->nullable();
                $table->unsignedBigInteger('max_points')->nullable();
                $table->string('cap_period')->nullable();
                $table->unsignedInteger('cap_count')->nullable();
                $table->timestamp('valid_from')->nullable();
                $table->timestamp('valid_until')->nullable();
                $table->json('conditions')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'event_type', 'status']);
                $table->index(['tenant_id', 'priority']);
            });
        }

        if (! Schema::hasTable('loyalty_events')) {
            Schema::create('loyalty_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('customer_id')->nullable()->constrained('loyalty_customers')->nullOnDelete();
                $table->string('type');
                $table->string('source')->nullable();
                $table->string('idempotency_key')->nullable();
                $table->string('status')->default('pending');
                $table->json('payload')->nullable();
                $table->timestamp('occurred_at')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'idempotency_key']);
                $table->index(['tenant_id', 'type']);
                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'customer_id']);
                $table->index(['occurred_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_events');
        Schema::dropIfExists('loyalty_points_rules');
        Schema::dropIfExists('loyalty_customer_tiers');
        Schema::dropIfExists('loyalty_customers');
        Schema::dropIfExists('loyalty_tiers');
    }
};
