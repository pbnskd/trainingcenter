<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PermissionController extends Controller
{
    public function __construct(
        protected PermissionService $permissionService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Permission::class);

        // Fetch paginated permissions via Service
        $permissions = $this->permissionService->getPaginatedPermissions(
            $request->query('search'), 
            10
        );

        return view('permissions.index', compact('permissions'));
    }

    // ---------------------------------------------------------------------
    // 1. CREATE METHOD (Uses Shared Form)
    // ---------------------------------------------------------------------
    public function create(): View
    {
        $this->authorize('create', Permission::class);

        // Pass 'permission' as null to signal "Create Mode" to the shared view
        return view('permissions.form', ['permission' => null]);
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $this->authorize('create', Permission::class);

        $this->permissionService->createPermission($request->validated());

        return redirect()->route('permissions.index')
            ->with('success', 'Permission node initialized successfully.');
    }

    public function show(Permission $permission): View
    {
        $this->authorize('view', $permission);
        
        return view('permissions.show', compact('permission'));
    }

    // ---------------------------------------------------------------------
    // 2. EDIT METHOD (Uses Shared Form)
    // ---------------------------------------------------------------------
    public function edit(Permission $permission): View
    {
        $this->authorize('update', $permission);

        // Pass the existing permission object to signal "Edit Mode"
        return view('permissions.form', compact('permission'));
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        $this->authorize('update', $permission);

        $this->permissionService->updatePermission($permission, $request->validated());

        return redirect()->route('permissions.index')
            ->with('success', 'Permission node recalibrated.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $this->authorize('delete', $permission);

        $this->permissionService->deletePermission($permission);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}