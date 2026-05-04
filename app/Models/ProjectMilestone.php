<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_request_id',
        'title',
        'description',
        'status',
        'due_date',
        'sort_order',
        'deliverable_paths',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'deliverable_paths' => 'array',
        ];
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'planned' => 'Planifié',
            'in_progress' => 'En cours',
            'validated' => 'Validé',
            'blocked' => 'Bloqué',
            'completed' => 'Terminé',
            default => ucfirst($this->status),
        };
    }
}
