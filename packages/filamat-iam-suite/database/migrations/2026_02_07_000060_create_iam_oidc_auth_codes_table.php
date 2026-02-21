<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = 'iam_oidc_auth_codes';

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->string('client_id', 191);
            $blueprint->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $blueprint->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $blueprint->text('redirect_uri');
            $blueprint->text('scope')->nullable();
            $blueprint->string('nonce', 191)->nullable();
            $blueprint->string('code_hash', 64)->unique();
            $blueprint->timestamp('expires_at');
            $blueprint->timestamp('consumed_at')->nullable();
            $blueprint->timestamps();

            $blueprint->index(['client_id', 'expires_at'], 'iam_oidc_auth_codes_client_exp_idx');
            $blueprint->index(['user_id', 'tenant_id'], 'iam_oidc_auth_codes_user_tenant_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iam_oidc_auth_codes');
    }
};
