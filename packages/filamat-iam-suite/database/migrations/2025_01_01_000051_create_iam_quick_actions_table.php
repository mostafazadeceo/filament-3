<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iam_quick_actions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('panel_id', 64);
            $table->string('type', 32)->default('custom');
            $table->string('label', 255);
            $table->string('description', 255)->nullable();
            $table->string('icon', 128)->nullable();
            $table->string('url', 2048);
            $table->unsignedSmallInteger('rank')->default(1);
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'panel_id']);
            $table->index(['tenant_id', 'user_id']);
            $table->index(['panel_id', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iam_quick_actions');
    }
};
