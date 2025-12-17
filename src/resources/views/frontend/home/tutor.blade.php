@extends('frontend.layouts.app')

@section('title', 'Tutor Dashboard - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <h2 class="text-2xl font-bold mb-4">Tutor Dashboard</h2>
            <p>Welcome back, {{ auth()->user()->name }}!</p>
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Session Requests -->
                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-xl border border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-semibold mb-2">Session Requests</h3>
                    <p class="text-gray-500">No pending requests.</p>
                </div>
                <!-- Schedule -->
                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-xl border border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-semibold mb-2">My Schedule</h3>
                    <p class="text-gray-500">Your calendar is empty.</p>
                </div>
                <!-- Earnings -->
                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-xl border border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-semibold mb-2">Earnings</h3>
                    <p class="text-gray-500">$0.00 this month</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
