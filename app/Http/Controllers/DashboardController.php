<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return redirect()->route(match (auth()->user()->role) {
            'admin' => 'admin.dashboard',
            'guru' => 'guru.dashboard',
            default => 'siswa.dashboard',
        });
    }
}
