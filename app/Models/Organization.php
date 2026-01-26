<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'website',
        'email',
        'phone',
        'logo',
        'billing_data',
        'billing_company',
        'billing_address',
        'billing_address_line2',
        'billing_postal_code',
        'billing_city',
        'billing_state',
        'billing_country',
        'tax_id',
        'bank_account',
        'payout_settings',
        'custom_platform_fee',
        'invoice_settings',
        'invoice_counter_booking',
        'invoice_counter_booking_year',
        'is_active',
        'is_verified',
        'verified_at',
        'paypal_enabled',
        'paypal_client_id',
        'paypal_client_secret',
        'paypal_mode',
        'paypal_webhook_id',
    ];

    protected function casts(): array
    {
        return [
            'billing_data' => 'array',
            'bank_account' => 'array',
            'payout_settings' => 'array',
            'custom_platform_fee' => 'array',
            'invoice_settings' => 'array',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
            'paypal_enabled' => 'boolean',
            'paypal_client_id' => 'encrypted',
            'paypal_client_secret' => 'encrypted',
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($organization) {
            if (empty($organization->slug)) {
                $organization->slug = Str::slug($organization->name);
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Users belonging to this organization
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_user')
            ->withPivot(['role', 'is_active', 'invited_at', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * Active users only
     */
    public function activeUsers(): BelongsToMany
    {
        return $this->users()->wherePivot('is_active', true);
    }

    /**
     * Organization owners
     */
    public function owners(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'owner');
    }

    /**
     * Organization admins
     */
    public function admins(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'admin');
    }

    /**
     * Organization members
     */
    public function members(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'member');
    }

    /**
     * Events created by this organization
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }


    /**
     * Get all bookings for this organization's events
     */
    public function bookings()
    {
        return Booking::whereHas('event', function ($query) {
            $query->where('organization_id', $this->id);
        });
    }

    /**
     * Check if user is member of this organization
     */
    public function hasMember(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if user is owner of this organization
     */
    public function hasOwner(User $user): bool
    {
        return $this->owners()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if user is admin of this organization
     */
    public function hasAdmin(User $user): bool
    {
        return $this->admins()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if user can manage this organization
     */
    public function canManage(User $user): bool
    {
        if ($user->hasRole('admin')) {
            return true; // Platform admins can manage all organizations
        }

        $pivot = $this->users()->where('user_id', $user->id)->first()?->pivot;

        return $pivot && $pivot->is_active && in_array($pivot->role, ['owner', 'admin']);
    }

    /**
     * Get user's role in this organization
     */
    public function getUserRole(User $user): ?string
    {
        return $this->users()->where('user_id', $user->id)->first()?->pivot?->role;
    }

    /**
     * Check if organization has complete billing data
     */
    public function hasCompleteBillingData(): bool
    {
        $billingData = $this->billing_data ?? [];

        $requiredFields = [
            'company_name',
            'company_address',
            'company_postal_code',
            'company_city',
            'company_country',
            'company_email',
            'company_phone',
            'tax_id',
        ];

        foreach ($requiredFields as $field) {
            if (empty($billingData[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if organization has complete bank account data
     */
    public function hasCompleteBankAccount(): bool
    {
        $bankAccount = $this->bank_account ?? [];

        $requiredFields = [
            'account_holder',
            'bank_name',
            'iban',
            'bic',
        ];

        foreach ($requiredFields as $field) {
            if (empty($bankAccount[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if organization can publish events (has complete billing and bank data)
     */
    public function canPublishEvents(): bool
    {
        return $this->hasCompleteBillingData() && $this->hasCompleteBankAccount();
    }

    /**
     * Get logo URL
     */
    public function logoUrl(): ?string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }

        return null;
    }

    /**
     * Get initials for avatar
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn($word) => Str::upper(Str::substr($word, 0, 1)))
            ->take(2)
            ->implode('');
    }

    /**
     * Check if PayPal is configured and enabled
     */
    public function hasPayPalConfigured(): bool
    {
        // Check if PayPal is enabled
        if (!$this->paypal_enabled) {
            return false;
        }

        // Get decrypted values
        $clientId = $this->paypal_client_id;
        $clientSecret = $this->paypal_client_secret;

        // Check if credentials exist and are not the placeholder value
        if (empty($clientId) || empty($clientSecret)) {
            return false;
        }

        if ($clientId === '********' || $clientSecret === '********') {
            return false;
        }

        // Client ID and Secret must be different
        if ($clientId === $clientSecret) {
            \Log::warning('PayPal configuration error: Client ID and Secret are identical', [
                'organization_id' => $this->id,
            ]);
            return false;
        }

        return true;
    }

    /**
     * Get PayPal credentials array
     */
    public function getPayPalCredentials(): ?array
    {
        if (!$this->hasPayPalConfigured()) {
            return null;
        }

        $clientId = $this->paypal_client_id;
        $clientSecret = $this->paypal_client_secret;

        // Double-check credentials are not placeholder values
        if ($clientId === '********' || $clientSecret === '********') {
            return null;
        }

        // Double-check they are not identical
        if ($clientId === $clientSecret) {
            return null;
        }

        return [
            'mode' => $this->paypal_mode ?? 'sandbox',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'webhook_id' => $this->paypal_webhook_id,
        ];
    }
}
