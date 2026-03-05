<x-mail::message>
# Batch Status Update

The status for **{{ $batch->batch_code }}** ({{ $batch->course->name }}) has changed.

<x-mail::panel>
**New Status: {{ ucfirst($batch->status) }}**
</x-mail::panel>

**Schedule:** {{ $batch->shift }}  
**Timeline:** {{ $batch->date_range['start_date'] }} to {{ $batch->date_range['end_date'] }}

@if($batch->status === 'cancelled')
Please contact administration for further details.
@endif

<x-mail::button :url="route('batches.show', $batch->id)">
View Batch Details
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>