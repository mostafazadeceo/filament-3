<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('site-builder-core.tables', []);
        $sitesTable = $tables['sites'] ?? 'sites';
        $brandingsTable = $tables['site_brandings'] ?? 'site_brandings';
        $historyTable = $tables['site_publish_histories'] ?? 'site_publish_histories';

        if (! Schema::hasTable($sitesTable)) {
            Schema::create($sitesTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('slug');
                $table->string('type')->default('website');
                $table->string('status')->default('draft');
                $table->string('default_locale', 10)->default('fa_IR');
                $table->string('currency', 10)->default('IRR');
                $table->string('timezone', 64)->default('Asia/Tehran');
                $table->string('theme_key', 64)->nullable();
                $table->timestamp('published_at')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'slug']);
                $table->index(['tenant_id', 'status']);
                $table->index('updated_at');
            });
        }

        if (! Schema::hasTable($brandingsTable)) {
            Schema::create($brandingsTable, function (Blueprint $table) use ($sitesTable) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('site_id')->constrained($sitesTable)->cascadeOnDelete();
                $table->string('brand_name')->nullable();
                $table->string('logo_path')->nullable();
                $table->string('favicon_path')->nullable();
                $table->string('primary_color', 32)->nullable();
                $table->string('secondary_color', 32)->nullable();
                $table->string('font_family', 64)->nullable();
                $table->text('footer_text')->nullable();
                $table->boolean('powered_by_enabled')->default(true);
                $table->timestamps();

                $table->unique('site_id');
                $table->index('tenant_id');
            });
        }

        if (! Schema::hasTable($historyTable)) {
            Schema::create($historyTable, function (Blueprint $table) use ($sitesTable) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('site_id')->constrained($sitesTable)->cascadeOnDelete();
                $table->string('from_status')->nullable();
                $table->string('to_status');
                $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'site_id']);
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        $tables = config('site-builder-core.tables', []);
        $historyTable = $tables['site_publish_histories'] ?? 'site_publish_histories';
        $brandingsTable = $tables['site_brandings'] ?? 'site_brandings';
        $sitesTable = $tables['sites'] ?? 'sites';

        Schema::dropIfExists($historyTable);
        Schema::dropIfExists($brandingsTable);
        Schema::dropIfExists($sitesTable);
    }
};
