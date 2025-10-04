<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StudentAssignmentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display assignments for a course
     */
    public function index(Course $course,Assignment $assignment)
    {
        $this->authorize('viewAnyAsStudent', $assignment);
        $assignments = $course->assignments()
            ->with(['submissions' => function($query) {
                $query->where('student_id', auth()->id());
            }])
            ->orderBy('due_date')
            ->get();

        return view('student.assignments.index', compact('course', 'assignments'));
    }

    /**
     * Show assignment details and submission form
     */
    public function show(Course $course, Assignment $assignment)
    {
        $this->authorize('viewAsStudent', $assignment);

        $submission = $assignment->studentSubmission(auth()->id());

        return view('student.assignments.show', compact('course', 'assignment', 'submission'));
    }

    /**
     * Submit assignment
     */
    public function submit(Request $request, Course $course, Assignment $assignment)
    {
        $this->authorize('viewAsStudent', $assignment);

        $request->validate([
            'submission_text' => 'nullable|string|max:5000',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        // Check if already submitted
        $existingSubmission = $assignment->studentSubmission(auth()->id());
        if ($existingSubmission) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted this assignment.'
            ], 422);
        }

        $data = [
            'student_id' => auth()->id(),
            'submission_text' => $request->submission_text,
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'assignment_' . $assignment->id . '_' . auth()->id() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('submissions', $fileName, 'public');
            $data['file_url'] = $filePath;
        }

        $submission = $assignment->submissions()->create($data);

        return response()->json([
            'success' => true,
            'submission' => $submission,
            'message' => 'Assignment submitted successfully!'
        ]);
    }

    /**
     * View submission details
     */
    public function submission(Course $course, Assignment $assignment, Submission $submission)
    {
        $this->authorize('viewAnyAsStudent', $assignment);
        
        return view('student.assignments.submission', compact('course', 'assignment', 'submission'));
    }

    /**
     * Download submission file
     */
    public function download(Course $course, Assignment $assignment, Submission $submission)
    {
        // Check if student owns the submission
        if ($submission->student_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$submission->file_url) {
            abort(404, 'No file found for this submission.');
        }

        return Storage::disk('public')->download($submission->file_url);
    }
}