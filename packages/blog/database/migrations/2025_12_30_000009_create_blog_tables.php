<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('blog.tables', []);
        $postsTable = $tables['posts'] ?? 'blog_posts';
        $categoriesTable = $tables['categories'] ?? 'blog_categories';
        $tagsTable = $tables['tags'] ?? 'blog_tags';
        $postTagTable = $tables['post_tag'] ?? 'blog_post_tag';

        if (! Schema::hasTable($categoriesTable)) {
            Schema::create($categoriesTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
                $table->string('name');
                $table->string('slug');
                $table->text('description')->nullable();
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['site_id', 'slug']);
                $table->index(['tenant_id', 'site_id']);
            });
        }

        if (! Schema::hasTable($tagsTable)) {
            Schema::create($tagsTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
                $table->string('name');
                $table->string('slug');
                $table->timestamps();

                $table->unique(['site_id', 'slug']);
                $table->index(['tenant_id', 'site_id']);
            });
        }

        if (! Schema::hasTable($postsTable)) {
            Schema::create($postsTable, function (Blueprint $table) use ($categoriesTable) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
                $table->foreignId('category_id')->nullable()->constrained($categoriesTable)->nullOnDelete();
                $table->string('title');
                $table->string('slug');
                $table->text('excerpt')->nullable();
                $table->string('status')->default('draft');
                $table->json('seo')->nullable();
                $table->longText('draft_content')->nullable();
                $table->longText('published_content')->nullable();
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

        if (! Schema::hasTable($postTagTable)) {
            Schema::create($postTagTable, function (Blueprint $table) use ($postsTable, $tagsTable) {
                $table->id();
                $table->foreignId('post_id')->constrained($postsTable)->cascadeOnDelete();
                $table->foreignId('tag_id')->constrained($tagsTable)->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['post_id', 'tag_id']);
            });
        }
    }

    public function down(): void
    {
        $tables = config('blog.tables', []);
        $postTagTable = $tables['post_tag'] ?? 'blog_post_tag';
        $postsTable = $tables['posts'] ?? 'blog_posts';
        $tagsTable = $tables['tags'] ?? 'blog_tags';
        $categoriesTable = $tables['categories'] ?? 'blog_categories';

        Schema::dropIfExists($postTagTable);
        Schema::dropIfExists($postsTable);
        Schema::dropIfExists($tagsTable);
        Schema::dropIfExists($categoriesTable);
    }
};
