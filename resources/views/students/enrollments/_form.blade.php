@foreach($batches as $batch)
    @php
        // Variable is now students_count, not enrollments_count
        $isFull = $batch->students_count >= $batch->max_capacity;
        $currentBatchId = $enrollment->currentBatchAssignment->batch_id ?? '';
        $isSelected = old('batch_id', $currentBatchId) == $batch->id;
    @endphp
    <option value="{{ $batch->id }}" 
        data-course="{{ $batch->course_id }}"
        {{ $isSelected ? 'selected' : '' }}
        {{ $isFull && !$isSelected ? 'disabled' : '' }} 
    >
        {{ $batch->name }} 
        ({{ $batch->students_count }}/{{ $batch->max_capacity }}) 
        {!! $isFull ? '— [FULL]' : '' !!}
    </option>
@endforeach
<div class="row g-3">
    <input type="hidden" name="student_id" value="{{ $student->id }}">

    <div class="col-md-6">
        <label class="form-label font-weight-bold">Course</label>
        @if($enrollment->exists)
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fa fa-book"></i></span>
                <input type="text" class="form-control bg-light" value="{{ $enrollment->course->title }}" readonly>
            </div>
            <input type="hidden" name="course_id" id="course_id" value="{{ $enrollment->course_id }}">
        @else
            <select name="course_id" id="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                <option value="">-- Choose Course --</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->title }}
                    </option>
                @endforeach
            </select>
        @endif
    </div>

    <div class="col-md-6">
        <label class="form-label font-weight-bold">Assign Batch</label>
        <select name="batch_id" id="batch_id" class="form-select @error('batch_id') is-invalid @enderror" required>
            <option value="">-- Select Course First --</option>
            @foreach($batches as $batch)
                @php
                    $isFull = $batch->enrollments_count >= $batch->max_capacity;
                    $currentBatchId = $enrollment->currentBatchAssignment->batch_id ?? '';
                    $isSelected = old('batch_id', $currentBatchId) == $batch->id;
                @endphp
                <option value="{{ $batch->id }}" 
                    data-course="{{ $batch->course_id }}"
                    {{ $isSelected ? 'selected' : '' }}
                    {{ $isFull && !$isSelected ? 'disabled' : '' }} 
                >
                    {{ $batch->name }} 
                    ({{ $batch->enrollments_count }}/{{ $batch->max_capacity }}) 
                    {!! $isFull ? '— [FULL]' : '' !!}
                </option>
            @endforeach
        </select>
        <small id="batch-help" class="form-text text-muted">Only active batches with available seats are shown.</small>
    </div>
    
    {{-- Rest of your form (Date, Status) stays the same --}}
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const courseSelect = document.getElementById('course_id');
    const batchSelect = document.getElementById('batch_id');
    const allBatchOptions = Array.from(batchSelect.options);

    function filterBatches() {
        const selectedCourseId = courseSelect.value;
        
        // Clear current options except the first one
        batchSelect.innerHTML = '<option value="">-- Select a Batch --</option>';

        const filtered = allBatchOptions.filter(option => {
            return option.dataset.course === selectedCourseId || option.value === "";
        });

        filtered.forEach(option => batchSelect.add(option));
    }

    // Run on change
    courseSelect.addEventListener('change', filterBatches);

    // Run on page load if course is already selected (for Edit mode or validation errors)
    if(courseSelect.value) filterBatches();
});
</script>