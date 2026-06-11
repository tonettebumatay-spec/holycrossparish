<x-app-layout>
    <div x-data="{ openModal: false }">

        <x-slot name="header">
            <div class="flex items-center justify-between">
                <h2 class="font-black text-xl text-gray-800 leading-tight italic uppercase tracking-tighter">
                    {{ __('Mass Schedules') }}
                </h2>

                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-gray-50 transition-all shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
            </div>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 font-black uppercase text-[10px] tracking-widest rounded-r-xl">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="flex justify-between mb-6 gap-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-6 py-3 bg-white border border-gray-200 text-gray-600 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-gray-50 transition-all shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Dashboard
                    </a>

                    <button @click="openModal = true" class="group flex items-center gap-3 px-6 py-3 bg-[#5D4037] hover:bg-[#4E342E] text-white rounded-2xl shadow-sm transition-all">
                        <span class="text-sm font-black uppercase tracking-widest">Post New Schedule</span>
                        <div class="bg-white/20 rounded-full p-1 group-hover:rotate-90 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                    </button>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-[30px] p-10 border border-gray-100">
                    <div class="flex items-center justify-between mb-8 border-b border-gray-50 pb-6">
                        <div>
                            <h3 class="text-lg font-black text-gray-800 uppercase tracking-widest">Mass Schedules</h3>
                            <p class="text-xs text-gray-400 font-bold uppercase mt-1">Holy Cross Parish Archive</p>
                        </div>
                        <span class="px-4 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-tighter">Live System</span>
                    </div>

                    <div class="space-y-8">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-md font-black text-gray-800 uppercase tracking-widest">Active / Live Schedules</h4>
                                <span class="px-4 py-1 bg-yellow-100 text-yellow-800 rounded-full text-[10px] font-black uppercase tracking-tighter">Pending</span>
                            </div>

                            <div class="space-y-4">
                                @forelse($liveSchedules as $schedule)
                                    <div class="flex items-center justify-between p-6 bg-gray-50 rounded-[20px] border border-gray-100 hover:border-green-300 transition-all gap-4">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-3 mb-1">
                                                <span class="text-[10px] font-black bg-yellow-500 text-white px-2 py-0.5 rounded uppercase tracking-tighter">Pending</span>
                                                <h4 class="font-black text-gray-800 uppercase tracking-tight">{{ $schedule->barangay }}</h4>
                                            </div>
                                            <p class="text-sm text-gray-500 font-medium">{{ $schedule->description }}</p>
                                        </div>

                                        <div class="text-right flex items-center gap-4">
                                            <div>
                                                <div class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ \Carbon\Carbon::parse($schedule->date)->format('M d, Y') }}</div>
                                                <div class="text-lg font-black text-green-600 italic">{{ \Carbon\Carbon::parse($schedule->time)->format('h:i A') }}</div>
                                            </div>

                                            <form
                                                action="{{ route('schedules.archive_status', ['schedule' => $schedule->id, 'archive_status' => 'done']) }}"
                                                method="POST"
                                                class="flex items-center gap-2"
                                                x-data="{ chosen: 'done' }"
                                                @submit="$el.action = $el.action.replace('done', chosen)"
                                            >
                                                @csrf
                                                <input type="hidden" name="archive_status" :value="chosen">

                                                <div class="flex items-center gap-2">
                                                    <select
                                                        x-model="chosen"
                                                        class="bg-white border border-gray-200 rounded-xl text-[10px] font-black uppercase tracking-widest py-2 px-3 text-gray-700 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#5D4037]"
                                                    >
                                                        <option value="done">Done</option>
                                                        <option value="cancelled">Cancel</option>
                                                    </select>

                                                    <button
                                                        type="submit"
                                                        class="px-4 py-2 bg-[#5D4037] hover:bg-[#4E342E] text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm transition-all"
                                                        title="Archive"
                                                    >
                                                        Update
                                                    </button>
                                                </div>
                                            </form>

                                            <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this schedule?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-100 border border-red-100 transition-all" aria-label="Delete schedule">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M18 6 6 18" />
                                                        <path d="M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-20 flex flex-col items-center justify-center border-2 border-dashed border-gray-100 rounded-[20px]">
                                        <div class="bg-gray-50 p-5 rounded-full mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-400 italic font-medium uppercase tracking-widest text-sm">No active schedules have been posted yet.</p>
                                        <p class="text-gray-300 text-[10px] uppercase font-black mt-2">Click the button above to add an entry</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-md font-black text-gray-800 uppercase tracking-widest">Archived Schedules</h4>
                                <span class="px-4 py-1 bg-gray-100 text-gray-700 rounded-full text-[10px] font-black uppercase tracking-tighter">Done / Cancelled</span>
                            </div>

                            <div class="space-y-4">
                                @forelse($archivedSchedules as $schedule)
                                    @php
                                        $status = $schedule->status;
                                    @endphp

                                    <div class="flex items-center justify-between p-6 bg-gray-50 rounded-[20px] border border-gray-100 hover:border-green-300 transition-all gap-4">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-3 mb-1">
                                                {{-- INAYOS DITO: Ginawang 'cancelled' para mag-match sa controller at database table --}}
                                                @if($status === 'done')
                                                    <span class="text-[10px] font-black bg-emerald-600 text-white px-2 py-0.5 rounded uppercase tracking-tighter">Done</span>
                                                @elseif($status === 'cancelled')
                                                    <span class="text-[10px] font-black bg-rose-600 text-white px-2 py-0.5 rounded uppercase tracking-tighter">Cancel</span>
                                                @else
                                                    <span class="text-[10px] font-black bg-yellow-500 text-white px-2 py-0.5 rounded uppercase tracking-tighter">Pending</span>
                                                @endif

                                                <h4 class="font-black text-gray-800 uppercase tracking-tight">{{ $schedule->barangay }}</h4>
                                            </div>

                                            <p class="text-sm text-gray-500 font-medium">{{ $schedule->description }}</p>
                                        </div>

                                        <div class="text-right flex items-center gap-4">
                                            <div>
                                                <div class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ \Carbon\Carbon::parse($schedule->date)->format('M d, Y') }}</div>
                                                <div class="text-lg font-black italic {{ $status === 'done' ? 'text-emerald-600' : ($status === 'cancelled' ? 'text-rose-600' : 'text-gray-600') }}">{{ \Carbon\Carbon::parse($schedule->time)->format('h:i A') }}</div>
                                            </div>

                                            <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this schedule?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-100 border border-red-100 transition-all" aria-label="Delete schedule">
                                                    <svg xmlns="http://www.w3.org/2000/xl" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M18 6 6 18" />
                                                        <path d="M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-20 flex flex-col items-center justify-center border-2 border-dashed border-gray-100 rounded-[20px]">
                                        <div class="bg-gray-50 p-5 rounded-full mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-400 italic font-medium uppercase tracking-widest text-sm">No archived schedules yet.</p>
                                        <p class="text-gray-300 text-[10px] uppercase font-black mt-2">Mark an active schedule as Done or Cancel</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak x-transition.opacity>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" @click="openModal = false"></div>

                <div class="relative bg-white rounded-[30px] shadow-2xl max-w-lg w-full p-8 transition-all transform">
                    <div class="mb-6">
                        <h3 class="text-xl font-black text-gray-800 uppercase italic">Add New Entry</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Schedule for Barangay Mass or Event</p>
                    </div>

                    <form action="{{ route('schedules.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1 ml-1">Barangay / Location</label>
                            <input type="text" name="location" required class="w-full border-none rounded-xl bg-gray-50 text-sm focus:ring-[#5D4037]" placeholder="e.g. Brgy. San Manuel">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1 ml-1">Date</label>
                                <input type="date" name="date" required class="w-full border-none rounded-xl bg-gray-50 text-sm focus:ring-[#5D4037]">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1 ml-1">Time</label>
                                <input type="time" name="time" required class="w-full border-none rounded-xl bg-gray-50 text-sm focus:ring-[#5D4037]">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1 ml-1">Event Description</label>
                            <textarea name="description" rows="3" class="w-full border-none rounded-xl bg-gray-50 text-sm focus:ring-[#5D4037]" placeholder="e.g. Patronal Feast Mass"></textarea>
                        </div>

                        <div class="pt-4 flex gap-3">
                            <button type="submit" class="flex-1 bg-[#5D4037] text-white py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-[#4E342E] transition-all shadow-lg">
                                Save Schedule
                            </button>
                            <button type="button" @click="openModal = false" class="px-6 py-4 bg-gray-100 text-gray-500 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-gray-200 transition-all">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>