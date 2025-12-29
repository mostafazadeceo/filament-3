<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('workhub_automation_rules') || Schema::hasColumn('workhub_automation_rules', 'last_ran_at')) {
            return;
        }

        Schema::table('workhub_automation_rules', function (Blueprint $table) {
            $table->timestamp('last_ran_at')->nullable()->after('actions');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('workhub_automation_rules') || ! Schema::hasColumn('workhub_automation_rules', 'last_ran_at')) {
            return;
        }

        Schema::table('workhub_automation_rules', function (Blueprint $table) {
            $table->dropColumn('last_ran_at');
        });
    }
};
