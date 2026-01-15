<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Get all conversations for the authenticated user.
     */
    public function conversations()
    {
        $user = Auth::user();

        // Get all messages where user is sender or receiver
        $messages = Message::where(function ($q) use ($user) {
            $q->where('sender_id', $user->id)
              ->orWhere('receiver_id', $user->id);
        })
        ->with(['sender', 'receiver'])
        ->orderBy('timestamp', 'desc')
        ->get();

        // Group by the other user in conversation
        $conversations = [];
        $processedUsers = [];

        foreach ($messages as $message) {
            $otherUserId = $message->sender_id === $user->id ? $message->receiver_id : $message->sender_id;

            if (!in_array($otherUserId, $processedUsers)) {
                $processedUsers[] = $otherUserId;

                $otherUser = $message->sender_id === $user->id ? $message->receiver : $message->sender;

                $conversations[] = [
                    'user_id' => $otherUserId,
                    'name' => $otherUser->name,
                    'email' => $otherUser->email,
                    'role' => $otherUser->role,
                    'last_message' => $message->message,
                    'last_message_time' => $message->timestamp,
                    'unread_count' => Message::where('sender_id', $otherUserId)
                        ->where('receiver_id', $user->id)
                        ->where('seen', false)
                        ->count(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $conversations
        ]);
    }

    /**
     * Get messages with a specific user.
     */
    public function getMessages($userId)
    {
        $user = Auth::user();
        $otherUser = User::find($userId);

        if (!$otherUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $messages = Message::where(function ($q) use ($user, $userId) {
            $q->where(function ($q) use ($user, $userId) {
                $q->where('sender_id', $user->id)
                  ->where('receiver_id', $userId);
            })->orWhere(function ($q) use ($user, $userId) {
                $q->where('sender_id', $userId)
                  ->where('receiver_id', $user->id);
            });
        })
        ->with(['sender', 'receiver'])
        ->orderBy('timestamp', 'asc')
        ->get();

        // Mark received messages as seen
        Message::where('sender_id', $userId)
            ->where('receiver_id', $user->id)
            ->update(['seen' => true]);

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    /**
     * Send a message to a user.
     */
    public function sendMessage(Request $request, $userId)
    {
        $user = Auth::user();
        $receiver = User::find($userId);

        if (!$receiver) {
            return response()->json([
                'success' => false,
                'message' => 'Receiver not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required_without:file_path|string',
            'file_path' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $userId,
            'message' => $request->message,
            'file_path' => $request->file_path,
            'receiver_role' => $receiver->role,
            'priority' => 'normal',
            'sender_role' => $user->role,
            'status' => 'sent',
            'seen' => false,
            'timestamp' => now(),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $message->load(['sender', 'receiver'])
        ], 201);
    }

    /**
     * Mark messages as read.
     */
    public function markAsRead($userId)
    {
        $user = Auth::user();

        Message::where('sender_id', $userId)
            ->where('receiver_id', $user->id)
            ->update(['seen' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Messages marked as read'
        ]);
    }

    /**
     * Get unread message count.
     */
    public function unreadCount()
    {
        $user = Auth::user();

        $count = Message::where('receiver_id', $user->id)
            ->where('seen', false)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $count
            ]
        ]);
    }
}
