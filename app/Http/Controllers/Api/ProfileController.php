<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Tenant;
use App\Models\Landlord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(Request $request)
    {
        $user = Auth::user();

        // Load role-specific data
        if ($user->isTenant()) {
            $user->load('tenant');
        } elseif ($user->isLandlord()) {
            $user->load('landlord');
        }

        return response()->json([
            'success' => true,
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100',
            'phone' => 'sometimes|required|string|max:15',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'bio' => 'nullable|string',
            'location' => 'nullable|string',
            'occupation' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update user basic info
        $user->update($request->only(['name', 'phone', 'email']));

        // Update role-specific data
        if ($user->isTenant()) {
            $tenant = $user->tenant;
            if ($tenant) {
                $tenant->update([
                    'full_name' => $request->name ?? $tenant->full_name,
                    'email' => $request->email ?? $tenant->email,
                    'phone' => $request->phone ?? $tenant->phone,
                    'bio' => $request->bio,
                    'location' => $request->location,
                    'occupation' => $request->occupation,
                ]);
            }
        } elseif ($user->isLandlord()) {
            $landlord = $user->landlord;
            if ($landlord) {
                $landlord->update([
                    'full_name' => $request->name ?? $landlord->full_name,
                    'about' => $request->bio,
                    'occupation' => $request->occupation,
                ]);
            }
        }

        // Reload with relationships
        if ($user->isTenant()) {
            $user->load('tenant');
        } elseif ($user->isLandlord()) {
            $user->load('landlord');
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Upload profile avatar.
     */
    public function uploadAvatar(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Delete old avatar if exists
        $oldAvatar = null;
        if ($user->isTenant() && $user->tenant) {
            $oldAvatar = $user->tenant->profile_pic;
        } elseif ($user->isLandlord() && $user->landlord) {
            $oldAvatar = $user->landlord->profile_pic;
        }

        if ($oldAvatar && $oldAvatar !== 'uploads/default.png') {
            Storage::disk('public')->delete($oldAvatar);
        }

        // Upload new avatar
        $image = $request->file('avatar');
        $filename = time() . '_avatar_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('avatars', $filename, 'public');

        // Update profile picture
        if ($user->isTenant()) {
            $tenant = $user->tenant;
            if ($tenant) {
                $tenant->update(['profile_pic' => $path]);
            }
        } elseif ($user->isLandlord()) {
            $landlord = $user->landlord;
            if ($landlord) {
                $landlord->update(['profile_pic' => $path]);
            }
        }

        $user->load($user->isTenant() ? 'tenant' : 'landlord');

        return response()->json([
            'success' => true,
            'message' => 'Avatar uploaded successfully',
            'data' => [
                'avatar_url' => Storage::url($path),
                'user' => new UserResource($user)
            ]
        ]);
    }
}
