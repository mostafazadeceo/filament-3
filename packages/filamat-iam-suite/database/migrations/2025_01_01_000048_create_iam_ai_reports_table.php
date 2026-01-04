<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('iam_ai_reports')) {
            return;
        }

        Schema::create('iam_ai_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->string('title');
            $table->longText('body')->nullable();
            $table->string('severity', 16)->default('low');
            $table->json('findings_json')->nullable();
            $table->string('status', 32)->default('new');
            $table->string('idempotency_key', 64)->nullable();
            $table->string('correlation_id', 64)->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'severity']);
            $table->unique(['tenant_id', 'idempotency_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iam_ai_reports');
    }
};
