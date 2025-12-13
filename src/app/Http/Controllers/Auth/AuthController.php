<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Actions\Fortify\CreateNewUser;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Fortify;

class AuthController extends Controller
{
    /**
     * Handle an incoming login request.
     */
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        if (Auth::attempt($validated, $request->boolean('remember'))) {
            // Check if user is banned
            if (Auth::user()->isBanned()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('error', 'Your account has been banned. Please contact support.');
            }

            $request->session()->regenerate();

            return app(LoginResponse::class);
        }

        return back()->withErrors([
            Fortify::username() => trans('auth.failed'),
        ])->onlyInput(Fortify::username());
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(RegisterRequest $request, CreateNewUser $creator)
    {
        // Validation handled by RegisterRequest, so we pass validated data to creator
        // But CreateNewUser expects 'array', and might have its own validation.
        // We should modify CreateNewUser to skip validation or just pass data.
        
        $user = $creator->create($request->validated());

        Auth::login($user);

        return app(RegisterResponse::class);
    }
}
