<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Http\Requests\GradeSubmissionRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course, Assignment $assignment)
    {
        $this->authorize('viewAny', [Submission::class, $assignment]);

        $submissions = $assignment->submissions()
            ->with('student')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => $assignment->submissions()->count(),
            'graded' => $assignment->submissions()->whereNotNull('grade')->count(),
            'pending' => $assignment->submissions()->whereNull('grade')->count(),
        ];

        return view('instructor.submissions.index', compact('course', 'assignment', 'submissions', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course, Assignment $assignment, Submission $submission)
    {
        $this->authorize('view', $submission);

        $submission->load(['student', 'assignment.course']);

        return view('instructor.submissions.show', compact('course', 'assignment', 'submission'));
    }

    public function grade(GradeSubmissionRequest  $request, Course $course, Assignment $assignment, Submission $submission)
    {
        $this->authorize('update', $submission);

        $submission->update($request->validated());

        return response()->json([
            'success' => true,
            'submission' => $submission->fresh(),
            'message' => 'Submission graded successfully!'
        ]);
    }

    public function download(Course $course, Assignment $assignment, Submission $submission)
    {
        $this->authorize('view', $submission);

        if (!$submission->file_url) {
            abort(404, 'No file found for this submission.');
        }

        return Storage::disk('public')->download($submission->file_url);
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Assignment $assignment, Submission $submission)
    {
        $this->authorize('delete', $submission);

        $submission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Submission deleted successfully!'
        ]);
    }

    /**
     * Display trashed submissions
     */
    public function trash(Course $course, Assignment $assignment)
    {
        $this->authorize('viewAny', [Submission::class, $assignment]);

        $submissions = $assignment->submissions()
            ->onlyTrashed()
            ->with('student')
            ->orderBy('deleted_at', 'desc')
            ->paginate(15);

        return view('instructor.submissions.trash', compact('course', 'assignment', 'submissions'));
    }

    public function restore(Course $course, Assignment $assignment, $submissionId)
    {
        $submission = Submission::withTrashed()->findOrFail($submissionId);
        
        $this->authorize('restore', $submission);

        $submission->restore();

        return response()->json([
            'success' => true,
            'message' => 'Submission restored successfully!'
        ]);
    }
}
