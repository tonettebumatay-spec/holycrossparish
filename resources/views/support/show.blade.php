<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-6">

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <a href="{{ route('support.index') }}" class="inline-flex items-center px-5 py-2 bg-white border border-gray-300 rounded-full text-sm font-semibold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    ← Back to Conversations
                </a>
                <h1 class="text-2xl font-black text-gray-800 uppercase">
                    {{ $conversation->user_name ?? 'Unknown User' }}
                </h1>
                <div class="flex gap-2">
                    <form action="{{ route('support.archive', $conversation->id) }}" method="POST" onsubmit="return confirm('Archive this conversation?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-amber-500 text-white rounded-full text-xs font-black uppercase hover:bg-amber-600">
                            Archive
                        </button>
                    </form>
                    <form action="{{ route('support.destroy', $conversation->id) }}" method="POST" onsubmit="return confirm('Delete this conversation permanently?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-full text-xs font-black uppercase hover:bg-red-700">
                            Delete
                        </button>
                    </form>
                </div>
            </div>

            <!-- Chat Container -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <!-- Messages -->
                <div id="chat-messages" class="p-6 space-y-4 overflow-y-auto" style="height: 500px;">
                    @foreach($conversation->messages as $msg)
                        @if($msg->sender_type === 'user')
                            <!-- User Message (Left) -->
                            <div class="flex justify-start">
                                <div class="max-w-md bg-gray-100 rounded-2xl px-4 py-3">
                                    <p class="text-xs font-black text-gray-500 uppercase mb-1">{{ $msg->sender_name }}</p>
                                    <p class="text-sm text-gray-800">{{ $msg->message }}</p>
                                    <p class="text-[10px] text-gray-400 mt-1">{{ $msg->created_at->format('M d, g:i A') }}</p>
                                </div>
                            </div>
                        @elseif($msg->sender_type === 'admin')
                            <!-- Admin Message (Right) -->
                            <div class="flex justify-end">
                                <div class="max-w-md bg-[#4A3728] rounded-2xl px-4 py-3">
                                    <p class="text-xs font-black text-white/70 uppercase mb-1">{{ $msg->sender_name }}</p>
                                    <p class="text-sm text-white">{{ $msg->message }}</p>
                                    <p class="text-[10px] text-white/60 mt-1">{{ $msg->created_at->format('M d, g:i A') }}</p>
                                </div>
                            </div>
                        @else
                            <!-- Auto-reply (Center) -->
                            <div class="flex justify-center">
                                <div class="max-w-md bg-amber-50 border border-amber-200 rounded-2xl px-4 py-3">
                                    <p class="text-xs font-black text-amber-600 uppercase mb-1">🤖 Auto-Reply</p>
                                    <p class="text-sm text-amber-800">{{ $msg->message }}</p>
                                    <p class="text-[10px] text-amber-500 mt-1">{{ $msg->created_at->format('M d, g:i A') }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Reply Form -->
                <div class="border-t border-gray-100 p-4 bg-gray-50">
                    <form action="{{ route('support.messages.store', $conversation->id) }}" method="POST" class="flex gap-3">
                        @csrf
                        <input type="text" name="message" required placeholder="Type your reply..."
                               class="flex-1 border-2 border-gray-300 rounded-full px-5 py-3 text-sm focus:outline-none focus:border-[#4A3728]">
                        <button type="submit" class="bg-[#4A3728] hover:bg-[#3a1f07] text-white font-black text-xs uppercase tracking-widest px-6 py-3 rounded-full transition">
                            Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ✅ Auto-scroll sa pinakababa
        document.addEventListener('DOMContentLoaded', function() {
            const chatBox = document.getElementById('chat-messages');
            chatBox.scrollTop = chatBox.scrollHeight;

            // ✅ Auto-refresh every 5 seconds (polling)
            setInterval(function() {
                location.reload();
            }, 5000);
        });
    </script>
</x-app-layout>