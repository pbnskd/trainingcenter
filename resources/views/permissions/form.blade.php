@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    
    {{-- Header Section --}}
    <div class="d-flex align-items-center justify-content-between mb-4 animate__animated animate__fadeIn">
        <div>
            <h4 class="fw-800 text-dark mb-1">
                {{ isset($permission) ? 'Edit Permission Node' : 'Initialize New Node' }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('permissions.index') }}" class="text-muted text-decoration-none hover-link">Registry</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ isset($permission) ? 'Configuration' : 'Initialization' }}
                    </li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('permissions.index') }}" class="btn btn-light rounded-pill border shadow-sm fw-bold px-4">
            <i class="fa-solid fa-xmark me-2"></i>Cancel
        </a>
    </div>

    {{-- Form Wrapper --}}
    <form action="{{ isset($permission) ? route('permissions.update', $permission->id) : route('permissions.store') }}" 
          method="POST" class="animate__animated animate__fadeInUp">
        @csrf
        @if(isset($permission))
            @method('PATCH')
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header border-0 py-4 px-4">
                <h5 class="fw-bold mb-0">Security Node Parameters</h5>
                <p class="text-muted small mb-0">Configure the unique identifier and functional intent for this system capability.</p>
            </div>

            <div class="card-body p-4 pt-0">
                <div class="row g-4">
                    {{-- Name Input --}}
                    <div class="col-md-12 form-group mb-0">
                        <label class="form-label text-uppercase tracking-widest fw-bold small">Permission String (Slug)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted px-3" style="border-radius: 0.5rem 0 0 0.5rem;">
                                <i class="fa-solid fa-key"></i>
                            </span>
                            <input type="text" name="name" 
                                value="{{ old('name', $permission->name ?? '') }}" 
                                class="form-control pro-input border-start-0 @error('name') is-invalid @enderror" 
                                placeholder="e.g., user-edit" required>
                        </div>
                        @error('name')
                            <div class="text-danger x-small fw-bold mt-2"><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}</div>
                        @enderror
                        <div class="mt-2 text-muted x-small d-flex align-items-center">
                            <i class="fa-solid fa-circle-info me-1 text-primary"></i> Use kebab-case (e.g., <code class="text-primary">module-action</code>) for system compatibility.
                        </div>
                    </div>

                    {{-- Description Input --}}
                    <div class="col-md-12 form-group mb-0">
                        <label class="form-label text-uppercase tracking-widest fw-bold small">Operational Description</label>
                        <textarea name="description" rows="3" 
                            class="form-control pro-input @error('description') is-invalid @enderror" 
                            placeholder="Briefly explain what actions this permission node authorizes...">{{ old('description', $permission->description ?? '') }}</textarea>
                        
                        @error('description')
                            <div class="text-danger x-small fw-bold mt-2"><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}</div>
                        @enderror
                        
                        <div class="mt-2 text-muted x-small d-flex align-items-center">
                            <i class="fa-solid fa-circle-info me-1 text-primary"></i> Detailed descriptions improve administrative transparency within the security registry.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-premium rounded-pill px-5 py-3 shadow-sm">
                <i class="fa-solid fa-floppy-disk me-2"></i> 
                <span class="fw-bold">{{ isset($permission) ? 'Recalibrate Node' : 'Initialize Node' }}</span>
            </button>
        </div>
    </form>
</div>
@endsection