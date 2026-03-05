@extends('layouts.app')

@push('styles')
<style>
    :root {
        --primary-soft: #e0e7ff;
        --primary-dark: #3730a3;
        --gray-soft: #f9fafb;
    }

    /* Card Styling */
    .card-modern {
        background: white;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        border-radius: 12px;
        transition: all 0.2s ease;
    }

    /* Syllabus Styling */
    .syllabus-section {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 0.75rem;
        overflow: hidden;
        background: white;
    }
    
    .syllabus-btn {
        background-color: #fff;
        color: #334155;
        font-weight: 600;
        padding: 1rem 1.25rem;
        transition: background-color 0.2s;
        border: none;
        width: 100%;
        text-align: left;
        display: flex;
        align-items: center;
    }
    .syllabus-btn:hover {
        background-color: #f8fafc;
    }
    .syllabus-btn:not(.collapsed) {
        background-color: #f1f5f9;
        color: #0f172a;
        box-shadow: none;
    }

    /* Lesson Items */
    .lesson-item {
        border-left: 3px solid transparent;
        transition: all 0.2s;
        padding: 0.75rem 1.25rem;
        border-top: 1px solid #f1f5f9;
        cursor: pointer;
    }
    .lesson-item:hover {
        background-color: #f8fafc;
        border-left-color: #6366f1;
    }
    
    /* Thumbnail */
    .course-thumbnail-container {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        aspect-ratio: 16/9;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        background-color: #cbd5e1;
    }
</style>
@endpush

