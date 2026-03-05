<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use App\Http\Requests\Students\StoreStudentEnrollmentRequest;
use App\Http\Requests\Students\UpdateStudentEnrollmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class StudentEnrollmentController extends Controller
{
    public function __construct(
        protected EnrollmentService $enrollmentService
    ) {}

    public function index()
    {
        // Optimized: Eager load only necessary nested relationships to prevent N+1
        $students = Student::with(['user:id,name,email', 'enrollments.course:id,title'])
            ->paginate(15);
            
        return view('students.enrollments.index', compact('students'));
    }

    public function show(Enrollment $enrollment)
    {
        $enrollment->load([
            'student.user:id,name,email', 
            'course:id,title,code', 
            'batchAssignments.batch:id,batch_code,name'
        ]);
        
        return view('students.enrollments.show', compact('enrollment'));
    }

    public function create(Request $request)
    {
        // Optimized: Only fetch ID and Name to drastically reduce memory usage
        $students = Student::with('user:id,name')->select('id', 'user_id')->get();
        
        $courses = Course::where('status', 'published')->select('id', 'title')->get();

        $batches = Batch::withCount('students')
            ->whereIn('status', ['Upcoming', 'Running'])
            ->select('id', 'batch_code', 'course_id', 'max_capacity')
            ->get();

        $enrollment = new Enrollment();

        return view('students.enrollments.create', compact('students', 'courses', 'batches', 'enrollment'));
    }

    public function store(StoreStudentEnrollmentRequest $request)
    {
        try {
            // Orchestration moved to the Service layer
            $enrollment = $this->enrollmentService->processNewEnrollment($request->validated());

            if ($request->has('return_to_student') || $request->filled('student_id')) {
                return redirect()->route('students.show', $request->student_id)
                    ->with('success', 'Student enrolled successfully.');
            }

            return redirect()->route('enrollments.index')
                ->with('success', 'Student enrolled successfully.');

        } catch (Exception $e) {
            Log::error('Enrollment Store Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit(Enrollment $enrollment)
    {
        $enrollment->load(['student.user:id,name', 'course:id,title', 'currentBatchAssignment.batch:id,batch_code']);

        $batches = Batch::withCount(['students' => function ($q) {
                $q->where('batch_students.status', 'Active');
            }])
            ->where('course_id', $enrollment->course_id)
            ->whereIn('status', ['Upcoming', 'Running'])
            ->get();

        return view('students.enrollments.edit', compact('enrollment', 'batches'));
    }

    public function update(UpdateStudentEnrollmentRequest $request, Enrollment $enrollment)
    {
        try {
            $this->enrollmentService->processEnrollmentUpdate($enrollment, $request->validated());

            return redirect()->route('enrollments.show', $enrollment)
                ->with('success', 'Enrollment updated successfully.');
        } catch (Exception $e) {
            Log::error('Enrollment Update Error: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Enrollment $enrollment)
    {
        $this->authorize('delete', $enrollment);
        $enrollment->delete();
        
        return redirect()->route('enrollments.index')->with('success', 'Enrollment removed.');
    }
}