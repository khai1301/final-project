<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTutorApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Only apply to tutors
        if (!$user || !$user->isTutor()) {
            return $next($request);
        }
        
        // Check if tutor is approved
        if (!$user->tutorProfile || !$user->tutorProfile->is_approved) {
            // Only block certain actions, allow profile editing
            $allowedRoutes = [
                'tutor.profile',
                'tutor.profile.edit',
                'tutor.profile.update',
                'tutor.certificate.delete',
                'tutors.browse',  // Can view other tutors
                'requests.browse', // Can view student requests
                'home.index',     // Homepage
                'notifications.*', // Notifications
            ];
            
            // Check if current route is allowed
            foreach ($allowedRoutes as $pattern) {
                if ($request->routeIs($pattern)) {
                    return $next($request);
                }
            }
            
            // Block with error message
            return redirect()->route('tutor.profile.edit')
                ->with('warning', __('messages.tutor_profile_pending'));
        }
        
        return $next($request);
    }
}
