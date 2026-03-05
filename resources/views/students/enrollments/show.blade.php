@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0 text-dark"><i class="fa fa-graduation-cap me-2 text-primary"></i> Enrollment Details</h3>
                <div>
                    <a href="{{ route('enrollments.index') }}" class="btn btn-outline-secondary shadow-sm me-2">
                        <i class="fa fa-arrow-left me-1"></i> Back to List
                    </a>
                    <a href="{{ route('enrollments.edit', $enrollment) }}" class="btn btn-warning shadow-sm fw-bold">
                        <i class="fa fa-edit me-1"></i> Edit / Transfer
                    </a>
                </div>
            </div>

            <div class="card shadow border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-primary bg-gradient text-white py-3 border-0">
                    <h5 class="mb-0 fw-bold">{{ $enrollment->course->title }}</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small text-uppercase fw-bold">Student Name</p>
                            <p class="fs-5 fw-semibold mb-0">{{ $enrollment->student->user->name }}</p>
                            <p class="text-muted small"><i class="fa fa-envelope me-1"></i> {{ $enrollment->student->user->email }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-muted small text-uppercase fw-bold">Enrollment Status</p>
                            @php
                                $badgeColor = match($enrollment->status) {
                                    'enrolled' => 'bg-info',
                                    'in_progress' => 'bg-success',
                                    'paused' => 'bg-warning text-dark',
                                    'completed' => 'bg-primary',
                                    'dropped' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeColor }} rounded-pill px-3 py-2 text-capitalize shadow-sm">
                                {{ str_replace('_', ' ', $enrollment->status) }}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-muted small text-uppercase fw-bold">Enrolled On</p>
                            <p class="fw-semibold mb-0"><i class="fa fa-calendar-alt text-muted me-1"></i> {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold mb-3 text-secondary"><i class="fa fa-history me-2"></i> Batch Assignment History</h5>
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-uppercase text-muted small fw-bold px-4 py-3">Batch Code</th>
                                <th class="text-uppercase text-muted small fw-bold py-3">Shift</th>
                                <th class="text-uppercase text-muted small fw-bold py-3">Enrolled At</th>
                                <th class="text-uppercase text-muted small fw-bold py-3">Status</th>
                                <th class="text-uppercase text-muted small fw-bold py-3">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enrollment->batchAssignments as $assignment)
                                <tr>
                                    <td class="px-4 py-3 fw-bold text-dark">
                                        <a href="{{ route('batches.show', $assignment->batch_id) }}" class="text-decoration-none">
                                            {{ $assignment->batch->batch_code }}
                                        </a>
                                    </td>
                                    <td class="py-3">{{ $assignment->batch->shift }}</td>
                                    <td class="py-3">{{ \Carbon\Carbon::parse($assignment->enrolled_at)->format('M d, Y') }}</td>
                                    <td class="py-3">
                                        @php
                                            $pivotBadge = match($assignment->status) {
                                                'Active' => 'bg-success',
                                                'Completed' => 'bg-primary',
                                                'Dropped' => 'bg-danger',
                                                'Transferred_Out' => 'bg-secondary',
                                                default => 'bg-light text-dark'
                                            };
                                        @endphp
                                        <span class="badge {{ $pivotBadge }} rounded-pill shadow-sm">{{ $assignment->status }}</span>
                                    </td>
                                    <td class="py-3 text-muted small">{{ $assignment->remark ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fa fa-info-circle me-1"></i> No batch assignments recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection