<?php

namespace Vendor\FilamentAccountingIr\Database\Seeders;

use Illuminate\Database\Seeder;
use Vendor\FilamentAccountingIr\Models\AccountType;

class AccountingIrSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'asset', 'name' => 'دارایی', 'normal_balance' => 'debit'],
            ['code' => 'liability', 'name' => 'بدهی', 'normal_balance' => 'credit'],
            ['code' => 'equity', 'name' => 'حقوق صاحبان سهام', 'normal_balance' => 'credit'],
            ['code' => 'income', 'name' => 'درآمد', 'normal_balance' => 'credit'],
            ['code' => 'expense', 'name' => 'هزینه', 'normal_balance' => 'debit'],
        ];

        foreach ($types as $type) {
            AccountType::query()->updateOrCreate(['code' => $type['code']], [
                'name' => $type['name'],
                'normal_balance' => $type['normal_balance'],
                'is_system' => true,
            ]);
        }
    }
}
