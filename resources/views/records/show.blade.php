<x-app-layout>
    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-6">
            
            <div class="flex justify-between items-center mb-12">
                <a href="{{ route('records.index') }}" class="border border-gray-300 rounded-full px-8 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    ← Back to Main Shelf
                </a>

                <!-- This variable is now defined in the controller -->
                <h2 class="text-6xl font-black text-[#1a202c] tracking-tight uppercase">{{ $title }}</h2>

                <div class="w-40"></div> <!-- Spacer for balance -->
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8">
                @foreach($volumes as $volume)
                    <div class="group flex flex-col items-center">
                        <a href="{{ route('records.index', ['category' => $category, 'book_number' => $volume]) }}" 
                           class="w-full bg-[#5d4037] aspect-[4/3] rounded-xl shadow-lg flex flex-col items-center justify-center text-white transition-all duration-200 group-hover:scale-105 border-l-[14px] border-black/25">
                            <span class="text-3xl font-black italic">Book</span>
                            <span class="text-5xl font-black">{{ $volume }}</span>
                        </a>
                        
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>