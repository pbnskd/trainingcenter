@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">

    {{-- Header Section --}}
    <div class="content-header d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 animate__animated animate__fadeIn">
        <div>
            <div class="d-flex align-items-center mb-2">
                <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 me-2 font-monospace border border-primary border-opacity-10">
                    REG: #{{ $student->registration_number }}
                </span>
                <span class="badge {{ $student->academic_status === 'enrolled' ? 'bg-soft-success text-success border-success' : 'bg-soft-secondary text-secondary border-secondary' }} rounded-pill px-3 py-1 me-2 text-uppercase x-small border border-opacity-10">
                    <i class="fa-solid fa-circle-check me-1"></i>{{ $student->academic_status }}
                </span>
            </div>
            <h2 class="fw-800 text-dark mb-0">Student Profile</h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('students.index') }}" class="btn btn-light rounded-pill border shadow-sm fw-bold px-4">
                <i class="fa-solid fa-arrow-left me-2"></i>Directory
            </a>
            @can('update', $student)
                <a href="{{ route('students.edit', $student->id) }}" class="btn btn-premium rounded-pill shadow-sm px-4 fw-bold">
                    <i class="fa-solid fa-pen-to-square me-2"></i>Edit Profile
                </a>
            @endcan
        </div>
    </div>

    <div class="row g-4">
        {{-- LEFT COLUMN: Personal Info --}}
        <div class="col-lg-4">
            {{-- Profile Identity Card --}}
            <div class="card border-0 shadow-sm mb-4 animate__animated animate__fadeInLeft">
                <div class="card-body p-4 text-center">
                    <div class="position-relative d-inline-block mb-3">
                        @if ($student->user->avatar)
                            <img src="{{ asset('storage/' . $student->user->avatar) }}" class="rounded-circle shadow-sm border border-3 border-white p-1" style="width: 130px; height: 130px; object-fit: cover;">
                        @else
                            <div class="bg-soft-primary text-primary fw-bold rounded-circle border border-3 border-white shadow-sm mx-auto d-flex align-items-center justify-content-center" style="width: 130px; height: 130px; font-size: 3rem;">
                                {{ strtoupper(substr($student->user->name, 0, 1)) }}
                            </div>
                        @endif
                        {{-- Integrated pulse-ring animation for active status --}}
                        <div class="online-status {{ $student->user->status ? 'status-active' : 'status-inactive' }}" style="width: 24px; height: 24px; border-width: 4px;"></div>
                    </div>

                    <h5 class="fw-bold mb-1 text-dark">{{ $student->user->name }}</h5>
                    <p class="text-muted small mb-4">{{ $student->user->email }}</p>

                    <div class="d-grid mb-4">
                        <button class="btn btn-premium rounded-pill shadow-xs py-2">
                            <i class="fa-regular fa-envelope me-2"></i>Send Notification
                        </button>
                    </div>

                    <div class="text-start">
                        <h6 class="fw-bold small mb-3 text-uppercase text-muted tracking-widest border-bottom pb-2">
                            Personal Details
                        </h6>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box d-flex align-items-center justify-content-center bg-light text-muted rounded-circle" style="width: 32px; height: 32px;">
                                    <i class="fa-solid fa-phone x-small"></i>
                                </div>
                                <div>
                                    <span class="d-block x-small text-muted text-uppercase fw-bold">Phone</span>
                                    <span class="small fw-bold text-dark">{{ $student->user->phone ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box d-flex align-items-center justify-content-center bg-light text-muted rounded-circle" style="width: 32px; height: 32px;">
                                    <i class="fa-solid fa-cake-candles x-small"></i>
                                </div>
                                <div>
                                    <span class="d-block x-small text-muted text-uppercase fw-bold">DOB</span>
                                    <span class="small fw-bold text-dark">{{ $student->dob ? $student->dob->format('M d, Y') : '-' }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box d-flex align-items-center justify-content-center bg-light text-muted rounded-circle" style="width: 32px; height: 32px;">
                                    <i class="fa-solid fa-location-dot x-small"></i>
                                </div>
                                <div>
                                    <span class="d-block x-small text-muted text-uppercase fw-bold">Address</span>
                                    <span class="small fw-bold text-dark lh-sm d-block">{{ $student->permanent_address ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Guardians Widget --}}
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
                <div class="card-header border-0 py-3 px-4 d-flex align-items-center">
                    <h6 class="fw-bold mb-0 small text-uppercase text-muted tracking-widest">Linked Guardians</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush rounded-bottom-4">
                        @forelse($student->guardians as $guardian)
                            <div class="list-group-item px-4 py-3 border-light transition-hover">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="icon-box d-flex align-items-center justify-content-center bg-soft-primary text-primary rounded-circle" style="width: 32px; height: 32px;">
                                            <i class="fa-solid fa-user-shield small"></i>
                                        </div>
                                        <div>
                                            <h6 class="text-dark fw-bold mb-0 small">{{ $guardian->name }}</h6>
                                            <span class="x-small text-muted text-uppercase tracking-widest">{{ $guardian->relationship }}</span>
                                        </div>
                                    </div>
                                    <a href="tel:{{ $guardian->phone }}" class="action-btn border shadow-sm" title="Call Guardian">
                                        <i class="fa-solid fa-phone text-muted small"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-4 text-center">
                                <i class="fa-solid fa-user-slash text-muted opacity-25 mb-2"></i>
                                <p class="text-muted small fst-italic mb-0">No guardians linked.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Academic Data --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4 animate__animated animate__fadeInRight">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <ul class="nav nav-pills gap-2 bg-light p-1 rounded-pill w-fit-content border shadow-xs" id="profileTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active rounded-pill fw-800 text-uppercase x-small px-3 py-2 transition-bounce font-inter" data-bs-toggle="tab" data-bs-target="#tab-journey">
                                <i class="fa-solid fa-route me-1"></i> Academic Journey
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link rounded-pill fw-800 text-uppercase x-small px-3 py-2 transition-bounce font-inter" data-bs-toggle="tab" data-bs-target="#tab-history">
                                <i class="fa-solid fa-university me-1"></i> Education
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link rounded-pill fw-800 text-uppercase x-small px-3 py-2 transition-bounce font-inter" data-bs-toggle="tab" data-bs-target="#tab-skills">
                                <i class="fa-solid fa-bolt-lightning me-1"></i> Skills
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content">

                        {{-- TAB 1: ACADEMIC JOURNEY --}}
                        <div class="tab-pane fade show active" id="tab-journey">
                            @forelse($academicData as $key => $data)
                                <div class="card border-0 bg-light rounded-4 mb-4 overflow-hidden shadow-xs transition-hover animate__animated animate__fadeInUp" style="animation-delay: {{ $key * 0.1 }}s">
                                    
                                    {{-- Header with standardized icon-box --}}
                                    <div class="p-3 bg-white border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-box d-flex align-items-center justify-content-center bg-soft-primary text-primary shadow-sm border rounded-3 me-3" style="width: 48px; height: 48px;">
                                                <i class="fa-solid fa-graduation-cap"></i>
                                            </div>
                                            <div>
                                                <h5 class="fw-800 text-dark mb-0 ls-tight">{{ $data->course->title }}</h5>
                                                <div class="d-flex align-items-center mt-1">
                                                    <span class="badge bg-light text-muted border font-monospace x-small me-2">#{{ $data->course->code }}</span>
                                                    <span class="text-muted x-small fw-bold text-uppercase tracking-widest font-inter">Joined {{ $data->enrollment->enrolled_at->format('M Y') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="badge {{ $data->enrollment->status === 'active' ? 'bg-soft-success text-success border-success' : 'bg-soft-secondary text-secondary border-secondary' }} border border-opacity-25 rounded-pill px-4 py-2 x-small fw-800">
                                            <i class="fa-solid fa-circle {{ $data->enrollment->status === 'active' ? 'status-active' : '' }} me-2" style="font-size: 6px;"></i>
                                            {{ strtoupper($data->enrollment->status) }}
                                        </span>
                                    </div>

                                    <div class="p-4">
                                        <div class="row g-4">
                                            {{-- Schedule Node --}}
                                            <div class="col-md-5">
                                                <div class="p-3 rounded-4 bg-white border border-dashed h-100 shadow-xs transition-bounce">
                                                    <h6 class="x-small fw-800 text-uppercase text-muted mb-3 tracking-widest font-inter">Node Infrastructure</h6>
                                                    @if ($data->batch)
                                                        <div class="mb-3 d-flex align-items-center">
                                                            <div class="icon-box d-flex align-items-center justify-content-center bg-soft-primary text-primary rounded-3 me-3 align-self-center" style="width: 32px; height: 32px;">
                                                                <i class="fa-solid fa-hashtag small"></i>
                                                            </div>
                                                            <div>
                                                                <span class="d-block x-small text-muted fw-bold text-uppercase font-inter">Registry ID</span>
                                                                <span class="small fw-800 text-dark font-monospace">{{ $data->batch->batch_code }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-box d-flex align-items-center justify-content-center bg-soft-primary text-primary rounded-3 me-3 align-self-center" style="width: 32px; height: 32px;">
                                                                <i class="fa-solid fa-clock-rotate-left small"></i>
                                                            </div>
                                                            <div>
                                                                <span class="d-block x-small text-muted fw-bold text-uppercase font-inter">Current Shift</span>
                                                                <span class="small fw-800 text-dark">{{ ucfirst($data->batch->shift) }} Session</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Performance Matrix --}}
                                            <div class="col-md-7">
                                                <div class="p-3 rounded-4 bg-white border border-dashed h-100 shadow-xs transition-bounce">
                                                    <h6 class="x-small fw-800 text-uppercase text-muted mb-3 tracking-widest font-inter">Performance Matrix</h6>

                                                    <div class="mb-4">
                                                        <div class="d-flex justify-content-between align-items-end mb-2">
                                                            <span class="x-small fw-800 text-dark text-uppercase font-inter">Participation</span>
                                                            <span class="badge {{ $data->stats['attendance'] < 75 ? 'text-danger' : 'text-success' }} p-0 fw-800 fs-6">{{ $data->stats['attendance'] }}%</span>
                                                        </div>
                                                        <div class="progress rounded-pill shadow-xs bg-light" style="height: 8px;">
                                                            <div class="progress-bar {{ $data->stats['attendance'] < 75 ? 'bg-danger' : 'bg-success' }} progress-bar-striped progress-bar-animated" style="width: {{ $data->stats['attendance'] }}%"></div>
                                                        </div>
                                                    </div>

                                                    <div class="mb-0">
                                                        <div class="d-flex justify-content-between align-items-end mb-2">
                                                            <span class="x-small fw-800 text-dark text-uppercase font-inter">Course Progress</span>
                                                            <span class="badge text-primary p-0 fw-800 fs-6">{{ $data->stats['progress'] }}%</span>
                                                        </div>
                                                        <div class="progress rounded-pill shadow-xs bg-light" style="height: 8px;">
                                                            <div class="progress-bar bg-primary" style="width: {{ $data->stats['progress'] }}%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-white p-3 text-center border-top">
                                        <a href="#" class="btn btn-link text-decoration-none x-small fw-800 text-primary text-uppercase tracking-widest transition-bounce">
                                            View Insight Analytics <i class="fa-solid fa-arrow-right-long ms-2"></i>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 bg-light rounded-4 border border-dashed opacity-75">
                                    <i class="fa-solid fa-layer-group fa-2x mb-2 text-muted"></i>
                                    <h6 class="fw-800 text-dark">No Registry Found</h6>
                                </div>
                            @endforelse
                        </div>

                        {{-- TAB 2: EDUCATION HISTORY (Added to prevent UI breakage) --}}
                        <div class="tab-pane fade" id="tab-history">
                            <div class="text-center py-5">
                                <p class="text-muted">Education history content goes here.</p>
                            </div>
                        </div>

                        {{-- TAB 3: SKILLS (Added to prevent UI breakage) --}}
                        <div class="tab-pane fade" id="tab-skills">
                            <div class="text-center py-5">
                                <p class="text-muted">Skills content goes here.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection