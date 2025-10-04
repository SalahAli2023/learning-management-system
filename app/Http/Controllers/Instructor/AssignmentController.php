<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AssignmentController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        $this->authorize('viewAny', [Assignment::class, $course]);

        $assignments = $course->assignments()
            ->withCount(['submissions', 'submissions as graded_submissions_count' => function($query) {
                $query->whereNotNull('grade');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('instructor.assignments.index', compact('course', 'assignments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Course $course)
    {
        $this->authorize('create', [Assignment::class, $course]);

        return view('instructor.assignments.create', compact('course'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssignmentRequest  $request, Course $course)
    {
        $this->authorize('create', [Assignment::class, $course]);

        $assignment = $course->assignments()->create($request->validated());

        return response()->json([
            'success' => true,
            'assignment' => $assignment,
            'message' => 'Assignment created successfully!'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course, Assignment $assignment)
    {
        $this->authorize('view', $assignment);

        $assignment->load(['submissions.student', 'course']);

        $course->load('instructor'); 

        $course->loadCount('enrollments');
        
        $submissions = $assignment->submissions()
            ->with('student')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('instructor.assignments.show', compact('course', 'assignment', 'submissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course, Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        return view('instructor.assignments.edit', compact('course', 'assignment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssignmentRequest $request, Course $course, Assignment $assignment)
    {
        $assignment->update($request->validated());
        
        $assignment->update($validated);

        return response()->json([
            'success' => true,
            'assignment' => $assignment,
            'message' => 'Assignment updated successfully!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Assignment $assignment)
    {
        $this->authorize('delete', $assignment);

        $assignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Assignment deleted successfully!'
        ]);
    }

    /**
     * Display trashed assignments
     */
    public function trash(Course $course)
    {
        $this->authorize('viewAny', [Assignment::class, $course]);

        $assignments = $course->assignments()
            ->onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('instructor.assignments.trash', compact('course', 'assignments'));
    }
    
    public function restore(Course $course, $assignmentId)
    {
        $assignment = Assignment::withTrashed()->findOrFail($assignmentId);
        
        $this->authorize('restore', $assignment);

        $assignment->restore();

        return response()->json([
            'success' => true,
            'message' => 'Assignment restored successfully!'
        ]);
    }
}
