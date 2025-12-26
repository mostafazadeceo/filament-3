<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relograde_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('connection_id')
                ->constrained('relograde_connections')
                ->cascadeOnDelete();
            $table->string('currency');
            $table->string('state')->nullable();
            $table->decimal('total_amount', 18, 4);
            $table->json('raw_json');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['connection_id', 'currency', 'state']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relograde_accounts');
    }
};
