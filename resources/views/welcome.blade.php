@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                {{-- Decorative Header --}}
                <div class="card-header bg-primary text-white p-4 text-center border-0">
                    <h4 class="m-0 fw-bold">{{ config('app.name', 'Laravel') }} Access Control</h4>
                </div>

                <div class="card-body p-5 text-center bg-white">
                    {{-- Hero Icon (SVG Shield) --}}
                    <div class="mb-4 text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-shield-lock-fill" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.777 11.777 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7.159 7.159 0 0 0 1.048-.625 11.775 11.775 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.541 1.541 0 0 0-1.044-1.263 62.467 62.467 0 0 0-2.887-.87C9.843.266 8.69 0 8 0zm0 5a1.5 1.5 0 0 1 .5 2.915l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99A1.5 1.5 0 0 1 8 5z"/>
                        </svg>
                    </div>

                    <h1 class="display-5 fw-bold mb-3">Welcome Aboard</h1>
                    <p class="lead text-muted mb-5">
                        Securely manage your organization's users, roles, and permissions in one centralized dashboard.
                    </p>

                    {{-- Feature Grid --}}
                    <div class="row g-4 mb-5 justify-content-center">
                        <div class="col-sm-4">
                            <div class="p-3 border rounded-3 bg-light">
                                <h5 class="fw-bold text-dark">Users</h5>
                                <small class="text-muted">Manage profiles</small>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 border rounded-3 bg-light">
                                <h5 class="fw-bold text-dark">Roles</h5>
                                <small class="text-muted">Assign access</small>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 border rounded-3 bg-light">
                                <h5 class="fw-bold text-dark">Security</h5>
                                <small class="text-muted">Strict controls</small>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-5 shadow-sm rounded-pill fw-bold">
                                <i class="bi bi-speedometer2 me-2"></i> Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5 shadow-sm rounded-pill fw-bold">
                                Login
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-outline-dark btn-lg px-5 shadow-sm rounded-pill fw-bold">
                                    Register
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>

                {{-- Footer Area --}}
                <div class="card-footer bg-light p-3 text-center border-0">
                    <small class="text-muted">
                        System Status: <span class="text-success fw-bold">● Operational</span> 
                        &nbsp;|&nbsp; Laravel v{{ Illuminate\Foundation\Application::VERSION }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection