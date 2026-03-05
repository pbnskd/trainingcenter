@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-800 text-dark mb-1">Student Directory</h2>
            <p class="text-muted small mb-0">Manage student records, academic status, and profiles.</p>
        </div>

        <div class="d-flex flex-column flex-md-row gap-3 mt-3 mt-md-0">
            {{-- Optional: If you want to add the search capsule here later --}}
            {{-- <form action="..." class="search-capsule d-flex align-items-center px-3 py-1">
                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                <input type="text" class="form-control border-0 shadow-none bg-transparent" placeholder="Search students...">
            </form> --}}

            @can('create', App\Models\Student::class)
                <a href="{{ route('students.create') }}" class="btn btn-premium rounded-pill shadow-sm px-4">
                    <i class="fa-solid fa-plus me-2"></i>Register Student
                </a>
            @endcan
        </div>
    </div>

    {{-- Filters/Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-soft-primary text-primary rounded-3 p-2 me-3">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                    <div>
                        <p class="text-muted x-small fw-bold text-uppercase mb-0">Total Students</p>
                        <h5 class="fw-bold mb-0 text-dark">{{ $students->total() }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Premium Grid --}}
    <div class="premium-grid">
        {{-- Grid Header (Hidden on mobile via CSS) --}}
        <div class="grid-header">
            <div class="ps-2">STUDENT INFO</div>
            <div>REGISTRATION NO.</div>
            <div>STATUS</div>
            <div class="text-end pe-2">ACTIONS</div>
        </div>

        {{-- Grid Body --}}
        <div class="grid-body">
            @forelse ($students as $key => $student)
                {{-- Add animation delay to stagger the row rendering --}}
                <div class="grid-row animate__animated animate__fadeInUp" style="animation-delay: {{ $key * 0.05 }}s">
                    
                    {{-- Column 1: Identity --}}
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-box">
                            @if ($student->user->avatar)
                                <img src="{{ asset('storage/' . $student->user->avatar) }}" alt="Avatar">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-soft-primary text-primary fw-bold" style="border-radius: 12px; font-size: 1.1rem;">
                                    {{ substr($student->user->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="online-status {{ $student->user->status ? 'status-active' : 'status-inactive' }}"></div>
                        </div>
                        <div>
                            <div class="fw-bold text-dark text-truncate">{{ $student->user->name }}</div>
                            <div class="text-muted x-small text-truncate">{{ $student->user->email }}</div>
                        </div>
                    </div>

                    {{-- Column 2: Reg Number --}}
                    <div>
                        <span class="role-tag bg-soft-secondary text-dark-50 font-monospace" style="font-size: 11px;">
                            #{{ $student->registration_number }}
                        </span>
                    </div>

                    {{-- Column 3: Status --}}
                    <div>
                        @php
                            $statusColors = [
                                'enrolled'  => 'bg-soft-success text-success border-success',
                                'graduated' => 'bg-soft-primary text-primary border-primary',
                                'suspended' => 'bg-soft-danger text-danger border-danger',
                                'alumni'    => 'bg-soft-warning text-warning border-warning', // Switched to soft warning to stand out
                            ];
                            // Reusing the standard badge border utilities from Bootstrap combined with your custom soft colors
                            $statusClass = $statusColors[$student->academic_status] ?? 'bg-soft-secondary text-secondary border-secondary';
                        @endphp
                        <span class="role-tag border {{ $statusClass }}">
                            {{ $student->academic_status }}
                        </span>
                    </div>

                    {{-- Column 4: Actions --}}
                    <div class="d-flex justify-content-md-end align-items-center gap-2 pe-2">
                        
                        {{-- Email Notification Button --}}
                        <form action="{{ route('students.notify.status', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Send an email notification regarding their {{ $student->academic_status }} status?');">
                            @csrf
                            <button type="submit" class="action-btn border shadow-sm" title="Email {{ ucfirst($student->academic_status) }} Status">
                                <i class="fa-solid fa-paper-plane text-info" style="font-size: 0.85rem;"></i>
                            </button>
                        </form>
                        
                        {{-- View Button --}}
                        <a href="{{ route('students.show', $student->id) }}" class="action-btn border shadow-sm" title="View Profile">
                            <i class="fa-solid fa-eye text-muted" style="font-size: 0.85rem;"></i>
                        </a>

                        {{-- Edit Button --}}
                        @can('update', $student)
                            <a href="{{ route('students.edit', $student->id) }}" class="action-btn border shadow-sm" title="Edit Record">
                                <i class="fa-solid fa-pen-to-square text-primary" style="font-size: 0.85rem;"></i>
                            </a>
                        @endcan

                        {{-- Delete Button --}}
                        @can('delete', $student)
                            <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive this record?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn border shadow-sm" title="Delete Record">
                                    <i class="fa-solid fa-trash text-danger" style="font-size: 0.85rem;"></i>
                                </button>
                            </form>
                        @endcan
                        
                    </div>
                </div>
            @empty
                <div class="p-5 text-center w-100">
                    <div class="text-muted opacity-50 mb-3"><i class="fa-solid fa-folder-open fa-3x"></i></div>
                    <h5 class="fw-bold text-dark">No student records found</h5>
                    <p class="text-muted mb-0">The directory is currently empty or no students match your search.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    @if($students->hasPages())
        <div class="d-flex justify-content-end mt-4">
            {{ $students->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>

{{-- Note: I removed the custom inline <style> block because 
     all those classes (bg-soft-*, fw-800, icon-box, etc.) 
     are now properly defined in your _layout.scss file! --}}
@endsection