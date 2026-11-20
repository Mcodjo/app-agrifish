<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'email', 'city', 'profile', 'comment',
        'show_name', 'accepts_updates', 'ip_hash', 'verified_at',
    ];

    protected $hidden = ['email', 'ip_hash'];

    protected function casts(): array
    {
        return [
            'show_name' => 'boolean',
            'accepts_updates' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    /** Signatures prises en compte dans le compteur public. */
    public function scopeCounted(Builder $query): Builder
    {
        return config('petition.require_verification')
            ? $query->whereNotNull('verified_at')
            : $query;
    }

    public function isCounted(): bool
    {
        return ! config('petition.require_verification') || $this->verified_at !== null;
    }

    /** Nom affiché publiquement : prénom + initiale du nom, ou anonyme. */
    public function publicName(): string
    {
        if (! $this->show_name) {
            return 'Signataire anonyme';
        }

        return $this->first_name.' '.mb_strtoupper(mb_substr($this->last_name, 0, 1)).'.';
    }

    public function profileLabel(): string
    {
        return config('petition.profiles')[$this->profile] ?? 'Autre';
    }

    public static function goalFor(int $count): int
    {
        foreach (config('petition.goal_steps') as $step) {
            if ($step > $count) {
                return $step;
            }
        }

        return (int) (ceil(($count + 1) / 100000) * 100000);
    }
}
