<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marriage Banns - Holy Cross Parish</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Great+Vibes&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Playfair Display', serif; 
            background-color: #F4F1EA; 
            background-image: url('https://www.transparenttextures.com/patterns/cream-paper.png');
        }
        .poster-card { 
            background-color: #fdfbf7; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.15); 
        }
        .script-font { 
            font-family: 'Great Vibes', cursive; 
        }
    </style>
</head>
<body class="py-12 px-4">

    <div class="max-w-3xl mx-auto">
        @forelse($banns as $bann)
            <!-- POSTER CARD -->
            <div class="poster-card border border-[#D4AF37] p-8 md:p-12 mb-10 relative overflow-hidden rounded-lg">
                
                <!-- Background Decor -->
                <div class="absolute top-0 left-0 w-full h-full opacity-5 pointer-events-none" style="background-image: url('https://www.transparenttextures.com/patterns/floral.png');"></div>

                <!-- Header -->
                <div class="text-center relative z-10">
                    <h1 class="text-3xl md:text-5xl font-bold uppercase tracking-widest text-[#3E2723]">Marriage Banns</h1>
                    <div class="w-24 h-1 bg-[#D4AF37] mx-auto my-4"></div>
                    
                    <p class="text-lg md:text-xl font-bold text-gray-800 mt-4">
                        Wedding on {{ \Carbon\Carbon::parse($bann->wedding_date)->format('F d, Y') }} 
                    </p>
                    <p class="text-sm md:text-md text-gray-700 uppercase tracking-widest mt-1">
                        at Holy Cross Parish
                    </p>
                </div>

                <!-- Photos (Side by Side) -->
                <div class="flex justify-center items-center gap-2 md:gap-6 mt-10 mb-8 relative z-10">
                    <!-- Groom Photo -->
                    <div class="relative">
                        @if($bann->groom_picture)
                            <img src="{{ asset('storage/' . $bann->groom_picture) }}" class="w-32 h-32 md:w-48 md:h-48 object-cover rounded-full border-4 border-[#D4AF37] shadow-xl" alt="Groom">
                        @else
                            <div class="w-32 h-32 md:w-48 md:h-48 bg-gray-200 rounded-full border-4 border-[#D4AF37] shadow-xl flex items-center justify-center text-gray-400 text-xs text-center p-4">No Groom Photo</div>
                        @endif
                    </div>
                    
                    <!-- Heart Icon -->
                    <div class="text-red-500 text-2xl md:text-4xl z-20 -mx-3 md:-mx-4 drop-shadow-md">
                        ❤️
                    </div>

                    <!-- Bride Photo -->
                    <div class="relative">
                        @if($bann->bride_picture)
                            <img src="{{ asset('storage/' . $bann->bride_picture) }}" class="w-32 h-32 md:w-48 md:h-48 object-cover rounded-full border-4 border-[#D4AF37] shadow-xl" alt="Bride">
                        @else
                            <div class="w-32 h-32 md:w-48 md:h-48 bg-gray-200 rounded-full border-4 border-[#D4AF37] shadow-xl flex items-center justify-center text-gray-400 text-xs text-center p-4">No Bride Photo</div>
                        @endif
                    </div>
                </div>

                <!-- Details -->
                <div class="text-center space-y-2 relative z-10">
                    <!-- Groom -->
                    <div>
                        <h2 class="text-lg md:text-2xl font-bold text-[#3E2723] uppercase tracking-wider">
                            GROOM: {{ $bann->groom_name }}
                        </h2>
                        <p class="text-sm md:text-base text-gray-700 mt-1">
                            Parents: {{ $bann->groom_father ?? 'N/A' }} &amp; {{ $bann->groom_mother ?? 'N/A' }}
                        </p>
                        <p class="text-xs md:text-sm text-gray-500">
                            Resident of {{ $bann->groom_address ?? 'N/A' }}
                        </p>
                    </div>

                    <!-- The Vow -->
                    <div class="py-3">
                        <p class="script-font text-3xl md:text-5xl text-[#D4AF37]">
                            wishes to contract holy matrimony with
                        </p>
                    </div>

                    <!-- Bride -->
                    <div>
                        <h2 class="text-lg md:text-2xl font-bold text-[#3E2723] uppercase tracking-wider">
                            BRIDE: {{ $bann->bride_name }}
                        </h2>
                        <p class="text-sm md:text-base text-gray-700 mt-1">
                            Parents: {{ $bann->bride_father ?? 'N/A' }} &amp; {{ $bann->bride_mother ?? 'N/A' }}
                        </p>
                        <p class="text-xs md:text-sm text-gray-500">
                            Resident of {{ $bann->bride_address ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <!-- Legal Text & Publication Dates -->
                <div class="mt-10 pt-6 border-t border-[#D4AF37]/50 text-center text-xs text-gray-600 relative z-10 max-w-xl mx-auto">
                    <p class="leading-relaxed italic">
                        "Everyone is enjoined to refrain from posting comments on this post. Any person having knowledge of any legal impediment to the said marriage may file a written opposition with the Holy Cross Parish within ten (10) days from the date of publication."
                    </p>
                    <p class="mt-4 font-bold text-gray-800 tracking-wider">
                        Date of Publication: 
                        @if($bann->publication_start && $bann->publication_end)
                            {{ \Carbon\Carbon::parse($bann->publication_start)->format('d F') }} to {{ \Carbon\Carbon::parse($bann->publication_end)->format('d F Y') }}
                        @else
                            {{ $bann->banns_date->format('d F Y') }} to {{ $bann->expires_at ? $bann->expires_at->format('d F Y') : $bann->banns_date->addDays(10)->format('d F Y') }}
                        @endif
                    </p>
                </div>
            </div>
        @empty
            <div class="bg-white p-12 text-center shadow-lg border border-gray-200 rounded-lg">
                <h3 class="text-xl font-bold text-gray-700">No Marriage Banns</h3>
                <p class="text-gray-400 mt-2">Walang naka-post na banns sa ngayon.</p>
            </div>
        @endforelse
    </div>

</body>
</html>