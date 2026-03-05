@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">

    {{-- Header Section --}}
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4 gap-3">
        <div>
            <h4 class="fw-800 text-dark mb-1">Course Catalog</h4>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill">
                    <i class="fa-solid fa-graduation-cap me-1"></i> {{ $courses->total() }} Courses
                </span>
                <span class="text-muted small">Manage and organize your learning modules</span>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row gap-3 mt-3 mt-lg-0">
            <form action="{{ route('courses.index') }}" method="GET" id="search-form" class="search-capsule d-flex align-items-center px-3 py-1">
                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="form-control border-0 bg-transparent shadow-none" 
                       placeholder="Search by title or code..." oninput="debounceSearch()" autocomplete="off">
            </form>

            <a class="btn btn-premium rounded-pill shadow-sm px-4 fw-bold" href="{{ route('courses.create') }}">
                <i class="fa-solid fa-plus-circle me-2"></i>Create Course
            </a>
        </div>
    </div>

    {{-- Premium Grid --}}
    <div class="premium-grid">
        
        {{-- Grid Header --}}
        <div class="grid-header">
            <div class="ps-2">COURSE DETAILS</div>
            <div>CLASSIFICATION</div>
            <div>PRICING MODEL</div>
            <div class="text-end pe-2">CONTROL</div>
        </div>

        {{-- Grid Body --}}
        <div class="grid-body">
            @forelse ($courses as $key => $course)
                <div class="grid-row animate__animated animate__fadeInUp" style="animation-delay: {{ $key * 0.05 }}s">
                    
                    {{-- Identity --}}
                    <div class="d-flex align-items-center gap-3">
                        {{-- Swapped your custom thumbnail for our global avatar-box which has hover animations built-in --}}
                        <div class="avatar-box border shadow-sm">
                            @if ($course->thumbnail_url)
                                <img src="{{ $course->thumbnail_url }}" alt="Thumbnail">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-soft-secondary text-secondary" style="border-radius: 12px;">
                                    <i class="fa-solid fa-book-bookmark"></i>
                                </div>
                            @endif
                        </div>

                        <div class="overflow-hidden">
                            <div class="fw-bold text-truncate text-dark mb-0 transition-hover">{{ $course->title }}</div>
                            <span class="badge bg-light text-primary border font-monospace mt-1 x-small">{{ $course->code }}</span>
                        </div>
                    </div>

                    {{-- Category & Status --}}
                    <div>
                        <div class="d-flex flex-column gap-1">
                            <span class="text-dark small fw-semibold">
                                <i class="fa-solid fa-tags me-1 text-muted"></i> {{ $course->type->name ?? 'General' }}
                            </span>
                            <div>
                                @php
                                    $statusClass = [
                                        'published' => 'text-success bg-soft-success',
                                        'draft' => 'text-muted bg-soft-secondary',
                                        'archived' => 'text-danger bg-soft-danger'
                                    ][$course->status] ?? 'text-secondary bg-soft-secondary';
                                    
                                    $statusIcon = [
                                        'published' => 'fa-circle-check',
                                        'draft' => 'fa-file-signature',
                                        'archived' => 'fa-box-archive'
                                    ][$course->status] ?? 'fa-circle-dot';
                                @endphp
                                <span class="badge {{ $statusClass }} border border-light rounded-pill px-2 py-1 x-small fw-bold">
                                    <i class="fa-solid {{ $statusIcon }} me-1"></i>{{ ucfirst($course->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Pricing --}}
                    <div>
                        @if ($course->price > 0)
                            <div class="fw-bold text-dark font-monospace">${{ number_format($course->price / 100, 2) }}</div>
                            @if ($course->compare_at_price > $course->price)
                                <div class="text-muted small text-decoration-line-through">${{ number_format($course->compare_at_price / 100, 2) }}</div>
                            @endif
                        @else
                            <span class="badge bg-soft-success text-success border border-success px-2 py-1">COMPLIMENTARY</span>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex justify-content-md-end gap-2 pe-2">
                        <a href="{{ route('courses.show', $course->id) }}" class="action-btn border shadow-sm" title="View">
                            <i class="fa-solid fa-arrow-right-to-bracket text-muted" style="font-size: 0.85rem;"></i>
                        </a>
                        <a href="{{ route('courses.edit', $course->id) }}" class="action-btn border shadow-sm" title="Settings">
                            <i class="fa-solid fa-gear text-primary" style="font-size: 0.85rem;"></i>
                        </a>
                        <button onclick="handleDelete('{{ $course->id }}')" class="action-btn border shadow-sm" title="Delete">
                            <i class="fa-solid fa-trash-can text-danger" style="font-size: 0.85rem;"></i>
                        </button>
                        <form id="delete-{{ $course->id }}" action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-none">
                            @csrf @method('DELETE')
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-5 text-center w-100">
                    <div class="display-1 text-muted opacity-25 mb-3"><i class="fa-solid fa-folder-open"></i></div>
                    <h5 class="fw-bold text-dark">No courses found</h5>
                    <p class="small text-muted mb-0">Try adjusting your search or create a new course to get started.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Footer/Pagination --}}
    @if($courses->hasPages())
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 gap-3">
            <p class="text-muted small mb-0">
                Showing <strong>{{ $courses->firstItem() ?? 0 }}</strong> to <strong>{{ $courses->lastItem() ?? 0 }}</strong> of <strong>{{ $courses->total() }}</strong> courses
            </p>
            <div>
                {!! $courses->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    @endif

</div>

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
                title: 'Delete Course?',
                text: "This action cannot be undone.",
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
            // Fallback if SweetAlert isn't loaded
            if(confirm('Are you sure you want to delete this course? This action cannot be undone.')) { 
                document.getElementById('delete-' + id).submit(); 
            }
        }
    }
</script>
@endsection