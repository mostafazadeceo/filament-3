<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_bulk_quiet_hours_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('name');
            $table->string('timezone', 64)->default('Asia/Tehran');
            $table->json('allowed_days')->nullable();
            $table->string('start_time', 5)->default('08:00');
            $table->string('end_time', 5)->default('22:00');
            $table->json('holidays')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_bulk_quiet_hours_profiles');
    }
};
