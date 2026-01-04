<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('webhooks')) {
            return;
        }

        Schema::table('webhooks', function (Blueprint $table) {
            if (! Schema::hasColumn('webhooks', 'headers_static')) {
                $table->json('headers_static')->nullable()->after('secret');
            }
            if (! Schema::hasColumn('webhooks', 'auth_mode')) {
                $table->string('auth_mode', 32)->default('hmac+nonce')->after('headers_static');
            }
            if (! Schema::hasColumn('webhooks', 'redaction_policy')) {
                $table->json('redaction_policy')->nullable()->after('events');
            }
            if (! Schema::hasColumn('webhooks', 'rate_limit')) {
                $table->json('rate_limit')->nullable()->after('redaction_policy');
            }
            if (! Schema::hasColumn('webhooks', 'is_ai_auditor')) {
                $table->boolean('is_ai_auditor')->default(false)->after('rate_limit');
            }

            $table->index(['type', 'enabled', 'is_ai_auditor'], 'webhooks_type_enabled_auditor_idx');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('webhooks')) {
            return;
        }

        Schema::table('webhooks', function (Blueprint $table) {
            if (Schema::hasColumn('webhooks', 'headers_static')) {
                $table->dropColumn('headers_static');
            }
            if (Schema::hasColumn('webhooks', 'auth_mode')) {
                $table->dropColumn('auth_mode');
            }
            if (Schema::hasColumn('webhooks', 'redaction_policy')) {
                $table->dropColumn('redaction_policy');
            }
            if (Schema::hasColumn('webhooks', 'rate_limit')) {
                $table->dropColumn('rate_limit');
            }
            if (Schema::hasColumn('webhooks', 'is_ai_auditor')) {
                $table->dropColumn('is_ai_auditor');
            }

            $table->dropIndex('webhooks_type_enabled_auditor_idx');
        });
    }
};
