<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    /** Kunci ikon => file di public/images/{kunci}.webp */
    public const ICONS = [
        'mapel-mtk' => 'Matematika',
        'mapel-indonesia' => 'Bahasa Indonesia',
        'mapel-english' => 'Bahasa Inggris',
        'mapel-seni' => 'Seni & Budaya',
        'mapel-rpl' => 'Rekayasa Perangkat Lunak',
        'books' => 'Tumpukan buku',
        'bulb' => 'Bohlam',
        'chart' => 'Grafik',
        'pie' => 'Diagram pai',
        'clipboard' => 'Papan klip',
        'plant' => 'Tanaman',
        'grad-cap' => 'Topi wisuda',
    ];

    protected $fillable = ['name', 'type', 'major_id', 'icon'];

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subject_teacher', 'subject_id', 'user_id');
    }

    public function iconUrl(): string
    {
        $key = array_key_exists($this->icon, self::ICONS) && file_exists(public_path('images/'.$this->icon.'.webp')) ? $this->icon : 'books';

        return asset('images/'.$key.'.webp');
    }

    public function typeLabel(): string
    {
        return $this->type === 'kejuruan' ? 'Kejuruan' : 'Umum';
    }
}
