<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('meeting_action_items')) {
            Schema::table('meeting_action_items', function (Blueprint $table) {
                $table->index(['tenant_id', 'status'], 'meeting_action_items_tenant_status_index');
                $table->index(['tenant_id', 'due_date'], 'meeting_action_items_tenant_due_date_index');
            });
        }

        if (Schema::hasTable('meeting_attendees')) {
            Schema::table('meeting_attendees', function (Blueprint $table) {
                $table->index(['tenant_id', 'attendance_status'], 'meeting_attendees_tenant_status_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('meeting_action_items')) {
            Schema::table('meeting_action_items', function (Blueprint $table) {
                $table->dropIndex('meeting_action_items_tenant_status_index');
                $table->dropIndex('meeting_action_items_tenant_due_date_index');
            });
        }

        if (Schema::hasTable('meeting_attendees')) {
            Schema::table('meeting_attendees', function (Blueprint $table) {
                $table->dropIndex('meeting_attendees_tenant_status_index');
            });
        }
    }
};
