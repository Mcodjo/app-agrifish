<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLES = ['client', 'expert', 'admin'];

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

    public const ROLE_LABELS = ['client' => 'Client', 'expert' => 'Expert', 'admin' => 'Administrateur'];

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
}
