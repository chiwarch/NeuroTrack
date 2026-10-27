<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\UserBadge;
use App\Models\XpTransaction;

class AchievementController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $earned = UserBadge::where('user_id', $user->id)->get()->keyBy('badge_id');

        return view('siswa.achievements', [
            'badges' => Badge::orderByRaw("case type when 'xp' then 1 when 'auto' then 2 else 3 end")->orderBy('required_xp')->orderBy('id')->get(),
            'earned' => $earned,
            'history' => XpTransaction::where('user_id', $user->id)->latest('created_at')->latest('id')->take(15)->get(),
        ]);
    }
}
