@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    
    {{-- 1. HEADER SECTION --}}
    <div class="content-header d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 animate__animated animate__fadeIn">
        <div>
            <div class="d-flex align-items-center mb-2">
                <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 me-2 font-monospace border border-primary border-opacity-10">
                    ID: #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}
                </span>
                
                @if(auth()->id() === $user->id)
                    <span class="badge bg-soft-success text-success rounded-pill px-3 py-1 me-2 border border-success border-opacity-10">This is You</span>
                @endif
                
                <span class="text-muted small fw-semibold d-flex align-items-center">
                    <i class="fa-solid fa-circle-check {{ $user->status ? 'text-success' : 'text-danger' }} me-1"></i>
                    Status: {{ $user->status ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <h2 class="fw-800 text-dark mb-0">{{ $user->name }}</h2>
        </div>

        <div class="action-stack d-flex gap-2 mt-3 mt-md-0">
            @if(auth()->id() === $user->id)
                <a href="{{ route('profile.edit') }}" class="btn btn-premium rounded-pill shadow-sm px-4">
                    <i class="fa-solid fa-pen-to-square me-2"></i>Edit Profile
                </a>
            @else
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-premium rounded-pill shadow-sm px-4">
                    <i class="fa-solid fa-pen-to-square me-2"></i>Edit User
                </a>
            @endif
        </div>
    </div>

    {{-- Success/Error Alerts --}}
    @if (session('success'))
        <div class="alert bg-soft-success text-success border-0 shadow-sm mb-4 d-flex align-items-center animate__animated animate__headShake">
            <i class="fa-solid fa-circle-check fs-4 me-3"></i>
            <div>
                <h6 class="fw-bold mb-0">Updated Successfully</h6>
                <p class="mb-0 small">{{ session('success') }}</p>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 2. STATS ROW --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-3 h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted x-small fw-bold text-uppercase mb-1 tracking-widest">Account Created</p>
                        <h5 class="fw-bold mb-0 text-dark">{{ $user->created_at->format('M Y') }}</h5>
                    </div>
                    <div class="icon-box bg-soft-primary text-primary rounded-3"><i class="fa-solid fa-calendar-days"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted x-small fw-bold text-uppercase mb-1 tracking-widest">Verified Email</p>
                        <h5 class="fw-bold mb-0 {{ $user->email_verified_at ? 'text-success' : 'text-warning' }}">
                            {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                        </h5>
                    </div>
                    <div class="icon-box bg-soft-success text-success rounded-3"><i class="fa-solid fa-shield-halved"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted x-small fw-bold text-uppercase mb-1 tracking-widest">Permissions</p>
                        <h5 class="fw-bold mb-0 text-dark">{{ $user->getAllPermissions()->count() }} Nodes</h5>
                    </div>
                    <div class="icon-box bg-soft-warning text-warning rounded-3"><i class="fa-solid fa-key"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 h-100 bg-primary animate__animated animate__fadeInUp" style="animation-delay: 0.4s">
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
        {{-- 3. LEFT COLUMN --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4 animate__animated animate__fadeInLeft">
                <div class="card-body p-4 text-center">
                    <div class="position-relative d-inline-block mb-3">
                        @if ($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" class="rounded-circle shadow-sm border p-1" style="width: 140px; height: 140px; object-fit: cover;">
                        @else
                            <div class="bg-soft-primary text-primary fw-bold rounded-circle border shadow-sm mx-auto d-flex align-items-center justify-content-center" style="width: 140px; height: 140px; font-size: 3rem;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="online-status {{ $user->status ? 'status-active' : 'status-inactive' }}" style="width: 24px; height: 24px; border-width: 4px;"></div>
                    </div>
                    <h5 class="fw-bold mb-2 text-dark">{{ $user->name }}</h5>
                    <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
                        @forelse ($user->getRoleNames() as $role)
                            <span class="role-tag role-user border shadow-xs">{{ $role }}</span>
                        @empty
                            <span class="text-muted small fst-italic">No Roles</span>
                        @endforelse
                    </div>

                    <div class="text-start mt-4">
                        <h6 class="fw-bold small mb-3 text-uppercase text-muted tracking-widest border-bottom pb-2">Contact Information</h6>
                        <div class="mb-3">
                            <label class="text-muted d-block x-small fw-bold text-uppercase">Email Address</label>
                            <span class="text-dark fw-bold small">{{ $user->email }}</span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted d-block x-small fw-bold text-uppercase">Phone Number</label>
                            <span class="text-dark fw-bold small">{{ $user->phone ?? 'Not Provided' }}</span>
                        </div>
                        <div class="mb-0">
                            <label class="text-muted d-block x-small fw-bold text-uppercase">Residential Address</label>
                            <span class="text-dark fw-bold small">{{ $user->address ?? 'Not Set' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Equipment Card --}}
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
                <div class="card-header border-0 py-3">
                    <h6 class="fw-bold mb-0 small text-uppercase text-muted tracking-widest">Assigned Equipment</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex align-items-center p-3 rounded-4 bg-light border border-dashed">
                        <div class="icon-box bg-white shadow-xs rounded-3 me-3 text-muted"><i class="fa-solid fa-desktop"></i></div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark small">Standard Workstation</div>
                            <div class="text-muted x-small">Provisioned: {{ $user->created_at->format('Y-m-d') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. RIGHT COLUMN --}}
        <div class="col-lg-8">
            
            {{-- Permissions Matrix --}}
            <div class="card border-0 shadow-sm mb-4 animate__animated animate__fadeInRight">
                <div class="card-body">
                    <h6 class="fw-bold mb-4 small text-uppercase text-muted tracking-widest">Security Access Matrix</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse ($user->getAllPermissions() as $permission)
                            <span class="badge border border-primary border-opacity-10 text-primary fw-medium bg-soft-primary rounded-pill px-3 py-2">
                                <i class="fa-solid fa-check-circle me-1 opacity-50"></i>
                                {{ str_replace('-', ' ', ucwords($permission->name)) }}
                            </span>
                        @empty
                            <span class="text-muted small fst-italic">No specific permissions assigned.</span>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- SECURITY SECTION (Only show for own profile) --}}
            @if(auth()->id() === $user->id)
            <div class="card border-0 shadow-sm mb-4 animate__animated animate__fadeInRight" style="animation-delay: 0.1s">
                <div class="card-header border-0 py-4 px-4">
                    <h6 class="fw-bold mb-0 small text-uppercase text-muted tracking-widest">
                        <i class="fa-solid fa-shield-halved me-2 text-warning"></i>Security Architecture
                    </h6>
                </div>
                <div class="card-body pt-0 px-4 pb-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center p-3 rounded-4 bg-light border border-dashed gap-3">
                        <div>
                            <p class="mb-1 fw-bold text-dark">Password & Authentication</p>
                            <p class="text-muted x-small mb-0">Update your access credentials. Ensure you use a strong, unique password.</p>
                        </div>
                        <button type="button" class="btn btn-premium rounded-pill px-4 shadow-sm fw-bold small" data-bs-toggle="modal" data-bs-target="#passwordModal">
                            <i class="fa-solid fa-key me-2"></i>Change Password
                        </button>
                    </div>
                </div>
            </div>
            @endif
            
            {{-- Recent Events --}}
            <div class="card border-0 shadow-sm mb-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                <div class="card-header border-0 py-4 px-4">
                    <h6 class="fw-bold mb-0 small text-uppercase text-muted tracking-widest">Audit Timeline</h6>
                </div>
                <div class="card-body pt-0 px-4 pb-4">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-start p-3 rounded-4 border border-dashed bg-light bg-opacity-50">
                            <div class="bg-soft-primary text-primary icon-box rounded-circle me-3"><i class="fa-solid fa-circle-info small"></i></div>
                            <div>
                                <p class="mb-0 small fw-bold text-dark">Account initialized in directory</p>
                                <span class="x-small text-muted">{{ $user->created_at->format('M d, Y • h:i A') }}</span>
                            </div>
                        </div>
                        @if($user->updated_at != $user->created_at)
                        <div class="d-flex align-items-start p-3 rounded-4 border border-dashed bg-light bg-opacity-50">
                            <div class="bg-soft-success text-success icon-box rounded-circle me-3"><i class="fa-solid fa-pen small"></i></div>
                            <div>
                                <p class="mb-0 small fw-bold text-dark">Profile synchronization complete</p>
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

{{-- 5. PASSWORD UPDATE MODAL --}}
@if(auth()->id() === $user->id)
<div class="modal fade" id="passwordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-soft-primary py-4 px-4">
                <h5 class="modal-title fw-800 text-primary"><i class="fa-solid fa-lock me-2"></i>Update Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('profile.password.update') }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="form-group mb-4">
                        <label class="form-label text-uppercase text-muted x-small fw-bold tracking-widest">Current Credentials</label>
                        <input type="password" name="current_password" class="form-control pro-input @error('current_password') is-invalid @enderror" placeholder="••••••••" required>
                        @error('current_password')<span class="text-danger x-small fw-bold mt-1 d-block">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label text-uppercase text-muted x-small fw-bold tracking-widest">New Password</label>
                        <input type="password" name="password" class="form-control pro-input @error('password') is-invalid @enderror" placeholder="Min. 8 characters" required>
                        @error('password')<span class="text-danger x-small fw-bold mt-1 d-block">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label text-uppercase text-muted x-small fw-bold tracking-widest">Verify Password</label>
                        <input type="password" name="password_confirmation" class="form-control pro-input" placeholder="Re-type new password" required>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0 d-flex gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold small border" data-bs-dismiss="modal">Discard</button>
                    <button type="submit" class="btn btn-premium rounded-pill px-4 shadow-sm fw-bold small">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection