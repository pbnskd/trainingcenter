@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    
    {{-- Header Section --}}
    <div class="d-flex align-items-center justify-content-between mb-4 animate__animated animate__fadeIn">
        <div>
            <h4 class="fw-800 text-dark mb-1">
                {{ isset($role) ? 'Edit Role Authority' : 'Initialize New Role' }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('roles.index') }}" class="text-muted text-decoration-none hover-link">Role Authority</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ isset($role) ? 'Configuration' : 'Initialization' }}
                    </li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('roles.index') }}" class="btn btn-light rounded-pill border shadow-sm fw-bold px-4">
            <i class="fa-solid fa-xmark me-2"></i>Cancel
        </a>
    </div>

    {{-- Form Wrapper --}}
    <form action="{{ isset($role) ? route('roles.update', $role->id) : route('roles.store') }}" 
          method="POST" class="animate__animated animate__fadeInUp">
        @csrf
        @if(isset($role))
            @method('PATCH')
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header border-0 py-4 px-4">
                <h5 class="fw-bold mb-0">Identity & Purpose</h5>
                <p class="text-muted small mb-0">Define the security designation and operational scope for this system node.</p>
            </div>

            <div class="card-body p-4 pt-0">
                <div class="row g-4">
                    
                    {{-- Role Name --}}
                    <div class="col-md-12 form-group mb-0">
                        <label class="form-label text-uppercase tracking-widest fw-bold small">Role Designation</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="fa-solid fa-id-badge"></i>
                            </span>
                            <input type="text" name="name" 
                                value="{{ old('name', $role->name ?? '') }}" 
                                class="form-control pro-input border-start-0 @error('name') is-invalid @enderror" 
                                placeholder="e.g. Senior Moderator" required>
                        </div>
                        @error('name')
                            <div class="text-danger x-small fw-bold mt-2"><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Role Description --}}
                    <div class="col-md-12 form-group mb-0">
                        <label class="form-label text-uppercase tracking-widest fw-bold small">Operational Description</label>
                        <textarea name="description" rows="3" 
                            class="form-control pro-input @error('description') is-invalid @enderror" 
                            placeholder="Explain the security scope and purpose of this role...">{{ old('description', $role->description ?? '') }}</textarea>
                        
                        @error('description')
                            <div class="text-danger x-small fw-bold mt-2"><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}</div>
                        @enderror
                        
                        <div class="mt-2 text-muted x-small d-flex align-items-center">
                            <i class="fa-solid fa-circle-info me-1 text-primary"></i> This description will be visible to other administrators in the Registry.
                        </div>
                    </div>

                    <div class="col-12">
                        <hr class="border-light my-2">
                    </div>

                    {{-- Permissions Matrix --}}
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="fw-bold mb-0">Security Authorization Matrix</h5>
                                <p class="text-muted small mb-0">Select the specific actions this role is authorized to perform.</p>
                            </div>
                            <button type="button" class="btn btn-sm btn-soft-primary rounded-pill px-4" onclick="toggleAllPermissions()">
                                <i class="fa-solid fa-arrows-rotate me-1"></i> <span class="fw-bold">Toggle All</span>
                            </button>
                        </div>

                        <div class="row g-3">
                            @foreach($permissions as $key => $permission)
                            <div class="col-md-4 col-xl-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $key * 0.02 }}s">
                                {{-- Logic to check if permission is assigned --}}
                                @php
                                    $isChecked = false;
                                    if (isset($rolePermissions)) {
                                        if (is_array($rolePermissions) && in_array($permission->id, $rolePermissions)) {
                                            $isChecked = true;
                                        } 
                                        elseif ($rolePermissions instanceof \Illuminate\Support\Collection && $rolePermissions->contains($permission->id)) {
                                            $isChecked = true;
                                        }
                                    }
                                @endphp

                                <label class="permission-card border rounded-4 p-3 bg-light d-flex align-items-center h-100 transition-hover border-dashed cursor-pointer">
                                    <div class="custom-switch me-3">
                                        <input type="checkbox" name="permission[]" class="permission-checkbox" 
                                               value="{{ $permission->id }}" {{ $isChecked ? 'checked' : '' }}>
                                        <span class="switch-slider"></span>
                                    </div>
                                    <div class="overflow-hidden">
                                        <span class="small fw-bold text-dark text-truncate d-block mb-1" title="{{ $permission->name }}">
                                            {{ ucwords(str_replace(['-', '.'], ' ', $permission->name)) }}
                                        </span>
                                        <span class="x-small text-muted text-uppercase tracking-widest fw-bold" style="font-size: 0.6rem;">
                                            Node ID: #{{ $permission->id }}
                                        </span>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        
                        @error('permission')
                            <div class="alert bg-soft-danger text-danger border-0 mt-4 d-flex align-items-center rounded-4">
                                <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i> 
                                <span class="fw-bold small">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-premium rounded-pill px-5 py-3 shadow-sm">
                <i class="fa-solid fa-shield-check me-2"></i> 
                <span class="fw-bold">{{ isset($role) ? 'Authorize Changes' : 'Initialize Role' }}</span>
            </button>
        </div>
    </form>
</div>

<script>
    /**
     * Toggle all permission checkboxes at once
     */
    function toggleAllPermissions() {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        const anyUnchecked = Array.from(checkboxes).some(c => !c.checked);
        checkboxes.forEach(c => {
            c.checked = anyUnchecked;
            // Trigger visual change for any switch wrappers if necessary
        });
    }

    /**
     * Optional: Hover effects for labels linked to switches
     */
    document.addEventListener("DOMContentLoaded", function() {
        const cards = document.querySelectorAll('.permission-card');
        cards.forEach(card => {
            card.addEventListener('click', function(e) {
                if (e.target.tagName !== 'INPUT') {
                    const input = this.querySelector('input');
                    input.checked = !input.checked;
                }
            });
        });
    });
</script>
@endsection