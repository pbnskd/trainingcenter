@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    
    {{-- Header / Back Link --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 animate__animated animate__fadeIn">
        <div>
            <div class="d-flex align-items-center mb-2">
                <a href="{{ route('profile.show') }}" class="text-muted small text-decoration-none hover-link me-2">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Profile
                </a>
            </div>
            <h2 class="fw-800 text-dark mb-0">Edit My Details</h2>
        </div>
    </div>

    {{-- Error Message Alert (Global) --}}
    @if ($errors->any())
        <div class="alert bg-soft-danger text-danger border-0 shadow-sm mb-4 d-flex align-items-center animate__animated animate__headShake">
            <i class="fa-solid fa-triangle-exclamation fs-4 me-3"></i>
            <div>
                <h6 class="fw-bold mb-0">Action Failed</h6>
                <p class="mb-0 small">Please check the form below for errors.</p>
            </div>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="row g-4">
            
            {{-- LEFT COLUMN: Avatar & Status --}}
            <div class="col-xl-3 col-lg-4">
                <div class="card mb-4 animate__animated animate__fadeInLeft">
                    <div class="card-body p-4 text-center">
                        
                        {{-- Avatar Section --}}
                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted fw-bold small tracking-widest mb-3">Profile Image</h6>
                            <div class="position-relative d-inline-block">
                                <div class="avatar-preview shadow-sm mx-auto mb-3 rounded-circle overflow-hidden position-relative border" 
                                     style="width: 130px; height: 130px; transition: var(--transition-bounce);">
                                    
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" id="imagePreview" class="object-fit-cover w-100 h-100 border border-3 border-white rounded-circle">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=6366f1&color=fff&size=200" id="imagePreview" class="object-fit-cover w-100 h-100 border border-3 border-white rounded-circle">
                                    @endif
                                    
                                    {{-- NEW: Refined Upload Button using global accent --}}
                                    <label for="avatarUpload" class="avatar-edit-btn shadow-sm" style="bottom: 5px; right: 5px;" data-bs-toggle="tooltip" title="Change Image">
                                        <i class="fa-solid fa-camera text-dark small"></i>
                                        <input type="file" name="avatar" id="avatarUpload" class="d-none" accept="image/*" onchange="previewImage(this)">
                                    </label>
                                </div>
                            </div>
                            <p class="text-muted x-small mb-0">High-res PNG or JPG preferred</p>
                            @error('avatar') <span class="text-danger x-small d-block mt-2 fw-bold">{{ $message }}</span> @enderror
                        </div>

                        <hr class="my-4 border-light">

                        {{-- Read-Only Status --}}
                        <div class="text-start">
                            <label class="form-label text-uppercase text-muted fw-bold small tracking-widest mb-3">Account Status</label>
                            <div class="p-3 rounded-4 bg-soft-success border border-dashed d-flex align-items-center">
                                <div class="icon-box bg-white text-success rounded-circle me-3 shadow-xs" style="width:35px; height:35px;">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-bold text-dark small">Active Account</span>
                                    <span class="d-block x-small text-muted">Your profile is visible</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Form Fields --}}
            <div class="col-xl-9 col-lg-8">
                <div class="card animate__animated animate__fadeInRight">
                    <div class="card-header border-0 py-4 px-4">
                        <h5 class="fw-bold mb-0">Profile Information</h5>
                        <p class="text-muted small mb-0">Update your personal identification and contact details.</p>
                    </div>
                    
                    <div class="card-body p-4 pt-0">
                        <div class="row g-4">
                            
                            {{-- Full Name --}}
                            <div class="col-md-6 form-group mb-0">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control pro-input" placeholder="Your full name">
                                @error('name') <span class="text-danger x-small fw-bold mt-1 d-block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Email (READ ONLY) --}}
                            <div class="col-md-6 form-group mb-0">
                                <label class="form-label">
                                    Email Address <i class="fa-solid fa-lock text-muted ms-1" style="font-size: 0.7rem;"></i>
                                </label>
                                <input type="email" value="{{ $user->email }}" class="form-control pro-input bg-light text-muted cursor-not-allowed" readonly>
                                <div class="mt-2 text-muted x-small d-flex align-items-center">
                                    <i class="fa-solid fa-circle-info me-1"></i>Login username cannot be modified here.
                                </div>
                            </div>

                            {{-- Phone --}}
                            <div class="col-md-6 form-group mb-0">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control pro-input" placeholder="e.g., +1 555-0123">
                                @error('phone') <span class="text-danger x-small fw-bold mt-1 d-block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Address --}}
                            <div class="col-md-6 form-group mb-0">
                                <label class="form-label">Mailing Address</label>
                                <input type="text" name="address" value="{{ old('address', $user->address) }}" class="form-control pro-input" placeholder="Street, City, Postcode">
                                @error('address') <span class="text-danger x-small fw-bold mt-1 d-block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Form Actions --}}
                            <div class="col-12 mt-5 pt-4 border-top d-flex flex-column flex-sm-row justify-content-end gap-3">
                                <a href="{{ route('profile.show') }}" class="btn btn-light rounded-pill px-5 py-2 border shadow-sm fw-bold text-muted text-center order-2 order-sm-1">
                                    Discard Changes
                                </a>
                                <button type="submit" class="btn btn-premium rounded-pill px-5 py-2 shadow-sm order-1 order-sm-2">
                                    <i class="fa-solid fa-save me-2"></i>Save My Profile
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    /**
     * Preview image on upload
     */
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                preview.src = e.target.result;
                // Add a small bounce animation to the preview when updated
                preview.classList.add('animate__animated', 'animate__pulse');
                preview.addEventListener('animationend', () => {
                    preview.classList.remove('animate__animated', 'animate__pulse');
                });
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection