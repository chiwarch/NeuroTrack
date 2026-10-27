<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Services\AttendanceRate;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $children = auth()->user()->children()->with('major:id,code', 'classes:id,name')->orderBy('name')->get()
            ->each(fn ($child) => $child->attendance = AttendanceRate::summary($child));

        return view('ortu.dashboard', compact('children'));
    }
}
