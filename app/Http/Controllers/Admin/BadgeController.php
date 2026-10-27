<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BadgeController extends Controller
{
    private function rules(?Badge $badge = null): array
    {
        $locked = $badge?->type === 'auto'; // tipe & syarat lencana otomatis ditentukan kode, hanya tampilannya yang bisa diubah

        return [
            'name' => ['required', 'string', 'max:150', Rule::unique('badges', 'name')->ignore($badge)],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['required', Rule::in(Badge::ICONS)],
            'tone' => ['required', Rule::in(array_keys(Badge::TONES))],
            'xp_reward' => ['required', 'integer', 'between:0,500'],
        ] + ($locked ? [] : [
            'type' => ['required', Rule::in(['xp', 'manual'])],
            'required_xp' => ['nullable', 'required_if:type,xp', 'integer', 'between:1,100000'],
        ]);
    }

    public function index()
    {
        return view('admin.badges.index', [
            'badges' => Badge::withCount('userBadges')->orderByRaw("case type when 'xp' then 1 when 'auto' then 2 else 3 end")->orderBy('required_xp')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.badges.form', ['badge' => new Badge(['icon' => 'award', 'tone' => 'brand', 'type' => 'manual'])]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['required_xp'] = $data['type'] === 'xp' ? $data['required_xp'] : 0;
        Badge::create($data);

        return redirect()->route('admin.badges.index')->with('success', 'Lencana ditambahkan.');
    }

    public function edit(Badge $badge)
    {
        return view('admin.badges.form', compact('badge'));
    }

    public function update(Request $request, Badge $badge)
    {
        $data = $request->validate($this->rules($badge));
        if (isset($data['type'])) {
            $data['required_xp'] = $data['type'] === 'xp' ? $data['required_xp'] : 0;
        }
        $badge->update($data);

        return redirect()->route('admin.badges.index')->with('success', 'Lencana disimpan. Jalankan `php artisan xp:backfill` bila syarat XP diubah dan ingin diterapkan ke siswa yang sudah ada.');
    }

    public function destroy(Badge $badge)
    {
        if ($badge->type === 'auto') {
            return back()->with('error', 'Lencana otomatis tidak bisa dihapus.');
        }
        $badge->delete();

        return back()->with('success', 'Lencana dihapus.');
    }
}
