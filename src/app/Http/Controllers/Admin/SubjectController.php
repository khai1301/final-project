<?php
namespace App\Http\Controllers\Admin;

use App\Models\Subject;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class SubjectController extends Controller
{
    /**
     * Display a listing of subjects.
     */
    public function index(Request $request)
    {
        $query = Subject::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $subjects = $query->latest()->paginate(15);

        return view('admin.subjects.index', compact('subjects'));
    }

    /**
     * Store a newly created subject.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Subject::create($validated);

        return redirect()
            ->route('admin.subjects.index')
            ->with('swal', [
                'type' => 'success',
                'title' => 'Success',
                'text' => 'Subject created successfully.'
            ]);
    }

    /**
     * Update the specified subject.
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name,' . $subject->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $subject->update($validated);

        return redirect()
            ->route('admin.subjects.index')
            ->with('swal', [
                'type' => 'success',
                'title' => 'Success',
                'text' => 'Subject updated successfully.'
            ]);
    }

    /**
     * Remove the specified subject.
     */
    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()
            ->route('admin.subjects.index')
            ->with('swal', [
                'type' => 'success',
                'title' => 'Success',
                'text' => 'Subject deleted successfully.'
            ]);
    }
}
