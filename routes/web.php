<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Instructor\CourseController;
use App\Http\Controllers\Instructor\LessonController;
use App\Http\Controllers\Instructor\AssignmentController;
use App\Http\Controllers\Student\StudentAssignmentController;
use App\Http\Controllers\Instructor\SubmissionController;
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
        // Route::get('/courses/{course}/enrollments', [EnrollmentController::class, 'index'])
        //     ->name('enrollments.index');
            
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

// routes/student
Route::middleware(['auth', 'role:student'])->prefix('student')->group(function () {
    Route::get('/enrollments', [EnrollmentController::class, 'myEnrollments'])
            ->name('student.enrollments.index');
    // Courses
    Route::get('/courses', [CourseController::class, 'index'])->name('student.courses.index');
    Route::get('/courses/{course}', [StudentController::class, 'show'])->name('student.courses.show');
    Route::post('/courses/{course}/enroll', [StudentController::class, 'enroll'])->name('student.courses.enroll');
    Route::post('/courses/{course}/cancel', [StudentController::class, 'cancelEnrollment'])->name('enrollments.cancel');
    Route::get('/courses/{course}/progress', [StudentController::class, 'progress'])->name('student.courses.progress');
    
    // Lessons
    Route::get('/courses/{course}/lessons/{lesson}', [StudentController::class, 'play'])->name('student.lessons.show');

    // Assignments
    Route::get('/courses/{course}/assignments', [StudentAssignmentController::class, 'index'])->name('student.assignments.index');
    Route::get('/courses/{course}/assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('student.assignments.show');
    Route::post('/courses/{course}/assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'])->name('student.assignments.submit');
    Route::get('/courses/{course}/assignments/{assignment}/submissions/{submission}', [StudentAssignmentController::class, 'submission'])->name('student.assignments.submission');
    Route::get('/courses/{course}/assignments/{assignment}/submissions/{submission}/download', [StudentAssignmentController::class, 'download'])->name('student.assignments.download');
});

// routes/instructor
Route::middleware(['auth', 'role:instructor'])->prefix('instructor')->group(function () {
    // ... existing assignment routes ...
    
     // Assignments
    Route::get('/courses/{course}/assignments', [AssignmentController::class, 'index'])->name('instructor.assignments.index');
    Route::get('/courses/{course}/assignments/create', [AssignmentController::class, 'create'])->name('instructor.assignments.create');
    Route::post('/courses/{course}/assignments', [AssignmentController::class, 'store'])->name('instructor.assignments.store');
    Route::get('/courses/{course}/assignments/{assignment}', [AssignmentController::class, 'show'])->name('instructor.assignments.show');
    Route::get('/courses/{course}/assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('instructor.assignments.edit');
    Route::put('/courses/{course}/assignments/{assignment}', [AssignmentController::class, 'update'])->name('instructor.assignments.update');
    Route::delete('/courses/{course}/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('instructor.assignments.destroy');
    Route::post('/courses/{course}/assignments/{assignment}/toggle-publish', [AssignmentController::class, 'togglePublish'])->name('instructor.assignments.toggle-publish');

    // Assignment Trash
    Route::get('/courses/{course}/assignments/trash', [AssignmentController::class, 'trash'])->name('instructor.assignments.trash');
    Route::post('/courses/{course}/assignments/{assignment}/restore', [AssignmentController::class, 'restore'])->name('instructor.assignments.restore');
    
    // Submissions
    Route::get('/courses/{course}/assignments/{assignment}/submissions', [SubmissionController::class, 'index'])->name('instructor.submissions.index');
    Route::get('/courses/{course}/assignments/{assignment}/submissions/{submission}', [SubmissionController::class, 'show'])->name('instructor.submissions.show');
    Route::post('/courses/{course}/assignments/{assignment}/submissions/{submission}/grade', [SubmissionController::class, 'grade'])->name('instructor.submissions.grade');
    Route::put('/courses/{course}/assignments/{assignment}/submissions/{submission}/status', [SubmissionController::class, 'updateStatus'])->name('instructor.submissions.update-status');
    Route::get('/courses/{course}/assignments/{assignment}/submissions/{submission}/download', [SubmissionController::class, 'download'])->name('instructor.submissions.download');
    
    // Submission Trash
    Route::get('/courses/{course}/assignments/{assignment}/submissions/trash', [SubmissionController::class, 'trash'])->name('instructor.submissions.trash');
    Route::post('/courses/{course}/assignments/{assignment}/submissions/{submission}/restore', [SubmissionController::class, 'restore'])->name('instructor.submissions.restore');
});

require __DIR__.'/auth.php';