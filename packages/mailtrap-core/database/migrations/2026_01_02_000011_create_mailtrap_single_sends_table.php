<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('mailtrap-core.tables.single_sends', 'mailtrap_single_sends');
        $connections = config('mailtrap-core.tables.connections', 'mailtrap_connections');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) use ($connections) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('connection_id')->constrained($connections)->cascadeOnDelete();
            $table->string('to_email', 190);
            $table->string('to_name', 190)->nullable();
            $table->string('subject', 190);
            $table->longText('html_body')->nullable();
            $table->longText('text_body')->nullable();
            $table->string('status', 40)->default('pending');
            $table->string('error_message')->nullable();
            $table->json('response')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status'], 'mailtrap_single_sends_status_idx');
            $table->index(['tenant_id', 'to_email'], 'mailtrap_single_sends_email_idx');
        });
    }

    public function down(): void
    {
        $table = config('mailtrap-core.tables.single_sends', 'mailtrap_single_sends');
        Schema::dropIfExists($table);
    }
};
