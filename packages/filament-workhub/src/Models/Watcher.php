<?php

namespace Haida\FilamentWorkhub\Models;

use App\Models\User;
use Haida\FilamentWorkhub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Watcher extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'workhub_watchers';

    protected $fillable = [
        'tenant_id',
        'work_item_id',
        'user_id',
    ];

    public function workItem(): BelongsTo
    {
        return $this->belongsTo(WorkItem::class, 'work_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
