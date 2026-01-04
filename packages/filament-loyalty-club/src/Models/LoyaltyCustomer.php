<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LoyaltyCustomer extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_customers';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'tier_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'external_refs',
        'status',
        'birth_date',
        'joined_at',
        'marketing_opt_in',
        'marketing_opt_in_at',
        'marketing_opt_in_source',
        'sms_opt_in',
        'sms_opt_in_at',
        'whatsapp_opt_in',
        'whatsapp_opt_in_at',
        'telegram_opt_in',
        'telegram_opt_in_at',
        'bale_opt_in',
        'bale_opt_in_at',
        'webpush_opt_in',
        'webpush_opt_in_at',
        'email_opt_in',
        'email_opt_in_at',
    ];

    protected $casts = [
        'external_refs' => 'array',
        'birth_date' => 'date',
        'joined_at' => 'datetime',
        'marketing_opt_in' => 'boolean',
        'marketing_opt_in_at' => 'datetime',
        'sms_opt_in' => 'boolean',
        'sms_opt_in_at' => 'datetime',
        'whatsapp_opt_in' => 'boolean',
        'whatsapp_opt_in_at' => 'datetime',
        'telegram_opt_in' => 'boolean',
        'telegram_opt_in_at' => 'datetime',
        'bale_opt_in' => 'boolean',
        'bale_opt_in_at' => 'datetime',
        'webpush_opt_in' => 'boolean',
        'webpush_opt_in_at' => 'datetime',
        'email_opt_in' => 'boolean',
        'email_opt_in_at' => 'datetime',
    ];

    public function tier(): BelongsTo
    {
        return $this->belongsTo(LoyaltyTier::class, 'tier_id');
    }

    public function walletAccount(): HasOne
    {
        return $this->hasOne(LoyaltyWalletAccount::class, 'customer_id');
    }

    public function rewardRedemptions(): HasMany
    {
        return $this->hasMany(LoyaltyRewardRedemption::class, 'customer_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(LoyaltyEvent::class, 'customer_id');
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(LoyaltyCoupon::class, 'issued_to_customer_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(LoyaltyReferral::class, 'referrer_customer_id');
    }

    public function donationPledges(): HasMany
    {
        return $this->hasMany(LoyaltyDonationPledge::class, 'customer_id');
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(LoyaltyWalletLedger::class, 'customer_id');
    }

    public function segments(): BelongsToMany
    {
        return $this->belongsToMany(LoyaltySegment::class, 'loyalty_customer_segments', 'customer_id', 'segment_id')
            ->withPivot(['source', 'assigned_at'])
            ->withTimestamps();
    }
}
