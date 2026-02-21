<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_bulk_import_jobs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('phonebook_id')->nullable()->constrained('sms_bulk_phonebooks')->nullOnDelete();
            $table->string('type', 16)->default('import')->index();
            $table->string('status', 32)->default('pending')->index();
            $table->string('input_path')->nullable();
            $table->string('output_path')->nullable();
            $table->unsignedInteger('total_rows')->nullable();
            $table->unsignedInteger('success_rows')->nullable();
            $table->unsignedInteger('failed_rows')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_bulk_import_jobs');
    }
};
