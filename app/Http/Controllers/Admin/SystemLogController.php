<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = DB::table(config('logtodb.collection', 'log'))
            ->orderBy('datetime', 'desc');

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level_name', $request->level);
        }

        // Filter by channel
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('datetime', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('datetime', '<=', $request->to);
        }

        // Search in message
        if ($request->filled('search')) {
            $query->where('message', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(50);

        // Get unique levels and channels for filters
        $levels = DB::table(config('logtodb.collection', 'log'))
            ->distinct()
            ->pluck('level_name')
            ->filter()
            ->sort()
            ->values();

        $channels = DB::table(config('logtodb.collection', 'log'))
            ->distinct()
            ->pluck('channel')
            ->filter()
            ->sort()
            ->values();

        return view('admin.system-logs.index', compact('logs', 'levels', 'channels'));
    }

    public function show($id)
    {
        $log = DB::table(config('logtodb.collection', 'log'))
            ->where('id', $id)
            ->first();

        if (!$log) {
            abort(404, 'Log-Eintrag nicht gefunden');
        }

        // Decode JSON context and extra
        $log->context_decoded = $log->context ? json_decode($log->context, true) : null;
        $log->extra_decoded = $log->extra ? json_decode($log->extra, true) : null;

        return view('admin.system-logs.show', compact('log'));
    }

    public function destroy($id)
    {
        DB::table(config('logtodb.collection', 'log'))
            ->where('id', $id)
            ->delete();

        return redirect()->route('system-logs.index')->with('success', 'System-Log-Eintrag wurde gelöscht.');
    }

    public function clear(Request $request)
    {
        $request->validate([
            'older_than' => 'required|integer|min:1',
        ]);

        $date = now()->subDays($request->older_than);
        $count = DB::table(config('logtodb.collection', 'log'))
            ->where('datetime', '<', $date)
            ->delete();

        return redirect()->route('system-logs.index')->with('success', "{$count} System-Log-Einträge wurden gelöscht.");
    }

    public function clearByLevel(Request $request)
    {
        $request->validate([
            'level' => 'required|string',
        ]);

        $count = DB::table(config('logtodb.collection', 'log'))
            ->where('level_name', $request->level)
            ->delete();

        return back()->with('success', "{$count} {$request->level}-Einträge wurden gelöscht.");
    }

    public function export(Request $request)
    {
        $query = DB::table(config('logtodb.collection', 'log'))
            ->orderBy('datetime', 'desc');

        // Apply same filters as index
        if ($request->filled('level')) {
            $query->where('level_name', $request->level);
        }
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }
        if ($request->filled('from')) {
            $query->whereDate('datetime', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('datetime', '<=', $request->to);
        }
        if ($request->filled('search')) {
            $query->where('message', 'like', '%' . $request->search . '%');
        }

        $filename = 'system_logs_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            fputcsv($file, ['ID', 'Datum/Zeit', 'Level', 'Channel', 'Nachricht']);

            $query->chunk(100, function ($logs) use ($file) {
                foreach ($logs as $log) {
                    fputcsv($file, [
                        $log->id,
                        $log->datetime,
                        $log->level_name,
                        $log->channel ?? '-',
                        $log->message,
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function statistics()
    {
        $stats = [
            'total' => DB::table(config('logtodb.collection', 'log'))->count(),
            'today' => DB::table(config('logtodb.collection', 'log'))
                ->whereDate('datetime', today())
                ->count(),
            'week' => DB::table(config('logtodb.collection', 'log'))
                ->where('datetime', '>=', now()->subDays(7))
                ->count(),
            'by_level' => DB::table(config('logtodb.collection', 'log'))
                ->select('level_name', DB::raw('count(*) as count'))
                ->groupBy('level_name')
                ->orderBy('count', 'desc')
                ->get(),
            'by_channel' => DB::table(config('logtodb.collection', 'log'))
                ->select('channel', DB::raw('count(*) as count'))
                ->whereNotNull('channel')
                ->groupBy('channel')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'recent_errors' => DB::table(config('logtodb.collection', 'log'))
                ->whereIn('level_name', ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'])
                ->orderBy('datetime', 'desc')
                ->limit(10)
                ->get(),
        ];

        return view('admin.system-logs.statistics', compact('stats'));
    }
}

