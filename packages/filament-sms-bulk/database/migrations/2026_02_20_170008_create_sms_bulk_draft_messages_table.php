<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_bulk_draft_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('draft_group_id')->constrained('sms_bulk_draft_groups')->cascadeOnDelete();
            $table->json('title_translations')->nullable();
            $table->json('body_translations');
            $table->string('language', 8)->default('fa')->index();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'draft_group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_bulk_draft_messages');
    }
};
