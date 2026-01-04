<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IamAiReport extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'findings_json' => 'array',
    ];

    public function proposals(): HasMany
    {
        return $this->hasMany(IamAiActionProposal::class, 'report_id');
    }
}
