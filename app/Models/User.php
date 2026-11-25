<?php

namespace App\Models;

use App\Models\Shop\Address;
use App\Models\Shop\Order;
use App\Models\Shop\ProductReview;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLES = ['client', 'expert', 'manager', 'admin'];

    /**
     * The attributes that are mass assignable.
     *
     * Le rôle n'en fait pas partie : il s'attribue avec forceFill().
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'preferences' => 'array',
            'suspended_at' => 'datetime',
        ];
    }

    public const ROLE_LABELS = ['client' => 'Client', 'expert' => 'Expert', 'manager' => 'Gestionnaire boutique', 'admin' => 'Administrateur'];

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'client_id');
    }

    public function assignedRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'assigned_expert_id');
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    /** Préférence d'e-mail ; activée tant que l'utilisateur ne l'a pas désactivée. */
    public function wantsEmail(string $preference): bool
    {
        return (bool) ($this->preferences[$preference] ?? true);
    }

    public function roleLabel(): string
    {
        return self::ROLE_LABELS[$this->role] ?? ucfirst((string) $this->role);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isExpert(): bool
    {
        return $this->role === 'expert';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function canManageShop(): bool
    {
        return in_array($this->role, ['admin', 'manager'], true);
    }

    /** Les comptes qui gèrent la plateforme se connectent avec un code reçu par e-mail. */
    public function requiresTwoFactor(): bool
    {
        return $this->canManageShop();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->latest();
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class)->orderByDesc('is_default')->orderBy('id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    /** Rattache les commandes passées sans compte avec la même adresse e-mail, une fois celle-ci confirmée. */
    public function claimGuestOrders(): int
    {
        if (! $this->hasVerifiedEmail()) {
            return 0;
        }

        return Order::query()->whereNull('user_id')->where('email', $this->email)->update(['user_id' => $this->id]);
    }
}
