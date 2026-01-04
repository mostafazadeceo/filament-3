<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('page-builder.tables', []);
        $templatesTable = $tables['templates'] ?? 'page_builder_templates';
        $revisionsTable = $tables['revisions'] ?? 'page_builder_revisions';

        if (! Schema::hasTable($templatesTable)) {
            Schema::create($templatesTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
                $table->string('name');
                $table->string('key');
                $table->string('status')->default('draft');
                $table->json('schema')->nullable();
                $table->json('draft_content')->nullable();
                $table->json('published_content')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['site_id', 'key']);
                $table->index(['tenant_id', 'site_id', 'status']);
                $table->index('updated_at');
            });
        }

        if (! Schema::hasTable($revisionsTable)) {
            Schema::create($revisionsTable, function (Blueprint $table) use ($templatesTable) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('template_id')->constrained($templatesTable)->cascadeOnDelete();
                $table->unsignedInteger('version')->default(1);
                $table->string('status')->default('draft');
                $table->json('payload')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'template_id']);
                $table->index(['template_id', 'version']);
            });
        }
    }

    public function down(): void
    {
        $tables = config('page-builder.tables', []);
        $revisionsTable = $tables['revisions'] ?? 'page_builder_revisions';
        $templatesTable = $tables['templates'] ?? 'page_builder_templates';

        Schema::dropIfExists($revisionsTable);
        Schema::dropIfExists($templatesTable);
    }
};
