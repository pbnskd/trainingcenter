@extends('layouts.app')

@section('content')
    {{-- Initializing Alpine Data --}}
    {{-- We check if a specific success message exists to switch tabs automatically --}}
    <div class="main-canvas p-4" 
         x-data="{ tab: '{{ session('success') === 'Schedule added successfully.' ? 'schedule' : (session('success') === 'Faculty assigned successfully.' ? 'faculty' : 'students') }}' }">

        {{-- Header Section --}}
        <div class="content-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h4 class="fw-800 text-dark mb-0">{{ $batch->batch_code }}</h4>
                    @php
                        $statusClass = match ($batch->status) {
                            'Upcoming' => 'bg-soft-warning text-warning',
                            'Running' => 'bg-soft-success text-success',
                            'Completed' => 'bg-soft-primary text-primary',
                            'Cancelled' => 'bg-soft-danger text-danger',
                            default => 'bg-soft-secondary text-secondary',
                        };
                    @endphp
                    <span class="badge {{ $statusClass }} px-2 py-1 rounded-pill small">
                        {{ ucfirst($batch->status) }}
                    </span>
                </div>
                <p class="text-muted small mb-0">
                    <i class="fa-solid fa-book-open me-1"></i> {{ $batch->course->title ?? $batch->course->name }}
                    <span class="mx-2">•</span>
                    <i class="fa-solid fa-clock me-1"></i> {{ ucfirst($batch->shift) }} Shift
                </p>
            </div>

            <div class="action-stack d-flex gap-2 mt-3 mt-lg-0">
                <a href="{{ route('batches.index') }}" class="btn btn-outline-secondary shadow-sm px-3 fw-bold">
                    <i class="fa-solid fa-arrow-left me-2"></i>Back
                </a>
                
                {{-- Optional: Notification Button (Based on your previous request) --}}
                <form action="{{ route('batches.notify', $batch) }}" method="POST" class="d-inline" onsubmit="return confirm('Send status update email to all students and faculty?');">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary shadow-sm px-3 fw-bold" title="Send Email Notification">
                        <i class="fa-regular fa-bell"></i>
                    </button>
                </form>

                @can('update', $batch)
                    <a href="{{ route('batches.edit', $batch) }}" class="btn btn-primary shadow-sm px-3 fw-bold">
                        <i class="fa-solid fa-gear me-2"></i>Settings
                    </a>
                @endcan
            </div>
        </div>

        <div class="row g-4">

            {{-- Left Column: Batch Overview --}}
            <div class="col-12 col-lg-4">
                <div class="bg-white rounded-4 shadow-sm border overflow-hidden p-4 h-100">
                    <h6 class="fw-bold text-uppercase letter-spacing-1 text-muted small border-bottom pb-3 mb-3">
                        Batch Overview
                    </h6>

                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small"><i class="fa-regular fa-calendar-plus me-2"></i>Start Date</span>
                            <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($batch->date_range['start_date'] ?? '')->format('M d, Y') }}</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small"><i class="fa-regular fa-calendar-check me-2"></i>End Date</span>
                            <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($batch->date_range['end_date'] ?? '')->format('M d, Y') }}</span>
                        </div>

                        <div class="p-3 bg-soft-secondary rounded-3 mt-2">
                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <span class="small fw-bold text-dark">Enrollment Capacity</span>
                                <span class="small fw-bold text-primary">{{ $batch->students->count() }} /
                                    {{ $batch->max_capacity }}</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                @php $percent = ($batch->max_capacity > 0) ? ($batch->students->count() / $batch->max_capacity) * 100 : 0; @endphp
                                <div class="progress-bar {{ $percent >= 100 ? 'bg-danger' : 'bg-primary' }}"
                                    role="progressbar" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>

                        @if ($batch->description)
                            <div class="mt-2">
                                <span class="text-muted small d-block mb-1">Description</span>
                                <p class="small text-dark bg-light p-3 rounded mb-0 border">
                                    {{ $batch->description }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column: Tabs --}}
            <div class="col-12 col-lg-8">
                <div class="bg-white rounded-4 shadow-sm border overflow-hidden h-100">

                    {{-- Tab Navigation --}}
                    <div class="d-flex border-bottom bg-light px-4 pt-3">
                        <button @click="tab = 'students'" :class="tab === 'students' ? 'active-tab' : 'text-muted'"
                            class="pb-3 px-3 fw-bold small bg-transparent border-0 transition-all cursor-pointer">
                            <i class="fa-solid fa-users me-1"></i> Students
                        </button>
                        <button @click="tab = 'schedule'" :class="tab === 'schedule' ? 'active-tab' : 'text-muted'"
                            class="pb-3 px-3 fw-bold small bg-transparent border-0 transition-all cursor-pointer">
                            <i class="fa-regular fa-calendar-days me-1"></i> Schedule
                        </button>
                        <button @click="tab = 'faculty'" :class="tab === 'faculty' ? 'active-tab' : 'text-muted'"
                            class="pb-3 px-3 fw-bold small bg-transparent border-0 transition-all cursor-pointer">
                            <i class="fa-solid fa-chalkboard-user me-1"></i> Faculty
                        </button>
                    </div>

                    <div class="p-4">

                        {{-- Tab: Students --}}
                        <div x-show="tab === 'students'" x-cloak class="animate__animated animate__fadeIn">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-dark mb-0">Enrolled Students</h6>
                                <a href="{{ route('batches.enroll', $batch) }}" class="btn btn-sm btn-primary shadow-sm">
                                    <i class="fa-solid fa-user-plus me-1"></i> Enroll Student
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 border rounded">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-3 py-2">Name</th>
                                            <th class="py-2">Status</th>
                                            <th class="py-2">Enrolled Date</th>
                                            <th class="py-2 text-end pe-4">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($batch->students as $student)
                                            <tr>
                                                <td class="ps-3">
                                                    <div class="fw-bold text-dark">{{ $student->user->name ?? 'Unknown Student' }}</div>
                                                    <div class="text-muted ultra-small">{{ $student->user->email ?? '' }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-soft-success text-success border border-success-subtle rounded-pill">
                                                        {{ ucfirst($student->pivot->status) }}
                                                    </span>
                                                </td>
                                                <td class="text-muted small">
                                                    {{ \Carbon\Carbon::parse($student->pivot->enrolled_at)->format('M d, Y') }}
                                                </td>
                                                <td class="text-end pe-3">
                                                    <div class="d-flex justify-content-end gap-1">
                                                        {{-- Show/Profile --}}
                                                        <a href="{{ route('students.show', $student) }}" 
                                                           class="btn btn-sm btn-soft-info" title="View Profile">
                                                            <i class="fa-solid fa-eye"></i>
                                                        </a>

                                                        {{-- Transfer --}}
                                                        <a href="{{ route('batches.transfer', ['batch' => $batch, 'student' => $student]) }}"
                                                           class="btn btn-sm btn-soft-primary" title="Transfer Student">
                                                            <i class="fa-solid fa-right-left"></i>
                                                        </a>

                                                        {{-- Edit Enrollment --}}
                                                        <a href="{{ route('students.edit', $student) }}" 
                                                           class="btn btn-sm btn-soft-secondary" title="Edit Student">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </a>

                                                        {{-- Delete/Unenroll --}}
                                                        <form action="{{ route('batches.unenroll', ['batch' => $batch, 'student' => $student]) }}" method="POST" 
                                                              onsubmit="return confirm('Are you sure you want to unenroll this student from this batch?');" 
                                                              style="display: inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-soft-danger" title="Unenroll Student">
                                                                <i class="fa-solid fa-trash-can"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5 text-muted small">
                                                    <i class="fa-solid fa-user-slash d-block fs-4 mb-2 opacity-25"></i>
                                                    No students enrolled yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Tab: Schedule --}}
                        <div x-show="tab === 'schedule'" x-cloak class="animate__animated animate__fadeIn">
                            <div class="mb-4">
                                @forelse($batch->schedules as $schedule)
                                    <div class="d-flex align-items-center p-3 border rounded mb-2 bg-white shadow-sm hover-elevate">
                                        <div class="bg-soft-primary text-primary fw-bold rounded px-2 py-2 me-3 text-center"
                                            style="min-width: 60px; font-size: 0.8rem;">
                                            {{ substr($schedule->day_of_week, 0, 3) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold text-dark">{{ $schedule->description }}</div>
                                            <div class="text-muted small">{{ $schedule->hours_per_day }} Hours</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 bg-light rounded border border-dashed">
                                        <p class="text-muted small mb-0">No schedule defined.</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="bg-soft-secondary p-3 rounded-3 border">
                                <h6 class="fw-bold text-dark small mb-3"><i class="fa-solid fa-plus me-1"></i> Add Schedule Slot</h6>
                                <form action="{{ route('batches.schedule.store', $batch) }}" method="POST" class="row g-2 align-items-end">
                                    @csrf
                                    <div class="col-md-3">
                                        <label class="small text-muted mb-1">Day</label>
                                        <select name="day_of_week" class="form-select form-select-sm border-0 shadow-sm" required>
                                            <option value="">Select Day</option>
                                            @foreach (['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                                <option value="{{ $day }}" {{ old('day_of_week') == $day ? 'selected' : '' }}>
                                                    {{ $day }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="small text-muted mb-1">Hours</label>
                                        <input type="number" name="hours_per_day" step="0.5" value="1" min="0.5" 
                                               class="form-control form-control-sm border-0 shadow-sm">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="small text-muted mb-1">Activity</label>
                                        <input type="text" name="description" placeholder="e.g. Lab Session" 
                                               class="form-control form-control-sm border-0 shadow-sm">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-sm btn-dark w-100 shadow-sm">Add</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Tab: Faculty --}}
                        <div x-show="tab === 'faculty'" x-cloak class="animate__animated animate__fadeIn">
                            <div class="row g-3 mb-4">
                                @forelse($batch->faculty as $assign)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 border rounded bg-white shadow-sm hover-elevate h-100">
                                            <div class="thumbnail-box rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3 shadow-sm"
                                                style="width: 48px; height: 48px;">
                                                {{ substr($assign->faculty->name ?? 'F', 0, 1) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold text-dark">{{ $assign->faculty->name ?? 'Unknown' }}</div>
                                                <div class="mt-1">
                                                    @if ($assign->assignable_type === 'App\Models\CurriculumItem' && $assign->assignable)
                                                        <span class="badge bg-soft-info text-info border border-info-subtle small">
                                                            <i class="fa-solid fa-layer-group me-1"></i> Unit:
                                                            {{ $assign->assignable->title }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-soft-secondary text-secondary border border-secondary-subtle small">
                                                            <i class="fa-solid fa-globe me-1"></i> Entire Course
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-5 bg-light rounded border border-dashed">
                                        <p class="text-muted small mb-0">No faculty members assigned yet.</p>
                                    </div>
                                @endforelse
                            </div>

                            {{-- Assign Faculty Form --}}
                            <div class="bg-soft-secondary p-4 rounded-3 border">
                                <h6 class="fw-bold text-dark small mb-3 text-uppercase letter-spacing-1">Assign Faculty</h6>
                                <form action="{{ route('batches.faculty.store', $batch) }}" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="small text-muted fw-bold mb-1">Faculty Member</label>
                                            <select name="faculty_id" class="form-select border-0 shadow-sm" required>
                                                <option value="">-- Select --</option>
                                                @foreach ($availableFaculty as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="small text-muted fw-bold mb-1">Assign to Unit (Optional)</label>
                                            <select name="unit_id" class="form-select border-0 shadow-sm">
                                                <option value="">Full Course</option>
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}">{{ $unit->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="small text-muted fw-bold mb-1">Role/Notes</label>
                                            <input type="text" name="instructions" class="form-control border-0 shadow-sm"
                                                placeholder="e.g. Lead Trainer">
                                        </div>
                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-dark fw-bold px-4 shadow-sm">Assign Faculty</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .active-tab {
            color: #3b82f6 !important;
            border-bottom: 2px solid #3b82f6 !important;
        }

        .fw-800 { font-weight: 800; }
        .letter-spacing-1 { letter-spacing: 0.5px; }
        .ultra-small { font-size: 0.75rem; }

        /* Soft Backgrounds */
        .bg-soft-primary { background-color: #f0f7ff; }
        .bg-soft-success { background-color: #ecfdf5; }
        .bg-soft-warning { background-color: #fffbeb; }
        .bg-soft-danger { background-color: #fef2f2; }
        .bg-soft-secondary { background-color: #f8fafc; }
        
        .btn-soft-info { background-color: #e0f2fe; color: #0284c7; border:none; }
        .btn-soft-info:hover { background-color: #0284c7; color: #fff; }

        .btn-soft-primary { background-color: #eff6ff; color: #3b82f6; border:none; }
        .btn-soft-primary:hover { background-color: #3b82f6; color: #fff; }

        .btn-soft-secondary { background-color: #f1f5f9; color: #64748b; border:none; }
        .btn-soft-secondary:hover { background-color: #64748b; color: #fff; }

        .btn-soft-danger { background-color: #fef2f2; color: #ef4444; border:none; }
        .btn-soft-danger:hover { background-color: #ef4444; color: #fff; }

        .hover-elevate { transition: transform 0.2s, box-shadow 0.2s; }
        .hover-elevate:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .05) !important; }

        .transition-all { transition: all 0.2s; }
        .cursor-pointer { cursor: pointer; }
    </style>
@endsection