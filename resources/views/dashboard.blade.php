<x-app-layout>
    <div class="relative min-h-[calc(100vh-140px)]">

        <!-- Background -->
        <div
            class="fixed inset-0 -z-10 bg-cover bg-center"
            style="background-image: url('https://seepangasinan.com/wp-content/uploads/2022/08/1499243164_explora-holy-cross-parish-reliquary-2-1536x863.jpg');"
        ></div>
        
        <!-- ✅ Inalis ang blur at ginawang 30% transparent lang -->
        <div class="fixed inset-0 -z-10 bg-white/30"></div>

        <main class="relative py-12">
            <div class="max-w-7xl mx-auto px-6">
                
                <!-- GRID: 4 Columns -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                    @foreach($cards as $card)
                        <a href="{{ $card['link_url'] }}" 
                           class="bg-[#FCFAF5] rounded-[24px] p-6 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group h-full border border-[#E8E0D5]">
                            
                            <div>
                                <!-- Title -->
                                <div class="flex items-center gap-3 mb-6">
                                    <!-- Icon Box (Beige) -->
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-[#EBE3D5] text-xl shadow-sm group-hover:scale-110 transition-transform">
                                        {{ $card['icon'] }}
                                    </div>
                                    <!-- Title Text -->
                                    <h4 class="text-sm font-black text-[#3E2723] uppercase tracking-wider">
                                        {{ $card['title'] }}
                                    </h4>
                                </div>

                                <!-- Count (Dark Brown) -->
                                <p class="text-6xl font-black text-[#3E2723] mb-6 tracking-tighter">
                                    {{ $card['count'] }}
                                </p>
                            </div>

                            <!-- Link Text -->
                            <div class="flex items-center justify-between mt-auto pt-4 border-t border-[#E8E0D5]/50">
                                <span class="text-[11px] font-bold text-[#5D4037] uppercase tracking-widest">
                                    {{ $card['link_text'] }}
                                </span>
                                
                                <!-- MALAKING ARROW NA MAY BILOG -->
                                <div class="w-8 h-8 rounded-full bg-[#EBE3D5] flex items-center justify-center group-hover:bg-[#3E2723] transition-colors duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#3E2723] group-hover:text-white transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach

                </div>
            </div>
        </main>
    </div>
</x-app-layout>