@php
    // HELPER LOGIC: Safely extract values from Object (DB) or Array (Input)
    
    // 1. ID
    $sId = is_array($section) ? ($section['id'] ?? '') : ($section->id ?? '');

    // 2. Title
    $sTitle = is_array($section) ? ($section['title'] ?? '') : ($section->title ?? '');

    // 3. Video URL (Stored in JSON 'properties' column in DB)
    $sVideo = '';
    if (is_array($section)) {
        $sVideo = $section['video_url'] ?? ''; // From Form Input name
    } elseif (isset($section->properties) && is_array($section->properties)) {
        $sVideo = $section->properties['video_url'] ?? ''; // From DB JSON
    }

    // 4. Type
    $sType = is_array($section) ? ($section['type'] ?? 'video') : ($section->type ?? 'video');

    // 5. Duration
    $sDuration = is_array($section) ? ($section['duration_minutes'] ?? '') : ($section->duration_minutes ?? '');

    // 6. Preview (Boolean)
    $sPreview = is_array($section) ? ($section['is_preview'] ?? false) : ($section->is_preview ?? false);
@endphp

<div class="section-item row g-2 mb-3 align-items-start animate__animated animate__fadeIn">
    <div class="section-line"></div>
    
    {{-- Hidden ID for Updates --}}
    <input type="hidden" 
           name="modules[{{ $mIndex }}][sections][{{ $sIndex }}][id]" 
           value="{{ old('modules.'.$mIndex.'.sections.'.$sIndex.'.id', $sId) }}">

    {{-- Title & Video URL --}}
    <div class="col-md-6">
        <div class="mb-2">
            <input type="text" 
                   name="modules[{{ $mIndex }}][sections][{{ $sIndex }}][title]" 
                   class="form-control form-control-sm border-light bg-light fw-bold @error('modules.'.$mIndex.'.sections.'.$sIndex.'.title') is-invalid @enderror" 
                   placeholder="Lesson Title" 
                   value="{{ old('modules.'.$mIndex.'.sections.'.$sIndex.'.title', $sTitle) }}" 
                   required>
        </div>

        <div class="input-group input-group-sm">
            <span class="input-group-text border-0 bg-light text-muted"><i class="fa-brands fa-youtube"></i></span>
            <input type="url" 
                   name="modules[{{ $mIndex }}][sections][{{ $sIndex }}][video_url]" 
                   class="form-control border-0 bg-light" 
                   placeholder="Video URL" 
                   value="{{ old('modules.'.$mIndex.'.sections.'.$sIndex.'.video_url', $sVideo) }}">
        </div>
    </div>

    {{-- Meta: Type, Time, Preview --}}
    <div class="col-md-5">
        <div class="row g-2">
            {{-- Type Select --}}
            <div class="col-6">
                <select name="modules[{{ $mIndex }}][sections][{{ $sIndex }}][type]" 
                        class="form-select form-select-sm border-light bg-light">
                    @foreach(['video', 'text', 'quiz', 'assignment'] as $t)
                        <option value="{{ $t }}" {{ old('modules.'.$mIndex.'.sections.'.$sIndex.'.type', $sType) == $t ? 'selected' : '' }}>
                            {{ ucfirst($t) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Duration --}}
            <div class="col-6">
                <div class="input-group input-group-sm">
                    <input type="number" 
                           name="modules[{{ $mIndex }}][sections][{{ $sIndex }}][duration_minutes]" 
                           class="form-control border-light bg-light" 
                           placeholder="Min" 
                           value="{{ old('modules.'.$mIndex.'.sections.'.$sIndex.'.duration_minutes', $sDuration) }}">
                    <span class="input-group-text border-0 bg-light text-muted">m</span>
                </div>
            </div>

            {{-- Preview Checkbox --}}
            <div class="col-12">
                <div class="form-check form-check-inline small mt-1">
                    {{-- Hidden input ensures '0' is sent if unchecked --}}
                    <input type="hidden" name="modules[{{ $mIndex }}][sections][{{ $sIndex }}][is_preview]" value="0">
                    
                    <input class="form-check-input" 
                           type="checkbox" 
                           name="modules[{{ $mIndex }}][sections][{{ $sIndex }}][is_preview]" 
                           value="1" 
                           id="prev_{{ $mIndex }}_{{ $sIndex }}" 
                           {{ old('modules.'.$mIndex.'.sections.'.$sIndex.'.is_preview', $sPreview) ? 'checked' : '' }}>
                    <label class="form-check-label text-muted" for="prev_{{ $mIndex }}_{{ $sIndex }}">Free Preview?</label>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Button --}}
    <div class="col-md-1 text-end">
        <button type="button" class="btn btn-link text-muted p-0" onclick="removeElement(this, '.section-item')">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
</div>