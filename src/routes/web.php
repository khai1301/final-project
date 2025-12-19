<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TutorController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\EducationLevelController;
use App\Http\Controllers\Admin\LearningModeController;
use App\Http\Controllers\StudentRequestController;
use App\Http\Controllers\TutorProfileController;


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

// Tutor Profile routes
Route::middleware(['auth', 'role:tutor'])->group(function () {
    Route::get('/tutor/profile', [TutorProfileController::class, 'show'])->name('tutor.profile');
    Route::get('/tutor/profile/edit', [TutorProfileController::class, 'edit'])->name('tutor.profile.edit');
    Route::put('/tutor/profile', [TutorProfileController::class, 'update'])->name('tutor.profile.update');
    Route::delete('/tutor/certificates/{id}', [TutorProfileController::class, 'deleteCertificate'])->name('tutor.certificate.delete');
});




Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-ban', [UserController::class, 'toggleBan'])->name('users.toggle-ban');
    Route::patch('users/{user}/approve-tutor', [UserController::class, 'approveTutor'])->name('users.approve-tutor');

    // Learning Requests Management
    Route::get('/requests', [AdminRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{id}', [AdminRequestController::class, 'show'])->name('requests.show');
    Route::patch('/requests/{id}/status', [AdminRequestController::class, 'updateStatus'])->name('requests.update-status');
    Route::delete('/requests/{id}', [AdminRequestController::class, 'destroy'])->name('requests.destroy');

    // System Configuration
    Route::resource('subjects', SubjectController::class)->except(['show']);
    Route::resource('education-levels', EducationLevelController::class)->except(['show']);
    Route::resource('learning-modes', LearningModeController::class)->except([' show']);

    // Tutor Profiles Management
    Route::get('/tutor-profiles', [\App\Http\Controllers\Admin\TutorProfileController::class, 'index'])->name('tutor-profiles.index');
    Route::get('/tutor-profiles/{id}', [\App\Http\Controllers\Admin\TutorProfileController::class, 'show'])->name('tutor-profiles.show');
    Route::patch('/tutor-profiles/{id}/approve', [\App\Http\Controllers\Admin\TutorProfileController::class, 'approve'])->name('tutor-profiles.approve');
    Route::patch('/tutor-profiles/{id}/unapprove', [\App\Http\Controllers\Admin\TutorProfileController::class, 'unapprove'])->name('tutor-profiles.unapprove');
    Route::delete('/tutor-profiles/{id}', [\App\Http\Controllers\Admin\TutorProfileController::class, 'destroy'])->name('tutor-profiles.destroy');
});

// Home route - redirects based on role after login
Route::get('/home', function () {
    $user = auth()->user();
    
    // All roles redirect to '/' which will show appropriate view based on HomeController logic
    return redirect('/');
})->middleware(['auth', 'verified'])->name('home');
