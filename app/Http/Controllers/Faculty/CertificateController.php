<?php

namespace App\Http\Controllers\Faculty;

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
        $certificates = Certificate::with(['batchStudent.student.user', 'batchStudent.batch.course'])
            ->pendingForFaculty(Auth::id())
            ->latest()
            ->paginate(15);

        return view('certificates.faculty.index', compact('certificates'));
    }

    public function process(ProcessCertificateRequest $request, Certificate $certificate): RedirectResponse
    {
        $this->authorize('approveFaculty', $certificate);

        try {
            $this->service->processFacultyDecision($certificate, Auth::id(), $request->validated());
            return back()->with('success', 'Request evaluated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}