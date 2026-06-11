<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-10">
            <div class="flex justify-between items-center mb-10 gap-6">
                <a href="{{ route('viewing.index') }}" class="border-2 border-gray-200 rounded-full px-6 py-2 text-xs font-black uppercase tracking-widest text-gray-700 hover:bg-gray-100 transition shadow-sm bg-white inline-flex items-center gap-2">
                    ← Back to Viewings
                </a>

                <h1 class="text-4xl font-black text-gray-800 tracking-tighter italic uppercase underline decoration-indigo-600 decoration-4 underline-offset-8 text-center">
                    Viewing Details
                </h1>

                <div class="w-32"></div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 font-black uppercase text-[10px] tracking-widest rounded-r-xl">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">
                <div class="h-[320px] bg-gray-50">
                    <img
                        src="data:image/*;base64,{{ $viewing->image }}"
                        alt="{{ $viewing->title }}"
                        class="w-full h-full object-cover"
                    />
                </div>

                <div class="p-10">
                    <h2 class="text-3xl font-black text-gray-800 uppercase tracking-widest">
                        {{ $viewing->title }}
                    </h2>

                    <div class="mt-6">
                        <h3 class="text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Description</h3>
                        <div class="prose prose-indigo max-w-none">
                            {!! nl2br(e($viewing->description)) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-4">
                <form method="POST" action="{{ route('viewing.destroy', $viewing->id) }}" onsubmit="return confirm('Are you sure you want to delete this viewing?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-3 rounded-2xl bg-red-600 hover:bg-red-700 text-white text-xs font-black uppercase tracking-widest transition shadow-sm">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

