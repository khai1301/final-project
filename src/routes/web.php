<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TutorController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\EducationLevelController;
use App\Http\Controllers\Admin\LearningModeController;
use App\Http\Controllers\StudentRequestController;
use App\Http\Controllers\TutorProfileController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\NotificationController;

// Public Routes
Route::get('/tutors/{id}', [TutorProfileController::class, 'showPublic'])->name('tutor.show');

// NOTE: All auth routes (login, register, logout, password reset, email verification)
// are automatically registered by Laravel Fortify.
// See config/fortify.php and app/Providers/FortifyServiceProvider.php

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

// Matching routes (for authenticated students and tutors)
Route::middleware(['auth'])->group(function () {
    Route::post('/matching/connect', [MatchingController::class, 'store'])->name('matching.connect');
    Route::patch('/matching/{id}/accept', [MatchingController::class, 'accept'])->name('matching.accept');
    Route::patch('/matching/{id}/decline', [MatchingController::class, 'decline'])->name('matching.decline');
    Route::delete('/matching/{id}/cancel', [MatchingController::class, 'cancel'])->name('matching.cancel');
    Route::get('/my-connections', [MatchingController::class, 'index'])->name('matching.index');
    Route::get('/my-requests', [MatchingController::class, 'myRequests'])->name('matching.my-requests');
    
    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    
    // Contact Unlock routes
    Route::post('/contact/unlock/{matching}', [\App\Http\Controllers\ContactUnlockController::class, 'unlock'])->name('contact.unlock');
    Route::get('/payment/callback', [\App\Http\Controllers\ContactUnlockController::class, 'paymentCallback'])->name('payment.callback');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
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

    // Settings Management
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    
    // Matchings Management
    Route::get('/matchings', [\App\Http\Controllers\Admin\MatchingController::class, 'index'])->name('matchings.index');
    Route::get('/matchings/{id}', [\App\Http\Controllers\Admin\MatchingController::class, 'show'])->name('matchings.show');
});

// CV Parser routes (for tutors)
Route::middleware(['auth', 'role:tutor'])->prefix('cv')->group(function () {
    Route::post('/upload', [\App\Http\Controllers\CVParserController::class, 'upload'])->name('cv.upload');
    Route::post('/apply', [\App\Http\Controllers\CVParserController::class, 'applyParsedData'])->name('cv.apply');
});

// Home route - redirects based on role after login
Route::get('/home', function () {
    $user = auth()->user();
    
    // All roles redirect to '/' which will show appropriate view based on HomeController logic
    return redirect('/');
})->middleware(['auth', 'verified'])->name('home');
