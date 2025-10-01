<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EnrollmentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display enrollments for a course (for instructors)
     */
    public function index(Course $course)
    {
        // $this->authorize('viewAny', [Enrollment::class, $course]);

        $enrollments = $course->enrollments()
            ->with('student')
            ->paginate(10);

        return view('enrollments.index', compact('course', 'enrollments'));
    }

    /**
     * Student enrolls in a course
     */
    public function store(Request $request, Course $course)
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

        return response()->json([
            'success' => true,
            'enrollment' => $enrollment,
            'message' => 'Successfully enrolled in the course!'
        ]);
    }

    /**
     * Update enrollment status (approve/complete/cancel)
     */
    public function update(Request $request, Course $course, Enrollment $enrollment)
    {
        $this->authorize('update', $enrollment);

        $request->validate([
            'status' => 'required|in:pending,active,completed,cancelled'
        ]);

        $enrollment->update([
            'status' => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : null
        ]);

        return response()->json([
            'success' => true,
            'enrollment' => $enrollment->fresh(),
            'message' => 'Enrollment updated successfully!'
        ]);
    }

    /**
     * Student withdraws from course
     */
    public function destroy(Course $course, Enrollment $enrollment)
    {
        $this->authorize('delete', $enrollment);

        $enrollment->update(['status' => 'cancelled']);
        $enrollment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully withdrawn from the course.'
        ]);
    }

    /**
     * Show student's enrollments
     */
    public function myEnrollments(Request $request,Course $course)
    {
        $enrollments = $request->user()->enrollments()
            ->with(['course.instructor', 'course.lessons'])
            ->paginate(10);

        return view('student.enrollments.index', compact('enrollments','course'));
    }
}