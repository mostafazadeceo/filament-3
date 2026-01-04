<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('filament-storefront-builder.tables', []);
        $pagesTable = $tables['pages'] ?? 'store_pages';
        $pageVersionsTable = $tables['page_versions'] ?? 'store_page_versions';
        $blocksTable = $tables['blocks'] ?? 'store_blocks';
        $menusTable = $tables['menus'] ?? 'store_menus';
        $menuItemsTable = $tables['menu_items'] ?? 'store_menu_items';
        $themesTable = $tables['themes'] ?? 'store_themes';
        $redirectsTable = $tables['redirects'] ?? 'store_redirects';

        if (! Schema::hasTable($pagesTable)) {
            Schema::create($pagesTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->unsignedBigInteger('site_id')->nullable();
                $table->string('title');
                $table->string('slug');
                $table->string('status')->default('draft');
                $table->json('blocks')->nullable();
                $table->json('seo')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->timestamp('scheduled_publish_at')->nullable();
                $table->unsignedInteger('version')->default(1);
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'slug']);
                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'site_id']);
            });
        }

        if (! Schema::hasTable($pageVersionsTable)) {
            Schema::create($pageVersionsTable, function (Blueprint $table) use ($pagesTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('page_id')->constrained($pagesTable)->cascadeOnDelete();
                $table->unsignedInteger('version')->default(1);
                $table->json('blocks')->nullable();
                $table->json('seo')->nullable();
                $table->string('status')->default('draft');
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['tenant_id', 'page_id']);
            });
        }

        if (! Schema::hasTable($blocksTable)) {
            Schema::create($blocksTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('key');
                $table->string('type');
                $table->string('name');
                $table->string('status')->default('active');
                $table->json('schema')->nullable();
                $table->json('content')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'key']);
                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable($menusTable)) {
            Schema::create($menusTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('key');
                $table->string('name');
                $table->string('status')->default('active');
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'key']);
                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable($menuItemsTable)) {
            Schema::create($menuItemsTable, function (Blueprint $table) use ($menusTable, $pagesTable, $menuItemsTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('menu_id')->constrained($menusTable)->cascadeOnDelete();
                $table->foreignId('parent_id')->nullable()->constrained($menuItemsTable)->nullOnDelete();
                $table->foreignId('page_id')->nullable()->constrained($pagesTable)->nullOnDelete();
                $table->string('label');
                $table->string('url')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'menu_id']);
            });
        }

        if (! Schema::hasTable($themesTable)) {
            Schema::create($themesTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('status')->default('draft');
                $table->json('config')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('activated_at')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable($redirectsTable)) {
            Schema::create($redirectsTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('from_path');
                $table->string('to_path');
                $table->unsignedSmallInteger('status_code')->default(301);
                $table->string('status')->default('active');
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'from_path']);
                $table->index(['tenant_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        $tables = config('filament-storefront-builder.tables', []);
        $redirectsTable = $tables['redirects'] ?? 'store_redirects';
        $themesTable = $tables['themes'] ?? 'store_themes';
        $menuItemsTable = $tables['menu_items'] ?? 'store_menu_items';
        $menusTable = $tables['menus'] ?? 'store_menus';
        $blocksTable = $tables['blocks'] ?? 'store_blocks';
        $pageVersionsTable = $tables['page_versions'] ?? 'store_page_versions';
        $pagesTable = $tables['pages'] ?? 'store_pages';

        Schema::dropIfExists($redirectsTable);
        Schema::dropIfExists($themesTable);
        Schema::dropIfExists($menuItemsTable);
        Schema::dropIfExists($menusTable);
        Schema::dropIfExists($blocksTable);
        Schema::dropIfExists($pageVersionsTable);
        Schema::dropIfExists($pagesTable);
    }
};
