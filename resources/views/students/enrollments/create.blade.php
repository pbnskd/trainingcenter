@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row g-4 justify-content-center">
        
        {{-- LEFT COLUMN: Context & Instructions --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-primary text-white h-100 rounded-4 overflow-hidden position-relative">
                <div class="card-body p-4 position-relative z-1">
                    <div class="mb-4">
                        <div class="icon-box bg-white text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px; font-size: 1.5rem;">
                            <i class="fa fa-user-plus"></i>
                        </div>
                        <h3 class="fw-bold mb-1">New Enrollment</h3>
                        <p class="text-white-50">Assign a student to a new academic path.</p>
                    </div>

                    <div class="d-flex flex-column gap-4 mt-5">
                        <div class="d-flex">
                            <div class="me-3">
                                <span class="badge bg-white text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">1</span>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Select Student</h6>
                                <p class="small text-white-50 mb-0">Search the database for an existing student profile.</p>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="me-3">
                                <span class="badge bg-white text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">2</span>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Choose Course</h6>
                                <p class="small text-white-50 mb-0">Pick the program they wish to join.</p>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="me-3">
                                <span class="badge bg-primary-subtle text-primary border border-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">3</span>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Assign Batch (Optional)</h6>
                                <p class="small text-white-50 mb-0">Place them directly into an active class schedule.</p>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Decorative circles --}}
                <div class="position-absolute top-0 end-0 bg-white opacity-10 rounded-circle" style="width: 200px; height: 200px; transform: translate(30%, -30%);"></div>
                <div class="position-absolute bottom-0 start-0 bg-white opacity-10 rounded-circle" style="width: 150px; height: 150px; transform: translate(-30%, 30%);"></div>
            </div>
        </div>

        {{-- RIGHT COLUMN: The Form --}}
        <div class="col-lg-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    
                    {{-- ERROR BLOCK --}}
                    @if($errors->any())
                        <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-exclamation-triangle fa-lg me-3"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Enrollment Failed</h6>
                                    <ul class="mb-0 small ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('enrollments.store') }}" method="POST" id="enrollmentForm">
                        @csrf
                        
                        {{-- Hidden inputs for redirection logic --}}
                        @if(request('student_id'))
                            <input type="hidden" name="return_to_student" value="1">
                        @endif

                        {{-- SECTION 1 --}}
                        <div class="mb-5">
                            <h5 class="text-primary fw-bold mb-4 border-bottom pb-2">Student Information</h5>
                            
                            <div class="form-floating mb-3">
                                <select name="student_id" id="student_id" class="form-select select2-student" required>
                                    <option value=""></option> {{-- Placeholder for Select2 --}}
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('student_id', request('student_id')) == $student->id ? 'selected' : '' }}>
                                            {{ $student->user->name }} ({{ $student->registration_number }})
                                        </option>
                                    @endforeach
                                </select>
                                {{-- Fake label for styling consistency if Select2 hides original --}}
                                <label class="d-none d-md-block" style="opacity: 0.65; transform: scale(.85) translateY(-.5rem) translateX(.15rem);">Search Student Profile</label>
                            </div>
                            <div class="form-text text-muted"><i class="fa fa-search me-1"></i> Start typing name, email or ID to search.</div>
                        </div>

                        {{-- SECTION 2 --}}
                        <div class="mb-4">
                            <h5 class="text-primary fw-bold mb-4 border-bottom pb-2">Academic Details</h5>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        @if(isset($enrollment) && $enrollment->exists)
                                            <input type="text" class="form-control bg-light" value="{{ $enrollment->course->title }}" readonly>
                                            <label>Selected Course</label>
                                            <input type="hidden" name="course_id" id="course_id" value="{{ $enrollment->course_id }}">
                                        @else
                                            <select name="course_id" id="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                                                <option value="" selected disabled>Select a course</option>
                                                @foreach($courses as $course)
                                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                        {{ $course->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="course_id">Select Course <span class="text-danger">*</span></label>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating position-relative">
                                        <select name="batch_id" id="batch_id" class="form-select @error('batch_id') is-invalid @enderror" disabled>
                                            <option value="">-- No Batch (Course Only) --</option>
                                            {{-- Options populated via JS --}}
                                            @foreach($batches as $batch)
                                                <option value="{{ $batch->id }}" 
                                                    data-course="{{ $batch->course_id }}"
                                                    class="d-none"
                                                >
                                                    {{ $batch->batch_code }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="batch_id">Assign Batch (Optional)</label>
                                        
                                        {{-- Loading indicator --}}
                                        <div id="batch-loading" class="position-absolute top-50 end-0 translate-middle-y me-3 d-none">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                        </div>
                                    </div>
                                    <div id="batch-help" class="form-text mt-2 small text-muted">
                                        Select a course first to view available batches.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Additional Options --}}
                        <div class="mb-5">
                            <div class="form-check form-switch p-3 bg-light rounded-3 border">
                                <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="is_full_course" name="is_full_course" value="1" checked>
                                <label class="form-check-label fw-semibold" for="is_full_course">
                                    Enroll for Full Course Curriculum
                                    <small class="d-block text-muted fw-normal">Uncheck if student is taking specific modules only.</small>
                                </label>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                            <a href="{{ url()->previous() }}" class="btn btn-light px-4 fw-medium text-muted">Cancel</a>
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm hover-lift" id="submitBtn">
                                Complete Enrollment <i class="fa fa-arrow-right ms-2"></i>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- STYLES --}}
