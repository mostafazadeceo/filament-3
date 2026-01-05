<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('filamat-iam.mega_super_admins.emails', []);
        config()->set('filamat-iam.mega_super_admins.user_ids', []);
    }
}
