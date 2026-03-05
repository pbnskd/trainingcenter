<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Models\Role;
use App\Models\Permission;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{
    public function __construct(
        protected RoleService $roleService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Role::class);

        $roles = $this->roleService->getPaginatedRoles(
            $request->query('search'), 
            5
        );

        return view('roles.index', compact('roles'));
    }

   public function create()
{
    $this->authorize('create', Role::class);
    
    $permissions = Permission::all();
    
    return view('roles.form', [
        'role' => null,
        'permissions' => $permissions,
        'rolePermissions' => [] // Empty array for create
    ]);
}

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->authorize('create', Role::class);

        // Service handles creation and permission syncing
        $this->roleService->createRole($request->validated());

        return redirect()->route('roles.index')
            ->with('success', 'New Security Role deployed successfully');
    }

    public function show(Role $role): View
    {
        $this->authorize('view', $role);

        // Let the model or service handle the relationship loading
        $role->load('permissions');

        return view('roles.show', [
            'role' => $role,
            'rolePermissions' => $role->permissions
        ]);
    }

   public function edit(Role $role)
{
    $this->authorize('update', $role);

    if($role->name == 'Super Admin'){
        abort(403, 'SUPER ADMIN CANNOT BE EDITED');
    }

    $permissions = Permission::all();
    
    // Get array of permission IDs associated with this role
    $rolePermissions = $role->permissions->pluck('id'); 

    return view('roles.form', compact('role', 'permissions', 'rolePermissions'));
}

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->authorize('update', $role);

        $this->roleService->updateRole($role, $request->validated());

        return redirect()->route('roles.index')
            ->with('success', 'Security Protocol updated successfully');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);

        // Service handles the "Super Admin" protection check
        $this->roleService->deleteRole($role);

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully');
    }
}