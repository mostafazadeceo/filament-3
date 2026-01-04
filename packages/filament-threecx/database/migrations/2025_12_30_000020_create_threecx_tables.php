<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $instancesTable = config('filament-threecx.tables.instances', 'threecx_instances');
        $tokenTable = config('filament-threecx.tables.token_caches', 'threecx_token_caches');
        $cursorTable = config('filament-threecx.tables.sync_cursors', 'threecx_sync_cursors');
        $callLogsTable = config('filament-threecx.tables.call_logs', 'threecx_call_logs');
        $contactsTable = config('filament-threecx.tables.contacts', 'threecx_contacts');
        $auditLogsTable = config('filament-threecx.tables.api_audit_logs', 'threecx_api_audit_logs');

        if (! Schema::hasTable($instancesTable)) {
            Schema::create($instancesTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('base_url');
                $table->boolean('verify_tls')->default(true);
                $table->timestamp('last_health_at')->nullable();
                $table->text('last_error')->nullable();
                $table->string('last_version')->nullable();
                $table->json('last_capabilities_json')->nullable();
                $table->text('client_id')->nullable();
                $table->text('client_secret')->nullable();
                $table->text('crm_connector_key')->nullable();
                $table->string('crm_connector_key_hash', 64)->nullable();
                $table->boolean('xapi_enabled')->default(false);
                $table->boolean('call_control_enabled')->default(false);
                $table->boolean('crm_connector_enabled')->default(false);
                $table->string('route_point_dn')->nullable();
                $table->json('monitored_dns')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'name'], 'threecx_instances_tenant_name_unique');
                $table->index(['crm_connector_key_hash'], 'threecx_instances_connector_idx');
                $table->index(['tenant_id', 'base_url'], 'threecx_instances_tenant_url_idx');
                $table->index(['tenant_id', 'updated_at'], 'threecx_instances_tenant_updated_idx');
            });
        }

        if (! Schema::hasTable($tokenTable)) {
            Schema::create($tokenTable, function (Blueprint $table) use ($instancesTable) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('instance_id')->constrained($instancesTable)->cascadeOnDelete();
                $table->string('scope');
                $table->text('access_token');
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'instance_id', 'scope'], 'threecx_token_unique');
                $table->index(['instance_id', 'expires_at'], 'threecx_token_exp_idx');
            });
        }

        if (! Schema::hasTable($cursorTable)) {
            Schema::create($cursorTable, function (Blueprint $table) use ($instancesTable) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('instance_id')->constrained($instancesTable)->cascadeOnDelete();
                $table->string('entity');
                $table->json('cursor')->nullable();
                $table->timestamp('last_synced_at')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'instance_id', 'entity'], 'threecx_cursor_unique');
                $table->index(['instance_id', 'entity'], 'threecx_cursor_instance_entity_idx');
            });
        }

        if (! Schema::hasTable($callLogsTable)) {
            Schema::create($callLogsTable, function (Blueprint $table) use ($instancesTable) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('instance_id')->constrained($instancesTable)->cascadeOnDelete();
                $table->string('direction', 20)->nullable();
                $table->string('from_number')->nullable();
                $table->string('to_number')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('ended_at')->nullable();
                $table->unsignedInteger('duration')->nullable();
                $table->string('status')->nullable();
                $table->string('external_id')->nullable();
                $table->json('raw_payload')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'instance_id', 'started_at'], 'threecx_call_logs_started_idx');
                $table->index(['tenant_id', 'direction'], 'threecx_call_logs_direction_idx');
                $table->index(['tenant_id', 'status'], 'threecx_call_logs_status_idx');
                $table->index(['external_id'], 'threecx_call_logs_external_idx');
            });
        }

        if (! Schema::hasTable($contactsTable)) {
            Schema::create($contactsTable, function (Blueprint $table) use ($instancesTable) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('instance_id')->constrained($instancesTable)->cascadeOnDelete();
                $table->string('name')->nullable();
                $table->json('phones')->nullable();
                $table->json('emails')->nullable();
                $table->string('external_id')->nullable();
                $table->string('crm_url')->nullable();
                $table->json('raw_payload')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'instance_id', 'external_id'], 'threecx_contacts_ext_idx');
                $table->index(['tenant_id', 'name'], 'threecx_contacts_name_idx');
            });
        }

        if (! Schema::hasTable($auditLogsTable)) {
            Schema::create($auditLogsTable, function (Blueprint $table) use ($instancesTable) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('instance_id')->constrained($instancesTable)->cascadeOnDelete();
                $table->string('actor_type')->nullable();
                $table->unsignedBigInteger('actor_id')->nullable();
                $table->string('api_area');
                $table->string('method', 10);
                $table->string('path');
                $table->unsignedSmallInteger('status_code')->nullable();
                $table->unsignedInteger('duration_ms')->nullable();
                $table->string('correlation_id')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'instance_id', 'created_at'], 'threecx_audit_tenant_created_idx');
                $table->index(['api_area'], 'threecx_audit_area_idx');
                $table->index(['status_code'], 'threecx_audit_status_idx');
                $table->index(['correlation_id'], 'threecx_audit_corr_idx');
            });
        }
    }

    public function down(): void
    {
        $auditLogsTable = config('filament-threecx.tables.api_audit_logs', 'threecx_api_audit_logs');
        $contactsTable = config('filament-threecx.tables.contacts', 'threecx_contacts');
        $callLogsTable = config('filament-threecx.tables.call_logs', 'threecx_call_logs');
        $cursorTable = config('filament-threecx.tables.sync_cursors', 'threecx_sync_cursors');
        $tokenTable = config('filament-threecx.tables.token_caches', 'threecx_token_caches');
        $instancesTable = config('filament-threecx.tables.instances', 'threecx_instances');

        Schema::dropIfExists($auditLogsTable);
        Schema::dropIfExists($contactsTable);
        Schema::dropIfExists($callLogsTable);
        Schema::dropIfExists($cursorTable);
        Schema::dropIfExists($tokenTable);
        Schema::dropIfExists($instancesTable);
    }
};
