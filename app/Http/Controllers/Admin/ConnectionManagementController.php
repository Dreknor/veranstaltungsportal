<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserConnection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConnectionManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'admin']);
    }

    /**
     * Display all connections
     */
    public function index(Request $request)
    {
        $query = UserConnection::with(['follower', 'following']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search by user
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('follower', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('following', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $connections = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total' => UserConnection::count(),
            'accepted' => UserConnection::where('status', 'accepted')->count(),
            'pending' => UserConnection::where('status', 'pending')->count(),
            'blocked' => UserConnection::where('status', 'blocked')->count(),
            'most_connected_user' => $this->getMostConnectedUser(),
        ];

        return view('admin.connections.index', compact('connections', 'stats'));
    }

    /**
     * Get user with most connections
     */
    private function getMostConnectedUser()
    {
        $userId = UserConnection::where('status', 'accepted')
            ->select('follower_id', DB::raw('COUNT(*) as count'))
            ->groupBy('follower_id')
            ->orderBy('count', 'desc')
            ->first()?->follower_id;

        return $userId ? User::find($userId) : null;
    }

    /**
     * Show connection details
     */
    public function show(UserConnection $connection)
    {
        $connection->load(['follower', 'following']);

        return view('admin.connections.show', compact('connection'));
    }

    /**
     * Update connection status
     */
    public function updateStatus(Request $request, UserConnection $connection)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,blocked',
        ]);

        $connection->status = $request->status;
        $connection->save();

        return back()->with('success', 'Verbindungsstatus wurde aktualisiert.');
    }

    /**
     * Delete a connection
     */
    public function destroy(UserConnection $connection)
    {
        $followerName = $connection->follower->name;
        $followingName = $connection->following->name;

        $connection->delete();

        return redirect()
            ->route('admin.connections.index')
            ->with('success', "Verbindung zwischen {$followerName} und {$followingName} wurde gelöscht.");
    }

    /**
     * Show statistics
     */
    public function statistics(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays((int) $period);

        // Connections over time
        $connectionsByDay = UserConnection::where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // By status
        $byStatus = UserConnection::where('created_at', '>=', $startDate)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Most active users
        $mostActiveUsers = UserConnection::where('status', 'accepted')
            ->where('created_at', '>=', $startDate)
            ->select('follower_id', DB::raw('COUNT(*) as count'))
            ->groupBy('follower_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $user = User::find($item->follower_id);
                return [
                    'user' => $user,
                    'count' => $item->count,
                ];
            });

        return view('admin.connections.statistics', compact(
            'connectionsByDay',
            'byStatus',
            'mostActiveUsers',
            'period'
        ));
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,block,delete',
            'connection_ids' => 'required|array',
            'connection_ids.*' => 'exists:user_connections,id',
        ]);

        $connections = UserConnection::whereIn('id', $request->connection_ids)->get();

        foreach ($connections as $connection) {
            switch ($request->action) {
                case 'approve':
                    $connection->update(['status' => 'accepted']);
                    break;

                case 'block':
                    $connection->update(['status' => 'blocked']);
                    break;

                case 'delete':
                    $connection->delete();
                    break;
            }
        }

        return back()->with('success', 'Bulk-Aktion erfolgreich ausgeführt.');
    }
}

