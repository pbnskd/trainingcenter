@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4">
    <h1 class="text-2xl font-bold mb-6">Create New Batch</h1>
    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('batches.store') }}" method="POST">
            @csrf
            @include('batches._form', ['batch' => null])
        </form>
    </div>
</div>
@endsection