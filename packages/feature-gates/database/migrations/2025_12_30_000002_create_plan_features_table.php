<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('feature-gates.tables', []);
        $planFeaturesTable = $tables['plan_features'] ?? 'plan_features';

        if (Schema::hasTable($planFeaturesTable)) {
            return;
        }

        Schema::create($planFeaturesTable, function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('subscription_plans')->cascadeOnDelete();
            $table->string('feature_key');
            $table->boolean('enabled')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->json('limits')->nullable();
            $table->timestamps();
            $table->unique(['plan_id', 'feature_key']);
            $table->index(['feature_key', 'enabled']);
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        $tables = config('feature-gates.tables', []);
        $planFeaturesTable = $tables['plan_features'] ?? 'plan_features';

        Schema::dropIfExists($planFeaturesTable);
    }
};
