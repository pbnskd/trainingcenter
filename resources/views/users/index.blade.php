@extends('layouts.app')

@section('content')
<div class="main-canvas p-4">
    <div class="content-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4">
        <div>
            <h4 class="fw-800 text-dark mb-1">Team Directory</h4>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-soft-primary text-primary px-2 py-1">
                    <i class="fa-solid fa-users me-1"></i> {{ $data->total() }} Members
                </span>
                @if(request('search'))
                    <span class="text-muted small">• Search results for "{{ request('search') }}"</span>
                    <a href="{{ route('users.index') }}" class="text-danger small text-decoration-none ms-2">Clear</a>
                @endif
            </div>
        </div>

        <div class="action-stack d-flex gap-2 mt-3 mt-lg-0">
            <form action="{{ route('users.index') }}" method="GET" id="search-form" class="search-capsule d-none d-md-flex align-items-center">
                <i class="fa-solid fa-magnifying-glass ms-3 text-muted"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="form-control border-0 bg-transparent shadow-none" 
                       placeholder="Quick find member..." oninput="debounceSearch()" autocomplete="off">
            </form>

            {{-- POLICY CHECK: Create User --}}
            @can('create', App\Models\User::class)
            <a class="btn btn-premium rounded-pill shadow-sm px-4" href="{{ route('users.create') }}">
                <i class="fa-solid fa-plus me-2"></i>New Member
            </a>
            @endcan
        </div>
    </div>

    <div class="premium-grid bg-white rounded-3 shadow-sm border">
        <div class="grid-header d-none d-md-grid py-3 border-bottom bg-light fw-bold text-muted small">
            <div class="grid-col ps-4">MEMBER IDENTITY</div>
            <div class="grid-col">ACCESS LEVEL</div>
            <div class="grid-col">CONTACT INFO</div>
            <div class="grid-col pe-4 text-end">ACTIONS</div>
        </div>

        <div class="grid-body">
            @forelse ($data as $key => $user)
            <div class="grid-row d-grid align-items-center py-3 border-bottom animate__animated animate__fadeInUp" style="animation-delay: {{ $key * 0.05 }}s">
                
                {{-- Column 1: Identity --}}
                <div class="grid-col ps-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-box">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="rounded-circle" style="width: 42px; height: 42px; object-fit: cover;">
                            @else
                                <div class="avatar-placeholder bg-soft-primary text-primary fw-bold rounded-circle">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="online-status {{ $user->status ? 'status-active' : 'status-inactive' }}" title="{{ $user->status ? 'Active' : 'Inactive' }}"></div>
                        </div>
                        <div class="overflow-hidden">
                            <div class="fw-bold text-truncate text-dark">{{ $user->name }}</div>
                            <div class="text-muted small text-truncate">{{ $user->email }}</div>
                        </div>
                    </div>
                </div>

                {{-- Column 2: Roles --}}
                <div class="grid-col">
                    <div class="role-tags d-flex flex-wrap gap-1">
                        @forelse($user->getRoleNames() as $role)
                            <span class="role-tag role-{{ strtolower(str_replace(' ', '-', $role)) }}">{{ $role }}</span>
                        @empty
                            <span class="text-muted small">No Role</span>
                        @endforelse
                    </div>
                </div>

                {{-- Column 3: Contact --}}
                <div class="grid-col">
                    <div class="d-flex flex-column">
                        <span class="text-dark small fw-semibold"><i class="fa-solid fa-phone me-1 text-muted"></i> {{ $user->phone ?? 'N/A' }}</span>
                        <span class="text-muted small text-truncate" style="max-width: 180px;"><i class="fa-solid fa-location-dot me-1"></i> {{ $user->address ?? 'Not set' }}</span>
                    </div>
                </div>

                {{-- Column 4: Actions --}}
                <div class="grid-col pe-4 text-end">
                    <div class="row-actions d-flex justify-content-end gap-2">
                        
                        {{-- POLICY CHECK: View (Show) --}}
                        @can('view', $user)
                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-light border" title="View Details">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        @endcan

                        {{-- POLICY CHECK: Update (Edit) --}}
                        @can('update', $user)
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-light border" title="Edit Member">
                            <i class="fa-solid fa-sliders"></i>
                        </a>
                        @endcan

                        {{-- POLICY CHECK: Delete --}}
                        @can('delete', $user)
                            <button onclick="handleDelete('{{ $user->id }}')" class="btn btn-sm btn-light border text-danger" title="Remove Member">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                            <form id="delete-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-none">
                                @csrf 
                                @method('DELETE')
                            </form>
                        @endcan

                    </div>
                </div>
            </div>
            @empty
            <div class="p-5 text-center">
                <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" style="width: 80px; opacity: 0.5" alt="No results">
                <p class="text-muted mt-3">No members found matching your search.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    <div class="grid-footer d-flex justify-content-between align-items-center mt-4">
        <p class="text-muted small mb-0">Displaying {{ $data->firstItem() ?? 0 }}-{{ $data->lastItem() ?? 0 }} of {{ $data->total() }} members</p>
        <div class="pagination-minimal">
            {!! $data->links('pagination::bootstrap-5') !!}
        </div>
    </div>
</div>



<script>
    let timeout = null;
    function debounceSearch() {
        clearTimeout(timeout);
        timeout = setTimeout(() => { document.getElementById('search-form').submit(); }, 500);
    }

    function handleDelete(id) {
        if(confirm('Are you sure you want to remove this member? This action cannot be undone.')) { 
            document.getElementById('delete-'+id).submit(); 
        }
    }
</script>
@endsection