<style>
    .hover-lift:hover { transform: translateY(-2px); transition: transform 0.2s; }
    
    /* Floating Label Fixes for Select2 */
    .select2-container .select2-selection--single {
        height: 58px; /* Match Bootstrap floating label height */
        padding-top: 20px;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 56px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 56px;
        padding-left: 12px;
    }
</style>

{{-- SCRIPTS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    
    // 1. Initialize Select2
    if (typeof jQuery !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
        $('.select2-student').select2({
            width: '100%',
            placeholder: "Search student...",
            allowClear: true
        });
    }

    // 2. Batch Logic
    const courseSelect = document.getElementById('course_id');
    const batchSelect = document.getElementById('batch_id');
    const batchHelp = document.getElementById('batch-help');
    
    // Cache all options immediately
    const allBatchOptions = Array.from(batchSelect.querySelectorAll('option:not([value=""])'));

    function filterBatches() {
        const selectedCourseId = courseSelect.value;
        const loadingIcon = document.getElementById('batch-loading');

        // UI Reset
        batchSelect.value = "";
        batchSelect.disabled = true;
        loadingIcon.classList.remove('d-none'); // Show loading
        
        // Slight delay for UX smoothness
        setTimeout(() => {
            let matchCount = 0;

            if (selectedCourseId) {
                batchSelect.disabled = false;
                
                allBatchOptions.forEach(option => {
                    if (option.dataset.course === selectedCourseId) {
                        option.classList.remove('d-none'); // Show valid
                        matchCount++;
                    } else {
                        option.classList.add('d-none'); // Hide invalid
                    }
                });

                if (matchCount > 0) {
                    batchHelp.innerHTML = `<span class="text-success"><i class="fa fa-check-circle me-1"></i>${matchCount} active batch(es) found.</span>`;
                } else {
                    batchHelp.innerHTML = '<span class="text-warning"><i class="fa fa-info-circle me-1"></i>No active batches for this course.</span>';
                }
            } else {
                batchHelp.innerText = 'Select a course first.';
            }
            
            loadingIcon.classList.add('d-none'); // Hide loading
        }, 300);
    }

    if (courseSelect) {
        courseSelect.addEventListener('change', filterBatches);
        // Run on load if editing or error returned old value
        if(courseSelect.value) filterBatches();
    }

    // 3. Submit Loading
    document.getElementById('enrollmentForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
        btn.disabled = true;
    });
});
</script>
@endsection