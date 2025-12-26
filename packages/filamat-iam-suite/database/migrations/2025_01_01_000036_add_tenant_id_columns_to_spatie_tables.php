<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addTenantColumn('roles');
        $this->addTenantColumn('permissions');
        $this->addTenantColumn('model_has_roles');
        $this->addTenantColumn('model_has_permissions');
        $this->addTenantColumn('role_has_permissions');
    }

    public function down(): void
    {
        $this->dropTenantColumn('role_has_permissions');
        $this->dropTenantColumn('model_has_permissions');
        $this->dropTenantColumn('model_has_roles');
        $this->dropTenantColumn('permissions');
        $this->dropTenantColumn('roles');
    }

    private function addTenantColumn(string $table): void
    {
        if (! Schema::hasTable($table) || Schema::hasColumn($table, 'tenant_id')) {
            return;
        }

        Schema::table($table, function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
        });
    }

    private function dropTenantColumn(string $table): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'tenant_id')) {
            return;
        }

        Schema::table($table, function (Blueprint $table) {
            $table->dropIndex(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
    }
};
