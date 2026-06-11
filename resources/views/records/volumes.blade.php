<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-6">
            
            <div class="flex justify-between items-center mb-16">
                <a href="{{ route('records.index') }}" class="border-2 border-gray-200 rounded-full px-8 py-2.5 text-xs font-black uppercase tracking-widest text-gray-700 hover:bg-gray-50 transition shadow-sm bg-white">
                    ← Back to Main Shelf
                </a>
                <h1 class="text-4xl font-black text-gray-800 tracking-tighter italic uppercase underline decoration-[#4d290a] decoration-4 underline-offset-8">
                    {{ $title }}
                </h1>
                <div class="w-32"></div> </div>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8">
                @foreach($volumes as $num)
                    <a href="{{ route('records.index', ['category' => $category, 'book_number' => $num]) }}" 
                       class="bg-[#5d4037] rounded-3xl p-8 text-center text-white shadow-2xl hover:scale-105 transition-all duration-300 group relative overflow-hidden border-l-8 border-black/20">
                        
                        <div class="border-t border-b border-white/10 py-6">
                            <p class="text-[10px] tracking-[0.4em] uppercase opacity-60 mb-2 font-bold">Book</p>
                            <p class="text-5xl font-black mb-2 drop-shadow-md">{{ $num }}</p>
                            <p class="text-[10px] tracking-[0.3em] uppercase opacity-40 font-bold">{{ $category }}</p>
                        </div>

                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-white/10"></div>
                        <div class="absolute right-4 bottom-4 opacity-10 group-hover:opacity-30 transition-opacity">
                            <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24"><path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71z"/></svg>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="w-full h-8 bg-[#4d290a] rounded-full shadow-2xl mt-16 border-t border-white/10"></div>
        </div>
    </div>
</x-app-layout>
Compose
Write to xiao xiao


