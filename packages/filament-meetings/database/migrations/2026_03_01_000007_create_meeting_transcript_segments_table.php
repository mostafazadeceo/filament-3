<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('meeting_transcript_segments')) {
            return;
        }

        Schema::create('meeting_transcript_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('meeting_id')->constrained('meetings')->cascadeOnDelete();
            $table->unsignedInteger('t_start_sec')->nullable();
            $table->unsignedInteger('t_end_sec')->nullable();
            $table->string('speaker_label')->nullable();
            $table->text('text');
            $table->timestamps();

            $table->index(['tenant_id', 'meeting_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_transcript_segments');
    }
};
