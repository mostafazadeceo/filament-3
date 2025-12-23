<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fn_triggers', function (Blueprint $table) {
            $table->id();
            $table->string('panel_id')->index();
            $table->string('key');
            $table->string('label');
            $table->string('type');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['panel_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fn_triggers');
    }
};
