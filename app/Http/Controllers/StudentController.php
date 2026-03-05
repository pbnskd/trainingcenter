<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Http\Requests\Students\StoreStudentRequest;
use App\Http\Requests\Students\UpdateStudentRequest;
use App\Services\StudentService; // Import Service
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Students\QuickStoreStudentRequest;
    use App\Models\Course;
    use App\Models\Batch;



class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Student::class);

        $query = Student::with('user');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('registration_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
        }

        $students = $query->latest()->paginate(10);
        return view('students.index', compact('students'));
    }

    public function create()
    {
        $this->authorize('create', Student::class);
        
        // Generate the number for display purposes when form loads
        $nextRegistrationNumber = $this->studentService->generateRegistrationNumber();
        
        return view('students.form', compact('nextRegistrationNumber'));
    }

 public function store(StoreStudentRequest $request)
    {
        try {
            $student = $this->studentService->createStudent(
                $request->validated(), 
                $request->file('avatar')
            );

            return redirect()->route('students.index')
                ->with('success', "Student registered successfully. ID: {$student->registration_number}");

        } catch (\Throwable $e) { // Catch Throwable to handle Errors and Exceptions
            // Log the error for developer debugging
            Log::error('Student Creation Failed: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Unable to create student. Please check your data and try again. Error: ' . $e->getMessage());
        }
    }

   public function show($id)
    {
        // 1. Eager Load everything needed for the profile to prevent N+1 queries
        $student = Student::with([
            'user',
            'skills',
            'education',
            'guardians',
            'enrollments.course.curriculumItems',
           'enrollments.batchAssignments.batch.faculty.faculty',
            'enrollments.batchAssignments.selectedItems'
        ])->findOrFail($id);

        $this->authorize('view', $student);

        // 2. Prepare Academic Data
        $academicData = $student->enrollments->map(function ($enrollment) {
            
            // Get the active batch assignment
            $activeAssignment = $enrollment->batchAssignments->first();
            $batch = $activeAssignment ? $activeAssignment->batch : null;

            // --- Calculation Logic ---
            $totalClasses = 40; // Mock data
            $attended = 32;     // Mock data
            $attendancePercent = ($totalClasses > 0) ? round(($attended / $totalClasses) * 100) : 0;

            // Curriculum Progress Logic
            $totalItems = $enrollment->course->curriculumItems->count();
            $completedItems = $activeAssignment ? $activeAssignment->selectedItems->count() : 0;
            $progressPercent = ($totalItems > 0) ? round(($completedItems / $totalItems) * 100) : 0;

            return (object) [
                'course' => $enrollment->course,
                'enrollment' => $enrollment,
                'batch_student' => $activeAssignment,
                'batch' => $batch,
                'stats' => [
                    'attendance' => $attendancePercent,
                    'attended_count' => $attended,
                    'total_classes' => $totalClasses,
                    'progress' => $progressPercent,
                    'completed_items' => $completedItems,
                    'total_items' => $totalItems
                ]
            ];
        });

        // 3. Pass BOTH variables to the view
        return view('students.show', compact('student', 'academicData'));
    }

    public function edit(Student $student)
    {
        $this->authorize('update', $student);
        $student->load(['user', 'education', 'skills', 'guardians']);
        return view('students.form', compact('student'));
    }

  public function update(UpdateStudentRequest $request, Student $student)
    {
        try {
            $this->studentService->updateStudent(
                $student,
                $request->validated(),
                $request->file('avatar')
            );

            return redirect()->route('students.index')
                ->with('success', 'Student profile updated successfully.');

        } catch (\Throwable $e) {
            Log::error('Student Update Failed: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Update failed. Error: ' . $e->getMessage());
        }
    }
   public function destroy(Student $student)
    {
        $this->authorize('delete', $student);

        try {
            DB::transaction(function() use ($student) {
                $user = $student->user;
                $student->delete(); 
                if ($user) $user->delete();
            });

            return redirect()->route('students.index')
                ->with('success', 'Student and associated user account deleted.');

        } catch (\Throwable $e) {
            Log::error('Student Deletion Failed: ' . $e->getMessage());
            return back()->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

    public function quickCreate()
    {
        $this->authorize('quickCreate', Student::class);
        
        $nextRegistrationNumber = $this->studentService->generateRegistrationNumber();
        $courses = Course::where('status', 'published')->get();
        
        return view('students.quick_form', compact('nextRegistrationNumber', 'courses'));
    }

    public function quickStore(QuickStoreStudentRequest $request)
    {
        $this->authorize('quickCreate', Student::class);

        try {
            $student = $this->studentService->quickCreateStudent(
                $request->validated(), 
                $request->file('avatar')
            );

            return redirect()->route('students.index')
                ->with('success', "Student {$student->user->name} successfully registered and enrolled. ID: {$student->registration_number}");

        } catch (\Throwable $e) {
            Log::error('Quick Student Creation Failed: ' . $e->getMessage());
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // AJAX Endpoint for the dynamic dropdown
    public function getCourseBatches($courseId)
    {
        $batches = Batch::where('course_id', $courseId)
            ->whereIn('status', ['Upcoming', 'Running']) // Only allow active batches
            ->select('id', 'batch_code', 'shift')
            ->get();
            
        return response()->json($batches);
    }

    public function notifyStatus(Student $student)
    {
        // Call the service method we just created
        $wasSent = $this->studentService->notifyStatus($student);

        if (!$wasSent) {
            return back()->with('error', 'Cannot send email: Student has no associated user account or email.');
        }

        return back()->with('success', "Status notification email sent successfully to {$student->user->name}.");
    }
}