<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
    ];

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
}
