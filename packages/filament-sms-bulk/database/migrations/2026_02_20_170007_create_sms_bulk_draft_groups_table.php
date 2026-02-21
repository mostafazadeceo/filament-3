<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_bulk_draft_groups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->json('name_translations');
            $table->json('description_translations')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_bulk_draft_groups');
    }
};
