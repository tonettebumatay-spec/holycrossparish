{{-- Archive status modal (reusable) --}}
<div x-show="openArchiveModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak x-transition.opacity>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" @click="openArchiveModal = false"></div>

        <div class="relative bg-white rounded-[30px] shadow-2xl max-w-lg w-full p-8 transition-all transform">
            <div class="mb-6">
                <h3 class="text-xl font-black text-gray-800 uppercase italic">Archive Schedule</h3>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Mark as Done or Cancelled</p>
            </div>

            <form :action="''" method="POST" class="space-y-4">
                {{-- We'll submit to a fixed route using schedule id from openArchiveModalId --}}
                @csrf

                <div class="flex gap-3">
                    <form action="{{ route('schedules.archive_status', ['schedule' => '_ID_']) }}" method="POST" class="flex-1">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="archive_status" value="done">
                        {{-- hidden id injected via JS replacement --}}
                        <input type="hidden" name="_schedule_id" :value="openArchiveModalId">
                        <button
                            type="button"
                            class="flex-1 bg-emerald-600 text-white py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-emerald-500 transition-all shadow-lg"
                            @click="window.location.href = '{{ route('schedules.archive_status', ['schedule' => '_ID_']) }}'.replace('_ID_', openArchiveModalId).replace(/__ID__/g, openArchiveModalId)"
                        >
                            Done
                        </button>
                    </form>

                    <form action="{{ route('schedules.archive_status', ['schedule' => '_ID_']) }}" method="POST" class="flex-1">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="archive_status" value="cancelled">
                        <input type="hidden" name="_schedule_id" :value="openArchiveModalId">
                        <button
                            type="button"
                            class="flex-1 bg-rose-600 text-white py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-rose-500 transition-all shadow-lg"
                            @click="window.location.href = '{{ route('schedules.archive_status', ['schedule' => '_ID_']) }}'.replace('_ID_', openArchiveModalId).replace(/__ID__/g, openArchiveModalId)"
                        >
                            Cancelled
                        </button>
                    </form>
                </div>

                <div class="pt-2">
                    <button type="button" @click="openArchiveModal = false" class="w-full px-6 py-4 bg-gray-100 text-gray-500 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-gray-200 transition-all">
                        Back
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>