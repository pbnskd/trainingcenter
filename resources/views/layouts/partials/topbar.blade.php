<header class="premium-topbar px-4 py-2 bg-white border-bottom d-flex align-items-center justify-content-between sticky-top">
    <div class="d-flex align-items-center gap-3">
        @auth
            <button class="btn btn-light d-lg-none shadow-sm border" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars-staggered"></i>
            </button>
        @endauth

        <nav aria-label="breadcrumb" class="d-none d-md-block">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item small">
                    <a href="{{ route('dashboard') }}" class="text-muted text-decoration-none">Nexus</a>
                </li>
                <li class="breadcrumb-item small active fw-bold text-dark" aria-current="page">
                    {{ ucfirst(request()->segment(1) ?? 'Dashboard') }}
                </li>
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center gap-2">
        @auth
            {{-- Quick Actions: Only show if user has CREATE permissions for either User or Permission --}}
            @if(Auth::user()->can('create', App\Models\User::class) || Auth::user()->can('create', App\Models\Permission::class))
                <div class="dropdown d-none d-sm-block">
                    <button class="btn btn-soft-primary rounded-circle p-2 border-0" data-bs-toggle="dropdown" title="Quick Actions">
                        <i class="fa-solid fa-circle-plus"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2 mt-3 animate__animated animate__fadeIn">
                        <li><h6 class="dropdown-header x-small fw-800 text-muted">Quick Create</h6></li>
                        
                        {{-- Policy Check: Create User --}}
                        @can('create', App\Models\User::class)
                            <li>
                                <a class="dropdown-item rounded-3 small py-2 d-flex align-items-center" href="{{ route('users.create') }}">
                                    <i class="fa-solid fa-user-plus me-2 text-primary"></i> New User
                                </a>
                            </li>
                        @endcan

                        {{-- Policy Check: Create Permission --}}
                        @can('create', App\Models\Permission::class)
                            <li>
                                <a class="dropdown-item rounded-3 small py-2 d-flex align-items-center" href="{{ route('permissions.create') }}">
                                    <i class="fa-solid fa-shield-plus me-2 text-success"></i> New Permission
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
            @endif

            {{-- Secure Messages --}}
            <div class="dropdown">
                <button class="btn btn-light rounded-circle p-2 position-relative border-0 shadow-none" data-bs-toggle="dropdown">
                    <i class="fa-regular fa-comment-dots text-muted"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary border border-white" style="font-size: 0.5rem;">3</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-0 rounded-4 overflow-hidden animate__animated animate__fadeIn" style="width: 320px;">
                    <div class="px-3 py-3 bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 small fw-bold text-uppercase tracking-wider">Secure Messages</h6>
                            <span class="badge bg-white text-primary x-small">3 New</span>
                        </div>
                    </div>
                    <div class="message-list p-2" style="max-height: 280px; overflow-y: auto;">
                        <a href="#" class="dropdown-item d-flex align-items-start gap-3 p-3 rounded-4 mb-1">
                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Felix" class="rounded-circle bg-light border" width="35" height="35">
                            <div class="overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold small text-dark">System Protocol</span>
                                    <span class="x-small text-muted">2m ago</span>
                                </div>
                                <div class="text-muted x-small text-truncate">Security node #402 was recalibrated successfully...</div>
                            </div>
                        </a>
                    </div>
                    <a href="#" class="dropdown-item text-center py-2 border-top x-small fw-bold text-primary bg-light">View All Transmissions</a>
                </div>
            </div>

            {{-- Notifications --}}
            <button class="btn btn-light rounded-circle p-2 position-relative border-0 shadow-none">
                <i class="fa-regular fa-bell text-muted"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
            </button>

            {{-- User Dropdown --}}
            <div class="dropdown ms-2">
                <div class="d-flex align-items-center gap-2 border-start ps-3 cursor-pointer" role="button" data-bs-toggle="dropdown">
                    <div class="text-end d-none d-md-block">
                        <div class="fw-bold small lh-1 text-dark">{{ Auth::user()->name }}</div>
                        <span class="text-success" style="font-size: 0.6rem;">
                            <i class="fa-solid fa-circle me-1" style="font-size: 0.4rem;"></i> Verified Identity
                        </span>
                    </div>
                    
                    {{-- Fixed Avatar Logic --}}
                    @if (Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                             class="rounded-circle shadow-sm border p-1 object-fit-cover" 
                             width="38" height="38">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4e73df&color=fff&size=200" 
                             class="rounded-circle shadow-sm border p-1" 
                             width="38" height="38">
                    @endif
                </div>

                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-2 rounded-4 animate__animated animate__fadeIn" style="min-width: 280px;">
                    <li class="px-3 py-3 bg-light rounded-4 mb-2">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="x-small fw-800 text-muted text-uppercase tracking-wider">Security Level</span>
                            <span class="badge bg-soft-success text-success p-1 x-small">Maximum</span>
                        </div>
                        <div class="progress shadow-sm" style="height: 5px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                        </div>
                    </li>

                    <li><h6 class="dropdown-header x-small fw-800 text-muted tracking-wider pb-1">Identity & Bio</h6></li>
                    <li>
                        {{-- Corrected Profile Route --}}
                        <a class="dropdown-item d-flex align-items-center py-2 rounded-3" href="{{ route('profile.show') }}">
                            <div class="icon-box bg-soft-primary text-primary me-3"><i class="fa-solid fa-id-card"></i></div>
                            <div>
                                <div class="fw-semibold small">View Profile</div>
                                <div class="x-small text-muted">Manage personal metadata</div>
                            </div>
                        </a>
                    </li>

                    <li><h6 class="dropdown-header x-small fw-800 text-muted tracking-wider mt-2 pb-1">Security Architecture</h6></li>
                    <li>
                        {{-- Corrected Password Route --}}
                        <a class="dropdown-item d-flex align-items-center py-2 rounded-4" href="{{ route('profile.show') }}#password-section">
                            <div class="icon-box bg-soft-warning text-warning me-3"><i class="fa-solid fa-lock-open"></i></div>
                            <div class="flex-grow-1">
                                <div class="fw-bold small lh-1 mb-1">Change Password</div>
                                <div class="x-small text-muted opacity-75">Update security credentials</div>
                            </div>
                            <i class="fa-solid fa-chevron-right x-small text-muted opacity-25 ms-2"></i>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center py-2 rounded-3" href="#">
                            <div class="icon-box bg-soft-info text-info me-3"><i class="fa-solid fa-shield-check"></i></div>
                            <div>
                                <div class="fw-semibold small">Multi-Factor Auth</div>
                                <div class="x-small text-muted">Manage 2FA protocols</div>
                            </div>
                        </a>
                    </li>

                    <li><hr class="dropdown-divider opacity-50 my-2"></li>

                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item d-flex align-items-center text-danger py-2 rounded-3">
                                <div class="icon-box bg-soft-danger text-danger me-3"><i class="fa-solid fa-power-off"></i></div>
                                <div>
                                    <div class="fw-bold small">Terminate Session</div>
                                    <div class="x-small opacity-75">Securely exit Nexus</div>
                                </div>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @else
            {{-- Guest View --}}
            <div class="d-flex gap-2">
                <a href="{{ route('login') }}" class="btn btn-sm btn-link text-muted text-decoration-none fw-bold">Sign In</a>
                <a href="{{ route('register') }}" class="btn btn-sm btn-primary rounded-pill px-3">Join Nexus</a>
            </div>
        @endauth
    </div>
</header>