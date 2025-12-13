<?php

use Illuminate\Support\Facades\Route;

// Auth Routes Override (using FormRequest)
Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login'])
    ->middleware(['guest', 'throttle:login']);
Route::post('/register', [\App\Http\Controllers\Auth\AuthController::class, 'register'])
    ->middleware(['guest']);

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::patch('users/{user}/toggle-ban', [\App\Http\Controllers\Admin\UserController::class, 'toggleBan'])->name('users.toggle-ban');
    Route::patch('users/{user}/approve-tutor', [\App\Http\Controllers\Admin\UserController::class, 'approveTutor'])->name('users.approve-tutor');
});

// Home route - redirects based on role after login
Route::get('/home', function () {
    $user = auth()->user();
    
    if ($user->isAdmin()) {
        return redirect('/admin/dashboard');
    } elseif ($user->isTutor()) {
        return redirect('/admin/dashboard');
    } else {
        return redirect('/admin/dashboard');
    }
})->middleware(['auth', 'verified'])->name('home');
