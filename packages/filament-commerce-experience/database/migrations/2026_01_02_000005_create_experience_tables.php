<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('filament-commerce-experience.tables', []);
        $reviewsTable = $tables['reviews'] ?? 'exp_reviews';
        $reviewVotesTable = $tables['review_votes'] ?? 'exp_review_votes';
        $questionsTable = $tables['questions'] ?? 'exp_questions';
        $answersTable = $tables['answers'] ?? 'exp_answers';
        $csatSurveysTable = $tables['csat_surveys'] ?? 'exp_csat_surveys';
        $csatResponsesTable = $tables['csat_responses'] ?? 'exp_csat_responses';
        $npsSurveysTable = $tables['nps_surveys'] ?? 'exp_nps_surveys';
        $npsResponsesTable = $tables['nps_responses'] ?? 'exp_nps_responses';
        $buyNowTable = $tables['buy_now_preferences'] ?? 'exp_buy_now_preferences';

        if (! Schema::hasTable($reviewsTable)) {
            Schema::create($reviewsTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->unsignedTinyInteger('rating')->default(0);
                $table->string('title')->nullable();
                $table->text('body')->nullable();
                $table->string('status')->default('pending');
                $table->boolean('verified_purchase')->default(false);
                $table->unsignedInteger('helpful_count')->default(0);
                $table->boolean('abuse_flag')->default(false);
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('published_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'product_id']);
                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable($reviewVotesTable)) {
            Schema::create($reviewVotesTable, function (Blueprint $table) use ($reviewsTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('review_id')->constrained($reviewsTable)->cascadeOnDelete();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('vote')->default('helpful');
                $table->timestamps();

                $table->unique(['tenant_id', 'review_id', 'user_id']);
            });
        }

        if (! Schema::hasTable($questionsTable)) {
            Schema::create($questionsTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->text('question');
                $table->string('status')->default('pending');
                $table->timestamp('answered_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'product_id']);
                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable($answersTable)) {
            Schema::create($answersTable, function (Blueprint $table) use ($questionsTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('question_id')->constrained($questionsTable)->cascadeOnDelete();
                $table->unsignedBigInteger('answered_by_user_id')->nullable();
                $table->text('answer');
                $table->string('status')->default('approved');
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'question_id']);
            });
        }

        if (! Schema::hasTable($csatSurveysTable)) {
            Schema::create($csatSurveysTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->string('channel')->nullable();
                $table->string('status')->default('pending');
                $table->timestamp('sent_at')->nullable();
                $table->timestamp('answered_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable($csatResponsesTable)) {
            Schema::create($csatResponsesTable, function (Blueprint $table) use ($csatSurveysTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('survey_id')->constrained($csatSurveysTable)->cascadeOnDelete();
                $table->unsignedTinyInteger('score')->default(0);
                $table->text('comment')->nullable();
                $table->timestamp('responded_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'survey_id']);
            });
        }

        if (! Schema::hasTable($npsSurveysTable)) {
            Schema::create($npsSurveysTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->string('channel')->nullable();
                $table->string('status')->default('pending');
                $table->timestamp('sent_at')->nullable();
                $table->timestamp('answered_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable($npsResponsesTable)) {
            Schema::create($npsResponsesTable, function (Blueprint $table) use ($npsSurveysTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('survey_id')->constrained($npsSurveysTable)->cascadeOnDelete();
                $table->unsignedTinyInteger('score')->default(0);
                $table->text('comment')->nullable();
                $table->timestamp('responded_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'survey_id']);
            });
        }

        if (! Schema::hasTable($buyNowTable)) {
            Schema::create($buyNowTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->unsignedBigInteger('default_address_id')->nullable();
                $table->string('default_payment_provider')->nullable();
                $table->string('status')->default('active');
                $table->boolean('requires_2fa')->default(false);
                $table->timestamp('consent_at')->nullable();
                $table->string('consent_ip')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'customer_id']);
            });
        }
    }

    public function down(): void
    {
        $tables = config('filament-commerce-experience.tables', []);
        $buyNowTable = $tables['buy_now_preferences'] ?? 'exp_buy_now_preferences';
        $npsResponsesTable = $tables['nps_responses'] ?? 'exp_nps_responses';
        $npsSurveysTable = $tables['nps_surveys'] ?? 'exp_nps_surveys';
        $csatResponsesTable = $tables['csat_responses'] ?? 'exp_csat_responses';
        $csatSurveysTable = $tables['csat_surveys'] ?? 'exp_csat_surveys';
        $answersTable = $tables['answers'] ?? 'exp_answers';
        $questionsTable = $tables['questions'] ?? 'exp_questions';
        $reviewVotesTable = $tables['review_votes'] ?? 'exp_review_votes';
        $reviewsTable = $tables['reviews'] ?? 'exp_reviews';

        Schema::dropIfExists($buyNowTable);
        Schema::dropIfExists($npsResponsesTable);
        Schema::dropIfExists($npsSurveysTable);
        Schema::dropIfExists($csatResponsesTable);
        Schema::dropIfExists($csatSurveysTable);
        Schema::dropIfExists($answersTable);
        Schema::dropIfExists($questionsTable);
        Schema::dropIfExists($reviewVotesTable);
        Schema::dropIfExists($reviewsTable);
    }
};
