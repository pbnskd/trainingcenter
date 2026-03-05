@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 animate__animated animate__fadeIn">
        <div>
            <h4 class="fw-800 text-dark mb-1">Node Inspection</h4>
            <p class="text-muted small mb-0 d-flex align-items-center">
                Reviewing security configuration for 
                <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 ms-2 font-monospace border border-primary border-opacity-10">
                    {{ $permission->name }}
                </span>
            </p>
        </div>
        <a href="{{ route('permissions.index') }}" class="btn btn-light rounded-pill border shadow-sm fw-bold px-4">
            <i class="fa-solid fa-arrow-left me-2"></i> Back to Registry
        </a>
    </div>

    <div class="row g-4">
        {{-- Main Detail Card --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInLeft">
                <div class="card-header border-0 py-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-soft-primary text-primary rounded-3 me-3" style="width: 45px; height: 45px;">
                            <i class="fa-solid fa-shield-halved fs-5"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">Permission Details</h5>
                    </div>
                </div>

                <div class="card-body p-4 pt-0">
                    <div class="mb-4">
                        <span class="d-block text-muted x-small fw-bold text-uppercase tracking-widest mb-2">Identification String</span>
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-4 bg-light border border-dashed">
                            <code class="fs-6 text-dark font-monospace fw-bold">{{ $permission->name }}</code>
                            <span class="badge bg-white text-muted border border-light px-3 py-2 rounded-pill shadow-xs x-small">
                                ID: #{{ str_pad($permission->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <span class="d-block text-muted x-small fw-bold text-uppercase tracking-widest mb-2">Operational Description</span>
                        <div class="p-3 rounded-4 bg-light border border-dashed text-dark small lh-base">
                            {{ $permission->description ?: 'No operational description provided for this security node.' }}
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 rounded-4 bg-light border border-dashed h-100">
                                <span class="d-block text-muted x-small fw-bold text-uppercase tracking-widest mb-2">Guard Interface</span>
                                <div class="fw-bold text-dark d-flex align-items-center">
                                    <div class="icon-box bg-white shadow-xs rounded-circle me-2" style="width: 30px; height: 30px;">
                                        <i class="fa-solid fa-server x-small text-muted"></i>
                                    </div>
                                    {{ $permission->guard_name }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-4 bg-light border border-dashed h-100">
                                <span class="d-block text-muted x-small fw-bold text-uppercase tracking-widest mb-2">Initialized On</span>
                                <div class="fw-bold text-dark d-flex align-items-center">
                                    <div class="icon-box bg-white shadow-xs rounded-circle me-2" style="width: 30px; height: 30px;">
                                        <i class="fa-regular fa-calendar x-small text-muted"></i>
                                    </div>
                                    {{ $permission->created_at->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer border-0 p-4 bg-light bg-opacity-50">
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        {{-- Policy Check: Update Permission --}}
                        @can('update', $permission)
                            <a href="{{ route('permissions.edit', $permission->id) }}"
                                class="btn btn-premium rounded-pill px-5 py-2 shadow-sm">
                                <i class="fa-solid fa-sliders me-2"></i> Edit Configuration
                            </a>
                        @endcan

                        {{-- Policy Check: Delete Permission --}}
                        @can('delete', $permission)
                            <button onclick="handlePurge('{{ $permission->id }}')"
                                class="btn btn-light text-danger border border-danger border-opacity-10 rounded-pill px-5 py-2 fw-bold shadow-xs">
                                <i class="fa-solid fa-trash-can me-2"></i> Purge Node
                            </button>
                            <form id="purge-{{ $permission->id }}"
                                action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        {{-- Side Note Column --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-primary text-white p-4 mb-4 animate__animated animate__fadeInRight">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-box bg-white bg-opacity-20 rounded-3 me-2" style="width: 35px; height: 35px;">
                        <i class="fa-solid fa-circle-info small"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-uppercase small tracking-widest">System Note</h6>
                </div>
                <p class="small opacity-75 mb-0" style="line-height: 1.6;">
                    This permission node is part of the core security architecture. Any modifications or purging of this node may 
                    immediately affect active user access sessions across the management interface.
                </p>
            </div>

            {{-- Optional: Linked Roles visualization --}}
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                <div class="card-header border-0 py-3 px-4">
                    <h6 class="fw-bold mb-0 small text-uppercase text-muted tracking-widest">Registry Linkage</h6>
                </div>
                <div class="card-body pt-0 px-4 pb-4 text-center py-4">
                    <div class="mb-3 opacity-25">
                        <i class="fa-solid fa-link text-muted fa-2x"></i>
                    </div>
                    <p class="small text-muted mb-0">Attached to multiple security profiles within the Nexus protocol.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Handle security node purging with confirm logic
     */
    function handlePurge(id) {
        if(typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Purge Security Node?',
                text: "This may break existing roles and revoke user access across the system.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, purge it',
                customClass: {
                    confirmButton: 'btn btn-danger rounded-pill px-4',
                    cancelButton: 'btn btn-light border rounded-pill px-4 ms-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('purge-' + id).submit();
                }
            });
        } else {
            if(confirm('Are you sure you want to purge this security node? This may break existing roles.')) {
                document.getElementById('purge-' + id).submit();
            }
        }
    }
</script>
@endsection