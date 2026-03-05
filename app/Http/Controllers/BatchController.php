<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Student;
use App\Services\BatchService;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Batch\{
    StoreBatchRequest, UpdateBatchRequest, EnrollStudentRequest, 
    AssignFacultyRequest, StoreBatchScheduleRequest, StoreBatchTransferRequest
};

class BatchController extends Controller
{
    public function __construct(protected BatchService $batchService)
    {
        $this->authorizeResource(Batch::class, 'batch');
    }

    public function index(): View
    {
        $batches = $this->batchService->getPaginatedBatches();
        return view('batches.index', compact('batches'));
    }

    public function create(): View
    {
        $courses = $this->batchService->getAvailableCoursesForDropdown();
        return view('batches.create', compact('courses'));
    }

    public function store(StoreBatchRequest $request): RedirectResponse
    {
        $this->batchService->createBatch($request->validated());
        return redirect()->route('batches.index')->with('success', 'Batch created successfully.');
    }

    public function show(Batch $batch): View
    {
        $data = $this->batchService->getBatchDetailsForShow($batch);
        return view('batches.show', $data);
    }

    public function edit(Batch $batch): View
    {
        $courses = $this->batchService->getAvailableCoursesForDropdown();
        return view('batches.edit', compact('batch', 'courses'));
    }

    public function update(UpdateBatchRequest $request, Batch $batch): RedirectResponse
    {
        $this->batchService->updateBatch($batch, $request->validated());
        return redirect()->route('batches.index')->with('success', 'Batch updated successfully.');
    }

    public function destroy(Batch $batch): RedirectResponse
    {
        try {
            $this->batchService->deleteBatch($batch);
            return redirect()->route('batches.index')->with('success', 'Batch deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function enrollForm(Batch $batch): View
    {
        $students = $this->batchService->getEligibleStudentsForEnrollment($batch);
        return view('batches.enroll', compact('batch', 'students'));
    }

    public function enroll(EnrollStudentRequest $request, Batch $batch): RedirectResponse
    {
        try {
            $this->batchService->processStudentEnrollment($batch, $request->student_id, $request->validated());
            return redirect()->route('batches.show', $batch)->with('success', 'Student enrolled successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function storeFaculty(AssignFacultyRequest $request, Batch $batch): RedirectResponse
    {
        $this->batchService->assignFaculty($batch, array_merge($request->validated(), ['batch_id' => $batch->id]));
        return redirect()->route('batches.show', $batch)->with('success', 'Faculty assigned successfully.');
    }

    public function addSchedule(StoreBatchScheduleRequest $request, Batch $batch): RedirectResponse
    {
        $this->batchService->addSchedule($batch, $request->validated());
        return back()->with('success', 'Schedule added successfully.');
    }

    public function transfer(StoreBatchTransferRequest $request): RedirectResponse
    {
        try {
            $this->batchService->processStudentTransfer($request->validated());
            return redirect()->route('batches.show', $request->to_batch_id)->with('success', 'Student transferred successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function unenroll(Batch $batch, Student $student): RedirectResponse
    {
        $this->batchService->unenrollStudent($batch, $student);
        return redirect()->route('batches.show', $batch)->with('success', 'Student unenrolled successfully.');
    }
}