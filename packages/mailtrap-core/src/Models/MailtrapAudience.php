<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MailtrapAudience extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'status',
        'description',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    public function contacts(): HasMany
    {
        return $this->hasMany(MailtrapAudienceContact::class, 'audience_id');
    }

    public function getTable(): string
    {
        return config('mailtrap-core.tables.audiences', 'mailtrap_audiences');
    }
}
