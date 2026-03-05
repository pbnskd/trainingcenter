<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $data = $this->userService->getPaginatedUsers(
            $request->query('search'), 
            10
        );

        return view('users.index', compact('data'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);
        
        $roles = Role::pluck('name', 'name')->all();
        
        // CORRECTION: Point to 'users.form' and pass null user
        return view('users.form', [
            'roles' => $roles,
            'user' => null,     // Tells the form we are creating
            'userRole' => []    // Empty roles for create mode
        ]);
    }

   public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        // Get validated data (now includes phone and address)
        $data = $request->validated();

        // Optional: If roles were hidden in the UI and not sent, set a default role here.
        // if (!isset($data['roles'])) {
        //     $data['roles'] = ['User']; // Default role name
        // }

        $this->userService->createUser($data);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);
        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $roles = Role::pluck('name', 'name')->all();
        
        // Get simple array of role names for the form
        $userRole = $user->roles->pluck('name')->toArray();

        // CORRECTION: Point to 'users.form'
        return view('users.form', compact('user', 'roles', 'userRole'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        // Get validated data
        $data = $request->validated();

        // FIX: Remove password from the array if it is null or empty
        // This prevents overwriting the existing hash with a blank string
        if (empty($data['password'])) {
            unset($data['password']);
        }

        // If roles are missing (e.g., hidden by permissions), preserve existing roles
        // depending on how your UserService handles missing keys. 
        // If your Service overwrites roles, you might need to unset it here too if not present.
        if (!$request->has('roles')) {
            unset($data['roles']); 
        }

        $this->userService->updateUser($user, $data);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        try {
            $this->userService->deleteUser($user);
            
            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully');
                
        } catch (\Exception $e) {
            // Handle cases where Super Admin cannot be deleted
            return back()->with('error', $e->getMessage());
        }
    }
}