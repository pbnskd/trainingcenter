@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    
    {{-- Header Section --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-800 text-dark mb-1">{{ isset($user) ? 'Edit User Profile' : 'Create New User' }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-muted text-decoration-none">Team</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ isset($user) ? 'Edit' : 'Create' }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-light border shadow-sm rounded-pill px-4">
            <i class="fa-solid fa-xmark me-2"></i>Cancel
        </a>
    </div>

    {{-- Form Wrapper --}}
    <form action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" 
          method="POST" 
          enctype="multipart/form-data">
        @csrf
        @if(isset($user))
            @method('PATCH')
        @endif

        <div class="row g-4">
            {{-- Left Column: Avatar & Status --}}
            <div class="col-xl-3 col-lg-4">
                <div class="card mb-4">
                    <div class="card-body p-4 text-center">
                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted fw-bold small tracking-widest mb-3">Profile Image</h6>
                            
                            <div class="position-relative d-inline-block">
                                {{-- Avatar Wrapper --}}
                                <div class="avatar-preview shadow-sm mx-auto mb-3 rounded-circle overflow-hidden position-relative border" 
                                     style="width: 120px; height: 120px; transition: var(--transition-bounce);">
                                    
                                    {{-- 1. Placeholder --}}
                                    <div id="placeholderPreview"
                                        class="bg-soft-primary d-flex align-items-center justify-content-center w-100 h-100 {{ isset($user) && $user->avatar ? 'd-none' : '' }}">
                                        <i class="fa-solid fa-user text-primary" style="font-size: 3rem;"></i>
                                    </div>

                                    {{-- 2. Actual Image --}}
                                    <img id="imagePreview"
                                        src="{{ isset($user) && $user->avatar ? asset('storage/' . $user->avatar) : '#' }}"
                                        class="object-fit-cover w-100 h-100 {{ isset($user) && $user->avatar ? '' : 'd-none' }}"
                                        alt="Profile Preview">
                                </div>

                                {{-- 3. Upload Button --}}
                                <label for="avatarUpload" 
                                       class="btn btn-premium rounded-circle position-absolute bottom-0 end-0 mb-1 me-1 shadow-sm z-3"
                                       style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer; padding: 0;">
                                    <i class="fa-solid fa-camera small text-white"></i>
                                    <input type="file" name="avatar" id="avatarUpload" class="d-none" accept="image/*">
                                </label>
                            </div>
                            
                            <p class="text-muted small mb-0 mt-2">High-res PNG or JPG preferred</p>
                        </div>

                        <hr class="my-4 border-light">
                        
                        {{-- Account Status --}}
                        <div class="text-start form-group mb-0">
                            <label class="form-label text-uppercase text-muted fw-bold small tracking-widest mb-3">Account Status</label>
                            
                            {{-- Integrated your Premium Custom Switch Toggle --}}
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-light">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-power-off text-muted"></i>
                                    <span class="fw-semibold text-dark small">Active Account</span>
                                </div>
                                
                                {{-- Hidden input to ensure 0 is sent if switch is toggled off --}}
                                <input type="hidden" name="status" value="0">
                                
                                <label class="custom-switch mb-0">
                                    <input type="checkbox" name="status" value="1" 
                                        {{ old('status', isset($user) ? $user->status : 1) == 1 ? 'checked' : '' }}>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: User Details --}}
            <div class="col-xl-9 col-lg-8">
                <div class="card">
                    <div class="card-header border-0 py-4 px-4">
                        <h5 class="fw-bold mb-0">User Identity</h5>
                        <p class="text-muted small mb-0">Fill in the details below to {{ isset($user) ? 'update' : 'register' }} an account.</p>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="row g-4">
                            
                            {{-- Name --}}
                            <div class="col-md-6 form-group mb-0">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" 
                                    value="{{ old('name', optional($user ?? null)->name) }}"
                                    class="form-control pro-input" placeholder="Enter first and last name">
                                @error('name') <span class="text-danger x-small mt-1 d-block">{{ $message }}</span> @enderror
                            </div>
                            
                            {{-- Email --}}
                            <div class="col-md-6 form-group mb-0">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" 
                                    value="{{ old('email', optional($user ?? null)->email) }}"
                                    class="form-control pro-input" placeholder="username@domain.com">
                                @error('email') <span class="text-danger x-small mt-1 d-block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Phone --}}
                            <div class="col-md-6 form-group mb-0">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" 
                                    value="{{ old('phone', optional($user ?? null)->phone) }}"
                                    class="form-control pro-input" placeholder="e.g., +1 555-0123">
                                @error('phone') <span class="text-danger x-small mt-1 d-block">{{ $message }}</span> @enderror
                            </div>
                            
                            {{-- Address --}}
                            <div class="col-md-6 form-group mb-0">
                                <label class="form-label">Mailing Address</label>
                                <input type="text" name="address" 
                                    value="{{ old('address', optional($user ?? null)->address) }}"
                                    class="form-control pro-input" placeholder="Street, City, Postcode">
                                @error('address') <span class="text-danger x-small mt-1 d-block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Security Section --}}
                            <div class="col-12 mt-5">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="bg-soft-primary p-2 rounded-3 me-2 text-primary d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                    <h6 class="fw-bold mb-0">Security Credentials</h6>
                                </div>
                                <div class="row g-3 p-3 rounded-4 border bg-light">
                                    <div class="col-md-6 form-group mb-0">
                                        <label class="form-label">{{ isset($user) ? 'Change Password' : 'Password' }}</label>
                                        <input type="password" name="password" class="form-control pro-input"
                                            placeholder="{{ isset($user) ? 'Leave empty to keep current' : 'Min. 8 characters' }}"
                                            autocomplete="new-password">
                                        @error('password') <span class="text-danger x-small mt-1 d-block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6 form-group mb-0">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password" name="password_confirmation"
                                            class="form-control pro-input" placeholder="Re-type password"
                                            autocomplete="new-password">
                                    </div>
                                </div>
                            </div>

                            {{-- Roles Section (Protected by Policy) --}}
                            @can('viewAny', App\Models\Role::class)
                            <div class="col-12 mt-5 form-group mb-0">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="bg-soft-warning p-2 rounded-3 me-2 text-warning d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="fa-solid fa-shield-halved"></i>
                                    </span>
                                    <h6 class="fw-bold mb-0">Access Permissions</h6>
                                </div>
                                
                                <select name="roles[]" class="form-select pro-input" multiple size="3" style="min-height: 120px;">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role }}" class="py-2 px-3 border-bottom"
                                            {{ in_array($role, old('roles', isset($userRole) ? $userRole : [])) ? 'selected' : '' }}>
                                            {{ $role }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="mt-2 text-muted small"><i class="fa-solid fa-circle-info me-1 text-primary"></i> Hold CTRL (Windows) or CMD (Mac) to select multiple roles.</div>
                                @error('roles') <span class="text-danger x-small mt-1 d-block">{{ $message }}</span> @enderror
                            </div>
                            @endcan
                            
                            {{-- Form Actions --}}
                            <div class="col-12 mt-5 pt-4 border-top d-flex flex-column flex-sm-row gap-3">
                                <button type="submit" class="btn btn-premium rounded-pill px-5 py-2">
                                    <i class="fa-solid fa-save me-2"></i>{{ isset($user) ? 'Update Profile' : 'Create Account' }}
                                </button>
                                <a href="{{ route('users.index') }}" class="btn btn-light rounded-pill px-5 py-2 border shadow-sm fw-semibold text-muted text-center">Cancel</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Avatar Live Preview Script
    document.addEventListener("DOMContentLoaded", function() {
        const uploadInput = document.getElementById('avatarUpload');
        
        if (uploadInput) {
            uploadInput.onchange = function(evt) {
                const file = evt.target.files[0];
                if (file) {
                    const preview = document.getElementById('imagePreview');
                    const placeholder = document.getElementById('placeholderPreview');

                    const objectUrl = URL.createObjectURL(file);

                    preview.src = objectUrl;
                    preview.classList.remove('d-none');

                    if (placeholder) {
                        placeholder.classList.add('d-none');
                    }

                    // Cleanup memory
                    preview.onload = () => URL.revokeObjectURL(objectUrl);
                }
            };
        }
    });
</script>
@endsection