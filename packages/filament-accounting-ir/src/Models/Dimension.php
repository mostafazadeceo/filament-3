<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class Dimension extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'accounting_ir_dimensions';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'name',
        'code',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'metadata' => 'array',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(DimensionValue::class, 'dimension_id');
    }
}
