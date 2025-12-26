<?php

namespace Haida\FilamentRelograde\Database\Seeders;

use Haida\FilamentRelograde\Models\RelogradeConnection;
use Illuminate\Database\Seeder;

class RelogradeSeeder extends Seeder
{
    public function run(): void
    {
        if (RelogradeConnection::query()->exists()) {
            return;
        }

        $apiKey = env('RELOGRADE_API_KEY');
        if (! $apiKey) {
            return;
        }

        RelogradeConnection::create([
            'name' => 'Default',
            'environment' => 'sandbox',
            'api_key' => $apiKey,
            'is_default' => true,
        ]);
    }
}
