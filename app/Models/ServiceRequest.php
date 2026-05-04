<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRequest extends Model
{
    use HasFactory;

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
