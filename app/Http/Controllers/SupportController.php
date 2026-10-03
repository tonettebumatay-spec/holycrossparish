<?php

namespace App\Http\Controllers;

use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Models\SupportSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SupportController extends Controller
{
    /**
     * Display a listing of conversations.
     */
    public function index()
    {
        $conversations = SupportConversation::with('latestMessage')
            ->where('is_archived', false)
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->get();

        return view('support.index', compact('conversations'));
    }

    /**
     * Display the specified conversation.
     */
    public function show($id)
    {
        $conversation = SupportConversation::with('messages')->findOrFail($id);

        // ✅ Mark all user messages as read by admin
        $conversation->messages()
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        // ✅ Reset unread count
        $conversation->update(['unread_by_admin' => 0]);

        return view('support.show', compact('conversation'));
    }

    /**
     * Store a new message from admin.
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $conversation = SupportConversation::findOrFail($id);

        $message = SupportMessage::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'sender_type' => 'admin',
            'sender_name' => Auth::user()->name ?? 'Admin',
            'message' => $request->message,
            'is_read' => false,
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'unread_by_user' => $conversation->unread_by_user + 1,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', 'Message sent successfully!');
    }

    /**
     * Archive a conversation.
     */
    public function archive($id)
    {
        $conversation = SupportConversation::findOrFail($id);
        $conversation->update(['is_archived' => true]);

        return redirect()->route('support.index')
            ->with('success', 'Conversation archived successfully!');
    }

    /**
     * Delete a conversation.
     */
    public function destroy($id)
    {
        $conversation = SupportConversation::findOrFail($id);
        $conversation->delete();

        return redirect()->route('support.index')
            ->with('success', 'Conversation deleted successfully!');
    }

    /**
     * Display support settings.
     */
    public function settings()
    {
        $settings = SupportSetting::all()->pluck('value', 'key')->toArray();

        return view('support.settings', compact('settings'));
    }

    /**
     * Update support settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'auto_reply_message' => 'required|string|max:2000',
            'office_hours' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_address' => 'nullable|string|max:500',
        ]);

        SupportSetting::setValue('auto_reply_message', $request->auto_reply_message);
        SupportSetting::setValue('auto_reply_enabled', $request->has('auto_reply_enabled') ? '1' : '0');
        SupportSetting::setValue('office_hours', $request->office_hours);
        SupportSetting::setValue('contact_email', $request->contact_email);
        SupportSetting::setValue('contact_address', $request->contact_address);

        return redirect()->route('support.settings')
            ->with('success', 'Settings updated successfully!');
    }
}