@section('content')
{{-- Added generic background class to replace the body CSS --}}
<div class="container-fluid bg-light py-5 min-vh-100">
    <div class="container">
        {{-- Header Area --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small fw-medium">
                        <li class="breadcrumb-item"><a href="{{ route('courses.index') }}" class="text-decoration-none text-muted">Courses</a></li>
                        <li class="breadcrumb-item active text-dark" aria-current="page">Overview</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark m-0">{{ $course->title }}</h2>
                @if($course->summary)
                    <p class="text-muted mt-1 mb-0">{{ $course->summary }}</p>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('courses.edit', $course) }}" class="btn btn-dark shadow-sm px-4 fw-bold rounded-pill">
                    <i class="fa-solid fa-pen-nib me-2"></i>Edit Course
                </a>
            </div>
        </div>

        <div class="row g-4">
            
            {{-- LEFT COLUMN --}}
            <div class="col-lg-8">
                
                {{-- Stats Row --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="stat-card shadow-sm card-modern p-3 d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                                <i class="fa-solid fa-layer-group fa-lg"></i>
                            </div>
                            <div>
                                <div class="small text-muted fw-bold text-uppercase">Level</div>
                                <div class="h5 fw-bold mb-0">{{ ucfirst(str_replace('_', ' ', $course->level ?? 'All Levels')) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card shadow-sm card-modern p-3 d-flex align-items-center gap-3">
                            <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                                <i class="fa-solid fa-book-open fa-lg"></i>
                            </div>
                            <div>
                                <div class="small text-muted fw-bold text-uppercase">Structure</div>
                                <div class="h5 fw-bold mb-0">{{ $course->syllabus->count() }} Sections</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card shadow-sm card-modern p-3 d-flex align-items-center gap-3">
                            <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                                <i class="fa-solid fa-tag fa-lg"></i>
                            </div>
                            <div>
                                <div class="small text-muted fw-bold text-uppercase">Price</div>
                                <div class="h5 fw-bold mb-0">{{ $course->price > 0 ? '$'.$course->price_formatted : 'Free' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Description Card --}}
                <div class="card-modern p-4 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light rounded-circle p-2 me-3">
                            <i class="fa-solid fa-align-left text-secondary"></i>
                        </div>
                        <h5 class="fw-bold m-0">About this Course</h5>
                    </div>
                    <div class="text-secondary lh-lg">
                        @if($course->description)
                            {!! nl2br(e($course->description)) !!}
                        @else
                            <span class="text-muted fst-italic">No description provided.</span>
                        @endif
                    </div>
                </div>

                {{-- Curriculum / Syllabus --}}
                <div class="card-modern p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <h5 class="fw-bold m-0"><i class="fa-solid fa-list-check text-primary me-2"></i>Curriculum</h5>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">
                            {{ $course->curriculumItems->whereNotNull('parent_id')->count() }} Lessons
                        </span>
                    </div>

                    @if($course->syllabus->isEmpty())
                        <div class="text-center py-5 bg-light rounded-3 border border-dashed">
                            <i class="fa-solid fa-folder-open fa-3x text-muted opacity-25 mb-3"></i>
                            <h6 class="text-muted fw-bold">Curriculum is Empty</h6>
                            <p class="small text-muted mb-0">Go to "Edit Course" to add sections.</p>
                        </div>
                    @else
                        <div class="accordion accordion-flush" id="syllabusList">
                            @foreach($course->syllabus as $index => $item)
                                
                                {{-- Case: Section (Unit) --}}
                                @if($item->type === 'section')
                                    <div class="syllabus-section shadow-sm">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button syllabus-btn {{ $index !== 0 ? 'collapsed' : '' }}" 
                                                    type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#unit-{{ $item->id }}">
                                                <div class="d-flex align-items-center w-100">
                                                    <span class="fw-bold text-dark me-2">{{ $item->title }}</span>
                                                    <span class="ms-auto me-3 badge bg-white border text-muted fw-normal">
                                                        {{ $item->children->count() }} items
                                                    </span>
                                                </div>
                                            </button>
                                        </h2>

                                        <div id="unit-{{ $item->id }}" 
                                             class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                                             data-bs-parent="#syllabusList">
                                            <div class="accordion-body p-0">
                                                
                                                {{-- Unit Description (Safely checking array key) --}}
                                                @if(!empty($item->content['description'] ?? null))
                                                    <div class="p-3 bg-light border-bottom text-secondary small">
                                                        {!! nl2br(e($item->content['description'])) !!}
                                                    </div>
                                                @endif

                                                {{-- Child Lessons Loop --}}
                                                @forelse($item->children as $child)
                                                    <div class="d-flex align-items-center lesson-item" 
                                                         data-bs-toggle="collapse" 
                                                         data-bs-target="#lesson-content-{{ $child->id }}" 
                                                         aria-expanded="false">
                                                        
                                                        <div class="me-3 {{ $child->is_active ? 'text-primary' : 'text-muted' }} opacity-75">
                                                            <i class="fa-regular fa-circle-play"></i>
                                                        </div>
                                                        
                                                        <div class="flex-grow-1">
                                                            <span class="{{ $child->is_active ? 'text-dark fw-semibold' : 'text-muted text-decoration-line-through' }} small">
                                                                {{ $child->title }}
                                                            </span>
                                                        </div>
                                                        
                                                        <i class="fa-solid fa-chevron-down text-muted opacity-50 small ms-2"></i>

                                                        @if(!$child->is_active)
                                                            <span class="badge bg-secondary ms-2" style="font-size:0.6rem">DRAFT</span>
                                                        @endif
                                                    </div>

                                                    {{-- Collapsible Body for Child Lesson --}}
                                                    <div id="lesson-content-{{ $child->id }}" class="collapse bg-light border-bottom">
                                                        <div class="p-3 ps-5">
                                                            @if(!empty($child->content['html'] ?? null))
                                                                <div class="small text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.7rem;">Lesson Content Preview:</div>
                                                                <div class="text-secondary small bg-white p-2 border rounded">
                                                                    {{-- Safely handle null strings for PHP 8.1+ strip_tags --}}
                                                                    {{ Str::limit(strip_tags($child->content['html'] ?? ''), 200) }}
                                                                    @if(strlen(strip_tags($child->content['html'] ?? '')) > 200)
                                                                        <span class="text-primary fst-italic">... (view full in edit)</span>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <span class="text-muted fst-italic small">No content added yet.</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="p-3 text-center text-muted small fst-italic">
                                                        No lessons in this section.
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                
                                {{-- Case: Standalone Lesson --}}
                                @else
                                    <div class="syllabus-section shadow-sm">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button syllabus-btn collapsed" 
                                                    type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#unit-{{ $item->id }}">
                                                <div class="d-flex align-items-center w-100">
                                                    <i class="fa-solid fa-file-video text-success me-3"></i>
                                                    <span class="fw-bold text-dark">{{ $item->title }}</span>
                                                    <span class="ms-auto badge bg-success bg-opacity-10 text-success me-3">Standalone</span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="unit-{{ $item->id }}" class="accordion-collapse collapse" data-bs-parent="#syllabusList">
                                            <div class="accordion-body p-4">
                                                @if(!empty($item->content['description'] ?? null))
                                                    <div class="mb-3 text-muted">
                                                        <strong>Summary:</strong><br>
                                                        {!! nl2br(e($item->content['description'])) !!}
                                                    </div>
                                                @endif
                                                @if(!empty($item->content['html'] ?? null))
                                                     <div class="p-3 bg-light rounded border border-light">
                                                        <span class="badge bg-secondary mb-2">Content Preview</span>
                                                        <div class="small text-secondary text-truncate" style="max-height: 60px;">
                                                            {{ Str::limit(strip_tags($item->content['html'] ?? ''), 150) }}
                                                        </div>
                                                     </div>
                                                @else
                                                    <span class="text-muted fst-italic small">No content added yet.</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- RIGHT COLUMN --}}
            <div class="col-lg-4">
                <div class="position-sticky" style="top: 2rem;">
                    <div class="card-modern p-3 mb-4">
                        <div class="course-thumbnail-container mb-3">
                            @if($course->thumbnail_url)
                                {{-- Added missing alt attribute for accessibility --}}
                                <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }} Thumbnail" class="w-100 h-100 object-fit-cover">
                            @else
                                <div class="d-flex align-items-center justify-content-center w-100 h-100 bg-secondary text-white-50">
                                    <i class="fa-regular fa-image fa-3x"></i>
                                </div>
                            @endif
                            <div class="position-absolute top-0 end-0 m-2">
                                 <span class="badge {{ $course->status === 'published' ? 'bg-success' : 'bg-warning text-dark' }} shadow-sm">
                                    {{ ucfirst($course->status) }}
                                </span>
                            </div>
                        </div>
                        <h6 class="text-uppercase text-muted x-small fw-bold mb-3 px-1">Details</h6>
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between px-0 py-2 border-0">
                                <span class="text-muted">Type</span>
                                <span class="fw-semibold text-dark">{{ $course->type->name ?? 'General' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0 py-2 border-0">
                                <span class="text-muted">Code</span>
                                <span class="font-monospace bg-light px-2 rounded">{{ $course->code }}</span>
                            </li>
                        </ul>
                        <hr class="text-muted opacity-25 my-3">
                        <div class="d-grid">
                            <a href="{{ route('courses.edit', $course) }}" class="btn btn-outline-primary btn-sm fw-bold">Manage Content</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection