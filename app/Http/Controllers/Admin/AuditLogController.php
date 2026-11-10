<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $logs = AuditLog::with('user:id,name,role')
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                ->where('event', 'like', "%{$q}%")->orWhere('description', 'like', "%{$q}%")->orWhere('ip', 'like', "%{$q}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%"))))
            ->latest('created_at')->latest('id')->paginate(30)->withQueryString();

        return view('admin.audit.index', compact('logs', 'q'));
    }
}
