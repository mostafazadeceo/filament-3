<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fn_templates', function (Blueprint $table) {
            $table->id();
            $table->string('panel_id')->index();
            $table->string('name');
            $table->string('channel');
            $table->string('subject')->nullable();
            $table->longText('body');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['panel_id', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fn_templates');
    }
};
