<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_bulk_suppression_lists', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('msisdn', 32)->index();
            $table->string('reason')->nullable();
            $table->string('source', 32)->default('manual')->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'msisdn'], 'sms_bulk_suppression_tenant_msisdn_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_bulk_suppression_lists');
    }
};
