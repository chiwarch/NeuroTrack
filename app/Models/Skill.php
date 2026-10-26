<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = ['name', 'category', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    /** Soft skill aktif, urut sesuai id (urutan sumbu radar tetap). */
    public static function softSkills()
    {
        return static::where('category', 'soft_skill')->where('is_active', true)->orderBy('id')->get();
    }
}
