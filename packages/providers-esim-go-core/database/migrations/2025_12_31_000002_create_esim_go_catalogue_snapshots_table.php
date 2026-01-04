<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('providers-esim-go-core.tables.catalogue_snapshots', 'esim_go_catalogue_snapshots');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->timestamp('fetched_at')->nullable();
            $table->json('filters')->nullable();
            $table->string('hash')->index('esim_go_catalogue_hash_idx');
            $table->longText('payload');
            $table->string('source_version')->default('v2.5');
            $table->timestamps();

            $table->index(['tenant_id', 'fetched_at'], 'esim_go_catalogue_tenant_idx');
        });
    }

    public function down(): void
    {
        $table = config('providers-esim-go-core.tables.catalogue_snapshots', 'esim_go_catalogue_snapshots');
        Schema::dropIfExists($table);
    }
};
