<?php

namespace App\Http\Controllers;

use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Models\SupportSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupportApiController extends Controller
{
    /**
     * Get or create a conversation for the logged-in user.
     */
    public function getOrCreateConversation(Request $request)
    {
        try {
            $user = $request->user();

            // ✅ Hanapin ang existing conversation
            $conversation = SupportConversation::where('user_id', $user->id)
                ->where('is_archived', false)
                ->first();

            // ✅ Kung wala, gumawa ng bago
            if (!$conversation) {
                $conversation = SupportConversation::create([
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'last_message_at' => now(),
                    'unread_by_admin' => 0,
                    'unread_by_user' => 0,
                    'is_archived' => false,
                ]);
            }

            return response()->json([
                'success' => true,
                'conversation' => [
                    'id' => $conversation->id,
                    'user_name' => $conversation->user_name,
                    'last_message_at' => $conversation->last_message_at?->toDateTimeString(),
                    'unread_by_user' => $conversation->unread_by_user,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get messages for the logged-in user's conversation.
     */
    public function getMessages(Request $request)
    {
        try {
            $user = $request->user();

            $conversation = SupportConversation::where('user_id', $user->id)
                ->where('is_archived', false)
                ->first();

            if (!$conversation) {
                return response()->json([
                    'success' => true,
                    'messages' => [],
                ]);
            }

            // ✅ Mark all admin/auto_reply messages as read
            $conversation->messages()
                ->whereIn('sender_type', ['admin', 'auto_reply'])
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            // ✅ Reset unread count
            $conversation->update(['unread_by_user' => 0]);

            $messages = $conversation->messages()
                ->orderBy('created_at')
                ->get()
                ->map(function ($msg) {
                    return [
                        'id' => $msg->id,
                        'sender_type' => $msg->sender_type,
                        'sender_name' => $msg->sender_name,
                        'message' => $msg->message,
                        'is_read' => $msg->is_read,
                        'created_at' => $msg->created_at->toDateTimeString(),
                    ];
                });

            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'messages' => $messages,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send a message from the user.
     */
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:2000',
            ]);

            $user = $request->user();

            // ✅ Hanapin o gumawa ng conversation
            $conversation = SupportConversation::where('user_id', $user->id)
                ->where('is_archived', false)
                ->first();

            if (!$conversation) {
                $conversation = SupportConversation::create([
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'last_message_at' => now(),
                    'unread_by_admin' => 0,
                    'unread_by_user' => 0,
                    'is_archived' => false,
                ]);
            }

            // ✅ Save user message
            $userMessage = SupportMessage::create([
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'sender_type' => 'user',
                'sender_name' => $user->name,
                'message' => $request->message,
                'is_read' => false,
            ]);

            $conversation->update([
                'last_message_at' => now(),
                'unread_by_admin' => $conversation->unread_by_admin + 1,
            ]);

            // ✅ Check kung may auto-reply
            $autoReplyEnabled = SupportSetting::getValue('auto_reply_enabled', '1');
            $autoReplyMessage = SupportSetting::getValue('auto_reply_message');

            $autoReply = null;

            // ✅ Kung first message, mag-send ng auto-reply
            $messageCount = $conversation->messages()->count();

            if ($autoReplyEnabled === '1' && $autoReplyMessage && $messageCount <= 1) {
                // ✅ Check kung may admin message na (baka online ang admin)
                $hasAdminMessage = $conversation->messages()
                    ->where('sender_type', 'admin')
                    ->exists();

                if (!$hasAdminMessage) {
                    $autoReply = SupportMessage::create([
                        'conversation_id' => $conversation->id,
                        'user_id' => null,
                        'sender_type' => 'auto_reply',
                        'sender_name' => 'Holy Cross Parish',
                        'message' => $autoReplyMessage,
                        'is_read' => false,
                    ]);

                    $conversation->update([
                        'unread_by_user' => $conversation->unread_by_user + 1,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully!',
                'data' => [
                    'user_message' => $userMessage,
                    'auto_reply' => $autoReply,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get support info (contact details, office hours).
     */
    public function getSupportInfo()
    {
        try {
            $settings = SupportSetting::all()->pluck('value', 'key')->toArray();

            return response()->json([
                'success' => true,
                'support_info' => [
                    'office_hours' => $settings['office_hours'] ?? null,
                    'contact_number' => $settings['contact_number'] ?? null,
                    'contact_email' => $settings['contact_email'] ?? null,
                    'contact_address' => $settings['contact_address'] ?? null,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }
}