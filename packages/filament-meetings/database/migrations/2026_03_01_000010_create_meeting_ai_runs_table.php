<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('meeting_ai_runs')) {
            return;
        }

        Schema::create('meeting_ai_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('meeting_id')->constrained('meetings')->cascadeOnDelete();
            $table->string('action_type');
            $table->string('provider')->nullable();
            $table->string('output_hash')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tenant_id', 'meeting_id', 'action_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_ai_runs');
    }
};
