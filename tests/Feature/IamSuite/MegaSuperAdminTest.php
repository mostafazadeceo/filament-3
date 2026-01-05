<?php

namespace Tests\Feature\IamSuite;

use App\Models\User;
use Filamat\IamSuite\Support\MegaSuperAdmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MegaSuperAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_mega_super_admin_requires_allow_list_when_configured(): void
    {
        config()->set('filamat-iam.mega_super_admins.emails', ['owner@example.test']);

        $owner = User::query()->create([
            'name' => 'Owner',
            'email' => 'owner@example.test',
            'password' => bcrypt('secret'),
        ]);
        $owner->forceFill(['is_super_admin' => true])->save();

        $other = User::query()->create([
            'name' => 'Other',
            'email' => 'other@example.test',
            'password' => bcrypt('secret'),
        ]);
        $other->forceFill(['is_super_admin' => true])->save();

        $this->assertTrue(MegaSuperAdmin::check($owner));
        $this->assertFalse(MegaSuperAdmin::check($other));
    }
}
