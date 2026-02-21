<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_bulk_phonebook_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('phonebook_id')->constrained('sms_bulk_phonebooks')->cascadeOnDelete();
            $table->string('name');
            $table->string('type', 16)->default('string');
            $table->boolean('is_required')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'phonebook_id', 'name'], 'sms_bulk_phonebook_options_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_bulk_phonebook_options');
    }
};
