@extends('layouts.app')

@section('content')
<div class="main-canvas p-4">

    <div class="max-w-800 mx-auto">
        {{-- Header / Back Link --}}
        <div class="mb-4 d-flex align-items-center justify-content-between">
            <h4 class="fw-800 text-dark mb-0">Daily Attendance</h4>
            <a href="{{ route('batches.show', $batch) }}" class="btn btn-outline-secondary btn-sm fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Batch
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-4 border overflow-hidden">
            
            {{-- Batch Context Banner --}}
            <div class="bg-soft-primary p-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-clipboard-user fs-5"></i>
                    </div>
                    <div>
                        <div class="text-uppercase small text-primary fw-bold letter-spacing-1">Marking Attendance For</div>
                        <h5 class="fw-bold text-dark mb-0">{{ $batch->batch_code }}</h5>
                        <div class="text-muted small mt-1">
                            <i class="fa-solid fa-book-open me-1"></i> {{ $batch->course->name }}
                        </div>
                    </div>
                </div>

                {{-- Date Display (Visual Only, Input is below) --}}
                <div class="bg-white px-3 py-2 rounded border shadow-sm text-center">
                    <div class="text-uppercase small text-muted fw-bold" style="font-size: 0.65rem;">Today</div>
                    <div class="fw-bold text-dark">{{ now()->format('M d, Y') }}</div>
                </div>
            </div>

            <form action="{{ route('attendance.store') }}" method="POST">
                @csrf
                <input type="hidden" name="batch_id" value="{{ $batch->id }}">

                {{-- Date Selector Toolbar --}}
                <div class="p-3 bg-light border-bottom d-flex align-items-center gap-3">
                    <label class="small text-muted fw-bold text-nowrap">Select Date:</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control form-control-sm border-0 shadow-sm" style="max-width: 160px;" required>
                </div>

                {{-- Student List Table --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3 border-0" style="width: 35%;">Student Name</th>
                                <th class="py-3 border-0 text-center" style="width: 30%;">Status</th>
                                <th class="pe-4 py-3 border-0" style="width: 35%;">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batch->students as $index => $student)
                            <tr>
                                {{-- Student Name --}}
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="thumbnail-box rounded-circle bg-soft-secondary text-secondary d-flex align-items-center justify-content-center fw-bold small" style="width: 32px; height: 32px;">
                                            {{ substr($student->user->name ?? 'S', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $student->user->name ?? 'Unknown Student' }}</div>
                                            <div class="text-muted small" style="font-size: 0.75rem;">ID: {{ $student->id }}</div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="attendances[{{ $index }}][student_id]" value="{{ $student->id }}">
                                </td>

                                {{-- Status Toggle --}}
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <input type="radio" class="btn-check" name="attendances[{{ $index }}][is_present]" id="present-{{$index}}" value="1" checked autocomplete="off">
                                        <label class="btn btn-outline-success px-3 fw-bold" for="present-{{$index}}">
                                            <i class="fa-solid fa-check me-1"></i> Present
                                        </label>
                                      
                                        <input type="radio" class="btn-check" name="attendances[{{ $index }}][is_present]" id="absent-{{$index}}" value="0" autocomplete="off">
                                        <label class="btn btn-outline-danger px-3 fw-bold" for="absent-{{$index}}">
                                            <i class="fa-solid fa-xmark me-1"></i> Absent
                                        </label>
                                    </div>
                                </td>

                                {{-- Remarks --}}
                                <td class="pe-4">
                                    <input type="text" name="attendances[{{ $index }}][remarks]" 
                                           class="form-control form-control-sm border-0 bg-light shadow-none focus-bg-white" 
                                           placeholder="Add note...">
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="py-5 text-center text-muted">
                                    <div class="mb-2"><i class="fa-solid fa-users-slash fs-3 opacity-25"></i></div>
                                    <p class="small mb-0">No active students found in this batch.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Action Footer --}}
                <div class="p-4 bg-light border-top d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Save Attendance Sheet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .max-w-800 { max-width: 800px; }
    .fw-800 { font-weight: 800; }
    .letter-spacing-1 { letter-spacing: 1px; }
    
    .bg-soft-primary { background-color: #f0f7ff; }
    .bg-soft-secondary { background-color: #f8fafc; }

    /* Focus effect for remarks input */
    .focus-bg-white:focus { background-color: #fff !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important; }
    
    /* Button Group Styling overrides for sharper look */
    .btn-check:checked + .btn-outline-success { background-color: #ecfdf5; color: #16a34a; border-color: #16a34a; }
    .btn-check:checked + .btn-outline-danger { background-color: #fef2f2; color: #dc2626; border-color: #dc2626; }
</style>
@endsection