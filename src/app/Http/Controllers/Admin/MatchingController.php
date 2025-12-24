<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matching;
use Illuminate\Http\Request;

class MatchingController extends Controller
{
    /**
     * Display all matchings with filters.
     */
    public function index(Request $request)
    {
        $query = Matching::with(['student', 'tutor', 'sender']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by sender role
        if ($request->has('sender_role') && $request->sender_role != '') {
            $query->whereHas('sender', function($q) use ($request) {
                $q->where('role', $request->sender_role);
            });
        }

        // Search by user name
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('student', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhereHas('tutor', function($tq) use ($search) {
                    $tq->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date != '') {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date != '') {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $matchings = $query->latest()->paginate(20);

        // Get statistics
        $stats = [
            'total' => Matching::count(),
            'pending' => Matching::where('status', 'pending')->count(),
            'accepted' => Matching::where('status', 'accepted')->count(),
            'declined' => Matching::where('status', 'declined')->count(),
        ];

        return view('admin.matchings.index', compact('matchings', 'stats'));
    }

    /**
     * Display matching details.
     */
    public function show($id)
    {
        $matching = Matching::with(['student', 'tutor', 'sender', 'notifications'])->findOrFail($id);

        return view('admin.matchings.show', compact('matching'));
    }
}
