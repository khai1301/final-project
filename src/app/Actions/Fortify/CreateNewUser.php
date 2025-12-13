<?php

namespace App\Actions\Fortify;

use App\Models\StudentProfile;
use App\Models\TutorProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        // Validation is now handled by RegisterRequest

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'role' => $input['role'],
            'phone' => $input['phone'] ?? null,
        ]);

        // Automatically create profile based on role
        if ($user->isStudent()) {
            StudentProfile::create([
                'user_id' => $user->id,
            ]);
        } elseif ($user->isTutor()) {
            TutorProfile::create([
                'user_id' => $user->id,
            ]);
        }

        return $user;
    }
}
