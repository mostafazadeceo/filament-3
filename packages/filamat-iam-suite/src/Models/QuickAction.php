<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuickAction extends Model
{
    use BelongsToTenant;

    protected $table = 'iam_quick_actions';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'rank' => 'integer',
        'sort' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }
}
