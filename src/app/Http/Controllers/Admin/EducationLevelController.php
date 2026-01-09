<?php
namespace App\Http\Controllers\Admin;

use App\Models\EducationLevel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EducationLevelController extends Controller
{
    /**
     * Display a listing of education levels.
     */
    public function index(Request $request)
    {
        $query = EducationLevel::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $educationLevels = $query->ordered()->paginate(15);

        return view('admin.education-levels.index', compact('educationLevels'));
    }

    /**
     * Store a newly created education level.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:education_levels,name',
            'order' => 'required|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        EducationLevel::create($validated);

        return redirect()
            ->route('admin.education-levels.index')
            ->with('swal', [
                'type' => 'success',
                'title' => 'Success',
                'text' => 'Education level created successfully.'
            ]);
    }

    /**
     * Update the specified education level.
     */
    public function update(Request $request, EducationLevel $educationLevel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:education_levels,name,' . $educationLevel->id,
            'order' => 'required|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $educationLevel->update($validated);

        return redirect()
            ->route('admin.education-levels.index')
            ->with('swal', [
                'type' => 'success',
                'title' => 'Success',
                'text' => 'Education level updated successfully.'
            ]);
    }

    /**
     * Remove the specified education level.
     */
    public function destroy(EducationLevel $educationLevel)
    {
        $educationLevel->delete();

        return redirect()
            ->route('admin.education-levels.index')
            ->with('swal', [
                'type' => 'success',
                'title' => 'Success',
                'text' => 'Education level deleted successfully.'
            ]);
    }
}
