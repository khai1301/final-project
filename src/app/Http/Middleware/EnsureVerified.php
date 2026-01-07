<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!$request->user()->is_verified) {
            return back()->with('swal', [
                'type' => 'warning',
                'title' => 'Cần xác thực CCCD',
                'text' => 'Bạn cần xác thực CCCD trước khi thực hiện hành động này. Vui lòng vào Hồ sơ → Xác thực CCCD.'
            ]);
        }

        return $next($request);
    }
}
