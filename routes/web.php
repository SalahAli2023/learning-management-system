<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Instructor\CourseController;
use App\Http\Controllers\Instructor\LessonController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\StudentController;
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

Route::middleware(['auth','verified'])->prefix('instructor')
    ->name('instructor.')->group(function () {  
        
        Route::resource('courses', CourseController::class);
        
        //  routes for restore 
        Route::post('courses/{course}/restore', [CourseController::class, 'restore'])
            ->name('courses.restore');
            
        Route::delete('courses/{course}/force', [CourseController::class, 'forceDelete'])
            ->name('courses.forceDelete');

        
        Route::resource('courses.lessons', LessonController::class);
        Route::post('/lessons/{lesson}/restore', [LessonController::class, 'restore'])
            ->name('lessons.restore');

        Route::post('/lessons/{lesson}/force', [LessonController::class, 'forceDelete'])
            ->name('lessons.forceDelete');

        Route::resource('courses.enrollments', EnrollmentController::class);
        Route::get('/courses/{course}/enrollments', [EnrollmentController::class, 'index'])
            ->name('enrollments.index');
            
        Route::put('/courses/{course}/enrollments/{enrollment}', [EnrollmentController::class, 'update'])
            ->name('enrollments.update');
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


// routes/web.php



Route::middleware(['auth', 'role:student'])->prefix('student')->group(function () {
    Route::get('/enrollments', [EnrollmentController::class, 'myEnrollments'])
            ->name('student.enrollments.index');
    // Courses
    Route::get('/courses', [CourseController::class, 'index'])->name('student.courses.index');
    Route::get('/courses/{course}', [StudentController::class, 'show'])->name('student.courses.show');
    Route::post('/courses/{course}/enroll', [StudentController::class, 'enroll'])->name('student.courses.enroll');
    Route::get('/courses/{course}/progress', [StudentController::class, 'progress'])->name('student.courses.progress');
    
    // Lessons
    Route::get('/courses/{course}/lessons/{lesson}', [StudentController::class, 'play'])->name('student.lessons.show');
});
require __DIR__.'/auth.php';