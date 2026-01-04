<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('mailtrap-core.tables.campaigns', 'mailtrap_campaigns');
        $connections = config('mailtrap-core.tables.connections', 'mailtrap_connections');
        $audiences = config('mailtrap-core.tables.audiences', 'mailtrap_audiences');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) use ($connections, $audiences) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('connection_id')->constrained($connections)->cascadeOnDelete();
            $table->foreignId('audience_id')->nullable()->constrained($audiences)->nullOnDelete();
            $table->string('name', 190);
            $table->string('subject', 190);
            $table->string('from_email', 190)->nullable();
            $table->string('from_name', 190)->nullable();
            $table->longText('html_body')->nullable();
            $table->longText('text_body')->nullable();
            $table->string('status', 40)->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->json('stats')->nullable();
            $table->json('settings')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'name'], 'mailtrap_campaigns_tenant_name_unique');
            $table->index(['tenant_id', 'status'], 'mailtrap_campaigns_status_idx');
            $table->index(['tenant_id', 'scheduled_at'], 'mailtrap_campaigns_schedule_idx');
        });
    }

    public function down(): void
    {
        $table = config('mailtrap-core.tables.campaigns', 'mailtrap_campaigns');
        Schema::dropIfExists($table);
    }
};
