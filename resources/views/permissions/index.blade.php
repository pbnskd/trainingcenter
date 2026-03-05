@extends('layouts.app')

@section('content')
    <div class="main-canvas p-4">
        <div class="content-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4">
            <div>
                <h4 class="fw-800 text-dark mb-1">Permission Registry</h4>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill">
                        <i class="fa-solid fa-fingerprint me-1"></i> {{ $permissions->total() }} Security Nodes
                    </span>
                    @if (request('search'))
                        <span class="text-muted small">• Results for "{{ request('search') }}"</span>
                        <a href="{{ route('permissions.index') }}"
                            class="text-danger small text-decoration-none ms-2">Clear</a>
                    @endif
                </div>
            </div>

            <div class="action-stack d-flex gap-2 mt-3 mt-lg-0">
                <form action="{{ route('permissions.index') }}" method="GET" id="search-form"
                    class="search-capsule d-flex align-items-center">
                    <i class="fa-solid fa-magnifying-glass ms-3 text-muted"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search nodes..."
                        class="form-control border-0 bg-transparent shadow-none" oninput="debounceSearch()"
                        autocomplete="off">
                </form>

                {{-- POLICY CHECK: Create Permission --}}
                @can('create', App\Models\Permission::class)
                    <a class="btn btn-premium rounded-pill shadow-sm px-4" href="{{ route('permissions.create') }}">
                        <i class="fa-solid fa-plus-circle me-1"></i> New Node
                    </a>
                @endcan
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div
                class="alert alert-glass border-0 rounded-4 shadow-sm p-3 mb-4 d-flex align-items-center justify-content-between animate__animated animate__fadeIn">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-success text-white me-3 d-flex align-items-center justify-content-center"
                        style="width: 32px; height: 32px; border-radius: 50%;">
                        <i class="fa-solid fa-check-double small"></i>
                    </div>
                    <span class="fw-600 text-dark">{{ $message }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="premium-grid bg-white shadow-sm rounded-4 border overflow-hidden">
            <div class="grid-header d-none d-md-grid py-3 bg-light border-bottom text-muted small fw-bold px-4"
                style="grid-template-columns: 2fr 1fr 1fr 160px;">
                <div>PERMISSION ARCHITECTURE</div>
                <div>GUARD TYPE</div>
                <div>STATUS</div>
                <div class="text-end">ACTIONS</div>
            </div>

            <div class="grid-body">
                @forelse ($permissions as $key => $permission)
                    <div class="grid-row d-grid align-items-center py-3 border-bottom px-4 animate__animated animate__fadeInUp"
                        style="animation-delay: {{ $key * 0.05 }}s; grid-template-columns: 2fr 1fr 1fr 160px;">

                        {{-- Column 1: Name & Description --}}
                        <div class="overflow-hidden">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-key text-primary small"></i>
                                <code class="fw-bold text-dark fs-6">{{ $permission->name }}</code>
                            </div>
                            <div class="text-muted small text-truncate mt-1" style="max-width: 450px;"
                                title="{{ $permission->description }}">
                                {{ $permission->description ?? 'No operational description provided.' }}
                            </div>
                        </div>

                        {{-- Column 2: Guard --}}
                        <div>
                            <span class="badge bg-light text-muted border px-2 py-1 small">
                                <i class="fa-solid fa-shield-halved me-1 small"></i> {{ $permission->guard_name }}
                            </span>
                        </div>

                        {{-- Column 3: Status --}}
                        <div class="text-muted small">
                            <i class="fa-solid fa-circle-dot text-success me-1" style="font-size: 0.6rem;"></i>
                            Active Node
                        </div>

                        {{-- Column 4: Actions --}}
                        <div class="text-end d-flex justify-content-end gap-2">
                            
                            {{-- POLICY CHECK: View --}}
                            @can('view', $permission)
                            <a href="{{ route('permissions.show', $permission->id) }}" class="btn action-btn border"
                                title="Show">
                                <i class="fa-solid fa-eye text-dark"></i>
                            </a>
                            @endcan

                            {{-- POLICY CHECK: Update --}}
                            @can('update', $permission)
                                <a href="{{ route('permissions.edit', $permission->id) }}" class="btn action-btn border"
                                    title="Edit">
                                    <i class="fa-solid fa-sliders text-dark"></i>
                                </a>
                            @endcan

                            {{-- POLICY CHECK: Delete --}}
                            @can('delete', $permission)
                                <button onclick="handlePurge('{{ $permission->id }}')" class="btn action-btn border"
                                    title="Delete">
                                    <i class="fa-solid fa-trash-can text-danger"></i>
                                </button>
                                <form id="purge-{{ $permission->id }}"
                                    action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            @endcan
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-center">
                        <i class="fa-solid fa-database fa-3x text-light mb-3"></i>
                        <p class="text-muted mt-3 fw-500">No security nodes found in the current sector.</p>
                        <a href="{{ route('permissions.index') }}"
                            class="btn btn-sm btn-outline-primary rounded-pill">Reset Filter</a>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="grid-footer d-flex justify-content-between align-items-center mt-4">
            <p class="text-muted small mb-0">
                Logged: {{ $permissions->firstItem() ?? 0 }}-{{ $permissions->lastItem() ?? 0 }} of
                {{ $permissions->total() }}
            </p>
            <div class="pagination-minimal">
                {!! $permissions->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>

    {{-- Script to handle Delete & Search --}}
    <script>
        let timeout = null;

        function debounceSearch() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                document.getElementById('search-form').submit();
            }, 500);
        }

        function handlePurge(id) {
            if (confirm('Are you sure you want to purge this security node? This action is irreversible.')) {
                document.getElementById('purge-' + id).submit();
            }
        }
    </script>

    {{-- Specific Styles for this View --}}
    <style>
        .search-capsule {
            background: #f8f9fc;
            border: 1px solid #e3e6f0;
            border-radius: 50px;
            min-width: 250px;
        }

        /* Mobile Grid Adjustment */
        @media (max-width: 768px) {
            .grid-header {
                display: none !important;
            }

            .grid-row {
                display: flex !important;
                flex-direction: column;
                gap: 10px;
                align-items: flex-start !important;
            }

            .text-end {
                text-align: left !important;
                justify-content: flex-start !important;
            }
        }
        
        /* Action Button Styles */
        .action-btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background: #fff;
        }
        .action-btn:hover {
            background: #f8f9fa;
        }
    </style>
@endsection