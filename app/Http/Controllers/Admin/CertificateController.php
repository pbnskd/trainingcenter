<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Services\CertificateService;
use App\Http\Requests\ProcessCertificateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CertificateController extends Controller
{
    public function __construct(protected CertificateService $service) {}

    public function index(): View
    {
        $status = request('status', Certificate::STATUS_FACULTY_APPROVED);
        
        $certificates = Certificate::with(['batchStudent.student.user', 'batchStudent.batch.course', 'faculty'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        return view('certificates.admin.index', compact('certificates', 'status'));
    }

    public function process(ProcessCertificateRequest $request, Certificate $certificate): RedirectResponse
    {
        $this->authorize('approveAdmin', $certificate);

        try {
            $this->service->processAdminDecision($certificate, Auth::id(), $request->validated());
            return back()->with('success', 'Certificate processed successfully.');
        } catch (\Exception $e) {
            // Log the exception in a real app: Log::error($e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }
}