<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('providers-esim-go-core.tables.callbacks', 'esim_go_callbacks');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('event_type')->nullable();
            $table->string('iccid')->nullable();
            $table->string('bundle_ref')->nullable();
            $table->decimal('remaining_quantity', 18, 4)->nullable();
            $table->string('payload_hash')->nullable();
            $table->longText('raw_body')->nullable();
            $table->json('payload')->nullable();
            $table->boolean('signature_valid')->nullable();
            $table->string('correlation_id')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'event_type'], 'esim_go_callbacks_event_idx');
            $table->index('received_at', 'esim_go_callbacks_received_idx');
            $table->unique(['tenant_id', 'payload_hash'], 'esim_go_callbacks_hash_unique');
        });
    }

    public function down(): void
    {
        $table = config('providers-esim-go-core.tables.callbacks', 'esim_go_callbacks');
        Schema::dropIfExists($table);
    }
};
