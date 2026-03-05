@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">

    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 animate__animated animate__fadeIn">
        <div>
            <div class="d-flex align-items-center mb-2">
                <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 me-2 font-monospace border border-primary border-opacity-10">
                    ID: #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}
                </span>
                
                @if (auth()->id() == $user->id)
                    <span class="badge bg-soft-success text-success rounded-pill px-3 py-1 me-2 border border-success border-opacity-10">This is You</span>
                @endif
                
                <span class="text-muted small fw-semibold d-flex align-items-center">
                    <i class="fa-solid fa-circle-check text-success me-1"></i>
                    {{ $user->status ? 'Active Account' : 'Inactive' }}
                </span>
            </div>
            <h2 class="fw-800 text-dark mb-0">{{ $user->name }}</h2>
        </div>

        <div class="d-flex gap-2">
            {{-- Policy Check: View Any User (Directory) --}}
            @can('viewAny', App\Models\User::class)
                <a href="{{ route('users.index') }}" class="btn btn-light rounded-pill shadow-sm border text-muted px-4">
                    <i class="fa-solid fa-arrow-left me-2"></i>Directory
                </a>
            @endcan

            {{-- Policy Check: Update User --}}
            @can('update', $user)
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-premium rounded-pill shadow-sm px-4">
                    <i class="fa-solid fa-pen-to-square me-2"></i>Update Info
                </a>
            @endcan
        </div>
    </div>

    {{-- Stats Cards Row --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Created --}}
        <div class="col-md-3">
            <div class="card p-3 h-100 border-0 shadow-sm animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted x-small fw-bold text-uppercase mb-1 tracking-widest">Account Created</p>
                        <h5 class="fw-bold mb-0 text-dark">{{ $user->created_at->format('M Y') }}</h5>
                    </div>
                    <div class="icon-box bg-soft-primary text-primary rounded-3"><i class="fa-solid fa-calendar-days"></i></div>
                </div>
            </div>
        </div>
        {{-- Card 2: Verification --}}
        <div class="col-md-3">
            <div class="card p-3 h-100 border-0 shadow-sm animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted x-small fw-bold text-uppercase mb-1 tracking-widest">Verified Email</p>
                        <h5 class="fw-bold mb-0 text-success">
                            {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                        </h5>
                    </div>
                    <div class="icon-box bg-soft-success text-success rounded-3"><i class="fa-solid fa-shield-halved"></i></div>
                </div>
            </div>
        </div>
        {{-- Card 3: Permissions --}}
        <div class="col-md-3">
            <div class="card p-3 h-100 border-0 shadow-sm animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted x-small fw-bold text-uppercase mb-1 tracking-widest">Permissions</p>
                        <h5 class="fw-bold mb-0 text-dark">{{ $user->getAllPermissions()->count() }} Nodes</h5>
                    </div>
                    <div class="icon-box bg-soft-warning text-warning rounded-3"><i class="fa-solid fa-key"></i></div>
                </div>
            </div>
        </div>
        {{-- Card 4: Roles --}}
        <div class="col-md-3">
            <div class="card p-3 h-100 border-0 shadow-sm bg-primary animate__animated animate__fadeInUp" style="animation-delay: 0.4s">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="x-small fw-bold text-uppercase mb-1 opacity-75 text-white tracking-widest">Assigned Roles</p>
                        <h5 class="fw-bold mb-0 text-white">{{ $user->roles->count() }} Active</h5>
                    </div>
                    <div class="icon-box bg-white bg-opacity-25 text-white rounded-3"><i class="fa-solid fa-id-badge"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column: Identity Card --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4 animate__animated animate__fadeInLeft">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block mb-3">
                            @if ($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}"
                                    class="rounded-circle shadow-sm border p-1"
                                    style="width: 130px; height: 130px; object-fit: cover;">
                            @else
                                <div class="avatar-placeholder bg-soft-primary text-primary fw-bold rounded-circle shadow-sm border p-1 mx-auto d-flex align-items-center justify-content-center" 
                                     style="width: 130px; height: 130px; font-size: 3rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="online-status {{ $user->status ? 'status-active' : 'status-inactive' }}" 
                                 style="width: 24px; height: 24px; border-width: 4px;"></div>
                        </div>
                        
                        <h5 class="fw-bold mb-1 text-dark">{{ $user->name }}</h5>
                        
                        <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
                            @foreach ($user->getRoleNames() as $role)
                                <span class="role-tag role-user border shadow-xs">{{ $role }}</span>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-center gap-4 py-3 border-top border-bottom">
                            <div class="text-center">
                                <small class="d-block text-muted x-small fw-bold text-uppercase tracking-widest">Permissions</small>
                                <span class="fw-bold text-dark fs-5">{{ $user->getAllPermissions()->count() }}</span>
                            </div>
                            <div class="vr opacity-10"></div>
                            <div class="text-center">
                                <small class="d-block text-muted x-small fw-bold text-uppercase tracking-widest">Status</small>
                                <span class="fw-bold fs-5 text-{{ $user->status ? 'success' : 'danger' }}">
                                    {{ $user->status ? 'Online' : 'Offline' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold small mb-3 text-uppercase text-muted tracking-widest">Contact Information</h6>
                    <div class="mb-3 d-flex align-items-center gap-3">
                        <div class="icon-box bg-light rounded-circle text-muted" style="width:35px; height:35px;"><i class="fa-solid fa-envelope small"></i></div>
                        <div>
                            <label class="text-muted d-block x-small fw-bold text-uppercase">Email Address</label>
                            <span class="text-dark fw-bold small">{{ $user->email }}</span>
                        </div>
                    </div>
                    <div class="mb-3 d-flex align-items-center gap-3">
                        <div class="icon-box bg-light rounded-circle text-muted" style="width:35px; height:35px;"><i class="fa-solid fa-phone small"></i></div>
                        <div>
                            <label class="text-muted d-block x-small fw-bold text-uppercase">Phone Number</label>
                            <span class="text-dark fw-bold small">{{ $user->phone ?? 'Not Provided' }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-light rounded-circle text-muted" style="width:35px; height:35px;"><i class="fa-solid fa-location-dot small"></i></div>
                        <div>
                            <label class="text-muted d-block x-small fw-bold text-uppercase">Mailing Address</label>
                            <span class="text-dark fw-bold small">{{ $user->address ?? 'Not Set' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Equipment Card --}}
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0 small text-uppercase text-muted tracking-widest">Assigned Equipment</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex align-items-center p-2 rounded-3 bg-light border border-dashed border-opacity-50">
                        <div class="icon-box bg-white shadow-xs rounded-3 me-3 text-muted"><i class="fa-solid fa-desktop"></i></div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark small">Standard Workstation</div>
                            <div class="text-muted x-small">Assigned: {{ $user->created_at->format('Y-m-d') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Detailed Matrix --}}
        <div class="col-lg-8">
            
            {{-- Permissions Matrix --}}
            <div class="card border-0 shadow-sm mb-4 animate__animated animate__fadeInRight">
                <div class="card-body">
                    <h6 class="fw-bold mb-4 small text-uppercase text-muted tracking-widest">Role Permissions Matrix</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse ($user->getAllPermissions() as $permission)
                            <span class="badge border border-primary border-opacity-10 text-primary fw-medium bg-soft-primary rounded-pill px-3 py-2">
                                <i class="fa-solid fa-check-circle me-1 opacity-50"></i>
                                {{ str_replace('-', ' ', ucwords($permission->name)) }}
                            </span>
                        @empty
                            <div class="text-center w-100 py-4">
                                <i class="fa-solid fa-shield-slash text-muted opacity-25 mb-2" style="font-size: 2rem;"></i>
                                <p class="text-muted small fst-italic">No specific permissions assigned to this role hierarchy.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Activity / Workflow --}}
            <div class="card border-0 shadow-sm mb-4 animate__animated animate__fadeInRight" style="animation-delay: 0.1s">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 small text-uppercase text-muted tracking-widest">Current Task Workflow</h6>
                    </div>
                    <span class="badge bg-soft-success text-success fw-bold border border-success border-opacity-10">
                        <i class="fa-solid fa-bolt-lightning me-1"></i> Active Session
                    </span>
                </div>
                <div class="card-body pt-0 px-4 pb-4">
                    <div class="premium-grid rounded-3 overflow-hidden border">
                        <div class="grid-header py-2" style="grid-template-columns: 2fr 1fr 1fr; background: #fcfcfd;">
                            <div class="ps-3">PROJECT NAME</div>
                            <div>ACCESS</div>
                            <div class="pe-3">PROGRESS</div>
                        </div>
                        <div class="grid-body">
                            <div class="grid-row py-3" style="grid-template-columns: 2fr 1fr 1fr; border-bottom: none;">
                                <div class="ps-3 fw-bold text-dark small">User Management System</div>
                                <div><span class="badge bg-soft-primary text-primary x-small">Full Access</span></div>
                                <div class="pe-3">
                                    <div class="progress rounded-pill" style="height: 6px;">
                                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Security Log --}}
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                <div class="card-header bg-white border-0 py-4 px-4">
                    <h6 class="fw-bold mb-0 small text-uppercase text-muted tracking-widest">Recent Security Events</h6>
                </div>
                <div class="card-body pt-0 px-4 pb-4">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-start p-3 rounded-4 border border-dashed bg-light bg-opacity-50">
                            <div class="bg-soft-primary text-primary icon-box rounded-circle me-3">
                                <i class="fa-solid fa-circle-info small"></i>
                            </div>
                            <div>
                                <p class="mb-0 small fw-bold text-dark">Account created in system</p>
                                <span class="x-small text-muted">{{ $user->created_at->format('M d, Y • h:i A') }}</span>
                            </div>
                        </div>
                        
                        @if($user->roles->isNotEmpty())
                        <div class="d-flex align-items-start p-3 rounded-4 border border-dashed bg-light bg-opacity-50">
                            <div class="bg-soft-success text-success icon-box rounded-circle me-3">
                                <i class="fa-solid fa-shield-halved small"></i>
                            </div>
                            <div>
                                <p class="mb-0 small fw-bold text-dark">Role "{{ $user->getRoleNames()->first() }}" assigned</p>
                                <span class="x-small text-muted">{{ $user->updated_at->format('M d, Y • h:i A') }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection