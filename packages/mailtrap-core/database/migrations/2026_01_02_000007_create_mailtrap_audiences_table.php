<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('mailtrap-core.tables.audiences', 'mailtrap_audiences');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name', 190);
            $table->string('status', 40)->default('active');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'name'], 'mailtrap_audiences_tenant_name_unique');
            $table->index(['tenant_id', 'status'], 'mailtrap_audiences_tenant_status_idx');
        });
    }

    public function down(): void
    {
        $table = config('mailtrap-core.tables.audiences', 'mailtrap_audiences');
        Schema::dropIfExists($table);
    }
};
