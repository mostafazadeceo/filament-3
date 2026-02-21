<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Models;

use Haida\SmsBulk\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmsBulkPhonebook extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'sms_bulk_phonebooks';

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(SmsBulkPhonebookOption::class, 'phonebook_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(SmsBulkContact::class, 'phonebook_id');
    }
}
