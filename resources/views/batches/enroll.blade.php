@extends('layouts.app')

@section('content')
<div class="main-canvas p-4">
    
    <div class="max-w-600 mx-auto">
        {{-- Header / Back Link --}}
        <div class="mb-4 d-flex align-items-center justify-content-between">
            <h4 class="fw-800 text-dark mb-0">Enrollment</h4>
            <a href="{{ route('batches.show', $batch) }}" class="btn btn-outline-secondary btn-sm fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Batch
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-4 border overflow-hidden">
            
            {{-- Batch Context Banner --}}
            <div class="bg-soft-primary p-4 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-people-roof fs-5"></i>
                    </div>
                    <div>
                        <div class="text-uppercase small text-primary fw-bold letter-spacing-1">Adding Student To</div>
                        <h5 class="fw-bold text-dark mb-0">{{ $batch->batch_code }}</h5>
                        <div class="text-muted small mt-1">
                            <i class="fa-solid fa-book-open me-1"></i> {{ $batch->course->name }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Enrollment Form --}}
            <div class="p-4 p-md-5">
                <form action="{{ route('batches.enroll.store', $batch) }}" method="POST">
                    @csrf
                    
                    {{-- Student Select --}}
                    <div class="mb-4">
                        <label class="form-label small text-muted fw-bold">Select Student</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-user-graduate text-muted"></i></span>
                            <select name="student_id" class="form-select border-start-0 ps-0 shadow-none focus-ring">
                                <option value="">-- Search or Select Student --</option>
                                @foreach($students as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('student_id') 
                            <div class="text-danger small mt-1"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div> 
                        @enderror
                    </div>

                    {{-- Enrollment Date --}}
                    <div class="mb-4">
                        <label class="form-label small text-muted fw-bold">Join Date</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-regular fa-calendar-days text-muted"></i></span>
                            <input type="date" name="enrolled_at" value="{{ date('Y-m-d') }}" class="form-control border-start-0 ps-0 shadow-none">
                        </div>
                    </div>

                    {{-- Options Checkbox --}}
                    <div class="mb-5">
                        <div class="form-check p-3 border rounded bg-light hover-elevate cursor-pointer">
                            <input class="form-check-input me-2 mt-1" type="checkbox" name="is_full_course" value="1" id="fullCourseCheck" checked>
                            <label class="form-check-label w-100 cursor-pointer" for="fullCourseCheck">
                                <span class="d-block fw-bold text-dark">Full Course Enrollment</span>
                                <span class="d-block text-muted small mt-1">Student will be enrolled in all curriculum items for this batch.</span>
                            </label>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                        <i class="fa-solid fa-user-plus me-2"></i> Confirm Enrollment
                    </button>

                </form>
            </div>
        </div>
    </div>

</div>

<style>
    .max-w-600 { max-width: 600px; }
    .fw-800 { font-weight: 800; }
    .letter-spacing-1 { letter-spacing: 1px; }
    
    .bg-soft-primary { background-color: #f0f7ff; }
    
    /* Input Styling */
    .input-group-text { border-color: #dee2e6; }
    .form-control, .form-select { border-color: #dee2e6; padding-top: 0.6rem; padding-bottom: 0.6rem; }
    .form-control:focus, .form-select:focus { border-color: #3b82f6; box-shadow: none; }
    
    /* Checkbox Card Hover */
    .hover-elevate { transition: all 0.2s; }
    .hover-elevate:hover { background-color: #fff; border-color: #3b82f6; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .cursor-pointer { cursor: pointer; }
</style>
@endsection