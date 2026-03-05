<?php

namespace App\Services;

use App\Models\BatchStudent;
use App\Models\Certificate;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Notifications\CertificateReadyNotification;
use Exception;

class CertificateService
{
    public function calculateAttendance(BatchStudent $batchStudent): float
    {
        $totalClasses = Attendance::where('batch_id', $batchStudent->batch_id)
            ->distinct('date')
            ->count('date');
            
        if ($totalClasses === 0) return 0.0;

        $attendedClasses = Attendance::where('batch_id', $batchStudent->batch_id)
            ->where('student_id', $batchStudent->student_id)
            ->where('is_present', true)
            ->distinct('date')
            ->count('date');

        return round(($attendedClasses / $totalClasses) * 100, 2);
    }

    public function generatePendingRequest(BatchStudent $batchStudent): ?Certificate
    {
        $attendance = $this->calculateAttendance($batchStudent);

        if ($attendance < 80) {
            return null; // Not eligible
        }

        return Certificate::firstOrCreate(
            ['batch_student_id' => $batchStudent->id],
            [
                'attendance_percentage' => $attendance, 
                'status' => Certificate::STATUS_PENDING
            ]
        );
    }

    public function processFacultyDecision(Certificate $certificate, int $facultyId, array $data): Certificate
    {
        if ($certificate->status !== Certificate::STATUS_PENDING) {
            throw new Exception("This request is not pending faculty approval.");
        }

        $isApproved = $data['decision'] === 'approve';

        return DB::transaction(function () use ($certificate, $facultyId, $data, $isApproved) {
            $certificate->update([
                'faculty_id' => $facultyId,
                'faculty_approved_at' => $isApproved ? now() : null,
                'faculty_remarks' => $data['remarks'] ?? null,
                'status' => $isApproved ? Certificate::STATUS_FACULTY_APPROVED : Certificate::STATUS_REJECTED
            ]);

            return $certificate;
        });
    }

    public function processAdminDecision(Certificate $certificate, int $adminId, array $data): Certificate
    {
        if ($certificate->status !== Certificate::STATUS_FACULTY_APPROVED) {
            throw new Exception("This request must be approved by faculty first.");
        }

        $isApproved = $data['decision'] === 'approve';

        return DB::transaction(function () use ($certificate, $adminId, $data, $isApproved) {
            $certificate->update([
                'admin_id' => $adminId,
                'admin_approved_at' => $isApproved ? now() : null,
                'admin_remarks' => $data['remarks'] ?? null,
                'status' => $isApproved ? Certificate::STATUS_GENERATED : Certificate::STATUS_REJECTED
            ]);

            if ($isApproved) {
                $this->generatePdf($certificate);
                // Ensure relationships are loaded before notifying
                $certificate->loadMissing('batchStudent.student.user');
                $certificate->batchStudent->student->user->notify(new CertificateReadyNotification($certificate));
            }

            return $certificate;
        });
    }

    protected function generatePdf(Certificate $certificate): void
    {
        $certificate->loadMissing(['batchStudent.student.user', 'batchStudent.batch.course', 'faculty', 'admin']);
        
        $certNumber = $certificate->certificate_number ?? $this->generateUniqueNumber($certificate);

        $data = [
            'student_name' => $certificate->batchStudent->student->user->name,
            'course_name' => $certificate->batchStudent->batch->course->title,
            'batch_name' => $certificate->batchStudent->batch->batch_code,
            'completion_date' => $certificate->admin_approved_at->format('F d, Y'),
            'certificate_number' => $certNumber,
            'faculty_name' => $certificate->faculty->name ?? 'Faculty',
            'admin_name' => $certificate->admin->name ?? 'Admin',
        ];

        $pdf = Pdf::loadView('certificates.template', $data)->setPaper('a4', 'landscape');
        $fileName = "certificates/{$certNumber}.pdf";
        
        Storage::disk('public')->put($fileName, $pdf->output());

        $certificate->update([
            'certificate_number' => $certNumber,
            'file_path' => $fileName
        ]);
    }

    private function generateUniqueNumber(Certificate $certificate): string
    {
        $year = date('Y');
        $prefix = strtoupper(substr($certificate->batchStudent->batch->course->code, 0, 3));
        return "CERT-{$year}-{$prefix}-" . str_pad($certificate->id, 5, '0', STR_PAD_LEFT);
    }
}