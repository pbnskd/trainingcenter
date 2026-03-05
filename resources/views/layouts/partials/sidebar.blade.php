<aside id="sidebar" class="sidebar-wrapper">
    <div class="sidebar-brand">
        <i class="fas fa-bolt-lightning me-2 text-primary"></i>NEXUS
    </div>
    
    <nav class="sidebar-menu simplebar-scrollable-y">
        
        {{-- Section: Core Area --}}
        <div class="nav-group-label">Core Area</div>
        <a href="{{ route('dashboard') }}" 
           class="nav-link-premium {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-house-chimney"></i> Overview
        </a>

        {{-- Section: Personal --}}
        <div class="nav-group-label">Personal</div>
        <a href="{{ route('profile.show') }}" 
           class="nav-link-premium {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-circle"></i> My Profile
        </a>

        {{-- Section: Academic Management --}}
        @canany(['viewAny'], [App\Models\Student::class, App\Models\Course::class, App\Models\Batch::class, App\Models\Enrollment::class])
            <div class="nav-group-label">Academic Management</div>

            @can('viewAny', App\Models\Student::class)
                <a href="{{ route('students.index') }}" 
                   class="nav-link-premium {{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-graduate"></i> Students
                </a>
            @endcan

            @can('viewAny', App\Models\Course::class)
                <a href="{{ route('courses.index') }}" 
                   class="nav-link-premium {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-book-open"></i> Courses
                </a>
            @endcan

            @can('viewAny', App\Models\Batch::class)
                <a href="{{ route('batches.index') }}" 
                   class="nav-link-premium {{ request()->routeIs('batches.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-people-roof"></i> Batches
                </a>
            @endcan

            {{-- Enrollments Menu --}}
            @can('viewAny', App\Models\Enrollment::class)
                <a href="{{ route('enrollments.index') }}" 
                   class="nav-link-premium {{ request()->routeIs('enrollments.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-clipboard-user"></i> Enrollments
                </a>
            @endcan
        @endcanany

        {{-- Section: Certifications (NEW) --}}
        @canany(['approveAdmin', 'approveFaculty'], App\Models\Certificate::class)
            <div class="nav-group-label">Certifications</div>
            
            {{-- Admin View --}}
            @can('approveAdmin', App\Models\Certificate::class)
                <a href="{{ route('admin.certificates.index') }}" 
                   class="nav-link-premium {{ request()->routeIs('admin.certificates.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-certificate"></i> Manage Certificates
                </a>
            @endcan

            {{-- Faculty View --}}
            @can('approveFaculty', App\Models\Certificate::class)
                <a href="{{ route('faculty.certificates.index') }}" 
                   class="nav-link-premium {{ request()->routeIs('faculty.certificates.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-signature"></i> Pending Approvals
                </a>
            @endcan
        @endcanany

        {{-- Section: Access Control --}}
        @canany(['viewAny'], [App\Models\User::class, App\Models\Role::class, App\Models\Permission::class])
            <div class="nav-group-label">Access Control</div>
            
            @can('viewAny', App\Models\User::class)
                <a href="{{ route('users.index') }}" 
                   class="nav-link-premium {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-gear"></i> User Directory
                </a>
            @endcan

            @can('viewAny', App\Models\Role::class)
                <a href="{{ route('roles.index') }}" 
                   class="nav-link-premium {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-shield"></i> Role Authority
                </a>
            @endcan

            @can('viewAny', App\Models\Permission::class)
                <a href="{{ route('permissions.index') }}" 
                   class="nav-link-premium {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-fingerprint"></i> Permission Registry
                </a>
            @endcan
        @endcanany

    </nav>
</aside>

<div class="sidebar-overlay" onclick="toggleSidebar()"></div>