<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StudentController extends Controller
{
    use AuthorizesRequests;

    public function index(){
        // $this->authorize('viewAny', Course::class);
        return view('student.index', compact('course', 'enrollment'));
    } 

    /**
     * Display the specified course for students
     */
    public function show(Course $course)
    {
        // Check if student is enrolled or course is free
        $isEnrolled = auth()->user()->isEnrolledIn($course->id);
        $hasAccess = $isEnrolled || $course->is_free;

        $course->load([
            'instructor',
            'lessons' => function($query) {
                $query->whereNull('deleted_at')
                    ->orderBy('order')
                    ->orderBy('created_at');
            },
            'assignments' => function($query) {
                $query->with(['submissions' => function($q) {
                    $q->where('student_id', auth()->id());
                }]);
            }
        ]);

        $enrollment = $isEnrolled ? 
            Enrollment::where('student_id', auth()->id())
                ->where('course_id', $course->id)
                ->first() : null;

        return view('student.show', compact('course', 'enrollment', 'isEnrolled'));
    }

    public function play(Course $course,Lesson $lesson)
    {
        // $this->authorize('viewAny', $lesson);

        return view('student.lessons.show', compact('course', 'lesson'));
    }

    /**
     * Enroll student in course
     */
    public function enroll(Request $request, Course $course)
    {
        $this->authorize('create', Enrollment::class);

        // Check if already enrolled
        $existingEnrollment = Enrollment::where('student_id', $request->user()->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'success' => false,
                'message' => 'You are already enrolled in this course.'
            ], 422);
        }

        $enrollment = Enrollment::create([
            'student_id' => $request->user()->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        // Send notification to instructor
        // Notification::send($course->instructor, new NewEnrollmentNotification($enrollment));

        return response()->json([
            'success' => true,
            'enrollment' => $enrollment,
            'message' => 'Successfully enrolled in the course!'
        ]);
    }

    /**
     * Show student's course progress
     */
    public function progress(Course $course)
    {
        $enrollment = Enrollment::where('student_id', auth()->id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        $course->load(['lessons', 'assignments.submissions' => function($query) {
            $query->where('student_id', auth()->id());
        }]);

        $completedLessons = auth()->user()->completedLessons($course->id);
        $totalLessons = $course->lessons->count();
        $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        return view('student.courses.progress', compact('course', 'enrollment', 'progress'));
    }

    public function cancelEnrollment(Request $request, Course $course)
    {
        // $this->authorize('delete', Enrollment::class);

        // البحث عن ال enrollment
        $enrollment = Enrollment::where('student_id', $request->user()->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Enrollment not found.'
            ], 404);
        }

        // حذف ال enrollment
        $enrollment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Enrollment cancelled successfully!'
        ]);
    }
}