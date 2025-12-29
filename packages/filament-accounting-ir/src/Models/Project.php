<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class Project extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_projects';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'name',
        'code',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
