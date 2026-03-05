@extends('layouts.app')

@section('content')
    <div class="main-canvas" style="background-color: #f8f9fa; min-height: 100vh; padding: 2rem;">
        <div class="container-xl">

            {{-- 1. Success Message --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-circle-check fs-4 me-3"></i>
                        <div>
                            <strong>Success!</strong> {{ session('success') }}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- 2. Global Error Message (from Controller try/catch) --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-triangle-exclamation fs-4 me-3"></i>
                        <div>
                            <strong>System Error!</strong> {{ session('error') }}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- 3. Validation Errors Summary (Optional, but good for long forms) --}}
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
                    <h2 class="fw-bold text-dark mb-1 tracking-tight">
                        {{ isset($student) ? 'Edit Profile' : 'Student Registration' }}
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('students.index') }}"
                                    class="text-muted text-decoration-none">Students</a></li>
                            <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">
                                {{ isset($student) ? 'Edit' : 'Create' }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('students.index') }}" class="btn btn-light-secondary border fw-medium px-4">
                        Cancel
                    </a>
                </div>
            </div>

            <form action="{{ isset($student) ? route('students.update', $student->id) : route('students.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($student))
                    @method('PUT')
                @endif

                <div class="row g-4">
                    {{-- LEFT COLUMN: Avatar & Status --}}
                    <div class="col-lg-4 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top"
                            style="top: 2rem; z-index: 1;">
                            <div class="card-body p-4 text-center">
                                {{-- Avatar Upload --}}
                                <div class="mb-4 position-relative">
                                    <h6 class="text-uppercase text-muted fw-bold x-small tracking-widest mb-3">Profile Photo
                                    </h6>

                                    <div class="avatar-wrapper mx-auto mb-3 position-relative">
                                        <div class="avatar-container rounded-circle overflow-hidden shadow-sm position-relative"
                                            style="width: 140px; height: 140px;">
                                            {{-- Placeholder --}}
                                            <div id="placeholderPreview"
                                                class="bg-light d-flex align-items-center justify-content-center w-100 h-100 {{ isset($student) && $student->user->avatar ? 'd-none' : '' }}">
                                                <i class="fa-solid fa-user text-secondary fs-1 opacity-25"></i>
                                            </div>

                                            {{-- Image --}}
                                            <img id="imagePreview"
                                                src="{{ isset($student) && $student->user->avatar ? asset('storage/' . $student->user->avatar) : '#' }}"
                                                class="object-fit-cover w-100 h-100 {{ isset($student) && $student->user->avatar ? '' : 'd-none' }}"
                                                alt="Preview">

                                            {{-- Hover Overlay --}}
                                            <label for="avatarUpload"
                                                class="avatar-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center cursor-pointer">
                                                <i class="fa-solid fa-camera text-white fs-4"></i>
                                            </label>
                                        </div>
                                        <input type="file" name="avatar" id="avatarUpload" class="d-none"
                                            accept="image/*" onchange="previewImage(this)">
                                    </div>
                                    <p class="text-muted x-small mb-0">Allowed *.jpeg, *.jpg, *.png, *.gif <br> Max size of
                                        2 MB</p>
                                </div>

                                <hr class="border-light-subtle my-4">

                                {{-- Academic Status --}}
                                {{-- Academic Status Section --}}
                                <div class="text-start">
                                    <label
                                        class="form-label text-uppercase text-muted fw-bold x-small tracking-widest mb-3">
                                        Academic Status
                                    </label>

                                    @if (isset($student))
                                        {{-- EDIT MODE: Show All Options --}}
                                        <div class="d-grid gap-2">
                                            @foreach (['enrolled', 'graduated', 'suspended', 'alumni'] as $status)
                                                <input type="radio" name="academic_status"
                                                    id="status_{{ $status }}" value="{{ $status }}"
                                                    class="btn-check"
                                                    {{ old('academic_status', $student->academic_status) == $status ? 'checked' : '' }}>

                                                <label
                                                    class="status-card p-2 rounded-3 border d-flex align-items-center cursor-pointer transition-all"
                                                    for="status_{{ $status }}">
                                                    {{-- Dynamic Color Indicator --}}
                                                    <div class="status-indicator me-3 rounded-circle"
                                                        style="width: 10px; height: 10px; background-color: {{ $status == 'enrolled' ? '#198754' : ($status == 'graduated' ? '#0dcaf0' : ($status == 'suspended' ? '#dc3545' : '#6c757d')) }};">
                                                    </div>
                                                    <span
                                                        class="fw-medium small text-capitalize">{{ $status }}</span>
                                                    <i class="fa-solid fa-check ms-auto text-primary check-icon"
                                                        style="display: none;"></i>
                                                </label>
                                            @endforeach
                                        </div>

                                        {{-- CSS to show check icon when selected --}}
                                        <style>
                                            .btn-check:checked+.status-card {
                                                border-color: #0d6efd;
                                                background-color: #f0f7ff;
                                            }

                                            .btn-check:checked+.status-card .check-icon {
                                                display: block;
                                            }
                                        </style>
                                    @else
                                        {{-- CREATE MODE: Default only --}}
                                        <div
                                            class="p-3 bg-light-success border border-success-subtle rounded-3 d-flex align-items-center">
                                            <div class="rounded-circle bg-success me-3" style="width: 12px; height: 12px;">
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-dark mb-0 small">Enrolled</h6>
                                                <p class="text-muted x-small mb-0">Default status for new students</p>
                                            </div>
                                            <i class="fa-solid fa-lock text-muted opacity-25 ms-auto"></i>
                                        </div>
                                        {{-- Pass value secretly to satisfy any strict validation if needed, though Service ignores it --}}
                                        <input type="hidden" name="academic_status" value="enrolled">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT COLUMN: Form Data --}}
                    <div class="col-lg-8 col-xl-9">

                        {{-- CARD 1: Core Identity --}}
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-bottom border-light-subtle py-3 px-4">
                                <h5 class="fw-bold mb-0 fs-6 text-dark">
                                    <i class="fa-regular fa-id-card me-2 text-primary"></i>Identity & Login
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold x-small text-uppercase text-muted">Full Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="name"
                                            value="{{ old('name', $student->user->name ?? '') }}"
                                            class="form-control modern-input" placeholder="e.g. John Doe">
                                        @error('name')
                                            <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold x-small text-uppercase text-muted">Email Address
                                            <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 text-muted"><i
                                                    class="fa-regular fa-envelope"></i></span>
                                            <input type="email" name="email"
                                                value="{{ old('email', $student->user->email ?? '') }}"
                                                class="form-control modern-input border-start-0 ps-0 {{ isset($student) ? 'bg-light' : '' }}"
                                                placeholder="student@example.com" {{ isset($student) ? 'readonly' : '' }}>
                                        </div>
                                        @if (isset($student))
                                            <div class="form-text x-small"><i class="fa-solid fa-lock me-1"></i>Locked for
                                                security.</div>
                                        @endif
                                        @error('email')
                                            <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold x-small text-uppercase text-muted">Phone
                                            Number</label>
                                        <input type="text" name="phone"
                                            value="{{ old('phone', $student->user->phone ?? '') }}"
                                            class="form-control modern-input" placeholder="+1 (555) 000-0000">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold x-small text-uppercase text-muted">Registration
                                            ID</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 text-muted"><i
                                                    class="fa-solid fa-hashtag"></i></span>
                                            <input type="text"
                                                value="{{ isset($student) ? $student->registration_number : $nextRegistrationNumber ?? 'Generating...' }}"
                                                class="form-control modern-input border-start-0 ps-0 fw-bold text-dark bg-light"
                                                readonly disabled>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-bold x-small text-uppercase text-muted">Current
                                            Address</label>
                                        <textarea name="address" class="form-control modern-input" rows="2" placeholder="Correspondence address...">{{ old('address', $student->user->address ?? '') }}</textarea>
                                    </div>

                                    {{-- Password Accordion/Section --}}
                                    <div class="col-12">
                                        <div class="bg-light-subtle border rounded-3 p-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fa-solid fa-key text-muted me-2"></i>
                                                <span class="fw-bold small text-dark">Security Settings</span>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <input type="password" name="password"
                                                        class="form-control modern-input bg-white"
                                                        autocomplete="new-password"
                                                        placeholder="New Password {{ isset($student) ? '(Optional)' : '*' }}">
                                                    @error('password')
                                                        <span
                                                            class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="password" name="password_confirmation"
                                                        class="form-control modern-input bg-white"
                                                        placeholder="Confirm Password">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- CARD 2: Tabs for Details --}}
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white border-bottom border-light-subtle px-4">
                                <ul class="nav nav-tabs card-header-tabs modern-tabs" id="studentTab" role="tablist">
                                    <li class="nav-item">
                                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#personal"
                                            type="button">
                                            <i class="fa-regular fa-user me-2"></i>Personal
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#education"
                                            type="button">
                                            <i class="fa-solid fa-graduation-cap me-2"></i>Education
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#skills"
                                            type="button">
                                            <i class="fa-solid fa-lightbulb me-2"></i>Skills
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#guardians"
                                            type="button">
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
                                                <label class="form-label fw-bold x-small text-uppercase text-muted">Date of
                                                    Birth</label>
                                                <input type="date" name="dob" class="form-control modern-input"
                                                    value="{{ old('dob', isset($student->dob) ? $student->dob->format('Y-m-d') : '') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label
                                                    class="form-label fw-bold x-small text-uppercase text-muted">Emergency
                                                    Contact</label>
                                                <input type="text" name="emergency_contact"
                                                    class="form-control modern-input"
                                                    value="{{ old('emergency_contact', $student->emergency_contact ?? '') }}"
                                                    placeholder="Name / Phone">
                                            </div>
                                            <div class="col-12">
                                                <label
                                                    class="form-label fw-bold x-small text-uppercase text-muted">Permanent
                                                    Address</label>
                                                <textarea name="permanent_address" class="form-control modern-input" rows="2"
                                                    placeholder="Permanent residence...">{{ old('permanent_address', $student->permanent_address ?? '') }}</textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-bold x-small text-uppercase text-muted">Bio /
                                                    Notes</label>
                                                <textarea name="bio" class="form-control modern-input" rows="3" placeholder="Additional notes...">{{ old('bio', $student->bio ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Education Tab --}}
                                    <div class="tab-pane fade" id="education">
                                        <div id="education-wrapper" class="d-flex flex-column gap-3">
                                            @php $educations = old('education', $student->education ?? [[]]); @endphp
                                            @foreach ($educations as $index => $edu)
                                                <div
                                                    class="dynamic-row card border border-light-subtle shadow-sm bg-light-subtle">
                                                    <div class="card-body p-3">
                                                        <input type="hidden" name="education[{{ $index }}][id]"
                                                            value="{{ is_array($edu) ? $edu['id'] ?? '' : $edu->id }}">
                                                        <div class="row g-2 align-items-end">
                                                            <div class="col-md-3">
                                                                <label
                                                                    class="x-small text-muted fw-bold mb-1">DEGREE</label>
                                                                <input type="text"
                                                                    name="education[{{ $index }}][degree]"
                                                                    class="form-control form-control-sm modern-input bg-white"
                                                                    placeholder="e.g. BSc"
                                                                    value="{{ is_array($edu) ? $edu['degree'] ?? '' : $edu->degree }}">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label
                                                                    class="x-small text-muted fw-bold mb-1">INSTITUTION</label>
                                                                <input type="text"
                                                                    name="education[{{ $index }}][institution]"
                                                                    class="form-control form-control-sm modern-input bg-white"
                                                                    placeholder="University"
                                                                    value="{{ is_array($edu) ? $edu['institution'] ?? '' : $edu->institution }}">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="x-small text-muted fw-bold mb-1">YEAR</label>
                                                                <input type="text"
                                                                    name="education[{{ $index }}][passing_year]"
                                                                    class="form-control form-control-sm modern-input bg-white"
                                                                    placeholder="202X"
                                                                    value="{{ is_array($edu) ? $edu['passing_year'] ?? '' : $edu->passing_year }}">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label
                                                                    class="x-small text-muted fw-bold mb-1">GRADE</label>
                                                                <input type="text"
                                                                    name="education[{{ $index }}][grade_or_percentage]"
                                                                    class="form-control form-control-sm modern-input bg-white"
                                                                    placeholder="GPA"
                                                                    value="{{ is_array($edu) ? $edu['grade_or_percentage'] ?? '' : $edu->grade_or_percentage }}">
                                                            </div>
                                                            <div class="col-md-1 text-end">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-icon btn-light-danger text-danger"
                                                                    onclick="removeRow(this)"><i
                                                                        class="fa-solid fa-trash"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary mt-3 dashed-btn w-100"
                                            onclick="addEducationRow()">
                                            <i class="fa-solid fa-plus me-1"></i> Add Education Record
                                        </button>
                                    </div>

                                    {{-- Skills Tab --}}
                                    <div class="tab-pane fade" id="skills">
                                        <div id="skills-wrapper" class="d-flex flex-column gap-3">
                                            @php $skills = old('skills', $student->skills ?? [[]]); @endphp
                                            @foreach ($skills as $index => $skill)
                                                <div
                                                    class="dynamic-row card border border-light-subtle shadow-sm bg-light-subtle">
                                                    <div class="card-body p-3">
                                                        <input type="hidden" name="skills[{{ $index }}][id]"
                                                            value="{{ is_array($skill) ? $skill['id'] ?? '' : $skill->id }}">
                                                        <div class="row g-2 align-items-center">
                                                            <div class="col-md-6">
                                                                <input type="text"
                                                                    name="skills[{{ $index }}][skill_name]"
                                                                    class="form-control form-control-sm modern-input bg-white"
                                                                    placeholder="Skill Name (e.g. PHP)"
                                                                    value="{{ is_array($skill) ? $skill['skill_name'] ?? '' : $skill->skill_name }}">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <select name="skills[{{ $index }}][proficiency]"
                                                                    class="form-select form-select-sm modern-input bg-white cursor-pointer">
                                                                    @foreach (['Beginner', 'Intermediate', 'Expert'] as $level)
                                                                        <option value="{{ $level }}"
                                                                            {{ (is_array($skill) ? $skill['proficiency'] ?? '' : $skill->proficiency) == $level ? 'selected' : '' }}>
                                                                            {{ $level }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-1 text-end">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-icon btn-light-danger text-danger"
                                                                    onclick="removeRow(this)"><i
                                                                        class="fa-solid fa-trash"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary mt-3 dashed-btn w-100"
                                            onclick="addSkillRow()">
                                            <i class="fa-solid fa-plus me-1"></i> Add Skill
                                        </button>
                                    </div>

                                    {{-- Guardians Tab --}}
                                    <div class="tab-pane fade" id="guardians">
                                        <div id="guardians-wrapper" class="d-flex flex-column gap-3">
                                            @php $guardians = old('guardians', $student->guardians ?? [[]]); @endphp
                                            @foreach ($guardians as $index => $guardian)
                                                <div
                                                    class="dynamic-row card border border-light-subtle shadow-sm bg-light-subtle">
                                                    <div class="card-body p-3">
                                                        <input type="hidden" name="guardians[{{ $index }}][id]"
                                                            value="{{ is_array($guardian) ? $guardian['id'] ?? '' : $guardian->id }}">
                                                        <div class="row g-2 align-items-center">
                                                            <div class="col-md-3">
                                                                <input type="text"
                                                                    name="guardians[{{ $index }}][name]"
                                                                    class="form-control form-control-sm modern-input bg-white"
                                                                    placeholder="Guardian Name"
                                                                    value="{{ is_array($guardian) ? $guardian['name'] ?? '' : $guardian->name }}">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text"
                                                                    name="guardians[{{ $index }}][relationship]"
                                                                    class="form-control form-control-sm modern-input bg-white"
                                                                    placeholder="Relationship"
                                                                    value="{{ is_array($guardian) ? $guardian['relationship'] ?? '' : $guardian->relationship }}">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text"
                                                                    name="guardians[{{ $index }}][phone]"
                                                                    class="form-control form-control-sm modern-input bg-white"
                                                                    placeholder="Phone"
                                                                    value="{{ is_array($guardian) ? $guardian['phone'] ?? '' : $guardian->phone }}">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text"
                                                                    name="guardians[{{ $index }}][email]"
                                                                    class="form-control form-control-sm modern-input bg-white"
                                                                    placeholder="Email (Optional)"
                                                                    value="{{ is_array($guardian) ? $guardian['email'] ?? '' : $guardian->email }}">
                                                            </div>
                                                            <div class="col-md-1 text-end">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-icon btn-light-danger text-danger"
                                                                    onclick="removeRow(this)"><i
                                                                        class="fa-solid fa-trash"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary mt-3 dashed-btn w-100"
                                            onclick="addGuardianRow()">
                                            <i class="fa-solid fa-plus me-1"></i> Add Guardian
                                        </button>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="mt-4 pt-4 border-top d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm rounded-3">
                                        <i class="fa-regular fa-floppy-disk me-2"></i>Save Record
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Javascript for Dynamic Rows & Image Preview --}}
    <script>
        // Image Preview Logic
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    let preview = document.getElementById('imagePreview');
                    let placeholder = document.getElementById('placeholderPreview');
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                    placeholder.classList.add('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Remove Row
        function removeRow(btn) {
            const row = btn.closest('.dynamic-row');
            // If it's the only row, just clear inputs. If multiple, remove it.
            const wrapper = row.parentElement;
            if (wrapper.children.length > 1) {
                row.remove();
            } else {
                row.querySelectorAll('input').forEach(i => i.value = '');
                row.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
            }
        }

        // Dynamic Row Templates
        const templates = {
            education: (i) => `
                <div class="dynamic-row card border border-light-subtle shadow-sm bg-light-subtle">
                    <div class="card-body p-3">
                        <input type="hidden" name="education[${i}][id]" value="">
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
                        <input type="hidden" name="skills[${i}][id]" value="">
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
                        <input type="hidden" name="guardians[${i}][id]" value="">
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

        function addEducationRow() {
            addRow('education', 'education-wrapper');
        }

        function addSkillRow() {
            addRow('skills', 'skills-wrapper');
        }

        function addGuardianRow() {
            addRow('guardians', 'guardians-wrapper');
        }
    </script>

    <style>
        /* Utility */
        .cursor-pointer {
            cursor: pointer;
        }

        .x-small {
            font-size: 0.7rem;
            letter-spacing: 0.5px;
        }

        .transition-all {
            transition: all 0.2s ease;
        }

        .btn-light-secondary {
            background: white;
            color: #6c757d;
        }

        .btn-light-secondary:hover {
            background: #e9ecef;
        }

        .btn-light-danger {
            background: rgba(220, 53, 69, 0.1);
            border: none;
        }

        .btn-light-danger:hover {
            background: rgba(220, 53, 69, 0.2);
        }

        /* Modern Inputs */
        .modern-input {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 0.65rem 1rem;
            font-size: 0.95rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .modern-input:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        }

        .input-group-text {
            border-color: #dee2e6;
        }

        /* Avatar Styling */
        .avatar-overlay {
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .avatar-container:hover .avatar-overlay {
            opacity: 1;
        }

        /* Status Cards (Radio Button Styling) */
        .status-card {
            border: 1px solid #dee2e6;
            background: white;
            color: #495057;
        }

        .status-card:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
        }

        .btn-check:checked+.status-card {
            border-color: #0d6efd;
            background-color: #f0f7ff;
            color: #0d6efd;
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            background: #adb5bd;
        }

        .btn-check:checked+.status-card .status-indicator {
            background: #0d6efd;
        }

        .check-icon {
            display: none;
        }

        .btn-check:checked+.status-card .check-icon {
            display: block;
        }

        /* Status Specific Colors */
        [data-status="enrolled"] {
            background-color: #198754;
        }

        [data-status="graduated"] {
            background-color: #0dcaf0;
        }

        [data-status="suspended"] {
            background-color: #dc3545;
        }

        [data-status="alumni"] {
            background-color: #6c757d;
        }

        /* Modern Tabs */
        .modern-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 1rem 1.5rem;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
        }

        .modern-tabs .nav-link:hover {
            color: #0d6efd;
        }

        .modern-tabs .nav-link.active {
            color: #0d6efd;
            background: transparent;
            border-bottom: 2px solid #0d6efd;
        }

        /* Dashed Button */
        .dashed-btn {
            border-style: dashed;
            border-width: 2px;
            font-weight: 500;
        }

        .dashed-btn:hover {
            border-style: solid;
            background-color: #f0f7ff;
        }
    </style>
@endsection
