<?php
namespace App\Http\Controllers\Admin;

use App\Models\Request as LearningRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RequestController extends Controller
{
    /**
     * Display a listing of learning requests.
     */
    public function index(Request $request)
    {
        $query = LearningRequest::with('student');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($subQuery) use ($search) {
                      $subQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Mode filter
        if ($request->filled('mode')) {
            $query->where('mode', $request->input('mode'));
        }

        // Education level filter
        if ($request->filled('education_level')) {
            $query->where('education_level', $request->input('education_level'));
        }

        // Order by newest first
        $requests = $query->latest()->paginate(15);

        return view('admin.requests.index', compact('requests'));
    }

    /**
     * Display the specified learning request.
     */
    public function show($id)
    {
        $learningRequest = LearningRequest::with('student')->findOrFail($id);
        
        return view('admin.requests.show', compact('learningRequest'));
    }

    /**
     * Update the status of the specified request.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,locked,matched,closed,cancelled'
        ]);

        $learningRequest = LearningRequest::findOrFail($id);
        $learningRequest->update([
            'status' => $request->input('status')
        ]);

        return redirect()
            ->back()
            ->with('success', 'Request status updated successfully.');
    }

    /**
     * Remove the specified learning request.
     */
    public function destroy($id)
    {
        $learningRequest = LearningRequest::findOrFail($id);
        $learningRequest->delete();

        return redirect()
            ->route('admin.requests.index')
            ->with('success', 'Learning request deleted successfully.');
    }
}
