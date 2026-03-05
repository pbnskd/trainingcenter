@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4">
    <h1 class="text-2xl font-bold mb-6">Edit Batch: {{ $batch->batch_code }}</h1>
    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('batches.update', $batch) }}" method="POST">
            @csrf
            @method('PUT')
            @include('batches._form', ['batch' => $batch])
        </form>
    </div>
</div>
@endsection