<?php
namespace App\Http\Controllers\Admin;

use App\Models\LearningMode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LearningModeController extends Controller
{
    /**
     * Display a listing of learning modes.
     */
    public function index(Request $request)
    {
        $query = LearningMode::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $learningModes = $query->latest()->paginate(15);

        return view('admin.learning-modes.index', compact('learningModes'));
    }

    /**
     * Store a newly created learning mode.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:learning_modes,name',
            'icon' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');

        LearningMode::create($validated);

        return redirect()
            ->route('admin.learning-modes.index')
            ->with('swal', [
                'type' => 'success',
                'title' => 'Success',
                'text' => 'Learning mode created successfully.'
            ]);
    }

    /**
     * Update the specified learning mode.
     */
    public function update(Request $request, LearningMode $learningMode)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:learning_modes,name,' . $learningMode->id,
            'icon' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $learningMode->update($validated);

        return redirect()
            ->route('admin.learning-modes.index')
            ->with('swal', [
                'type' => 'success',
                'title' => 'Success',
                'text' => 'Learning mode updated successfully.'
            ]);
    }

    /**
     * Remove the specified learning mode.
     */
    public function destroy(LearningMode $learningMode)
    {
        $learningMode->delete();

        return redirect()
            ->route('admin.learning-modes.index')
            ->with('swal', [
                'type' => 'success',
                'title' => 'Success',
                'text' => 'Learning mode deleted successfully.'
            ]);
    }
}
