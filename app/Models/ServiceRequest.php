<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ServiceRequest extends Model
{
    use HasFactory;

    public const SERVICE_TYPES = [
        'agriculture' => 'Agriculture',
        'aquaculture' => 'Aquaculture',
        'etude-technique' => 'Étude technique',
        'formation' => 'Formation',
        'autre' => 'Autre',
    ];

    public const STATUSES = [
        'new' => 'Nouvelle',
        'reviewing' => 'En revue',
        'assigned' => 'Assignée',
        'in_progress' => 'En cours',
        'waiting_client' => 'En attente client',
        'completed' => 'Terminée',
        'closed' => 'Clôturée',
        'cancelled' => 'Annulée',
    ];

    /** Statuts dans lesquels le client peut encore annuler ou retirer un document. */
    public const EDITABLE_BY_CLIENT = ['new', 'reviewing'];

    public const CLOSED_STATUSES = ['completed', 'closed', 'cancelled'];

    public static function generateReference(): string
    {
        do {
            $reference = 'SRQ-'.Str::upper(Str::random(8));
        } while (static::query()->where('reference', $reference)->exists());

        return $reference;
    }

    public static function statusLabelFor(?string $status): string
    {
        return self::STATUSES[$status] ?? ucfirst((string) $status);
    }

    public function statusLabel(): string
    {
        return self::statusLabelFor($this->status);
    }

    protected $fillable = [
        'reference',
        'client_id',
        'service_type',
        'title',
        'description',
        'priority',
        'status',
        'assigned_expert_id',
        'document_paths',
        'status_notes',
        'status_changed_at',
    ];

    protected function casts(): array
    {
        return [
            'document_paths' => 'array',
            'status_changed_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function assignedExpert(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_expert_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class)->orderBy('sort_order');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ProjectMessage::class)->latest();
    }

    public function events(): HasMany
    {
        return $this->hasMany(ServiceRequestEvent::class)->latest('created_at')->latest('id');
    }

    public function record(string $type, ?User $user = null, array $attributes = []): ServiceRequestEvent
    {
        return $this->events()->create(array_merge([
            'type' => $type,
            'user_id' => $user?->id,
        ], $attributes));
    }

    public function isEditableByClient(): bool
    {
        return in_array($this->status, self::EDITABLE_BY_CLIENT, true);
    }

    public function isClosed(): bool
    {
        return in_array($this->status, self::CLOSED_STATUSES, true);
    }

    /**
     * Personnes à prévenir d'une activité sur le projet, hors son auteur.
     * Sans expert assigné, l'équipe d'administration suit le projet.
     *
     * @return Collection<int, User>
     */
    public function participantsExcept(?User $author): Collection
    {
        $people = collect([$this->client, $this->assignedExpert])->filter();

        if (! $this->assignedExpert) {
            $people = $people->merge(User::query()->where('role', 'admin')->whereNull('suspended_at')->get());
        }

        return $people
            ->unique('id')
            ->reject(fn (User $user) => $author && $user->id === $author->id)
            ->reject(fn (User $user) => $user->isSuspended())
            ->values();
    }
}
