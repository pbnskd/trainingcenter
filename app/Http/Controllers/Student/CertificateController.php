<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CertificateController extends Controller
{
    use AuthorizesRequests;

    public function download(Certificate $certificate): BinaryFileResponse
    {
        $this->authorize('download', $certificate);

        if ($certificate->status !== Certificate::STATUS_GENERATED || !Storage::disk('public')->exists($certificate->file_path)) {
            abort(404, 'Certificate not ready or not found.');
        }

        return response()->download(
            Storage::disk('public')->path($certificate->file_path),
            "Certificate_{$certificate->certificate_number}.pdf"
        );
    }
}