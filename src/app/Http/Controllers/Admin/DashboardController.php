<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Matching;
use App\Models\Payment;
use App\Models\Request as StudentRequest;
use App\Models\TutorProfile;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Users & Growth
        $totalUsers = User::count();
        $lastMonthUsers = User::where('created_at', '<', Carbon::now()->subMonth())->count();
        $userGrowth = $lastMonthUsers > 0 ? (($totalUsers - $lastMonthUsers) / $lastMonthUsers) * 100 : 0;
        
        // 2. Active Sessions (Interpreter as Accepted Matchings / Active Connections)
        // Calculating "vs yesterday" if possible, or just vs last month
        $activeSessions = Matching::where('status', 'accepted')->count();
        $yesterdaySessions = Matching::where('status', 'accepted')->where('updated_at', '<', Carbon::yesterday())->count();
        $sessionGrowth = $yesterdaySessions > 0 ? (($activeSessions - $yesterdaySessions) / $yesterdaySessions) * 100 : 0;

        // 3. Total Revenue
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $lastMonthRevenue = Payment::where('status', 'completed')->where('created_at', '<', Carbon::now()->subMonth())->sum('amount');
        $revenueGrowth = $lastMonthRevenue > 0 ? (($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;

        // 4. New Tutor Requests (Pending approvals)
        // Check "in the last hour" dynamic if needed, but for now just count all pending
        $newTutorRequests = TutorProfile::where('is_approved', false)->count();
        $lastHourTutorRequests = TutorProfile::where('is_approved', false)->where('created_at', '>=', Carbon::now()->subHour())->count();

        // 5. User Growth Data (30 Days)
        // Could be passed to chart if implemented
        
        // 6. Role Distribution
        $studentCount = User::where('role', 'student')->count();
        $tutorCount = User::where('role', 'tutor')->count();

        // 7. Latest Pending Requests (Student Requests)
        $latestRequests = StudentRequest::with(['student', 'subject'])
            ->whereIn('status', ['open', 'pending'])
            ->latest()
            ->take(5)
            ->get();

        // 8. Pending Tutor Verifications
        $pendingTutors = TutorProfile::with('user')
            ->where('is_approved', false)
            ->latest()
            ->take(5)
            ->get();

        // 9. Recent Transactions
        $recentTransactions = Payment::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'userGrowth',
            'activeSessions', 'sessionGrowth',
            'totalRevenue', 'revenueGrowth',
            'newTutorRequests', 'lastHourTutorRequests',
            'studentCount', 'tutorCount',
            'latestRequests',
            'pendingTutors',
            'recentTransactions'
        ));
    }
}
