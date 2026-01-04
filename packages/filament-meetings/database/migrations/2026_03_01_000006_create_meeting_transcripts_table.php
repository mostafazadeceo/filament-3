<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('meeting_transcripts')) {
            return;
        }

        Schema::create('meeting_transcripts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('meeting_id')->constrained('meetings')->cascadeOnDelete();
            $table->string('source')->default('manual');
            $table->string('language')->nullable();
            $table->longText('content_longtext')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'meeting_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_transcripts');
    }
};
