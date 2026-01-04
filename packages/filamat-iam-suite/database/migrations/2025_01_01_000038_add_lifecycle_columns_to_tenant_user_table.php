<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenant_user', function (Blueprint $table) {
            if (! Schema::hasColumn('tenant_user', 'invited_at')) {
                $table->timestamp('invited_at')->nullable()->after('status');
            }
            if (! Schema::hasColumn('tenant_user', 'activated_at')) {
                $table->timestamp('activated_at')->nullable()->after('invited_at');
            }
            if (! Schema::hasColumn('tenant_user', 'suspended_at')) {
                $table->timestamp('suspended_at')->nullable()->after('activated_at');
            }
            if (! Schema::hasColumn('tenant_user', 'suspension_reason')) {
                $table->string('suspension_reason')->nullable()->after('suspended_at');
            }
            if (! Schema::hasColumn('tenant_user', 'invited_by_id')) {
                $table->foreignId('invited_by_id')->nullable()->constrained('users')->nullOnDelete()->after('suspension_reason');
            }
            if (! Schema::hasColumn('tenant_user', 'activated_by_id')) {
                $table->foreignId('activated_by_id')->nullable()->constrained('users')->nullOnDelete()->after('invited_by_id');
            }
            if (! Schema::hasColumn('tenant_user', 'suspended_by_id')) {
                $table->foreignId('suspended_by_id')->nullable()->constrained('users')->nullOnDelete()->after('activated_by_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenant_user', function (Blueprint $table) {
            if (Schema::hasColumn('tenant_user', 'invited_by_id')) {
                $table->dropConstrainedForeignId('invited_by_id');
            }
            if (Schema::hasColumn('tenant_user', 'activated_by_id')) {
                $table->dropConstrainedForeignId('activated_by_id');
            }
            if (Schema::hasColumn('tenant_user', 'suspended_by_id')) {
                $table->dropConstrainedForeignId('suspended_by_id');
            }

            $columns = [
                'invited_at',
                'activated_at',
                'suspended_at',
                'suspension_reason',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('tenant_user', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
