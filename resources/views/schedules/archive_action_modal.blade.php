<div x-show="openArchiveModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak x-transition.opacity>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" @click="openArchiveModal = false"></div>

        <div class="relative bg-white rounded-[30px] shadow-2xl max-w-lg w-full p-8 transition-all transform">
            <div class="mb-6">
                <h3 class="text-xl font-black text-gray-800 uppercase italic">Archive Schedule</h3>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Choose status</p>
            </div>

            <div class="space-y-4">
                <form action="" method="POST" x-bind:action="'/schedules/' + openArchiveModalId + '/archive/done'">
                    @csrf
                    <button type="submit" class="w-full bg-emerald-600 text-white py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-emerald-500 transition-all shadow-lg">
                        Done
                    </button>
                </form>

                <form action="" method="POST" x-bind:action="'/schedules/' + openArchiveModalId + '/archive/cancel'">
                    @csrf
                    <button type="submit" class="w-full bg-rose-600 text-white py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-rose-500 transition-all shadow-lg">
                        Cancelled
                    </button>
                </form>

                <button type="button" @click="openArchiveModal = false" class="w-full px-6 py-4 bg-gray-100 text-gray-500 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-gray-200 transition-all">
                    Back
                </button>
            </div>
        </div>
    </div>
</div>