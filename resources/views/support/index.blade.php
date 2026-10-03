<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-6">

            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-5 py-2 bg-white border border-gray-300 rounded-full text-sm font-semibold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    ← Back to Dashboard
                </a>
                <h1 class="text-3xl font-black text-gray-800 uppercase tracking-tight">
                    💬 Customer Support
                </h1>
                <a href="{{ route('support.settings') }}" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-full text-xs font-black uppercase tracking-widest">
                    ⚙️ Settings
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Conversations List -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b flex items-center justify-between">
                    <h2 class="text-lg font-black text-gray-800 uppercase">Conversations</h2>
                    <span class="text-xs text-gray-500 font-semibold">Total: {{ $conversations->count() }}</span>
                </div>

                @if($conversations->isEmpty())
                    <div class="text-center py-16">
                        <p class="text-gray-400 italic">No conversations yet.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($conversations as $conv)
                            <a href="{{ route('support.show', $conv->id) }}" class="block px-6 py-4 hover:bg-gray-50 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-[#4A3728] rounded-full flex items-center justify-center text-white font-black">
                                            {{ strtoupper(substr($conv->user_name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $conv->user_name ?? 'Unknown User' }}</p>
                                            <p class="text-xs text-gray-500">{{ $conv->user_email ?? 'N/A' }}</p>
                                            @if($conv->latestMessage)
                                                <p class="text-xs text-gray-400 mt-1 truncate max-w-md">
                                                    {{ $conv->latestMessage->message }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($conv->unread_by_admin > 0)
                                            <span class="inline-flex px-3 py-1 bg-red-500 text-white rounded-full text-xs font-black">
                                                {{ $conv->unread_by_admin }} NEW
                                            </span>
                                        @endif
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ $conv->last_message_at?->diffForHumans() ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>