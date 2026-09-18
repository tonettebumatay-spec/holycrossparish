<x-app-layout>
    <div class="py-12 bg-white min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-6">

            <!-- Top bar: Dashboard + Search -->
            <div class="flex justify-between items-center mb-8">
                <a href="{{ route('dashboard') }}" class="border-2 border-gray-200 rounded-full px-8 py-2.5 font-black text-sm tracking-widest text-gray-700 hover:bg-gray-50 transition uppercase shadow-sm">
                    Dashboard
                </a>

                <!-- Search Bar -->
                <form method="GET" action="{{ route('records.search') }}" class="flex gap-2">
                    <input type="text" name="q" required placeholder="Search any name..."
                           class="w-72 border-2 border-gray-300 rounded-full px-5 py-2.5 text-sm focus:outline-none focus:border-[#4d290a]">
                    <button type="submit"
                            class="bg-[#4d290a] hover:bg-[#3a1f07] text-white font-black text-xs uppercase tracking-widest px-6 py-2.5 rounded-full transition">
                        Search
                    </button>
                </form>
            </div>

            <!-- Archival Shelf -->
            <div class="flex justify-center items-end gap-8 mb-2">
                @foreach($books as $book)
                    <a href="{{ route('records.index', ['category' => $book['category']]) }}" class="flex flex-col items-center group cursor-pointer">
                        <div class="w-48 transition-all duration-300 group-hover:-translate-y-12 group-hover:scale-110">
                            <img src="{{ asset('images/' . $book['file']) }}" class="w-full h-auto drop-shadow-2xl" alt="{{ $book['title'] }}">
                        </div>
                        <div class="mt-8">
                            <span class="font-black text-[#1a202c] uppercase tracking-tighter text-2xl group-hover:text-[#4d290a]">
                                {{ $book['title'] }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="w-full h-6 bg-[#4d290a] rounded-full shadow-2xl mt-2 border-t border-white/10"></div>
        </div>
    </div>
</x-app-layout>