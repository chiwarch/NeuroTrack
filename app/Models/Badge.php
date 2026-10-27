<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Badge extends Model
{
    public const TYPES = [
        'xp' => 'Otomatis dari total XP',
        'auto' => 'Otomatis dari pencapaian',
        'manual' => 'Diberikan guru/admin',
    ];

    /** Nama ikon yang tersedia di komponen <x-icon>. */
    public const ICONS = ['star', 'award', 'flame', 'check-circle', 'clock', 'bar-chart', 'book', 'users', 'send'];

    public const TONES = [
        'brand' => 'bg-brand-100 text-brand-600',
        'amber' => 'bg-amber-100 text-amber-600',
        'orange' => 'bg-orange-100 text-orange-600',
        'rose' => 'bg-rose-100 text-rose-600',
        'violet' => 'bg-violet-100 text-violet-600',
        'sky' => 'bg-sky-100 text-sky-600',
        'emerald' => 'bg-emerald-100 text-emerald-600',
    ];

    protected $fillable = ['name', 'description', 'icon', 'required_xp', 'type', 'code', 'xp_reward', 'tone'];

    public function userBadges(): HasMany
    {
        return $this->hasMany(UserBadge::class);
    }

    public function toneClasses(): string
    {
        return self::TONES[$this->tone] ?? self::TONES['brand'];
    }

    /** Cara mendapatkannya, untuk ditampilkan pada lencana yang masih terkunci. */
    public function hint(): string
    {
        return match ($this->type) {
            'xp' => 'Kumpulkan '.$this->required_xp.' XP',
            'auto' => $this->description ?: 'Capai tantangan khusus',
            default => 'Penghargaan dari guru atau sekolah',
        };
    }
}
