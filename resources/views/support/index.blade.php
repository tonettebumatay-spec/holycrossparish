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

            <!-- (Tinanggal ang Flash Message dito) -->

            <!-- Conversations List -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h2 class="text-sm font-black text-gray-700 uppercase tracking-widest">Conversations</h2>
                    <span class="text-xs font-bold text-gray-500">Total: {{ $conversations->count() }}</span>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($conversations as $conversation)
                        <a href="{{ route('support.show', $conversation->id) }}" class="block hover:bg-gray-50 transition">
                            <div class="px-6 py-4 flex items-center gap-4">
                                <!-- Avatar -->
                                <div class="w-12 h-12 rounded-full bg-[#4A3728] text-white flex items-center justify-center font-bold text-lg">
                                    {{ strtoupper(substr($conversation->user->name ?? 'U', 0, 1)) }}
                                </div>

                                <!-- Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-baseline mb-1">
                                        <h3 class="text-sm font-bold text-gray-900 truncate">{{ $conversation->user->name ?? 'Unknown User' }}</h3>
                                        <span class="text-[10px] font-bold text-gray-400">{{ $conversation->updated_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 truncate">{{ $conversation->user->email ?? 'No email' }}</p>
                                    <p class="text-xs text-gray-400 truncate mt-1">
                                        @if($conversation->messages->isNotEmpty())
                                            {{ Str::limit($conversation->messages->last()->message, 60) }}
                                        @else
                                            No messages yet.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="py-16 text-center">
                            <div class="inline-flex p-6 bg-gray-50 rounded-full text-gray-400 mb-4">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-700">No Conversations Yet</h3>
                            <p class="text-gray-400 mt-2 text-sm">Messages from parishioners will appear here.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>