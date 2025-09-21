<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Instructor\CourseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Student routes (accessible to all authenticated users)
    Route::get('/student',function () { 
        return view('dashboard');
    })->name('dashboard');

    // Admin-only routes
    Route::get('admin/users', function () { 
        return view('dashboard');
    })->middleware('role:admin')->name('dashboard');
});

Route::middleware(['auth','role:instructor,admin'])->prefix('instructor')
    ->name('instructor.')->group(function () {
        Route::resource('courses', CourseController::class);
    });

Route::get('/test-auth', function () {
    $user = auth()->user();
    dd([
        'user_id' => $user->id,
        'user_role' => $user->role,
        'user_email' => $user->email,
        'can_create_course' => Gate::allows('create', App\Models\Course::class),
        'auth_check' => auth()->check()
    ]);
});

Route::get('/test-policy', function () {
    $user = auth()->user();
    $policy = app()->make(App\Policies\CoursePolicy::class);
    
    dd([
        'user_role' => $user->role,
        'policy_result' => $policy->create($user),
        'gate_result' => Gate::allows('create', App\Models\Course::class)
    ]);
});

require __DIR__.'/auth.php';