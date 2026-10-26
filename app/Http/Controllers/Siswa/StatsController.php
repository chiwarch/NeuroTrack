<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Services\SkillStats;

class StatsController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        return view('siswa.stats', [
            'hard' => SkillStats::hardSkills($user),
            'soft' => SkillStats::softSkills($user),
        ]);
    }
}
