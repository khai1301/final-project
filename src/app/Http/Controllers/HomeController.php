<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application home page.
     * Redirects authenticated users to role-specific dashboards.
     */
    public function index()
    {
        // All users (guests, students, tutors, admins) land on the same public home page
        // The view itself (navbar, content) will adapt based on the user's authentication and role
        return view('frontend.home.index');
    }
}
