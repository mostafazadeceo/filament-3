<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('audit_logs')) {
            return;
        }

        Schema::table('audit_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('audit_logs', 'prev_hash')) {
                $table->string('prev_hash', 128)->nullable()->after('diff');
            }
            if (! Schema::hasColumn('audit_logs', 'hash')) {
                $table->string('hash', 128)->nullable()->after('prev_hash');
            }
            if (! Schema::hasColumn('audit_logs', 'hash_algo')) {
                $table->string('hash_algo', 32)->default('sha256')->after('hash');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('audit_logs')) {
            return;
        }

        Schema::table('audit_logs', function (Blueprint $table) {
            if (Schema::hasColumn('audit_logs', 'hash_algo')) {
                $table->dropColumn('hash_algo');
            }
            if (Schema::hasColumn('audit_logs', 'hash')) {
                $table->dropColumn('hash');
            }
            if (Schema::hasColumn('audit_logs', 'prev_hash')) {
                $table->dropColumn('prev_hash');
            }
        });
    }
};
