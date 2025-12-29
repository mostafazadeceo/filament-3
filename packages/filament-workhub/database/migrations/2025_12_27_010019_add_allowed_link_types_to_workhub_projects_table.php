<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('workhub_projects') || Schema::hasColumn('workhub_projects', 'allowed_link_types')) {
            return;
        }

        Schema::table('workhub_projects', function (Blueprint $table) {
            $table->json('allowed_link_types')->nullable()->after('due_date');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('workhub_projects') || ! Schema::hasColumn('workhub_projects', 'allowed_link_types')) {
            return;
        }

        Schema::table('workhub_projects', function (Blueprint $table) {
            $table->dropColumn('allowed_link_types');
        });
    }
};
