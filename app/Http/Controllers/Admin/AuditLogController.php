<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = AuditLog::with(['user', 'auditable'])
            ->orderBy('created_at', 'desc');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', $request->auditable_type);
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(50);

        $actions = AuditLog::distinct('action')->pluck('action');
        $modelTypes = AuditLog::distinct('auditable_type')
            ->whereNotNull('auditable_type')
            ->pluck('auditable_type')
            ->map(fn($type) => class_basename($type));

        return view('admin.audit-logs.index', compact('logs', 'actions', 'modelTypes'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load(['user', 'auditable']);

        return view('admin.audit-logs.show', compact('auditLog'));
    }

    public function destroy(AuditLog $auditLog)
    {
        $auditLog->delete();

        return back()->with('success', 'Audit-Log-Eintrag wurde gelöscht.');
    }

    public function clear(Request $request)
    {
        $request->validate([
            'older_than' => 'required|integer|min:1',
        ]);

        $date = now()->subDays($request->older_than);
        $count = AuditLog::where('created_at', '<', $date)->delete();

        return back()->with('success', "{$count} Audit-Log-Einträge wurden gelöscht.");
    }

    public function export(Request $request)
    {
        $query = AuditLog::with(['user'])->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $filename = 'audit_logs_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            fputcsv($file, ['ID', 'Datum', 'Benutzer', 'Aktion', 'Modell', 'IP', 'Beschreibung']);

            $query->chunk(100, function ($logs) use ($file) {
                foreach ($logs as $log) {
                    fputcsv($file, [
                        $log->id,
                        $log->created_at->format('d.m.Y H:i:s'),
                        $log->user?->name ?? 'System',
                        $log->action,
                        $log->auditable_type ? class_basename($log->auditable_type) : '',
                        $log->ip_address,
                        $log->description,
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

