<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-10">
            <div class="flex justify-between items-center mb-10 gap-6">
                <a href="{{ route('dashboard') }}" class="border-2 border-gray-200 rounded-full px-6 py-2 text-xs font-black uppercase tracking-widest text-gray-700 hover:bg-gray-100 transition shadow-sm bg-white inline-flex items-center gap-2">
                    ← Back to Dashboard
                </a>

                <h1 class="text-4xl font-black text-gray-800 tracking-tighter italic uppercase underline decoration-indigo-600 decoration-4 underline-offset-8 text-center">
                    Online Viewings
                </h1>

                <div>
                    <a href="{{ route('viewing.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-7 py-2 rounded-full text-xs font-black uppercase tracking-widest transition shadow-sm inline-flex items-center gap-2">
                        <span class="text-lg leading-none">+</span> Add Viewing
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 font-black uppercase text-[10px] tracking-widest rounded-r-xl">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($viewings as $viewing)
                    <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm hover:shadow-md transition-all overflow-hidden">
                        <div class="h-56 bg-gray-50">
                            <img
                                src="data:image/*;base64,{{ $viewing->image }}"
                                alt="{{ $viewing->title }}"
                                class="w-full h-full object-cover"
                            />
                        </div>

                        <div class="p-6">
                            <h2 class="text-lg font-black text-gray-800 uppercase tracking-widest">
                                {{ $viewing->title }}
                            </h2>
                            <p class="mt-3 text-sm text-gray-600 leading-relaxed">
{{ \Illuminate\Support\Str::limit($viewing->description, 140) }}
                            </p>

                            <div class="mt-6 flex items-center justify-between gap-3">
                                <a href="{{ route('viewing.show', $viewing->id) }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[11px] font-black uppercase tracking-widest transition shadow-sm">
                                    View Details
                                </a>

                                <form method="POST" action="{{ route('viewing.destroy', $viewing->id) }}" class="inline" onsubmit="return confirm('Delete this viewing?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 text-[11px] font-black uppercase tracking-widest transition shadow-sm">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 p-12 text-center">
                            <div class="inline-flex p-6 bg-indigo-50 rounded-full text-indigo-600 mb-4">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-black text-gray-800 uppercase mb-2">No Viewings Yet</h2>
                            <p class="text-gray-400 font-medium mb-8 uppercase tracking-widest text-xs">
                                Add your first online viewing to display content in the portal.
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>


