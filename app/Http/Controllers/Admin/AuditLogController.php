<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminActivityLog::with('admin')
            ->latest();

        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', 'like', "%{$request->subject_type}%");
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', "%{$request->search}%")
                    ->orWhere('ip_address', 'like', "%{$request->search}%")
                    ->orWhere('admin_name', 'like', "%{$request->search}%");
            });
        }

        $logs = $query->paginate(50)->withQueryString();

        $actions = AdminActivityLog::select('action')
            ->distinct()
            ->pluck('action');

        $admins = User::query()
            ->select('id', 'name')
            ->whereHas('adminActivityLogs')
            ->orderBy('name')
            ->get();

        return view('admin.audit-logs.index', compact('logs', 'actions', 'admins'));
    }

    public function show(AdminActivityLog $log)
    {
        $log->load('admin');

        return view('admin.audit-logs.show', compact('log'));
    }
}
