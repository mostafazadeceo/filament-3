<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('fn_webpush_subscriptions')) {
            Schema::create('fn_webpush_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->text('endpoint');
                $table->string('endpoint_hash', 64);
                $table->string('public_key');
                $table->string('auth_token');
                $table->string('content_encoding')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'endpoint_hash'], 'fn_webpush_subscriptions_user_id_endpoint_hash_unique');
            });

            return;
        }

        if (! Schema::hasColumn('fn_webpush_subscriptions', 'endpoint_hash')) {
            Schema::table('fn_webpush_subscriptions', function (Blueprint $table) {
                $table->string('endpoint_hash', 64)->nullable()->after('endpoint');
            });
        }

        $subscriptions = DB::table('fn_webpush_subscriptions')
            ->select('id', 'endpoint', 'endpoint_hash')
            ->get();

        foreach ($subscriptions as $subscription) {
            if (! $subscription->endpoint_hash && $subscription->endpoint) {
                DB::table('fn_webpush_subscriptions')
                    ->where('id', $subscription->id)
                    ->update([
                        'endpoint_hash' => hash('sha256', $subscription->endpoint),
                    ]);
            }
        }

        try {
            Schema::table('fn_webpush_subscriptions', function (Blueprint $table) {
                $table->unique(['user_id', 'endpoint_hash'], 'fn_webpush_subscriptions_user_id_endpoint_hash_unique');
            });
        } catch (\Throwable $exception) {
            // Ignore if the unique index already exists.
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fn_webpush_subscriptions');
    }
};
