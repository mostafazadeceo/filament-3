<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('crypto_payout_destinations')) {
            Schema::create('crypto_payout_destinations', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants');
                $table->string('label')->nullable();
                $table->string('address');
                $table->string('currency', 16)->nullable();
                $table->string('network', 32)->nullable();
                $table->string('status', 32)->default('active');
                $table->timestamp('approved_at')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'address', 'currency', 'network'], 'crypto_payout_destinations_uidx');
                $table->index(['tenant_id', 'status', 'updated_at']);
            });
        }

        if (Schema::hasTable('crypto_payouts')) {
            Schema::table('crypto_payouts', function (Blueprint $table): void {
                if (! Schema::hasColumn('crypto_payouts', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable()->after('is_final');
                }
                if (! Schema::hasColumn('crypto_payouts', 'approved_by')) {
                    $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
                }
                if (! Schema::hasColumn('crypto_payouts', 'approval_note')) {
                    $table->text('approval_note')->nullable()->after('approved_by');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('crypto_payouts')) {
            Schema::table('crypto_payouts', function (Blueprint $table): void {
                if (Schema::hasColumn('crypto_payouts', 'approval_note')) {
                    $table->dropColumn('approval_note');
                }
                if (Schema::hasColumn('crypto_payouts', 'approved_by')) {
                    $table->dropColumn('approved_by');
                }
                if (Schema::hasColumn('crypto_payouts', 'approved_at')) {
                    $table->dropColumn('approved_at');
                }
            });
        }

        Schema::dropIfExists('crypto_payout_destinations');
    }
};
