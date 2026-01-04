<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('petty_cash_expenses', function (Blueprint $table): void {
            $table->foreignId('workflow_rule_id')
                ->nullable()
                ->after('category_id')
                ->constrained('petty_cash_workflow_rules', 'id', 'petty_cash_exp_workflow_fk')
                ->nullOnDelete();
            $table->unsignedSmallInteger('approval_steps_required')->default(1)->after('workflow_rule_id');
            $table->unsignedSmallInteger('approval_steps_completed')->default(0)->after('approval_steps_required');
            $table->json('approval_history')->nullable()->after('approval_steps_completed');
        });

        Schema::table('petty_cash_replenishments', function (Blueprint $table): void {
            $table->foreignId('workflow_rule_id')
                ->nullable()
                ->after('fund_id')
                ->constrained('petty_cash_workflow_rules', 'id', 'petty_cash_rep_workflow_fk')
                ->nullOnDelete();
            $table->unsignedSmallInteger('approval_steps_required')->default(1)->after('workflow_rule_id');
            $table->unsignedSmallInteger('approval_steps_completed')->default(0)->after('approval_steps_required');
            $table->json('approval_history')->nullable()->after('approval_steps_completed');
        });
    }

    public function down(): void
    {
        Schema::table('petty_cash_expenses', function (Blueprint $table): void {
            $table->dropForeign('petty_cash_exp_workflow_fk');
            $table->dropColumn(['workflow_rule_id', 'approval_steps_required', 'approval_steps_completed', 'approval_history']);
        });

        Schema::table('petty_cash_replenishments', function (Blueprint $table): void {
            $table->dropForeign('petty_cash_rep_workflow_fk');
            $table->dropColumn(['workflow_rule_id', 'approval_steps_required', 'approval_steps_completed', 'approval_history']);
        });
    }
};
