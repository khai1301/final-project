<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TutorProfile;
use App\Models\User;
use Illuminate\Http\Request;

class TutorProfileController extends Controller
{
    /**
     * Display a listing of tutor profiles.
     */
    public function index(Request $request)
    {
        $query = TutorProfile::with('user');

        // Filter by approval status
        if ($request->has('status')) {
            if ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'approved') {
                $query->where('is_approved', true);
            }
        }

        // Search by name, email, or subjects
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $profiles = $query->latest()->paginate(15);
        $pendingCount = TutorProfile::where('is_approved', false)->count();

        return view('admin.tutor-profiles.index', compact('profiles', 'pendingCount'));
    }

    /**
     * Display the specified tutor profile.
     */
    public function show($id)
    {
        $profile = TutorProfile::with(['user', 'certificates'])->findOrFail($id);
        
        return view('admin.tutor-profiles.show', compact('profile'));
    }

    /**
     * Approve a tutor profile.
     */
    public function approve($id)
    {
        $profile = TutorProfile::findOrFail($id);
        $profile->update(['is_approved' => true]);

        return redirect()->back()->with('success', 'Tutor profile approved successfully!');
    }

    /**
     * Reject/unapprove a tutor profile.
     */
    public function unapprove($id)
    {
        $profile = TutorProfile::findOrFail($id);
        $profile->update(['is_approved' => false]);

        return redirect()->back()->with('success', 'Tutor profile unapproved!');
    }

    /**
     * Remove the specified tutor profile.
     */
    public function destroy($id)
    {
        $profile = TutorProfile::findOrFail($id);
        
        // Delete associated certificates and files
        foreach ($profile->certificates as $certificate) {
            if (\Storage::disk('public')->exists($certificate->file_path)) {
                \Storage::disk('public')->delete($certificate->file_path);
            }
            $certificate->delete();
        }

        // Delete CV if exists
        if ($profile->cv_path && \Storage::disk('public')->exists($profile->cv_path)) {
            \Storage::disk('public')->delete($profile->cv_path);
        }

        $profile->delete();

        return redirect()->route('admin.tutor-profiles.index')
            ->with('success', 'Tutor profile deleted successfully!');
    }
}
