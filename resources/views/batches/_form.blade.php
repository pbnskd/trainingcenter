<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    
    {{-- Card Header --}}
    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-2">
        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
            <i class="fa-solid fa-layer-group"></i>
        </div>
        <h6 class="fw-bold text-dark mb-0">Batch Configuration</h6>
    </div>

    {{-- Card Body --}}
    <div class="card-body p-4">
        <div class="row g-4">

            {{-- Course Selection --}}
            <div class="col-md-6">
                <label class="form-label text-uppercase text-secondary fw-bold small letter-spacing-1">
                    Academic Course <span class="text-danger">*</span>
                </label>
                <div class="input-group has-validation input-group-hover">
                    <span class="input-group-text bg-light border-end-0 text-secondary {{ $errors->has('course_id') ? 'border-danger text-danger' : '' }}">
                        <i class="fa-solid fa-book-open"></i>
                    </span>
                    <select name="course_id" class="form-select border-start-0 ps-0 shadow-none focus-ring {{ $errors->has('course_id') ? 'is-invalid border-danger' : '' }}">
                        <option value="">Select Course</option>
                        {{-- Note: Controller must use pluck('title', 'id') --}}
                        @foreach($courses as $id => $title)
                            <option value="{{ $id }}" {{ (old('course_id', $batch->course_id ?? '') == $id) ? 'selected' : '' }}>
                                {{ $title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('course_id')
                    <div class="text-danger small mt-1 d-flex align-items-center"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                @else
                    <div class="form-text text-muted small mt-1">Select the parent course for this batch.</div>
                @enderror
            </div>

            {{-- Status Selection (MATCHED TO SCHEMA) --}}
            <div class="col-md-6">
                <label class="form-label text-uppercase text-secondary fw-bold small letter-spacing-1">
                    Current Status <span class="text-danger">*</span>
                </label>
                <div class="input-group has-validation input-group-hover">
                    <span class="input-group-text bg-light border-end-0 text-secondary {{ $errors->has('status') ? 'border-danger text-danger' : '' }}">
                        <i class="fa-solid fa-signal"></i>
                    </span>
                    <select name="status" class="form-select border-start-0 ps-0 shadow-none focus-ring {{ $errors->has('status') ? 'is-invalid border-danger' : '' }}">
                        {{-- EXACT ENUM VALUES FROM MIGRATION --}}
                        @foreach(['Upcoming', 'Running', 'Completed', 'Cancelled', 'On_Hold'] as $status)
                            <option value="{{ $status }}" {{ (old('status', $batch->status ?? 'Upcoming') == $status) ? 'selected' : '' }}>
                                {{ str_replace('_', ' ', $status) }} {{-- Display 'On Hold' nicely --}}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('status')
                    <div class="text-danger small mt-1 d-flex align-items-center"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                @else
                    <div class="form-text text-muted small mt-1">Default is 'Upcoming'. Controls visibility.</div>
                @enderror
            </div>

            {{-- Divider --}}
            <div class="col-12 my-2">
                <hr class="text-muted opacity-10">
            </div>

            {{-- Shift Selection (MATCHED TO SCHEMA) --}}
            <div class="col-md-6">
                <label class="form-label text-uppercase text-secondary fw-bold small letter-spacing-1">
                    Time Shift <span class="text-danger">*</span>
                </label>
                <div class="input-group has-validation input-group-hover">
                    <span class="input-group-text bg-light border-end-0 text-secondary {{ $errors->has('shift') ? 'border-danger text-danger' : '' }}">
                        <i class="fa-regular fa-clock"></i>
                    </span>
                    <select name="shift" class="form-select border-start-0 ps-0 shadow-none focus-ring {{ $errors->has('shift') ? 'is-invalid border-danger' : '' }}">
                        <option value="">Select Shift</option>
                        {{-- EXACT ENUM VALUES FROM MIGRATION --}}
                        @foreach(['Morning', 'Afternoon', 'Evening'] as $shiftOption)
                            <option value="{{ $shiftOption }}" 
                                {{ (old('shift', $batch->shift ?? '') == $shiftOption) ? 'selected' : '' }}>
                                {{ $shiftOption }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('shift')
                    <div class="text-danger small mt-1 d-flex align-items-center"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                @enderror
            </div>

            {{-- Capacity Input --}}
            <div class="col-md-6">
                <label class="form-label text-uppercase text-secondary fw-bold small letter-spacing-1">
                    Max Capacity <span class="text-danger">*</span>
                </label>
                <div class="input-group has-validation input-group-hover">
                    <span class="input-group-text bg-light border-end-0 text-secondary {{ $errors->has('max_capacity') ? 'border-danger text-danger' : '' }}">
                        <i class="fa-solid fa-users-viewfinder"></i>
                    </span>
                    <input type="number" name="max_capacity" value="{{ old('max_capacity', $batch->max_capacity ?? 20) }}"
                           min="1"
                           class="form-control border-start-0 ps-0 shadow-none focus-ring {{ $errors->has('max_capacity') ? 'is-invalid border-danger' : '' }}">
                </div>
                @error('max_capacity')
                    <div class="text-danger small mt-1 d-flex align-items-center"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                @enderror
            </div>

            {{-- Timeline Header --}}
            <div class="col-12 mt-4 mb-1">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="fw-bold text-dark mb-0">Timeline</h6>
                    <div class="flex-grow-1 border-bottom"></div>
                </div>
            </div>

            {{-- Start Date --}}
            <div class="col-md-6">
                <label class="form-label text-uppercase text-secondary fw-bold small letter-spacing-1">Start Date <span class="text-danger">*</span></label>
                <div class="input-group has-validation input-group-hover">
                    <span class="input-group-text bg-light border-end-0 text-secondary {{ $errors->has('date_range.start_date') ? 'border-danger text-danger' : '' }}">
                        <i class="fa-regular fa-calendar-plus"></i>
                    </span>
                    <input type="date" name="date_range[start_date]" 
                           value="{{ old('date_range.start_date', $batch->date_range['start_date'] ?? '') }}"
                           class="form-control border-start-0 ps-0 shadow-none focus-ring {{ $errors->has('date_range.start_date') ? 'is-invalid border-danger' : '' }}">
                </div>
                @error('date_range.start_date')
                    <div class="text-danger small mt-1 d-flex align-items-center"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                @enderror
            </div>

            {{-- End Date --}}
            <div class="col-md-6">
                <label class="form-label text-uppercase text-secondary fw-bold small letter-spacing-1">End Date <span class="text-danger">*</span></label>
                <div class="input-group has-validation input-group-hover">
                    <span class="input-group-text bg-light border-end-0 text-secondary {{ $errors->has('date_range.end_date') ? 'border-danger text-danger' : '' }}">
                        <i class="fa-regular fa-calendar-check"></i>
                    </span>
                    <input type="date" name="date_range[end_date]" 
                           value="{{ old('date_range.end_date', $batch->date_range['end_date'] ?? '') }}"
                           class="form-control border-start-0 ps-0 shadow-none focus-ring {{ $errors->has('date_range.end_date') ? 'is-invalid border-danger' : '' }}">
                </div>
                @error('date_range.end_date')
                    <div class="text-danger small mt-1 d-flex align-items-center"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                @enderror
            </div>

            {{-- Description --}}
            <div class="col-12">
                <label class="form-label text-uppercase text-secondary fw-bold small letter-spacing-1">Description</label>
                <div class="input-group input-group-hover">
                    <span class="input-group-text bg-light border-end-0 text-secondary pt-2 align-items-start">
                        <i class="fa-solid fa-align-left mt-1"></i>
                    </span>
                    <textarea name="description" rows="3" class="form-control border-start-0 ps-0 shadow-none focus-ring {{ $errors->has('description') ? 'is-invalid border-danger' : '' }}" 
                              placeholder="Enter internal notes, goals, or specific requirements...">{{ old('description', $batch->description ?? '') }}</textarea>
                </div>
                @error('description')
                    <div class="text-danger small mt-1 d-flex align-items-center"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                @enderror
            </div>

        </div>
    </div>

    {{-- Card Footer --}}
    <div class="card-footer bg-light border-top p-4">
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('batches.index') }}" class="btn btn-link text-muted text-decoration-none fw-bold small">
                <i class="fa-solid fa-arrow-left me-1"></i> Cancel & Return
            </a>
            <button type="submit" class="btn btn-primary fw-bold px-4 py-2 shadow-sm rounded-3">
                <i class="fa-solid fa-floppy-disk me-2"></i> {{ isset($batch) ? 'Save Changes' : 'Create Batch' }}
            </button>
        </div>
    </div>
</div>

<style>
    /* Premium Input Styling */
    .letter-spacing-1 { letter-spacing: 0.8px; font-size: 0.7rem; }
    
    .input-group-text { 
        background-color: #fcfcfc; 
        border-color: #e2e8f0; 
        width: 46px; 
        justify-content: center;
        transition: all 0.2s;
    }
    
    .form-control, .form-select { 
        border-color: #e2e8f0; 
        padding: 0.7rem 1rem; 
        font-size: 0.95rem;
        color: #1e293b;
    }

    /* Hover & Focus Effects */
    .input-group-hover:hover .input-group-text {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
        color: #475569 !important;
    }
    .input-group-hover:hover .form-control, 
    .input-group-hover:hover .form-select {
        border-color: #cbd5e1;
    }

    .focus-ring:focus { 
        border-color: #3b82f6 !important; 
    }
    
    /* When input is focused, color the icon box */
    .input-group:focus-within .input-group-text { 
        border-color: #3b82f6 !important; 
        color: #3b82f6 !important; 
        background-color: #eff6ff !important; 
    }

    /* Error States */
    .is-invalid { 
        border-color: #ef4444 !important; 
        background-image: none !important; 
    }
    .input-group-text.border-danger { 
        border-color: #ef4444 !important; 
        background-color: #fef2f2 !important; 
        color: #ef4444 !important; 
    }
</style>