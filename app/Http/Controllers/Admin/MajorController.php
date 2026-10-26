<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MajorController extends Controller
{
    private function rules(?Major $major = null): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'code' => ['required', 'string', 'max:12', Rule::unique('majors', 'code')->ignore($major?->id)],
        ];
    }

    public function index()
    {
        return view('admin.majors.index', ['majors' => Major::withCount(['students', 'subjects'])->orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.majors.form', ['major' => new Major]);
    }

    public function store(Request $request)
    {
        Major::create($request->validate($this->rules()));

        return redirect()->route('admin.majors.index')->with('success', 'Jurusan ditambahkan.');
    }

    public function edit(Major $major)
    {
        return view('admin.majors.form', compact('major'));
    }

    public function update(Request $request, Major $major)
    {
        $major->update($request->validate($this->rules($major)));

        return redirect()->route('admin.majors.index')->with('success', 'Jurusan disimpan.');
    }

    public function destroy(Major $major)
    {
        if ($major->students()->exists()) {
            return back()->with('error', 'Jurusan masih punya siswa. Pindahkan siswanya terlebih dahulu.');
        }
        $major->delete();

        return back()->with('success', 'Jurusan dihapus.');
    }
}
