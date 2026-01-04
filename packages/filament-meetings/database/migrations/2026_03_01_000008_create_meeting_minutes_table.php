<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('meeting_minutes')) {
            return;
        }

        Schema::create('meeting_minutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('meeting_id')->constrained('meetings')->cascadeOnDelete();
            $table->text('overview_text')->nullable();
            $table->json('keywords_json')->nullable();
            $table->json('outline_json')->nullable();
            $table->longText('summary_markdown')->nullable();
            $table->json('decisions_json')->nullable();
            $table->json('risks_json')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'meeting_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_minutes');
    }
};
