@extends('layouts.app')

@section('content')
    <div class="main-canvas" style="background-color: #f8f9fa; min-height: 100vh; padding: 2rem;">
        <div class="container-xl">

            {{-- 1. Success Message --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-circle-check fs-4 me-3"></i>
                        <div><strong>Success!</strong> {{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- 2. Global Error Message --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-triangle-exclamation fs-4 me-3"></i>
                        <div><strong>System Error!</strong> {{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- 3. Validation Errors Summary --}}
            @if ($errors->any())
                <div class="alert alert-warning alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="fa-solid fa-circle-exclamation fs-4 me-3 mt-1"></i>
                        <div>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Header Section --}}
            <div class="d-flex align-items-center justify-content-between mb-5">
                <div>
                    <h2 class="fw-bold text-dark mb-1 tracking-tight">Quick Registration & Enrollment</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('students.index') }}" class="text-muted text-decoration-none">Students</a></li>
                            <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">One-Click Setup</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('students.index') }}" class="btn btn-light-secondary border fw-medium px-4">Cancel</a>
                </div>
            </div>

            <form action="{{ route('students.quick_store') }}" method="POST" enctype="multipart/form-data" id="enrollmentForm">
                @csrf

                <div class="row g-4">
                    {{-- LEFT COLUMN: Avatar & Status --}}
                    <div class="col-lg-4 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top" style="top: 2rem; z-index: 1;">
                            <div class="card-body p-4 text-center">
                                {{-- Avatar Upload --}}
                                <div class="mb-4 position-relative">
                                    <h6 class="text-uppercase text-muted fw-bold x-small tracking-widest mb-3">Profile Photo</h6>
                                    <div class="avatar-wrapper mx-auto mb-3 position-relative">
                                        <div class="avatar-container rounded-circle overflow-hidden shadow-sm position-relative" style="width: 140px; height: 140px;">
                                            <div id="placeholderPreview" class="bg-light d-flex align-items-center justify-content-center w-100 h-100">
                                                <i class="fa-solid fa-user text-secondary fs-1 opacity-25"></i>
                                            </div>
                                            <img id="imagePreview" src="#" class="object-fit-cover w-100 h-100 d-none" alt="Preview">
                                            <label for="avatarUpload" class="avatar-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center cursor-pointer">
                                                <i class="fa-solid fa-camera text-white fs-4"></i>
                                            </label>
                                        </div>
                                        <input type="file" name="avatar" id="avatarUpload" class="d-none" accept="image/*" onchange="previewImage(this)">
                                    </div>
                                    <p class="text-muted x-small mb-0">Allowed *.jpeg, *.jpg, *.png <br> Max size of 2 MB</p>
                                </div>

                                <hr class="border-light-subtle my-4">

                                {{-- Academic Status Section --}}
                                <div class="text-start">
                                    <label class="form-label text-uppercase text-muted fw-bold x-small tracking-widest mb-3">Academic Status</label>
                                    <div class="p-3 bg-light-success border border-success-subtle rounded-3 d-flex align-items-center">
                                        <div class="rounded-circle bg-success me-3" style="width: 12px; height: 12px;"></div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0 small">Enrolled</h6>
                                            <p class="text-muted x-small mb-0">Default status for new students</p>
                                        </div>
                                        <i class="fa-solid fa-lock text-muted opacity-25 ms-auto"></i>
                                    </div>
                                    <input type="hidden" name="academic_status" value="enrolled">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT COLUMN: Form Data --}}
                    <div class="col-lg-8 col-xl-9">

                        {{-- CARD 1: Core Identity --}}
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-bottom border-light-subtle py-3 px-4 d-flex align-items-center">
                                <span class="badge bg-primary rounded-pill me-2">1</span>
                                <h5 class="fw-bold mb-0 fs-6 text-dark">Identity & Login</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold x-small text-uppercase text-muted">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" value="{{ old('name') }}" class="form-control modern-input" placeholder="e.g. John Doe" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold x-small text-uppercase text-muted">Email Address <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-regular fa-envelope"></i></span>
                                            <input type="email" name="email" value="{{ old('email') }}" class="form-control modern-input border-start-0 ps-0" placeholder="student@example.com" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold x-small text-uppercase text-muted">Phone Number</label>
                                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control modern-input" placeholder="+1 (555) 000-0000">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold x-small text-uppercase text-muted">Registration ID</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-hashtag"></i></span>
                                            <input type="text" value="{{ $nextRegistrationNumber }}" class="form-control modern-input border-start-0 ps-0 fw-bold text-dark bg-light" readonly disabled>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-bold x-small text-uppercase text-muted">Current Address</label>
                                        <textarea name="address" class="form-control modern-input" rows="2" placeholder="Correspondence address...">{{ old('address') }}</textarea>
                                    </div>

                                    <div class="col-12">
                                        <div class="bg-light-subtle border rounded-3 p-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fa-solid fa-key text-muted me-2"></i>
                                                <span class="fw-bold small text-dark">Initial Password <span class="text-danger">*</span></span>
                                            </div>
                                            <input type="password" name="password" class="form-control modern-input bg-white" placeholder="Required for login" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- CARD 2: Enrollment Section --}}
                        <div class="card border-0 shadow-sm rounded-4 mb-4 border-primary border-top border-3">
                            <div class="card-header bg-white border-bottom border-light-subtle py-3 px-4 d-flex align-items-center">
                                <span class="badge bg-primary rounded-pill me-2">2</span>
                                <h5 class="fw-bold mb-0 fs-6 text-dark">Academic Assignment</h5>
                            </div>
                            <div class="card-body p-4 bg-light">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label for="course_id" class="form-label fw-semibold text-dark">Course <span class="text-danger">*</span></label>
                                        <div class="input-group shadow-sm rounded-3">
                                            <span class="input-group-text bg-white border-end-0"><i class="fa fa-book text-muted"></i></span>
                                            <select name="course_id" id="course_id" class="form-select border-start-0 py-2" required>
                                                <option value="">-- Choose a Course --</option>
                                                @foreach($courses as $course)
                                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                        {{ $course->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="batch_id" class="form-label fw-semibold text-dark">Batch <span class="text-danger">*</span></label>
                                        <div class="input-group shadow-sm rounded-3">
                                            <span class="input-group-text bg-white border-end-0" id="batch-icon"><i class="fa fa-users text-muted"></i></span>
                                            <select name="batch_id" id="batch_id" class="form-select border-start-0 py-2" required>
                                                <option value="">-- Select Course First --</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Join Date <span class="text-danger">*</span></label>
                                        <div class="input-group shadow-sm rounded-3">
                                            <span class="input-group-text bg-white border-end-0"><i class="fa-regular fa-calendar-days text-muted"></i></span>
                                            <input type="date" name="enrolled_at" value="{{ old('enrolled_at', date('Y-m-d')) }}" class="form-control border-start-0 py-2 shadow-none" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Curriculum Status</label>
                                        <div class="form-check p-2 border bg-white rounded shadow-sm hover-elevate cursor-pointer">
                                            {{-- FIX: Added hidden input to ensure '0' is sent if unchecked --}}
                                            <input type="hidden" name="is_full_course" value="0">
                                            <input class="form-check-input ms-2 me-2 mt-2" type="checkbox" name="is_full_course" value="1" id="fullCourseCheck" checked>
                                            <label class="form-check-label w-100 cursor-pointer pt-1" for="fullCourseCheck">
                                                <span class="fw-bold text-dark small">Full Course Enrollment</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- CARD 3: Tabs for Details --}}
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white border-bottom border-light-subtle px-4 d-flex align-items-center">
                                <span class="badge bg-primary rounded-pill me-2">3</span>
                                <ul class="nav nav-tabs card-header-tabs modern-tabs flex-grow-1" id="studentTab" role="tablist">
                                    <li class="nav-item">
                                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#personal" type="button">
                                            <i class="fa-regular fa-user me-2"></i>Personal Details
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#education" type="button">
                                            <i class="fa-solid fa-graduation-cap me-2"></i>Education
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#skills" type="button">
                                            <i class="fa-solid fa-lightbulb me-2"></i>Skills
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#guardians" type="button">
                                            <i class="fa-solid fa-users me-2"></i>Guardians
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            <div class="card-body p-4">
                                <div class="tab-content">
                                    {{-- Personal Tab --}}
                                    <div class="tab-pane fade show active" id="personal">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold x-small text-uppercase text-muted">Date of Birth</label>
                                                <input type="date" name="dob" class="form-control modern-input" value="{{ old('dob') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold x-small text-uppercase text-muted">Emergency Contact</label>
                                                <input type="text" name="emergency_contact" class="form-control modern-input" value="{{ old('emergency_contact') }}" placeholder="Name / Phone">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-bold x-small text-uppercase text-muted">Permanent Address</label>
                                                <textarea name="permanent_address" class="form-control modern-input" rows="2" placeholder="Permanent residence...">{{ old('permanent_address') }}</textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-bold x-small text-uppercase text-muted">Bio / Notes</label>
                                                <textarea name="bio" class="form-control modern-input" rows="3" placeholder="Additional notes...">{{ old('bio') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Education Tab --}}
                                    <div class="tab-pane fade" id="education">
                                        <div id="education-wrapper" class="d-flex flex-column gap-3">
                                            @php $educations = old('education', [[]]); @endphp
                                            @foreach ($educations as $index => $edu)
                                                <div class="dynamic-row card border border-light-subtle shadow-sm bg-light-subtle">
                                                    <div class="card-body p-3">
                                                        <div class="row g-2 align-items-end">
                                                            <div class="col-md-3"><label class="x-small text-muted fw-bold mb-1">DEGREE</label><input type="text" name="education[{{ $index }}][degree]" class="form-control form-control-sm modern-input bg-white" placeholder="e.g. BSc" value="{{ $edu['degree'] ?? '' }}"></div>
                                                            <div class="col-md-4"><label class="x-small text-muted fw-bold mb-1">INSTITUTION</label><input type="text" name="education[{{ $index }}][institution]" class="form-control form-control-sm modern-input bg-white" placeholder="University" value="{{ $edu['institution'] ?? '' }}"></div>
                                                            <div class="col-md-2"><label class="x-small text-muted fw-bold mb-1">YEAR</label><input type="text" name="education[{{ $index }}][passing_year]" class="form-control form-control-sm modern-input bg-white" placeholder="202X" value="{{ $edu['passing_year'] ?? '' }}"></div>
                                                            <div class="col-md-2"><label class="x-small text-muted fw-bold mb-1">GRADE</label><input type="text" name="education[{{ $index }}][grade_or_percentage]" class="form-control form-control-sm modern-input bg-white" placeholder="GPA" value="{{ $edu['grade_or_percentage'] ?? '' }}"></div>
                                                            <div class="col-md-1 text-end"><button type="button" class="btn btn-sm btn-icon btn-light-danger text-danger" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-3 dashed-btn w-100" onclick="addEducationRow()">
                                            <i class="fa-solid fa-plus me-1"></i> Add Education Record
                                        </button>
                                    </div>

                                    {{-- Skills Tab --}}
                                    <div class="tab-pane fade" id="skills">
                                        <div id="skills-wrapper" class="d-flex flex-column gap-3">
                                            @php $skills = old('skills', [[]]); @endphp
                                            @foreach ($skills as $index => $skill)
                                                <div class="dynamic-row card border border-light-subtle shadow-sm bg-light-subtle">
                                                    <div class="card-body p-3">
                                                        <div class="row g-2 align-items-center">
                                                            <div class="col-md-6"><input type="text" name="skills[{{ $index }}][skill_name]" class="form-control form-control-sm modern-input bg-white" placeholder="Skill Name (e.g. PHP)" value="{{ $skill['skill_name'] ?? '' }}"></div>
                                                            <div class="col-md-5">
                                                                <select name="skills[{{ $index }}][proficiency]" class="form-select form-select-sm modern-input bg-white cursor-pointer">
                                                                    @foreach (['Beginner', 'Intermediate', 'Expert'] as $level)
                                                                        <option value="{{ $level }}" {{ ($skill['proficiency'] ?? '') == $level ? 'selected' : '' }}>{{ $level }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-1 text-end"><button type="button" class="btn btn-sm btn-icon btn-light-danger text-danger" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-3 dashed-btn w-100" onclick="addSkillRow()">
                                            <i class="fa-solid fa-plus me-1"></i> Add Skill
                                        </button>
                                    </div>

                                    {{-- Guardians Tab --}}
                                    <div class="tab-pane fade" id="guardians">
                                        <div id="guardians-wrapper" class="d-flex flex-column gap-3">
                                            @php $guardians = old('guardians', [[]]); @endphp
                                            @foreach ($guardians as $index => $guardian)
                                                <div class="dynamic-row card border border-light-subtle shadow-sm bg-light-subtle">
                                                    <div class="card-body p-3">
                                                        <div class="row g-2 align-items-center">
                                                            <div class="col-md-3"><input type="text" name="guardians[{{ $index }}][name]" class="form-control form-control-sm modern-input bg-white" placeholder="Guardian Name" value="{{ $guardian['name'] ?? '' }}"></div>
                                                            <div class="col-md-2"><input type="text" name="guardians[{{ $index }}][relationship]" class="form-control form-control-sm modern-input bg-white" placeholder="Relationship" value="{{ $guardian['relationship'] ?? '' }}"></div>
                                                            <div class="col-md-3"><input type="text" name="guardians[{{ $index }}][phone]" class="form-control form-control-sm modern-input bg-white" placeholder="Phone" value="{{ $guardian['phone'] ?? '' }}"></div>
                                                            <div class="col-md-3"><input type="text" name="guardians[{{ $index }}][email]" class="form-control form-control-sm modern-input bg-white" placeholder="Email (Optional)" value="{{ $guardian['email'] ?? '' }}"></div>
                                                            <div class="col-md-1 text-end"><button type="button" class="btn btn-sm btn-icon btn-light-danger text-danger" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-3 dashed-btn w-100" onclick="addGuardianRow()">
                                            <i class="fa-solid fa-plus me-1"></i> Add Guardian
                                        </button>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="mt-5 pt-4 border-top d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary px-5 py-3 fw-bold shadow flex-grow-1 flex-md-grow-0 rounded-3" id="submitBtn">
                                        <i class="fa-solid fa-bolt me-2 text-warning"></i> Complete Quick Setup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Script Integration --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. Batch AJAX Fetching Logic ---
            const courseSelect = document.getElementById('course_id');
            const batchSelect = document.getElementById('batch_id');
            const batchIcon = document.getElementById('batch-icon').querySelector('i');
            const oldBatchId = "{{ old('batch_id') }}"; 

            function fetchBatches(courseId) {
                batchSelect.innerHTML = '<option value="">Loading batches...</option>';
                
                if (!courseId) {
                    batchSelect.innerHTML = '<option value="">-- Select Course First --</option>';
                    batchIcon.className = 'fa fa-users text-muted';
                    return;
                }

                batchIcon.className = 'fa fa-users text-primary spinner-grow spinner-grow-sm';

                // FIX: Used Laravel's url() helper to ensure the path works perfectly on localhost subfolders
                fetch(`{{ url('students/ajax/course-batches') }}/${courseId}`)
                    .then(response => response.json())
                    .then(data => {
                        batchIcon.className = 'fa fa-users text-primary';
                        batchSelect.innerHTML = '<option value="">-- Select an Active Batch --</option>';
                        
                        data.forEach(batch => {
                            const isSelected = (oldBatchId == batch.id) ? 'selected' : '';
                            batchSelect.innerHTML += `<option value="${batch.id}" ${isSelected}>
                                ${batch.batch_code} (${batch.shift})
                            </option>`;
                        });
                        
                        if (data.length === 0) {
                            batchSelect.innerHTML = '<option value="">No active batches found</option>';
                        }
                    })
                    .catch(error => {
                        batchIcon.className = 'fa fa-triangle-exclamation text-danger';
                        batchSelect.innerHTML = '<option value="">Error loading batches</option>';
                    });
            }

            courseSelect.addEventListener('change', function() { fetchBatches(this.value); });
            if (courseSelect.value) { fetchBatches(courseSelect.value); }

            // --- 2. Button State Logic ---
            const form = document.getElementById('enrollmentForm');
            const submitBtn = document.getElementById('submitBtn');
            form.addEventListener('submit', function() {
                if(form.checkValidity()) {
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing Setup...';
                    submitBtn.disabled = true;
                }
            });
        });

        // --- 3. Image & Dynamic Row Logic ---
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('imagePreview').classList.remove('d-none');
                    document.getElementById('placeholderPreview').classList.add('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeRow(btn) {
            const row = btn.closest('.dynamic-row');
            const wrapper = row.parentElement;
            if (wrapper.children.length > 1) {
                row.remove();
            } else {
                row.querySelectorAll('input').forEach(i => i.value = '');
                row.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
            }
        }

        const templates = {
            education: (i) => `
                <div class="dynamic-row card border border-light-subtle shadow-sm bg-light-subtle">
                    <div class="card-body p-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3"><label class="x-small text-muted fw-bold mb-1">DEGREE</label><input type="text" name="education[${i}][degree]" class="form-control form-control-sm modern-input bg-white" placeholder="e.g. BSc"></div>
                            <div class="col-md-4"><label class="x-small text-muted fw-bold mb-1">INSTITUTION</label><input type="text" name="education[${i}][institution]" class="form-control form-control-sm modern-input bg-white" placeholder="University"></div>
                            <div class="col-md-2"><label class="x-small text-muted fw-bold mb-1">YEAR</label><input type="text" name="education[${i}][passing_year]" class="form-control form-control-sm modern-input bg-white" placeholder="202X"></div>
                            <div class="col-md-2"><label class="x-small text-muted fw-bold mb-1">GRADE</label><input type="text" name="education[${i}][grade_or_percentage]" class="form-control form-control-sm modern-input bg-white" placeholder="GPA"></div>
                            <div class="col-md-1 text-end"><button type="button" class="btn btn-sm btn-icon btn-light-danger text-danger" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></div>
                        </div>
                    </div>
                </div>`,
            skills: (i) => `
                <div class="dynamic-row card border border-light-subtle shadow-sm bg-light-subtle">
                    <div class="card-body p-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-6"><input type="text" name="skills[${i}][skill_name]" class="form-control form-control-sm modern-input bg-white" placeholder="Skill Name"></div>
                            <div class="col-md-5"><select name="skills[${i}][proficiency]" class="form-select form-select-sm modern-input bg-white cursor-pointer"><option value="Beginner">Beginner</option><option value="Intermediate">Intermediate</option><option value="Expert">Expert</option></select></div>
                            <div class="col-md-1 text-end"><button type="button" class="btn btn-sm btn-icon btn-light-danger text-danger" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></div>
                        </div>
                    </div>
                </div>`,
            guardians: (i) => `
                <div class="dynamic-row card border border-light-subtle shadow-sm bg-light-subtle">
                    <div class="card-body p-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-3"><input type="text" name="guardians[${i}][name]" class="form-control form-control-sm modern-input bg-white" placeholder="Guardian Name"></div>
                            <div class="col-md-2"><input type="text" name="guardians[${i}][relationship]" class="form-control form-control-sm modern-input bg-white" placeholder="Relationship"></div>
                            <div class="col-md-3"><input type="text" name="guardians[${i}][phone]" class="form-control form-control-sm modern-input bg-white" placeholder="Phone"></div>
                            <div class="col-md-3"><input type="text" name="guardians[${i}][email]" class="form-control form-control-sm modern-input bg-white" placeholder="Email (Optional)"></div>
                            <div class="col-md-1 text-end"><button type="button" class="btn btn-sm btn-icon btn-light-danger text-danger" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></div>
                        </div>
                    </div>
                </div>`
        };

        function addRow(type, wrapperId) {
            const wrapper = document.getElementById(wrapperId);
            const index = new Date().getTime(); // Unique ID
            wrapper.insertAdjacentHTML('beforeend', templates[type](index));
        }

        function addEducationRow() { addRow('education', 'education-wrapper'); }
        function addSkillRow() { addRow('skills', 'skills-wrapper'); }
        function addGuardianRow() { addRow('guardians', 'guardians-wrapper'); }
    </script>

    {{-- Premium Styling Merged --}}
    <style>
        .cursor-pointer { cursor: pointer; }
        .x-small { font-size: 0.7rem; letter-spacing: 0.5px; }
        .letter-spacing-1 { letter-spacing: 1px; }
        
        /* Modern Inputs */
        .modern-input { border: 1px solid #dee2e6; border-radius: 0.5rem; padding: 0.65rem 1rem; font-size: 0.95rem; transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; }
        .modern-input:focus { border-color: #86b7fe; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); }
        .input-group-text { border-color: #dee2e6; }
        
        .form-control, .form-select { border-color: #dee2e6; padding-top: 0.6rem; padding-bottom: 0.6rem; }
        .form-control:focus, .form-select:focus { border-color: #3b82f6; box-shadow: none; }
        
        /* Buttons */
        .btn-light-secondary { background: white; color: #6c757d; }
        .btn-light-secondary:hover { background: #e9ecef; }
        .btn-light-danger { background: rgba(220, 53, 69, 0.1); border: none; }
        .btn-light-danger:hover { background: rgba(220, 53, 69, 0.2); }
        .dashed-btn { border-style: dashed; border-width: 2px; font-weight: 500; }
        .dashed-btn:hover { border-style: solid; background-color: #f0f7ff; }

        /* Avatar Styling */
        .avatar-overlay { background: rgba(0, 0, 0, 0.5); opacity: 0; transition: opacity 0.3s; }
        .avatar-container:hover .avatar-overlay { opacity: 1; }

        /* Checkbox Elevate */
        .hover-elevate { transition: all 0.2s; }
        .hover-elevate:hover { background-color: #f8f9fa; border-color: #3b82f6 !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }

        /* Modern Tabs */
        .modern-tabs .nav-link { border: none; color: #6c757d; font-weight: 500; padding: 1rem 1.5rem; border-bottom: 2px solid transparent; margin-bottom: -1px; }
        .modern-tabs .nav-link:hover { color: #0d6efd; }
        .modern-tabs .nav-link.active { color: #0d6efd; background: transparent; border-bottom: 2px solid #0d6efd; }
    </style>
@endsection