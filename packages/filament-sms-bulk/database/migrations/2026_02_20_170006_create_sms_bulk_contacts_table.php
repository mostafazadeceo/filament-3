<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_bulk_contacts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('phonebook_id')->constrained('sms_bulk_phonebooks')->cascadeOnDelete();
            $table->string('msisdn', 32);
            $table->string('full_name')->nullable();
            $table->json('option_values')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'phonebook_id', 'msisdn']);
            $table->unique(['tenant_id', 'phonebook_id', 'msisdn'], 'sms_bulk_contacts_unique_phonebook_msisdn');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_bulk_contacts');
    }
};
