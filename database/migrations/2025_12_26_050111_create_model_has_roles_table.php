<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('model_has_roles')) {
            return;
        }

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->unsignedBigInteger('tenant_id')->nullable();

            $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');
            $table->index('tenant_id');
            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();

            $table->primary(['role_id', 'model_id', 'model_type', 'tenant_id'], 'model_has_roles_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_has_roles');
    }
};
