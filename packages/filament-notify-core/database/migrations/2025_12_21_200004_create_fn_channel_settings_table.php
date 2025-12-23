<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fn_channel_settings', function (Blueprint $table) {
            $table->id();
            $table->string('panel_id')->index();
            $table->string('channel');
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->unique(['panel_id', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fn_channel_settings');
    }
};
