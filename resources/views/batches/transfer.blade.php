@extends('layouts.app')

@section('content')
<div class="main-canvas p-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            
            {{-- Navigation Back --}}
            <div class="mb-3">
                <a href="{{ route('batches.show', $batch) }}" class="text-muted text-decoration-none small fw-bold">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to {{ $batch->batch_code }}
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                {{-- Header --}}
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-right-left"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Transfer Student</h6>
                        <p class="text-muted small mb-0">Moving <strong>{{ $student->user->name }}</strong> to a new batch</p>
                    </div>
                </div>

                <form action="{{ route('batches.transfer.store') }}" method="POST">
                    @csrf
                    {{-- Hidden Inputs to maintain context --}}
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <input type="hidden" name="from_batch_id" value="{{ $batch->id }}">

                    <div class="card-body p-4">
                        <div class="row g-4">
                            
                            {{-- Current Batch Info (ReadOnly) --}}
                            <div class="col-md-6">
                                <label class="form-label text-uppercase text-secondary fw-bold small letter-spacing-1">From Current Batch</label>
                                <div class="p-3 bg-light rounded border">
                                    <div class="fw-bold text-dark">{{ $batch->batch_code }}</div>
                                    <div class="small text-muted">{{ $batch->course->title }}</div>
                                </div>
                            </div>

                            {{-- Target Batch Selection --}}
                            <div class="col-md-6">
                                <label class="form-label text-uppercase text-secondary fw-bold small letter-spacing-1">To Target Batch <span class="text-danger">*</span></label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text bg-white border-end-0 text-secondary">
                                        <i class="fa-solid fa-layer-group"></i>
                                    </span>
                                    <select name="to_batch_id" class="form-select border-start-0 ps-0 shadow-none focus-ring @error('to_batch_id') is-invalid @enderror" required>
                                        <option value="">Select Destination Batch</option>
                                        @foreach($availableBatches as $target)
                                            <option value="{{ $target->id }}" {{ old('to_batch_id') == $target->id ? 'selected' : '' }}>
                                                {{ $target->batch_code }} ({{ $target->status }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('to_batch_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @else
                                    <div class="form-text small">Only active/upcoming batches for this course are shown.</div>
                                @enderror
                            </div>

                            {{-- Transfer Date --}}
                            <div class="col-md-6">
                                <label class="form-label text-uppercase text-secondary fw-bold small letter-spacing-1">Effective Date <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-secondary">
                                        <i class="fa-regular fa-calendar-check"></i>
                                    </span>
                                    <input type="date" name="transfer_date" value="{{ old('transfer_date', date('Y-m-d')) }}" 
                                           class="form-control border-start-0 ps-0 shadow-none focus-ring @error('transfer_date') is-invalid @enderror" required>
                                </div>
                                @error('transfer_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Reason --}}
                            <div class="col-12">
                                <label class="form-label text-uppercase text-secondary fw-bold small letter-spacing-1">Reason for Transfer</label>
                                <textarea name="reason" rows="3" class="form-control shadow-none focus-ring" placeholder="e.g., Student requested shift change to Morning.">{{ old('reason') }}</textarea>
                            </div>

                        </div>
                    </div>

                    {{-- Footer Actions --}}
                    <div class="card-footer bg-light border-top p-4 d-flex justify-content-between align-items-center">
                        <span class="small text-muted">
                            <i class="fa-solid fa-circle-info me-1"></i> This action will mark the student as 'Dropped' in the current batch.
                        </span>
                        <button type="submit" class="btn btn-primary fw-bold px-4 py-2 shadow-sm">
                            Confirm Transfer <i class="fa-solid fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .letter-spacing-1 { letter-spacing: 0.8px; font-size: 0.7rem; }
    .input-group-text { border-color: #e2e8f0; width: 46px; justify-content: center; }
    .form-control, .form-select { border-color: #e2e8f0; padding: 0.7rem 1rem; }
    .focus-ring:focus { border-color: #3b82f6 !important; box-shadow: none; }
</style>
@endsection