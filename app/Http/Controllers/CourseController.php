<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseType;
use App\Services\CourseService;
use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(protected CourseService $courseService)
    {
        // Using PHP 8 constructor property promotion
    }

    public function index(): View
    {
        $this->authorize('viewAny', Course::class);

        $courses = Course::with('type:id,name') // Optimize: Only fetch needed columns
            ->latest()
            ->paginate(10);

        return view('courses.index', compact('courses'));
    }

    public function show(Course $course): View
    {
        $this->authorize('view', $course);
        $course->load('type:id,name');

        return view('courses.show', compact('course'));
    }

    public function create(): View
    {
        $this->authorize('create', Course::class);

        $course = new Course(); 
        $types = CourseType::select('id', 'name')->get(); // Optimize memory
        
        return view('courses.form', compact('course', 'types'));
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $this->authorize('create', Course::class);

        $this->courseService->saveCourse($request->validated());

        return redirect()
            ->route('courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function edit(Course $course): View
    {
        $this->authorize('update', $course);

        // Uses a clean relation/scope defined in the model
        $course->loadSyllabusForEditing();

        $types = CourseType::select('id', 'name')->get();
        
        return view('courses.form', compact('course', 'types'));
    }

    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $this->courseService->saveCourse($request->validated(), $course);

        return redirect()
            ->route('courses.index')
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $this->authorize('delete', $course);

        $course->delete();
        
        return back()->with('success', 'Course moved to trash.');
    }
}