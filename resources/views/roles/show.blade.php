@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3 animate__animated animate__fadeIn">
        <div>
            <div class="d-flex align-items-center mb-2">
                <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 me-2 border border-primary border-opacity-10">
                    Nexus Security Protocol
                </span>
                <span class="text-muted small fw-semibold d-flex align-items-center">
                    <i class="fa-solid fa-shield-halved text-primary me-1"></i> 
                    Role ID: #{{ str_pad($role->id, 3, '0', STR_PAD_LEFT) }}
                </span>
            </div>
            <h2 class="fw-800 text-dark mb-0">{{ $role->name }}</h2>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('roles.index') }}" class="btn btn-light rounded-pill border shadow-sm fw-bold px-4">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to Roles
            </a>
            
            {{-- POLICY CHECK: Update Role --}}
            @can('update', $role)
                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-premium rounded-pill shadow-sm px-4 fw-bold">
                    <i class="fa-solid fa-sliders me-2"></i>Configure Permissions
                </a>
            @endcan
        </div>
    </div>

    <div class="row g-4">
        
        {{-- LEFT COLUMN: Role Info --}}
        <div class="col-lg-4">
            {{-- Main Info Card --}}
            <div class="card border-0 shadow-sm mb-4 animate__animated animate__fadeInLeft">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="bg-soft-primary text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" 
                             style="width: 80px; height: 80px; transition: var(--transition-bounce);">
                            <i class="fa-solid fa-fingerprint fa-2x"></i>
                        </div>
                        <h5 class="fw-bold mb-1 text-dark">{{ $role->name }}</h5>
                        <span class="badge bg-light text-muted border px-3 rounded-pill mb-3 x-small tracking-widest fw-bold">
                            ACCESS CONTROL LEVEL
                        </span>
                    </div>

                    <div class="p-3 rounded-4 bg-light border border-dashed mb-4">
                        <label class="x-small text-primary fw-bold text-uppercase d-block mb-1 tracking-widest">
                            Functional Scope
                        </label>
                        <p class="text-muted small mb-0 lh-base">
                            {{ $role->description ?? 'No specific functional description has been assigned to this security profile yet.' }}
                        </p>
                    </div>

                    <hr class="border-light my-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-bold text-uppercase tracking-widest">Inherited Privileges</span>
                        <span class="badge bg-primary rounded-pill px-3 py-2 shadow-sm">
                            {{ $rolePermissions->count() }} Authorized Actions
                        </span>
                    </div>

                    <div class="alert bg-soft-primary border-0 small mb-0 mt-3 d-flex align-items-start p-3 rounded-4">
                        <i class="fa-solid fa-circle-info text-primary me-2 mt-1"></i>
                        <div class="text-dark-50">
                            <strong class="text-primary">Security Note:</strong> Users assigned to this role inherit all active matrix permissions immediately across the guard: <code class="text-primary">{{ $role->guard_name }}</code>.
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Metadata Card --}}
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
                <div class="card-header border-0 py-3">
                    <h6 class="fw-bold mb-0 small text-uppercase text-muted tracking-widest">Role Metadata</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="mb-3">
                        <label class="x-small text-muted fw-bold text-uppercase d-block tracking-widest">Auth Guard</label>
                        <span class="small fw-bold text-dark font-monospace">{{ $role->guard_name }}</span>
                    </div>
                    <div class="mb-0">
                        <label class="x-small text-muted fw-bold text-uppercase d-block tracking-widest">Registry Status</label>
                        <span class="badge bg-soft-success text-success border border-success border-opacity-10 rounded-pill px-2 py-1 x-small">
                            <i class="fa-solid fa-check-double me-1"></i>Verified Node
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Permission Matrix --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInRight">
                <div class="card-header border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 small text-uppercase text-muted tracking-widest">Permission Matrix</h6>
                    <span class="badge bg-light text-muted border px-3 py-2 rounded-pill x-small">
                        {{ $rolePermissions->count() }} Total Capabilities
                    </span>
                </div>
                <div class="card-body pt-0 px-4 pb-4">
                    <div class="row g-3">
                        @forelse($rolePermissions as $key => $permission)
                        <div class="col-md-6 col-xl-4 animate__animated animate__fadeInUp" style="animation-delay: {{ $key * 0.03 }}s">
                            <div class="p-3 border rounded-4 bg-light d-flex align-items-start h-100 transition-hover border-dashed">
                                <div class="bg-white rounded-circle shadow-xs text-success flex-shrink-0 d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;">
                                    <i class="fa-solid fa-check small"></i>
                                </div>
                                
                                <div class="overflow-hidden">
                                    <div class="fw-bold small text-dark text-truncate mb-1" title="{{ $permission->name }}">
                                        {{ ucwords(str_replace(['-', '.'], ' ', $permission->name)) }}
                                    </div>
                                    
                                    <div class="text-muted mb-2 text-truncate small" style="font-size: 0.75rem; line-height: 1.2;">
                                        {{ $permission->description ?? 'Standard system privilege' }}
                                    </div>

                                    <div class="x-small text-success fw-bold text-uppercase tracking-widest" style="font-size: 0.6rem;">
                                        <i class="fa-solid fa-shield-halved me-1"></i>Authorized
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center py-5">
                            <div class="mb-3 opacity-25">
                                <i class="fa-solid fa-shield-slash text-muted fa-4x"></i>
                            </div>
                            <h6 class="text-muted fw-bold">No permissions assigned</h6>
                            <p class="small text-muted mb-0">This role currently has no active authorized actions in the system.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection