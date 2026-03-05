@extends('layouts.app')

@section('content')
<div class="main-canvas p-4">
    
    {{-- Header Section --}}
    <div class="content-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4">
        <div>
            <h4 class="fw-800 text-dark mb-1">Security Architecture</h4>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill">
                    <i class="fa-solid fa-users-shield me-1"></i> {{ $roles->total() }} Active Roles
                </span>
                <span class="text-muted small">•</span>
                <span class="text-muted small">Updated {{ now()->diffForHumans() }}</span>
            </div>
        </div>

        <div class="action-stack d-flex gap-2 mt-3 mt-lg-0">
            <form action="{{ route('roles.index') }}" method="GET" id="search-form" class="search-capsule d-none d-md-flex align-items-center">
                <i class="fa-solid fa-magnifying-glass ms-3 text-muted"></i>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Quick find roles..." 
                       class="border-0 bg-transparent shadow-none form-control"
                       oninput="debounceSearch()"
                       autocomplete="off">
            </form>
            
            {{-- POLICY CHECK: Create Role --}}
            @can('create', App\Models\Role::class)
            <a class="btn btn-premium rounded-pill shadow-sm px-4" href="{{ route('roles.create') }}">
                <i class="fa-solid fa-plus-circle me-1"></i>New Role
            </a>
            @endcan
        </div>
    </div>

    {{-- Success Message --}}
    @if ($message = Session::get('success'))
    <div class="alert alert-glass border-0 rounded-4 shadow-sm p-3 mb-4 d-flex align-items-center justify-content-between animate__animated animate__fadeIn">
        <div class="d-flex align-items-center">
            <div class="icon-circle bg-success text-white me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 50%;">
                <i class="fa-solid fa-check small"></i>
            </div>
            <span class="fw-600 text-dark">{{ $message }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Grid Layout --}}
    <div class="premium-grid shadow-sm bg-white rounded-4 border overflow-hidden">
        <div class="grid-header d-none d-md-grid py-3 bg-light border-bottom text-muted small fw-bold px-4" 
             style="grid-template-columns: 1.5fr 1.2fr 1fr 120px 140px;">
            <div>ROLE IDENTITY</div>
            <div>DESCRIPTION</div>
            <div>ACCESS LEVEL</div>
            <div>RANK</div>
            <div class="text-end">ACTIONS</div>
        </div>

        <div class="grid-body">
            @forelse ($roles as $key => $role)
            <div class="grid-row d-grid align-items-center py-3 border-bottom px-4 animate__animated animate__fadeInUp" 
                 style="animation-delay: {{ $key * 0.05 }}s; grid-template-columns: 1.5fr 1.2fr 1fr 120px 140px;">
                
                {{-- Column 1: Identity --}}
                <div class="grid-col">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-box bg-light rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-fingerprint {{ in_array($role->name, ['Admin', 'Super Admin']) ? 'text-danger' : 'text-primary' }} fs-5"></i>
                        </div>
                        <div class="overflow-hidden">
                            <a href="{{ route('roles.show', $role->id) }}" class="fw-bold text-dark d-block text-decoration-none hover-link">
                                {{ $role->name }}
                            </a>
                            <div class="text-muted small text-truncate">System Identity</div>
                        </div>
                    </div>
                </div>

                {{-- Column 2: Description --}}
                <div class="grid-col pe-2">
                    <span class="text-muted small">
                        {{ Str::limit($role->description ?? 'No description provided.', 40) }}
                    </span>
                </div>

                {{-- Column 3: Access Level --}}
                <div class="grid-col">
                    @php 
                        $isAdmin = in_array($role->name, ['Admin', 'Super Admin']);
                    @endphp
                    <span class="role-tag {{ $isAdmin ? 'role-admin' : 'role-default' }} px-3 py-1 rounded-pill fw-bold">
                        {{ $isAdmin ? 'FULL ACCESS' : 'MANAGED' }}
                    </span>
                </div>

                {{-- Column 4: Rank --}}
                <div class="grid-col text-muted small">
                    <i class="fa-solid fa-hashtag me-1 opacity-50"></i>
                    Tier {{ str_pad($loop->iteration + ($roles->currentPage() - 1) * $roles->perPage(), 2, '0', STR_PAD_LEFT) }}
                </div>

                {{-- Column 5: Actions --}}
                <div class="grid-col text-end">
                    <div class="d-flex justify-content-end gap-2">
                        {{-- POLICY CHECK: View --}}
                        @can('view', $role)
                        <a href="{{ route('roles.show', $role->id) }}" class="btn btn-sm btn-light border action-btn" title="View">
                            <i class="fa-solid fa-eye text-dark"></i>
                        </a>
                        @endcan

                        {{-- POLICY CHECK: Edit --}}
                        @can('update', $role)
                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-light border action-btn" title="Edit">
                            <i class="fa-solid fa-sliders text-dark"></i>
                        </a>
                        @endcan
                        
                        {{-- POLICY CHECK: Delete --}}
                        @can('delete', $role)
                        <button onclick="handleDelete('{{ $role->id }}')" class="btn btn-sm btn-light border action-btn" title="Delete">
                            <i class="fa-solid fa-trash-can text-danger"></i>
                        </button>
                        <form id="delete-{{ $role->id }}" action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-none">
                            @csrf @method('DELETE')
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
            @empty
            <div class="p-5 text-center text-muted">
                <i class="fa-solid fa-folder-open fa-3x mb-3 opacity-50"></i>
                <p>No roles found matching your criteria.</p>
                <a href="{{ route('roles.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Clear Search</a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Footer Pagination --}}
    <div class="grid-footer d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
        <p class="text-muted small mb-3 mb-md-0">
            Showing <strong>{{ $roles->firstItem() ?? 0 }}</strong> to <strong>{{ $roles->lastItem() ?? 0 }}</strong> of {{ $roles->total() }} roles
        </p>
        <div>
            {!! $roles->appends(request()->query())->links('pagination::bootstrap-5') !!}
        </div>
    </div>
</div>

{{-- Scripts & Styles --}}
<script>
    let timeout = null;
    function debounceSearch() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            document.getElementById('search-form').submit();
        }, 700);
    }

    function handleDelete(id) {
        if(confirm('CRITICAL: Deleting a system role may affect all users assigned to it. Proceed?')) {
            document.getElementById('delete-' + id).submit();
        }
    }
</script>

<style>
    /* Mobile Grid Adjustment */
    @media (max-width: 768px) {
        .grid-header { display: none !important; }
        .grid-row { display: flex !important; flex-direction: column; gap: 10px; align-items: flex-start !important; padding: 1rem !important; }
        .grid-col { width: 100%; padding: 0 !important; }
        .text-end { text-align: left !important; justify-content: flex-start !important; }
    }

    .hover-link:hover { color: #4e73df !important; text-decoration: underline !important; }
    .search-capsule { background: #f8f9fc; border: 1px solid #e3e6f0; border-radius: 50px; min-width: 280px; }
    .role-tag { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
    .role-admin { background: #fee2e2; color: #b91c1c; }
    .role-default { background: #e0f2fe; color: #0369a1; }
    .grid-row:hover { background-color: #fcfdfe; transition: background 0.2s; }
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.1); }
    
    .action-btn { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; padding: 0; }
    .action-btn:hover { background-color: #e2e6ea; }
</style>
@endsection