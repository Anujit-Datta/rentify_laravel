<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlockedUser;
use App\Models\User;
use App\Models\ActionLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BlockController extends Controller
{
    /**
     * Get list of blocked users for the authenticated user.
     */
    public function index()
    {
        $user = Auth::user();

        $blockedUsers = BlockedUser::where('blocker_id', $user->id)
            ->with('blockedUser')
            ->get()
            ->map(function ($block) {
                return [
                    'id' => $block->id,
                    'blocked_user_id' => $block->blocked_id,
                    'name' => $block->blockedUser->name ?? 'Unknown User',
                    'email' => $block->blockedUser->email ?? '',
                    'reason' => $block->reason,
                    'blocked_at' => $block->blocked_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $blockedUsers
        ]);
    }

    /**
     * Block a user.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'blocked_user_id' => 'required|integer|exists:users,id',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $blockedUserId = $request->blocked_user_id;

        // Check if trying to block self
        if ($blockedUserId == $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot block yourself'
            ], 400);
        }

        // Check if already blocked
        $existingBlock = BlockedUser::where('blocker_id', $user->id)
            ->where('blocked_id', $blockedUserId)
            ->first();

        if ($existingBlock) {
            return response()->json([
                'success' => false,
                'message' => 'User is already blocked'
            ], 400);
        }

        // Block the user
        BlockedUser::create([
            'blocker_id' => $user->id,
            'blocked_id' => $blockedUserId,
            'reason' => $request->reason,
        ]);

        // Log the action
        ActionLog::create([
            'user_id' => $user->id,
            'action_type' => 'block_user',
            'target_user_id' => $blockedUserId,
            'description' => $request->reason ?? 'User blocked',
        ]);

        // Get blocked user name for notification
        $blockedUser = User::find($blockedUserId);

        // Notify admin about the block
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'User Blocked',
                'message' => "User {$user->name} blocked {$blockedUser->name ?? "User $blockedUserId"}",
                'type' => 'block',
                'related_id' => $blockedUserId,
                'is_read' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User blocked successfully'
        ], 201);
    }

    /**
     * Unblock a user.
     */
    public function destroy($blockedUserId)
    {
        $user = Auth::user();

        $blockedUser = BlockedUser::where('blocker_id', $user->id)
            ->where('blocked_id', $blockedUserId)
            ->first();

        if (!$blockedUser) {
            return response()->json([
                'success' => false,
                'message' => 'User is not blocked'
            ], 404);
        }

        $blockedUser->delete();

        return response()->json([
            'success' => true,
            'message' => 'User unblocked successfully'
        ]);
    }

    /**
     * Check if a user is blocked.
     */
    public function check($userId)
    {
        $authUser = Auth::user();

        $isBlocked = BlockedUser::where('blocker_id', $authUser->id)
            ->where('blocked_id', $userId)
            ->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'is_blocked' => $isBlocked
            ]
        ]);
    }
}
