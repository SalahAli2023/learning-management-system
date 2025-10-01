<?php

namespace App\Http\Controllers\Instructor;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LessonController extends Controller
{
    use AuthorizesRequests;
   /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        $this->authorize('viewAny', [Lesson::class, $course]);
        
        $lessons = $course->lessons()
            ->whereNull('deleted_at')
            ->orderBy('order')
            ->orderBy('created_at')
            ->get();

        return view('instructor.lessons.index', compact('course', 'lessons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Course $course)
    {
        $this->authorize('create', [Lesson::class, $course]);

        return view('instructor.lessons.create', compact('course'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLessonRequest $request, Course $course)
    {
        $data = $request->validated();
        $data['course_id'] = $course->id;
        
        // Calculate order (add to the end)
        $lastLesson = $course->lessons()->orderBy('order', 'desc')->first();
        $data['order'] = $lastLesson ? $lastLesson->order + 1 : 1;

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('lessons', $fileName, 'public');
            $data['file_url'] = $filePath;
        }

        Lesson::create($data);

        return redirect()
            ->route('instructor.courses.lessons.index', $course)
            ->with('success', 'Lesson created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course, Lesson $lesson)
    {
        $this->authorize('view', $lesson);

        // Load course with lessons for navigation
        $course->load(['lessons' => function($query) {
            $query->whereNull('deleted_at')->orderBy('order');
        }]);

        return view('instructor.lessons.show', compact('course', 'lesson'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course, Lesson $lesson)
    {
        $this->authorize('update', $lesson);

        return view('instructor.lessons.edit', compact('course', 'lesson'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLessonRequest $request, Course $course, Lesson $lesson)
    {
        $data = $request->validated();

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($lesson->file_url && Storage::disk('public')->exists($lesson->file_url)) {
                Storage::disk('public')->delete($lesson->file_url);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('lessons', $fileName, 'public');
            $data['file_url'] = $filePath;
        }

        $lesson->update($data);

        return redirect()
            ->route('instructor.courses.lessons.index', $course)
            ->with('success', 'Lesson updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Lesson $lesson)
    {
        $this->authorize('delete', $lesson);

        try {
            $lesson->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lesson moved to trash successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete lesson.'
            ], 500);
        }
    }

    /**
     * Update lesson order
     */
    public function updateOrder(Request $request, Course $course)
    {
        $this->authorize('update', [Lesson::class, $course]);

        $request->validate([
            'lessons' => 'required|array',
            'lessons.*.id' => 'required|exists:lessons,id',
            'lessons.*.order' => 'required|integer'
        ]);

        foreach ($request->lessons as $lessonData) {
            Lesson::where('id', $lessonData['id'])
                ->where('course_id', $course->id)
                ->update(['order' => $lessonData['order']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Restore soft deleted lesson.
     */
    public function restore(Course $course, $lessonId)
    {
        $lesson = Lesson::withTrashed()->findOrFail($lessonId);
        
        $this->authorize('restore', $lesson);

        $lesson->restore();

        return redirect()
            ->route('instructor.courses.lessons.index', $course)
            ->with('success', 'Lesson restored successfully!');
    }


    /**
     * Permanently delete lesson.
     */
    public function forceDelete(Course $course, $lessonId)
    {
        $lesson = Lesson::withTrashed()->findOrFail($lessonId);
        
        $this->authorize('forceDelete', $lesson);

        // Delete associated file
        if ($lesson->file_url && Storage::disk('public')->exists($lesson->file_url)) {
            Storage::disk('public')->delete($lesson->file_url);
        }

        $lesson->forceDelete();

        return redirect()
            ->route('instructor.courses.lessons.index', $course)
            ->with('success', 'Lesson permanently deleted!');
    }

    public function trash(Course $course)
    {
        $this->authorize('viewAny', [Lesson::class, $course]);

        $trashedLessons = $course->lessons()
            ->onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('instructor.lessons.trash', compact('course', 'trashedLessons'));
    }
}
