<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('iam_privilege_eligibilities')) {
            return;
        }

        Schema::create('iam_privilege_eligibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('eligible_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('can_request')->default(true);
            $table->boolean('active')->default(true);
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'user_id', 'role_id']);
            $table->index(['tenant_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iam_privilege_eligibilities');
    }
};
