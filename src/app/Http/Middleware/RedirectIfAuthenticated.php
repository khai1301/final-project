<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (auth()->guard($guard)->check()) {
                $user = auth()->guard($guard)->user();
                
                // Redirect based on role
                if ($user->isAdmin()) {
                    return redirect('/admin/dashboard');
                } elseif ($user->isTutor()) {
                    return redirect('/tutor/dashboard');
                } else {
                    return redirect('/student/dashboard');
                }
            }
        }

        return $next($request);
    }
}
