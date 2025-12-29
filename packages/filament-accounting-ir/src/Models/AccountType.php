<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    use HasFactory;

    protected $table = 'accounting_ir_account_types';

    protected $fillable = [
        'code',
        'name',
        'normal_balance',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'bool',
    ];
}
