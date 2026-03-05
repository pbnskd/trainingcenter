@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4 gap-3">
        <div>
            <h4 class="fw-800 text-dark mb-1">Pending Certificate Approvals</h4>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i> {{ $certificates->total() }} Pending
                </span>
                <span class="text-muted small">• Review attendance and submit faculty decisions</span>
            </div>
        </div>
    </div>

    {{-- Premium Grid --}}
    <div class="premium-grid">
        
        {{-- Grid Header --}}
        <div class="grid-header">
            <div class="ps-2">STUDENT</div>
            <div>COURSE</div>
            <div>ATTENDANCE</div>
            <div class="text-end pe-2">ACTION</div>
        </div>

        {{-- Grid Body --}}
        <div class="grid-body">
            @forelse($certificates as $key => $cert)
            <div class="grid-row animate__animated animate__fadeInUp" style="animation-delay: {{ $key * 0.05 }}s">
                
                {{-- Column 1: Student --}}
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-box border shadow-sm d-none d-sm-flex">
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-soft-primary text-primary fw-bold" style="border-radius: 12px; font-size: 1.1rem;">
                            {{ strtoupper(substr($cert->batchStudent->student->user->name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-bold text-truncate text-dark mb-0 transition-hover">{{ $cert->batchStudent->student->user->name }}</div>
                    </div>
                </div>

                {{-- Column 2: Course --}}
                <div>
                    <span class="text-muted small fw-semibold">
                        <i class="fa-solid fa-book-open me-1 text-secondary"></i> 
                        {{ $cert->batchStudent->batch->course->title }}
                    </span>
                </div>

                {{-- Column 3: Attendance --}}
                <div>
                    @php $isGoodAttendance = $cert->attendance_percentage >= 75; @endphp
                    <span class="badge {{ $isGoodAttendance ? 'bg-soft-success text-success border-success' : 'bg-soft-danger text-danger border-danger' }} border px-2 py-1 rounded-pill">
                        {{ $cert->attendance_percentage }}% Present
                    </span>
                </div>

                {{-- Column 4: Actions (Faculty Approval Form) --}}
                <div class="d-flex justify-content-lg-end pe-2 w-100">
                    <form action="{{ route('faculty.certificates.process', $cert) }}" method="POST" class="d-flex flex-column flex-sm-row gap-2 w-100 align-items-sm-center justify-content-sm-end form-approval-wrapper">
                        @csrf
                        
                        {{-- Decision Select --}}
                        <select name="decision" class="form-select form-select-sm pro-input shadow-none mb-0 py-1" style="min-width: 130px; border-radius: 8px;" required>
                            <option value="">Select Action...</option>
                            <option value="approve">Approve</option>
                            <option value="reject">Reject</option>
                        </select>
                        
                        {{-- Remarks Input --}}
                        <input type="text" name="remarks" placeholder="Remarks" class="form-control form-control-sm pro-input shadow-none mb-0 py-1" style="max-width: 180px; border-radius: 8px;">
                        
                        {{-- Submit Button --}}
                        <button type="submit" class="btn btn-sm btn-premium rounded-pill shadow-sm px-3 py-1 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-check me-1" style="font-size: 0.75rem;"></i> Submit
                        </button>
                    </form>
                </div>

            </div>
            @empty
            <div class="p-5 text-center w-100">
                <div class="display-1 text-muted opacity-25 mb-3"><i class="fa-solid fa-clock-rotate-left"></i></div>
                <h5 class="fw-bold text-dark">No pending approvals</h5>
                <p class="small text-muted mb-0">There are currently no certificate requests waiting for your review.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    @if($certificates->hasPages())
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-4 gap-3">
        <p class="text-muted small mb-0">Displaying <strong>{{ $certificates->firstItem() ?? 0 }}</strong> to <strong>{{ $certificates->lastItem() ?? 0 }}</strong> of <strong>{{ $certificates->total() }}</strong> records</p>
        <div>
            {!! $certificates->links('pagination::bootstrap-5') !!}
        </div>
    </div>
    @endif

</div>

{{-- Specific Grid Layout Overrides --}}
<style>
    /* Adjust grid specifically for this 4-column layout */
    @media (min-width: 992px) {
        .grid-header, .grid-row { 
            grid-template-columns: 2fr 1.5fr 1fr 2.5fr; 
        }
    }
    
    /* Ensure the inline form inputs stack beautifully on mobile */
    @media (max-width: 575.98px) {
        .form-approval-wrapper select,
        .form-approval-wrapper input,
        .form-approval-wrapper button {
            max-width: 100% !important;
            width: 100%;
        }
    }
</style>
@endsection