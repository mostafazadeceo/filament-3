<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurant_suppliers', function (Blueprint $table): void {
            $table->foreignId('accounting_party_id')
                ->nullable()
                ->after('company_id')
                ->constrained('accounting_ir_parties', 'id', 'rest_suppliers_party_fk')
                ->nullOnDelete();
        });

        Schema::table('restaurant_items', function (Blueprint $table): void {
            $table->foreignId('accounting_inventory_item_id')
                ->nullable()
                ->after('company_id')
                ->constrained('accounting_ir_inventory_items', 'id', 'rest_items_inv_item_fk')
                ->nullOnDelete();
        });

        Schema::table('restaurant_warehouses', function (Blueprint $table): void {
            $table->foreignId('accounting_inventory_warehouse_id')
                ->nullable()
                ->after('company_id')
                ->constrained('accounting_ir_inventory_warehouses', 'id', 'rest_wh_inv_wh_fk')
                ->nullOnDelete();
        });

        Schema::table('restaurant_inventory_docs', function (Blueprint $table): void {
            $table->foreignId('accounting_inventory_doc_id')
                ->nullable()
                ->after('id')
                ->constrained('accounting_ir_inventory_docs', 'id', 'rest_inv_docs_accounting_fk')
                ->nullOnDelete();
        });

        Schema::table('restaurant_goods_receipts', function (Blueprint $table): void {
            $table->foreignId('accounting_journal_entry_id')
                ->nullable()
                ->after('id')
                ->constrained('accounting_ir_journal_entries', 'id', 'rest_gr_journal_fk')
                ->nullOnDelete();
        });

        Schema::table('restaurant_menu_sales', function (Blueprint $table): void {
            $table->foreignId('accounting_journal_entry_id')
                ->nullable()
                ->after('id')
                ->constrained('accounting_ir_journal_entries', 'id', 'rest_sales_journal_fk')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_menu_sales', function (Blueprint $table): void {
            $table->dropForeign('rest_sales_journal_fk');
            $table->dropColumn('accounting_journal_entry_id');
        });

        Schema::table('restaurant_goods_receipts', function (Blueprint $table): void {
            $table->dropForeign('rest_gr_journal_fk');
            $table->dropColumn('accounting_journal_entry_id');
        });

        Schema::table('restaurant_inventory_docs', function (Blueprint $table): void {
            $table->dropForeign('rest_inv_docs_accounting_fk');
            $table->dropColumn('accounting_inventory_doc_id');
        });

        Schema::table('restaurant_warehouses', function (Blueprint $table): void {
            $table->dropForeign('rest_wh_inv_wh_fk');
            $table->dropColumn('accounting_inventory_warehouse_id');
        });

        Schema::table('restaurant_items', function (Blueprint $table): void {
            $table->dropForeign('rest_items_inv_item_fk');
            $table->dropColumn('accounting_inventory_item_id');
        });

        Schema::table('restaurant_suppliers', function (Blueprint $table): void {
            $table->dropForeign('rest_suppliers_party_fk');
            $table->dropColumn('accounting_party_id');
        });
    }
};
