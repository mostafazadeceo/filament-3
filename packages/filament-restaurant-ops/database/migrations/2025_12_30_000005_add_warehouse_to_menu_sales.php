<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurant_menu_sales', function (Blueprint $table): void {
            $table->foreignId('warehouse_id')
                ->nullable()
                ->after('branch_id')
                ->constrained('restaurant_warehouses', 'id', 'rest_sales_warehouse_fk')
                ->nullOnDelete();

            $table->index(['company_id', 'warehouse_id'], 'rest_sales_company_wh_idx');
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_menu_sales', function (Blueprint $table): void {
            $table->dropIndex('rest_sales_company_wh_idx');
            $table->dropConstrainedForeignId('warehouse_id');
        });
    }
};
