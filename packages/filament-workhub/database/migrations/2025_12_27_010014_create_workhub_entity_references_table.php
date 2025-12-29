<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableExists = Schema::hasTable('workhub_entity_references');

        if (! $tableExists) {
            Schema::create('workhub_entity_references', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('source_type', 191);
                $table->unsignedBigInteger('source_id');
                $table->string('target_type', 191);
                $table->unsignedBigInteger('target_id');
                $table->string('relation_type', 191)->nullable();
                $table->timestamps();

                $table->index(['source_type', 'source_id']);
                $table->index(['target_type', 'target_id']);
                $table->unique(['source_type', 'source_id', 'target_type', 'target_id', 'relation_type'], 'workhub_entity_ref_unique');
            });
        }

        if ($tableExists && Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE workhub_entity_references MODIFY source_type VARCHAR(191) NOT NULL');
            DB::statement('ALTER TABLE workhub_entity_references MODIFY target_type VARCHAR(191) NOT NULL');
            DB::statement('ALTER TABLE workhub_entity_references MODIFY relation_type VARCHAR(191) NULL');
        }

        if (! Schema::hasIndex('workhub_entity_references', 'workhub_entity_ref_unique')) {
            Schema::table('workhub_entity_references', function (Blueprint $table) {
                $table->unique(['source_type', 'source_id', 'target_type', 'target_id', 'relation_type'], 'workhub_entity_ref_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('workhub_entity_references');
    }
};
