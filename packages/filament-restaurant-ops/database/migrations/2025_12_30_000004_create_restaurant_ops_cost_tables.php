<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_recipes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'rest_recipes_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'rest_recipes_company_fk')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->decimal('yield_quantity', 14, 4)->default(1);
            $table->foreignId('yield_uom_id')
                ->nullable()
                ->constrained('restaurant_uoms', 'id', 'rest_recipes_uom_fk')
                ->nullOnDelete();
            $table->decimal('waste_percent', 6, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'name'], 'rest_recipes_company_name_uniq');
            $table->index(['tenant_id', 'company_id', 'is_active'], 'rest_recipes_tenant_company_active_idx');
        });

        Schema::create('restaurant_recipe_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('recipe_id')
                ->constrained('restaurant_recipes', 'id', 'rest_recipe_lines_recipe_fk')
                ->cascadeOnDelete();
            $table->foreignId('item_id')
                ->constrained('restaurant_items', 'id', 'rest_recipe_lines_item_fk')
                ->cascadeOnDelete();
            $table->foreignId('uom_id')
                ->nullable()
                ->constrained('restaurant_uoms', 'id', 'rest_recipe_lines_uom_fk')
                ->nullOnDelete();
            $table->decimal('quantity', 14, 4);
            $table->decimal('waste_percent', 6, 2)->default(0);
            $table->boolean('is_optional')->default(false);
            $table->timestamps();

            $table->index(['recipe_id', 'item_id'], 'rest_recipe_lines_recipe_item_idx');
        });

        Schema::create('restaurant_menu_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'rest_menu_items_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'rest_menu_items_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('recipe_id')
                ->nullable()
                ->constrained('restaurant_recipes', 'id', 'rest_menu_items_recipe_fk')
                ->nullOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('category')->nullable();
            $table->decimal('price', 14, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'name'], 'rest_menu_items_company_name_uniq');
            $table->index(['tenant_id', 'company_id', 'is_active'], 'rest_menu_items_tenant_company_active_idx');
        });

        Schema::create('restaurant_menu_sales', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'rest_sales_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'rest_sales_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'rest_sales_branch_fk')
                ->nullOnDelete();
            $table->date('sale_date')->nullable();
            $table->string('source')->default('manual');
            $table->string('external_ref')->nullable();
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'branch_id'], 'rest_sales_tenant_company_branch_idx');
            $table->index(['company_id', 'sale_date'], 'rest_sales_company_date_idx');
            $table->index(['company_id', 'status'], 'rest_sales_company_status_idx');
        });

        Schema::create('restaurant_menu_sale_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('menu_sale_id')
                ->constrained('restaurant_menu_sales', 'id', 'rest_sales_lines_sale_fk')
                ->cascadeOnDelete();
            $table->foreignId('menu_item_id')
                ->constrained('restaurant_menu_items', 'id', 'rest_sales_lines_item_fk')
                ->cascadeOnDelete();
            $table->decimal('quantity', 14, 4);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('line_total', 14, 2)->default(0);
            $table->timestamps();

            $table->index(['menu_sale_id', 'menu_item_id'], 'rest_sales_lines_sale_item_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_menu_sale_lines');
        Schema::dropIfExists('restaurant_menu_sales');
        Schema::dropIfExists('restaurant_menu_items');
        Schema::dropIfExists('restaurant_recipe_lines');
        Schema::dropIfExists('restaurant_recipes');
    }
};
