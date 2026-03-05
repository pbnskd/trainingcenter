@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">

    {{-- Header Section --}}
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4 gap-3">
        <div>
            <h4 class="fw-800 text-dark mb-1">Students & Enrolled Courses</h4>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill">
                    <i class="fa-solid fa-user-graduate me-1"></i> 
                    {{ method_exists($students, 'total') ? $students->total() : $students->count() }} Students
                </span>
                @if(request('search'))
                    <span class="text-muted small">• Search results for "{{ request('search') }}"</span>
                    <a href="{{ url()->current() }}" class="text-danger small text-decoration-none ms-2 fw-semibold">Clear</a>
                @endif
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row gap-3 mt-3 mt-lg-0">
            <form action="{{ url()->current() }}" method="GET" id="search-form" class="search-capsule d-flex align-items-center px-3 py-1">
                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="form-control border-0 bg-transparent shadow-none" 
                       placeholder="Quick find student..." oninput="debounceSearch()" autocomplete="off">
            </form>

            {{-- Create Enrollment / Student Button --}}
            <a class="btn btn-premium rounded-pill shadow-sm px-4 fw-bold" href="{{ route('enrollments.create') }}">
                <i class="fa-solid fa-plus-circle me-2"></i>Enroll Student
            </a>
        </div>
    </div>

    {{-- Premium Grid --}}
    <div class="premium-grid">
        
        {{-- Grid Header --}}
        <div class="grid-header">
            <div class="ps-2">STUDENT IDENTITY</div>
            <div>ENROLLED COURSES</div>
            <div>STATUS</div>
            <div class="text-end pe-2">ACTIONS</div>
        </div>

        {{-- Grid Body --}}
        <div class="grid-body">
            @forelse ($students as $key => $student)
                <div class="grid-row animate__animated animate__fadeInUp" style="animation-delay: {{ $key * 0.05 }}s">
                    
                    {{-- Column 1: Identity --}}
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-box border shadow-sm">
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-soft-primary text-primary fw-bold" style="border-radius: 12px; font-size: 1.1rem;">
                                {{ strtoupper(substr($student->user->name ?? 'S', 0, 1)) }}
                            </div>
                            <div class="online-status {{ ($student->status ?? 'active') === 'active' ? 'status-active' : 'status-inactive' }}" title="{{ ucfirst($student->status ?? 'Active') }}"></div>
                        </div>
                        
                        <div class="overflow-hidden">
                            <div class="fw-bold text-truncate text-dark mb-0 transition-hover">{{ $student->user->name }}</div>
                            <div class="text-muted small text-truncate mt-1">{{ $student->user->email }}</div>
                        </div>
                    </div>

                    {{-- Column 2: Enrollments --}}
                    <div>
                        <div class="d-flex flex-wrap gap-1">
                            @if($student->enrollments->count() > 0)
                                @foreach($student->enrollments as $enrollment)
                                    <span class="role-tag bg-soft-secondary text-dark-50 border">
                                        {{ $enrollment->course->title }} 
                                        <span class="opacity-75 fw-normal">({{ ucfirst($enrollment->status) }})</span>
                                    </span>
                                @endforeach
                            @else
                                <span class="text-muted small fst-italic">No enrollments</span>
                            @endif
                        </div>
                    </div>

                    {{-- Column 3: Status --}}
                    <div>
                        @if(($student->status ?? 'active') === 'active')
                            <span class="badge bg-soft-success text-success border border-success rounded-pill px-3 py-1">
                                <i class="fa-solid fa-circle-check me-1 small"></i> Active
                            </span>
                        @else
                            <span class="badge bg-soft-danger text-danger border border-danger rounded-pill px-3 py-1">
                                <i class="fa-solid fa-circle-xmark me-1 small"></i> {{ ucfirst($student->status) }}
                            </span>
                        @endif
                    </div>

                    {{-- Column 4: Actions --}}
                    <div class="d-flex justify-content-md-end gap-2 pe-2">
                        
                        {{-- Action: Enroll / Manage Course --}}
                        @if($student->enrollments->isNotEmpty())
                            <a href="{{ route('enrollments.edit', $student->enrollments->first()->id) }}" class="action-btn border shadow-sm" title="Manage Enrollment">
                                <i class="fa-solid fa-book-open-reader text-info" style="font-size: 0.85rem;"></i>
                            </a>
                        @else
                            <a href="{{ route('enrollments.create', ['student_id' => $student->id]) }}" class="action-btn border shadow-sm" title="Enroll Now">
                                <i class="fa-solid fa-plus text-success" style="font-size: 0.85rem;"></i>
                            </a>
                        @endif

                        {{-- Action: Show Student --}}
                        <a href="{{ route('students.show', $student->id) }}" class="action-btn border shadow-sm" title="View Student">
                            <i class="fa-solid fa-eye text-muted" style="font-size: 0.85rem;"></i>
                        </a>

                        {{-- Action: Edit Student --}}
                        <a href="{{ route('students.edit', $student->id) }}" class="action-btn border shadow-sm" title="Edit Student">
                            <i class="fa-solid fa-pen text-primary" style="font-size: 0.85rem;"></i>
                        </a>

                        {{-- Action: Delete Student --}}
                        <button onclick="handleDelete('{{ $student->id }}')" class="action-btn border shadow-sm" title="Remove Student">
                            <i class="fa-solid fa-trash-can text-danger" style="font-size: 0.85rem;"></i>
                        </button>
                        <form id="delete-{{ $student->id }}" action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-none">
                            @csrf 
                            @method('DELETE')
                        </form>

                    </div>
                </div>
            @empty
                <div class="p-5 text-center w-100">
                    <div class="display-1 text-muted opacity-25 mb-3"><i class="fa-solid fa-users-slash"></i></div>
                    <h5 class="fw-bold text-dark">No students found</h5>
                    <p class="small text-muted mb-0">Adjust your search or register a new student to get started.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    @if(method_exists($students, 'links') && $students->hasPages())
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 gap-3">
            <p class="text-muted small mb-0">
                Displaying <strong>{{ $students->firstItem() ?? 0 }}</strong> to <strong>{{ $students->lastItem() ?? 0 }}</strong> of <strong>{{ $students->total() }}</strong> students
            </p>
            <div>
                {!! $students->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    @endif

</div>

{{-- Inline override just for this page's specific grid column needs --}}
<style>
    @media (min-width: 768px) {
        .grid-header, .grid-row { 
            grid-template-columns: 1.5fr 1.5fr 1fr 1.5fr; 
        }
    }
</style>

<script>
    // Debounce Search
    let timeout = null;
    function debounceSearch() {
        clearTimeout(timeout);
        timeout = setTimeout(() => { document.getElementById('search-form').submit(); }, 600);
    }
    
    // Delete Confirmation with SweetAlert
    function handleDelete(id) {
        if(typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Remove Student?',
                text: "This action cannot be undone and will delete their enrollment records.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, remove them',
                customClass: {
                    confirmButton: 'btn btn-danger rounded-pill px-4',
                    cancelButton: 'btn btn-light border rounded-pill px-4 ms-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-' + id).submit();
                }
            });
        } else {
            // Fallback
            if(confirm('Are you sure you want to remove this student? This action cannot be undone.')) { 
                document.getElementById('delete-' + id).submit(); 
            }
        }
    }
</script>
@endsection