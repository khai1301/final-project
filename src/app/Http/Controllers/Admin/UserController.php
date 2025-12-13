<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\TutorProfile;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by search term
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'banned') {
                $query->whereNotNull('banned_at');
            } elseif ($request->status == 'active') {
                $query->whereNull('banned_at');
            }
        }

        // Default sorting
        $users = $query->latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'email_verified_at' => now(), // Auto verify created by admin
        ]);

        // Create profile based on role
        if ($user->role === 'student') {
            StudentProfile::create(['user_id' => $user->id]);
        } elseif ($user->role === 'tutor') {
            TutorProfile::create(['user_id' => $user->id]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($validated['password']);
        }

        // Handle role change profile logic
        if ($user->role !== $validated['role']) {
            // Delete old profile
            if ($user->role === 'student') {
                $user->studentProfile()->delete();
            } elseif ($user->role === 'tutor') {
                $user->tutorProfile()->delete();
            }

            // Create new profile
            if ($validated['role'] === 'student') {
                StudentProfile::firstOrCreate(['user_id' => $user->id]);
            } elseif ($validated['role'] === 'tutor') {
                TutorProfile::firstOrCreate(['user_id' => $user->id]);
            }
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle the ban status of a user.
     */
    public function toggleBan(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot ban yourself.');
        }

        if ($user->isBanned()) {
            $user->update(['banned_at' => null]);
            $message = 'User has been unbanned successfully.';
        } else {
            $user->update(['banned_at' => now()]);
            $message = 'User has been banned successfully.';
        }

        return back()->with('status', $message);
    }

    /**
     * Approve a tutor profile.
     */
    public function approveTutor(User $user)
    {
        if (!$user->isTutor()) {
            return back()->with('error', 'This user is not a tutor.');
        }

        $user->tutorProfile()->update(['is_approved' => true]);

        return back()->with('success', 'Tutor has been approved successfully.');
    }
}
