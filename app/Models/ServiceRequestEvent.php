<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequestEvent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'service_request_id',
        'user_id',
        'type',
        'from_status',
        'to_status',
        'details',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function summary(): string
    {
        $status = fn (?string $value) => ServiceRequest::statusLabelFor($value);

        return match ($this->type) {
            'created' => 'Demande déposée',
            'status' => 'Statut : '.$status($this->from_status).' → '.$status($this->to_status),
            'assigned' => 'Expert assigné : '.$this->details,
            'unassigned' => 'Expert retiré du projet',
            'cancelled' => 'Demande annulée par le client',
            'documents_added' => 'Documents ajoutés : '.$this->details,
            'document_removed' => 'Document retiré',
            default => ucfirst($this->type),
        };
    }
}
