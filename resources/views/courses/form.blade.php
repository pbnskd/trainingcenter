@extends('layouts.app')

@section('content')

{{-- Custom Builder Styles --}}
<style>
    .builder-area { min-height: 600px; }
    .item-card { transition: all 0.2s; border: 1px solid #e2e8f0; }
    .item-card:hover { border-color: #cbd5e0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .cursor-grab { cursor: grab; }
    .cursor-grab:active { cursor: grabbing; }

    /* TYPE: SECTION (Unit) */
    .type-section { background-color: #f8fafc; border-left: 4px solid #3b82f6; }
    .section-header { padding: 10px 15px; display: flex; align-items: center; justify-content: space-between; }
    .section-body { padding: 15px; border-top: 1px solid #e2e8f0; background: #fff; }

    /* TYPE: LESSON (Root or Child) */
    .type-lesson { background-color: #ffffff; border-left: 4px solid #10b981; }
    .lesson-header { padding: 10px 15px; display: flex; align-items: center; justify-content: space-between; }
    
    /* Child Lesson Indentation */
    .child-lesson { margin-left: 20px; border-left-width: 2px; border-left-color: #94a3b8; margin-top: 8px;}
</style>

<div class="container-fluid px-4 py-4 bg-light min-vh-100">
    
    <form action="{{ $course->exists ? route('courses.update', $course) : route('courses.store') }}" 
          method="POST" 
          id="course-form" 
          enctype="multipart/form-data">
        
        @csrf
        @if ($course->exists) @method('PUT') @endif

        {{-- TOP TOOLBAR --}}
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm sticky-top" style="top: 10px; z-index: 1000;">
            <div>
                <h5 class="fw-bold m-0 text-dark">{{ $course->exists ? 'Editing: ' . $course->code : 'Create New Course' }}</h5>
                <small class="text-muted">{{ $course->exists ? 'Update your curriculum structure below.' : 'Define course details and structure.' }}</small>
            </div>
            <div>
                <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary fw-bold px-4">
                    <i class="fa-solid fa-save me-1"></i> Save Changes
                </button>
            </div>
        </header>

        <div class="row g-4">
            {{-- LEFT COLUMN: Course Settings --}}
            <div class="col-xl-4 col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold py-3">Course Details</div>
                    <div class="card-body">
                        {{-- Title --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase text-muted">Title</label>
                            <input type="text" name="title" class="form-control fw-bold" value="{{ old('title', $course->title) }}" placeholder="e.g. Master Laravel 11" required>
                            @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        {{-- Slug --}}
                        @if($course->exists)
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase text-muted">Slug</label>
                            <input type="text" name="slug" class="form-control form-control-sm bg-light text-muted" value="{{ old('slug', $course->slug) }}">
                        </div>
                        @endif

                        {{-- Category --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase text-muted">Category</label>
                            <select name="course_type_id" class="form-select">
                                <option value="">Select Category...</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}" {{ old('course_type_id', $course->course_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Level & Price --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-uppercase text-muted">Level</label>
                                <select name="level" class="form-select">
                                    @foreach(['beginner', 'intermediate', 'advanced', 'all_levels'] as $l)
                                        <option value="{{ $l }}" {{ old('level', $course->level) == $l ? 'selected' : '' }}>{{ ucfirst($l) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-uppercase text-muted">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $course->price ? $course->price/100 : '') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Status & Date --}}
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-uppercase text-muted">Status</label>
                                <select name="status" class="form-select">
                                    <option value="draft" {{ old('status', $course->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status', $course->status) == 'published' ? 'selected' : '' }}>Published</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-uppercase text-muted">Date</label>
                                <input type="date" name="published_at" class="form-control" value="{{ old('published_at', $course->published_at?->format('Y-m-d')) }}">
                            </div>
                        </div>
                        
                        {{-- Summaries --}}
                        <div class="mb-3 mt-3">
                            <label class="form-label small fw-bold text-uppercase text-muted">Short Summary</label>
                            <textarea name="summary" class="form-control" rows="2">{{ old('summary', $course->summary) }}</textarea>
                        </div>
                         <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase text-muted">Full Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $course->description) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Thumbnail --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold py-3">Thumbnail</div>
                    <div class="card-body text-center">
                         @if($course->thumbnail_url)
                            <img src="{{ $course->thumbnail_url }}" class="img-fluid rounded mb-3" style="max-height: 150px;">
                        @endif
                        <input type="file" name="thumbnail" class="form-control form-control-sm">
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Curriculum Builder --}}
            <div class="col-xl-8 col-lg-7">
                <div class="card border-0 shadow-sm builder-area">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center sticky-top" style="top: 85px; z-index: 900;">
                        <h6 class="fw-bold m-0 text-uppercase text-primary"><i class="fa-solid fa-layer-group me-2"></i>Curriculum Builder</h6>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 me-2" onclick="addRootItem('section')">
                                <i class="fa-solid fa-folder-plus me-1"></i> Add Unit
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3" onclick="addRootItem('lesson')">
                                <i class="fa-solid fa-file-circle-plus me-1"></i> Add Direct Lesson
                            </button>
                        </div>
                    </div>

                    <div class="card-body bg-light">
                        <div id="curriculum-container">
                            {{-- JS will populate this --}}
                        </div>
                        <div id="empty-state" class="text-center py-5 text-muted {{ $course->curriculumItems->count() > 0 ? 'd-none' : '' }}">
                            <i class="fa-regular fa-clipboard fa-3x mb-3 opacity-25"></i>
                            <p>No curriculum items yet.<br>Click a button above to start.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- SCRIPTS --}}
<script>
    const initialData = @json($course->curriculumItems->load('children'));
    let itemCounter = 0;

    document.addEventListener('DOMContentLoaded', () => {
        if (initialData.length > 0) {
            initialData.forEach(item => {
                renderRootItem(item);
            });
        }
    });

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function addRootItem(type, data = null) {
        document.getElementById('empty-state').classList.add('d-none');
        renderRootItem({ type: type, ...data });
    }

    function renderRootItem(data) {
        const container = document.getElementById('curriculum-container');
        const uniqueId = itemCounter++; 
        
        const dbId = data.id || ''; 
        const title = escapeHtml(data.title || '');
        const type = data.type || 'section';
        
        let desc = '';
        let htmlContent = '';
        if (data.content) {
            desc = escapeHtml(data.content.description || '');
            htmlContent = escapeHtml(data.content.html || '');
        }

        const wrapper = document.createElement('div');
        wrapper.id = `root-item-${uniqueId}`;
        wrapper.className = `item-card rounded mb-3 shadow-sm ${type === 'section' ? 'type-section' : 'type-lesson'}`;
        
        let innerHtml = `
            <input type="hidden" name="items[${uniqueId}][type]" value="${type}">
            <input type="hidden" name="items[${uniqueId}][id]" value="${dbId}">
            
            <div class="${type === 'section' ? 'section-header' : 'lesson-header'}">
                <div class="d-flex align-items-center flex-grow-1">
                    <i class="fa-solid fa-grip-lines text-muted me-3 cursor-grab" title="Drag to reorder"></i>
                    <span class="badge ${type === 'section' ? 'bg-primary' : 'bg-success'} me-2">${type === 'section' ? 'UNIT' : 'LESSON'}</span>
                    <input type="text" name="items[${uniqueId}][title]" value="${title}" class="form-control form-control-sm border-0 bg-transparent fw-bold fs-6" placeholder="${type === 'section' ? 'Unit Title' : 'Lesson Title'}" required>
                </div>
                <div class="d-flex align-items-center">
                    ${type === 'section' ? `<button type="button" class="btn btn-sm btn-link text-dark me-2" data-bs-toggle="collapse" data-bs-target="#collapse-${uniqueId}"><i class="fa-solid fa-chevron-down"></i></button>` : ''}
                    <button type="button" class="btn btn-sm text-danger opacity-50 hover-danger" onclick="removeItem('${uniqueId}')"><i class="fa-regular fa-trash-can"></i></button>
                </div>
            </div>
        `;

        if (type === 'section') {
            innerHtml += `
            <div class="collapse show" id="collapse-${uniqueId}">
                <div class="section-body">
                    <div class="mb-3">
                        <label class="small text-muted fw-bold">Section Summary</label>
                        <input type="text" name="items[${uniqueId}][description]" value="${desc}" class="form-control form-control-sm bg-light border-0" placeholder="Enter a brief summary...">
                    </div>
                    <div id="children-container-${uniqueId}" class="ps-2 border-start"></div>
                    <div class="mt-3 ps-2">
                        <button type="button" class="btn btn-light btn-sm text-primary border border-primary border-opacity-25 rounded-pill px-3" onclick="addChildLesson('${uniqueId}')">
                            <i class="fa-solid fa-plus me-1"></i> Add Lesson
                        </button>
                    </div>
                </div>
            </div>`;
        } else {
            innerHtml += `
            <div class="p-3 border-top">
                <div class="mb-3">
                    <label class="small text-muted fw-bold">Description / Summary</label>
                    <input type="text" name="items[${uniqueId}][description]" value="${desc}" class="form-control form-control-sm bg-light border-0" placeholder="Brief lesson summary...">
                </div>
                <div class="mb-2">
                    <label class="small text-muted fw-bold">Lesson Content (HTML)</label>
                    <textarea name="items[${uniqueId}][html_content]" class="form-control form-control-sm" rows="3" placeholder="Enter lesson content here...">${htmlContent}</textarea>
                </div>
            </div>`;
        }

        wrapper.innerHTML = innerHtml;
        container.appendChild(wrapper);

        // FIXED: Calls correct function name 'addChildLesson'
        if (type === 'section' && data.children && data.children.length > 0) {
            data.children.forEach(child => {
                addChildLesson(uniqueId, child);
            });
        }
    }

    // FIXED: Added Description field to child lessons
    function addChildLesson(parentId, data = {}) {
        const container = document.getElementById(`children-container-${parentId}`);
        const childId = itemCounter++;
        
        const dbId = data.id || '';
        const title = escapeHtml(data.title || '');
        const desc = escapeHtml(data.content ? (data.content.description || '') : '');
        const htmlContent = escapeHtml(data.content ? (data.content.html || '') : '');

        const wrapper = document.createElement('div');
        wrapper.id = `child-item-${childId}`;
        wrapper.className = 'child-lesson item-card type-lesson rounded mb-2 bg-white';

        wrapper.innerHTML = `
            <input type="hidden" name="items[${parentId}][children][${childId}][type]" value="lesson">
            <input type="hidden" name="items[${parentId}][children][${childId}][id]" value="${dbId}">
            
            <div class="p-2 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center flex-grow-1">
                    <i class="fa-regular fa-file-lines text-muted me-2 ms-1"></i>
                    <input type="text" name="items[${parentId}][children][${childId}][title]" value="${title}" class="form-control form-control-sm border-0 px-1 fw-bold" placeholder="Lesson Title" required>
                </div>
                <div class="d-flex">
                    <button type="button" class="btn btn-sm text-muted" data-bs-toggle="collapse" data-bs-target="#child-content-${childId}"><i class="fa-solid fa-pen-to-square"></i></button>
                    <button type="button" class="btn btn-sm text-danger ms-1" onclick="removeChildItem('${childId}')"><i class="fa-solid fa-xmark"></i></button>
                </div>
            </div>
            
            <div class="collapse ${htmlContent || desc ? 'show' : ''} px-3 pb-3" id="child-content-${childId}">
                <div class="mb-2">
                    <input type="text" name="items[${parentId}][children][${childId}][description]" value="${desc}" class="form-control form-control-sm bg-light border-0 mb-1" placeholder="Lesson summary...">
                </div>
                <textarea name="items[${parentId}][children][${childId}][html_content]" class="form-control form-control-sm bg-light" rows="3" placeholder="Lesson content...">${htmlContent}</textarea>
            </div>
        `;

        container.appendChild(wrapper);
    }

    function removeItem(id) {
        if(confirm('Delete this unit/lesson?')) { document.getElementById(`root-item-${id}`)?.remove(); }
    }
    function removeChildItem(id) {
        if(confirm('Remove this lesson?')) { document.getElementById(`child-item-${id}`)?.remove(); }
    }
</script>
@endsection