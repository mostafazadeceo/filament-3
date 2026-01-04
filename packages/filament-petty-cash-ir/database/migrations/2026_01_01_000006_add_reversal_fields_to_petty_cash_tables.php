<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('petty_cash_expenses', function (Blueprint $table): void {
            $table->timestamp('reversed_at')->nullable()->after('paid_at');
            $table->foreignId('reversed_by')
                ->nullable()
                ->after('reversed_at')
                ->constrained('users', 'id', 'petty_cash_exp_reversed_fk')
                ->nullOnDelete();
            $table->foreignId('reversal_journal_entry_id')
                ->nullable()
                ->after('reversed_by')
                ->constrained('accounting_ir_journal_entries', 'id', 'petty_cash_exp_reversal_journal_fk')
                ->nullOnDelete();
            $table->text('reversal_reason')->nullable()->after('reversal_journal_entry_id');
        });

        Schema::table('petty_cash_replenishments', function (Blueprint $table): void {
            $table->timestamp('reversed_at')->nullable()->after('paid_at');
            $table->foreignId('reversed_by')
                ->nullable()
                ->after('reversed_at')
                ->constrained('users', 'id', 'petty_cash_rep_reversed_fk')
                ->nullOnDelete();
            $table->foreignId('reversal_journal_entry_id')
                ->nullable()
                ->after('reversed_by')
                ->constrained('accounting_ir_journal_entries', 'id', 'petty_cash_rep_reversal_journal_fk')
                ->nullOnDelete();
            $table->text('reversal_reason')->nullable()->after('reversal_journal_entry_id');
        });

        Schema::table('petty_cash_settlements', function (Blueprint $table): void {
            $table->timestamp('reversed_at')->nullable()->after('posted_at');
            $table->foreignId('reversed_by')
                ->nullable()
                ->after('reversed_at')
                ->constrained('users', 'id', 'petty_cash_settle_reversed_fk')
                ->nullOnDelete();
            $table->text('reversal_reason')->nullable()->after('reversed_by');
        });
    }

    public function down(): void
    {
        Schema::table('petty_cash_expenses', function (Blueprint $table): void {
            $table->dropForeign('petty_cash_exp_reversed_fk');
            $table->dropForeign('petty_cash_exp_reversal_journal_fk');
            $table->dropColumn(['reversed_at', 'reversed_by', 'reversal_journal_entry_id', 'reversal_reason']);
        });

        Schema::table('petty_cash_replenishments', function (Blueprint $table): void {
            $table->dropForeign('petty_cash_rep_reversed_fk');
            $table->dropForeign('petty_cash_rep_reversal_journal_fk');
            $table->dropColumn(['reversed_at', 'reversed_by', 'reversal_journal_entry_id', 'reversal_reason']);
        });

        Schema::table('petty_cash_settlements', function (Blueprint $table): void {
            $table->dropForeign('petty_cash_settle_reversed_fk');
            $table->dropColumn(['reversed_at', 'reversed_by', 'reversal_reason']);
        });
    }
};
