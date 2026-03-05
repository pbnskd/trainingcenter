@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">

    {{-- Header Section --}}
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4 gap-3">
        <div>
            <h4 class="fw-800 text-dark mb-1">Batch Management</h4>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill">
                    <i class="fa-solid fa-layer-group me-1"></i> {{ $batches->total() }} Batches
                </span>
                <span class="text-muted small">Monitor schedules and student enrollment</span>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row gap-3 mt-3 mt-lg-0">
            {{-- Search Form --}}
            <form action="{{ route('batches.index') }}" method="GET" id="search-form" class="search-capsule d-flex align-items-center px-3 py-1">
                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="form-control border-0 bg-transparent shadow-none" 
                       placeholder="Search batch code..." oninput="debounceSearch()" autocomplete="off">
            </form>

            @can('create', App\Models\Batch::class)
                <a class="btn btn-premium rounded-pill shadow-sm px-4 fw-bold" href="{{ route('batches.create') }}">
                    <i class="fa-solid fa-plus-circle me-2"></i>New Batch
                </a>
            @endcan
        </div>
    </div>

    {{-- Premium Grid --}}
    <div class="premium-grid">
        
        {{-- Grid Header --}}
        <div class="grid-header">
            <div class="ps-2">BATCH IDENTITY</div>
            <div>TIMELINE & SHIFT</div>
            <div>ENROLLMENT STATUS</div>
            <div class="text-end pe-2">CONTROL</div>
        </div>

        {{-- Grid Body --}}
        <div class="grid-body">
            @forelse ($batches as $key => $batch)
                <div class="grid-row animate__animated animate__fadeInUp" style="animation-delay: {{ $key * 0.05 }}s">
                    
                    {{-- Col 1: Identity (Batch Code + Course) --}}
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-box border shadow-sm bg-soft-secondary text-secondary d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-people-roof"></i>
                        </div>

                        <div class="overflow-hidden">
                            <div class="fw-bold text-truncate text-dark mb-0 transition-hover">{{ $batch->batch_code }}</div>
                            <div class="text-muted small text-truncate mt-1" style="max-width: 200px;">
                                {{ $batch->course->name ?? 'Unassigned Course' }}
                            </div>
                        </div>
                    </div>

                    {{-- Col 2: Timeline --}}
                    <div>
                        <div class="d-flex flex-column gap-1">
                            <span class="text-dark small fw-semibold">
                                <i class="fa-regular fa-calendar me-1 text-muted"></i>
                                {{ \Carbon\Carbon::parse($batch->date_range['start_date'] ?? '')->format('M d') }} 
                                - 
                                {{ \Carbon\Carbon::parse($batch->date_range['end_date'] ?? '')->format('M d, Y') }}
                            </span>
                            <span class="badge bg-light text-dark border mt-1" style="width: fit-content;">
                                <i class="fa-solid fa-clock me-1 text-muted"></i> {{ ucfirst($batch->shift) }}
                            </span>
                        </div>
                    </div>

                    {{-- Col 3: Status & Capacity --}}
                    <div>
                        <div class="d-flex flex-column gap-1" style="max-width: 140px;">
                            {{-- Status Badge --}}
                            @php
                                $statusClass = match($batch->status) {
                                    'active', 'Running' => 'text-success bg-soft-success border-success',
                                    'completed', 'Completed' => 'text-primary bg-soft-primary border-primary',
                                    'cancelled', 'Cancelled' => 'text-danger bg-soft-danger border-danger',
                                    default => 'text-warning bg-soft-warning border-warning' // pending/upcoming
                                };
                                $statusLabel = ucfirst($batch->status);
                            @endphp
                            <div>
                                <span class="badge {{ $statusClass }} border px-2 py-1 rounded-pill">
                                    {{ $statusLabel }}
                                </span>
                            </div>

                            {{-- Capacity Progress --}}
                            <div class="mt-2">
                                <div class="d-flex justify-content-between small text-muted mb-1" style="font-size: 0.75rem;">
                                    <span>Capacity</span>
                                    <span class="{{ $batch->students_count >= $batch->max_capacity ? 'text-danger fw-bold' : '' }}">
                                        {{ $batch->students_count }}/{{ $batch->max_capacity }}
                                    </span>
                                </div>
                                <div class="progress rounded-pill shadow-sm" style="height: 5px; background-color: #f1f5f9;">
                                    @php $percent = ($batch->max_capacity > 0) ? ($batch->students_count / $batch->max_capacity) * 100 : 0; @endphp
                                    <div class="progress-bar {{ $percent >= 100 ? 'bg-danger' : 'bg-primary' }}" 
                                         role="progressbar" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Col 4: Actions --}}
                    <div class="d-flex justify-content-md-end gap-2 pe-2">
                        {{-- View --}}
                        <a href="{{ route('batches.show', $batch) }}" class="action-btn border shadow-sm" title="View Details">
                            <i class="fa-solid fa-arrow-right-to-bracket text-muted" style="font-size: 0.85rem;"></i>
                        </a>

                        {{-- Attendance (Quick Link) --}}
                        <a href="{{ route('batches.attendance', $batch) }}" class="action-btn border shadow-sm" title="Mark Attendance">
                            <i class="fa-solid fa-clipboard-user text-success" style="font-size: 0.85rem;"></i>
                        </a>

                        {{-- Edit --}}
                        @can('update', $batch)
                            <a href="{{ route('batches.edit', $batch) }}" class="action-btn border shadow-sm" title="Settings">
                                <i class="fa-solid fa-gear text-primary" style="font-size: 0.85rem;"></i>
                            </a>
                        @endcan

                        {{-- Delete --}}
                        @can('delete', $batch)
                            <button onclick="handleDelete('{{ $batch->id }}')" class="action-btn border shadow-sm" title="Delete">
                                <i class="fa-solid fa-trash-can text-danger" style="font-size: 0.85rem;"></i>
                            </button>
                            <form id="delete-{{ $batch->id }}" action="{{ route('batches.destroy', $batch) }}" method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="p-5 text-center w-100">
                    <div class="display-1 text-muted opacity-25 mb-3"><i class="fa-solid fa-layer-group"></i></div>
                    <h5 class="fw-bold text-dark">No batches found</h5>
                    <p class="small text-muted mb-0">Create a new batch to start scheduling classes.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Footer/Pagination --}}
    @if($batches->hasPages())
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 gap-3">
            <p class="text-muted small mb-0">
                Showing <strong>{{ $batches->firstItem() ?? 0 }}</strong> to <strong>{{ $batches->lastItem() ?? 0 }}</strong> of <strong>{{ $batches->total() }}</strong> batches
            </p>
            <div>
                {!! $batches->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    @endif

</div>

{{-- Inline override just for this page's specific grid column needs --}}
<style>
    @media (min-width: 768px) {
        .grid-header, .grid-row { 
            grid-template-columns: 2fr 1.5fr 1.2fr 1fr; 
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
    
    // Delete Confirmation
    function handleDelete(id) {
        if(typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Delete Batch?',
                text: "This action cannot be undone. All enrollments will be detached.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it',
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
            if(confirm('Are you sure you want to delete this batch? All enrollments will be detached.')) {
                document.getElementById('delete-' + id).submit();
            }
        }
    }
</script>
@endsection