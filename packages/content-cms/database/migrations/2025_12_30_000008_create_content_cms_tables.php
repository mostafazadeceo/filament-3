<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('content-cms.tables', []);
        $pagesTable = $tables['pages'] ?? 'content_cms_pages';
        $revisionsTable = $tables['page_revisions'] ?? 'content_cms_page_revisions';

        if (! Schema::hasTable($pagesTable)) {
            Schema::create($pagesTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
                $table->string('slug');
                $table->string('title');
                $table->string('status')->default('draft');
                $table->json('seo')->nullable();
                $table->json('draft_content')->nullable();
                $table->json('published_content')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['site_id', 'slug']);
                $table->index(['tenant_id', 'site_id', 'status']);
                $table->index('published_at');
                $table->index('updated_at');
            });
        }

        if (! Schema::hasTable($revisionsTable)) {
            Schema::create($revisionsTable, function (Blueprint $table) use ($pagesTable) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('page_id')->constrained($pagesTable)->cascadeOnDelete();
                $table->unsignedInteger('version')->default(1);
                $table->string('status')->default('draft');
                $table->json('payload')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'page_id']);
                $table->index(['page_id', 'version']);
            });
        }
    }

    public function down(): void
    {
        $tables = config('content-cms.tables', []);
        $revisionsTable = $tables['page_revisions'] ?? 'content_cms_page_revisions';
        $pagesTable = $tables['pages'] ?? 'content_cms_pages';

        Schema::dropIfExists($revisionsTable);
        Schema::dropIfExists($pagesTable);
    }
};
