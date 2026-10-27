<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function index()
    {
        return view('announcements.feed', [
            'announcements' => Announcement::visibleTo(auth()->user())->with('creator:id,name', 'classes:id,name', 'majors:id,code')
                ->latest('published_at')->paginate(10),
        ]);
    }
}
