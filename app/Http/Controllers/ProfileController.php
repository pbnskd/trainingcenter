<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(
        protected ProfileService $profileService
    ) {}

    /**
     * Show the user profile dashboard.
     */
    public function show(): View
    {
        return view('users.profile.show', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit(Request $request): View
    {
        return view('users.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        // Service handles avatar upload and data persistence
        $this->profileService->updateProfileData(
            $request->user(), 
            $request->validated()
        );

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        // 1. Get the validated array
        $validated = $request->validated();

        // 2. Pass the specific password field to your service
        // We use $request->user() to ensure we are updating the LOGGED IN user
        $this->profileService->updatePassword(
            $request->user(), 
            $validated['password'] 
        );

        return back()->with('success', 'Password updated successfully.');
    }
}