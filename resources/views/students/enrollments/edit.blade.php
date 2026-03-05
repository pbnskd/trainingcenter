@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            
            <div class="card shadow border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-warning bg-gradient text-dark py-4 border-0">
                    <h4 class="mb-0 fw-bold"><i class="fa fa-edit me-2 opacity-75"></i> Edit Enrollment</h4>
                    <p class="mb-0 mt-1 small">Update academic status or transfer batch for this student.</p>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    
                    @if($errors->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <strong><i class="fa fa-exclamation-triangle me-2"></i> Update Failed!</strong> 
                            {{ $errors->first('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('enrollments.update', $enrollment) }}" method="POST" id="editEnrollmentForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="bg-light p-4 rounded-3 mb-4 border border-light-subtle shadow-sm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted">Student</label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-light border-end-0"><i class="fa fa-user text-muted"></i></span>
                                        <input type="text" class="form-control bg-light border-start-0 text-muted fw-bold px-0" value="{{ $enrollment->student->user->name }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted">Course</label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-light border-end-0"><i class="fa fa-book text-muted"></i></span>
                                        <input type="text" class="form-control bg-light border-start-0 text-muted fw-bold px-0" value="{{ $enrollment->course->title }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h6 class="text-uppercase text-secondary fw-bold mb-3 mt-4">Enrollment Status</h6>
                        <div class="bg-white p-4 rounded-3 mb-4 border border-light-subtle shadow-sm">
                            <div class="form-group mb-0">
                                <select name="status" class="form-select form-select-lg @error('status') is-invalid @enderror">
                                    <option value="enrolled" {{ $enrollment->status == 'enrolled' ? 'selected' : '' }}>Enrolled (Waiting to start)</option>
                                    <option value="in_progress" {{ $enrollment->status == 'in_progress' ? 'selected' : '' }}>In Progress (Active)</option>
                                    <option value="paused" {{ $enrollment->status == 'paused' ? 'selected' : '' }}>Paused (On Hold)</option>
                                    <option value="dropped" {{ $enrollment->status == 'dropped' ? 'selected' : '' }}>Dropped / Cancelled</option>
                                    <option value="completed" {{ $enrollment->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                @enderror
                                <small class="text-muted mt-2 d-block"><i class="fa fa-info-circle me-1"></i> Marking as Paused, Dropped, or Completed will automatically remove the student from their active batch.</small>
                            </div>
                        </div>

                        <h6 class="text-uppercase text-secondary fw-bold mb-3 mt-4">Batch Assignment</h6>
                        <div class="bg-white p-4 rounded-3 mb-4 border border-light-subtle shadow-sm">
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Current Batch</label>
                                @if($enrollment->currentBatchAssignment)
                                    <input type="text" class="form-control bg-light text-primary fw-bold" value="{{ $enrollment->currentBatchAssignment->batch->batch_code }}" readonly>
                                @else
                                    <input type="text" class="form-control bg-light text-warning fw-bold" value="No Active Batch Assignment" readonly>
                                @endif
                            </div>

                            <div class="form-check form-switch mt-3 mb-3">
                                <input class="form-check-input" type="checkbox" id="toggleTransfer" style="cursor: pointer;">
                                <label class="form-check-label fw-bold text-dark ms-2" for="toggleTransfer" style="cursor: pointer;">
                                    Transfer to a different batch
                                </label>
                            </div>

                            <div id="transferFields" class="d-none border-top pt-3 mt-3">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="transfer_to_batch_id" class="form-label fw-semibold">Target Batch <span class="text-danger">*</span></label>
                                        <select name="transfer_to_batch_id" id="transfer_to_batch_id" class="form-select @error('transfer_to_batch_id') is-invalid @enderror">
                                            <option value="">-- Select New Batch --</option>
                                            @foreach($batches as $batch)
                                                @php
                                                    $isCurrent = $enrollment->currentBatchAssignment && $enrollment->currentBatchAssignment->batch_id == $batch->id;
                                                    $isFull = $batch->students_count >= $batch->max_capacity;
                                                @endphp
                                                @if(!$isCurrent)
                                                    <option value="{{ $batch->id }}" {{ $isFull ? 'disabled' : '' }}>
                                                        {{ $batch->batch_code }} ({{ $batch->students_count }}/{{ $batch->max_capacity }}) 
                                                        {!! $isFull ? '&nbsp;&nbsp;[FULL]' : '' !!}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Transfer Date <span class="text-danger">*</span></label>
                                        <input type="date" name="transfer_date" class="form-control" value="{{ now()->toDateString() }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Reason (Optional)</label>
                                        <input type="text" name="transfer_reason" class="form-control" placeholder="e.g. Schedule conflict">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-5 pt-4 border-top">
                            <a href="{{ route('enrollments.index') }}" class="btn btn-outline-secondary px-4 py-2 mb-3 mb-sm-0 fw-medium w-100 w-sm-auto">Cancel</a>
                            <button type="submit" class="btn btn-warning px-5 py-2 fw-bold w-100 w-sm-auto shadow-sm" id="submitBtn">
                                Save Changes <i class="fa fa-save ms-2"></i>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleTransfer = document.getElementById('toggleTransfer');
    const transferFields = document.getElementById('transferFields');
    const targetBatchSelect = document.getElementById('transfer_to_batch_id');

    toggleTransfer.addEventListener('change', function() {
        if(this.checked) {
            transferFields.classList.remove('d-none');
            targetBatchSelect.setAttribute('required', 'required');
        } else {
            transferFields.classList.add('d-none');
            targetBatchSelect.removeAttribute('required');
            targetBatchSelect.value = ""; // Reset value when hiding
        }
    });

    const form = document.getElementById('editEnrollmentForm');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('submit', function() {
        if(form.checkValidity()) {
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Saving...';
            submitBtn.disabled = true;
        }
    });
});
</script>
@endsection