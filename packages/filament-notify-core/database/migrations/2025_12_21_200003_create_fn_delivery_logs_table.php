<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fn_delivery_logs', function (Blueprint $table) {
            $table->id();
            $table->string('panel_id')->index();
            $table->foreignId('rule_id')->nullable()->constrained('fn_notification_rules')->nullOnDelete();
            $table->string('trigger_key')->index();
            $table->string('channel')->index();
            $table->string('recipient');
            $table->string('status')->index();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fn_delivery_logs');
    }
};
