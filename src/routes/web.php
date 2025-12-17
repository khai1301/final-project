<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TutorController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\StudentRequestController;


// Auth Routes Override (using FormRequest) abc
Route::post('/login', [AuthController::class, 'login'])
    ->middleware(['guest', 'throttle:login']);
Route::post('/register', [AuthController::class, 'register'])
    ->middleware(['guest']);


Route::get('/', [HomeController::class, 'index'])->name('home.index');

// Student Learning Request routes
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student/request/create', [StudentRequestController::class, 'create'])->name('student.request.create');
    Route::post('/student/request', [StudentRequestController::class, 'store'])->name('student.request.store');
});




Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-ban', [UserController::class, 'toggleBan'])->name('users.toggle-ban');
    Route::patch('users/{user}/approve-tutor', [UserController::class, 'approveTutor'])->name('users.approve-tutor');
});

// Home route - redirects based on role after login
Route::get('/home', function () {
    $user = auth()->user();
    
    // All roles redirect to '/' which will show appropriate view based on HomeController logic
    return redirect('/');
})->middleware(['auth', 'verified'])->name('home');
