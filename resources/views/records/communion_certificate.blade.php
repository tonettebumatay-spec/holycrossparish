<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>First Communion Certificate - {{ $record->candidate_name ?? $record->first_name . ' ' . $record->last_name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <style>
        @media print {
            @page { size: letter; margin: 0 !important; }
            .no-print { display: none !important; }
            body { background: white !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .certificate-container { 
                margin: 0 !important; 
                box-shadow: none !important; 
                height: 100vh !important; 
                width: 100vw !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
            }
            input, select {
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                background: transparent !important;
                font-weight: bold !important;
            }
        }

        .border-gold {
            border-color: #C5A059;
        }

        .font-cinzel { font-family: 'Cinzel', serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        
        input, select {
            transition: all 0.2s ease;
            background: transparent;
        }
        
        input:hover, select:hover {
            background-color: #fef3c7;
        }
        
        input:focus, select:focus {
            outline: none;
            border-bottom-color: #8B4513;
            background-color: #fffbeb;
        }
    </style>
</head>
<body class="bg-gray-200 min-h-screen antialiased p-8 print:p-0">

    <!-- Back Button -->
    <div class="py-2 print:py-0 w-full my-auto no-print">
        <div class="max-w-4xl mx-auto flex justify-between items-center">
            <a href="{{ route('records.index', ['category' => 'communion', 'book_number' => $record->book_number ?? 1]) }}" 
               class="border border-gray-400 rounded-md px-6 py-2 text-xs font-bold text-gray-700 hover:bg-gray-100 transition uppercase tracking-widest shadow-sm">
               ← BACK TO RECORDS
            </a>
            <button type="button" onclick="window.print()" class="bg-[#431407] text-white px-8 py-2 rounded-full font-black text-xs tracking-widest hover:bg-[#7c2d12] transition uppercase shadow-lg">
                PRINT CERTIFICATE
            </button>
        </div>
    </div>

    <form action="{{ route('records.communion.update', $record->id) }}" method="POST">
        @csrf 
        @method('PUT')
        
        <div class="certificate-container max-w-4xl mx-auto bg-white p-4 shadow-2xl relative border-[12px] border-double border-gold mt-6 flex flex-col justify-between" style="height: 1056px; width: 816px; page-break-inside: avoid;">
            
            <div class="absolute inset-0 p-8">
                <div class="w-full h-full border border-gold opacity-30"></div>
            </div>

            <div class="p-10 flex-1 flex flex-col justify-between relative z-10 font-serif">
                
                <!-- Header with Logos -->
                <div class="flex justify-between items-center mb-8 px-4">
                    <!-- LEFT: Parish Logo (holylogo.png) -->
                    <div class="w-20 h-20 flex items-center justify-center">
                        <img src="{{ asset('images/holylogo.png') }}" alt="Parish Logo" class="object-contain max-h-full max-w-full rounded-full border border-gold/30 p-0.5" onerror="this.src='/baprec.png'">
                    </div>
                    
                    <!-- CENTER: Header Text -->
                    <div class="text-center">
                        <p class="text-[10px] italic text-gray-700 tracking-wider">Roman Catholic Diocese of Urdaneta</p>
                        <h1 class="text-2xl font-bold font-cinzel tracking-widest text-gray-900 my-0.5">HOLY CROSS PARISH</h1>
                        <p class="text-[10px] tracking-wide text-gray-600">Alcala, Pangasinan 2425</p>
                    </div>
                    
                    <!-- RIGHT: Diocese Logo (dialogo.png) -->
                    <div class="w-20 h-20 flex items-center justify-center">
                        <img src="{{ asset('images/diologo.png') }}" alt="Diocese Logo" class="object-contain max-h-full max-w-full rounded-full border border-gold/30 p-0.5" onerror="this.style.display='none'">
                    </div>
                </div>

                <!-- Title -->
                <div class="text-center space-y-10 flex-1 flex flex-col items-center justify-center pt-10">
                    <h2 class="text-5xl font-playfair font-normal italic text-[#2E7D32]" style="font-weight: 600;">Certificate of</h2>
                    <h2 class="text-7xl font-playfair font-normal italic text-[#1B5E20] -mt-6" style="font-weight: 700;">First Communion</h2>

                    <p class="text-sm italic text-gray-700 w-3/4 mx-auto leading-relaxed">
                        Jesus said, "I am the bread of life; he who comes to me shall not hunger, and he who believes in me shall never thirst." — John 6:35
                    </p>

                    <!-- Candidate Name - Editable -->
                    <div class="w-3/4 mx-auto mt-16">
                        <div class="border-b-2 border-gold pb-0.5 text-center">
                            <input type="text" name="candidate_name" value="{{ $record->candidate_name ?? $record->first_name . ' ' . $record->last_name }}" 
                                   class="text-3xl font-bold font-cinzel tracking-wider text-gray-950 uppercase text-center w-full bg-transparent border-none focus:outline-none">
                        </div>
                        <p class="text-[11px] mt-1.5 uppercase font-medium tracking-wider text-gray-600">Name of Recipient</p>
                    </div>

                    <div class="space-y-1 pt-6">
                        <p class="text-sm tracking-wide text-gray-800">received</p>
                        <p class="text-xl font-bold uppercase tracking-widest text-gray-950 font-cinzel">THE BODY AND BLOOD OF OUR LORD</p>
                        <p class="text-sm tracking-wide text-gray-800">for the first time</p>
                    </div>

                    <!-- Minister and Date - Editable -->
                    <div class="w-3/4 mx-auto pt-6 space-y-3.5 text-sm">
                        <div class="flex items-baseline justify-center gap-2">
                            from the hands of 
                            <input type="text" name="minister_name" value="{{ $record->minister_name }}" 
                                   class="font-bold border-b border-gold px-6 min-w-[250px] text-center bg-transparent focus:outline-none">
                        </div>
                        <p class="text-[11px] mt-1.5 uppercase font-medium tracking-wider text-gray-600 -ml-16">Minister of Holy Communion</p>
                        
                        <div class="pt-2 flex items-baseline justify-center gap-2">
                            on 
                            <input type="date" name="communion_date" value="{{ $record->communion_date ? \Carbon\Carbon::parse($record->communion_date)->format('Y-m-d') : '' }}" 
                                   class="font-bold border-b border-gold px-6 min-w-[150px] text-center bg-transparent focus:outline-none">
                        </div>
                        <p class="text-[11px] mt-1.5 uppercase font-medium tracking-wider text-gray-600">Date of First Communion</p>

                        <p class="pt-3 text-sm italic text-gray-700">In the assembly of this Parish Community.</p>
                    </div>
                </div>

                <!-- Footer Section -->
                <div class="w-full mt-auto pt-20 px-8 space-y-20">
                    <!-- Issue Date -->
                    <div class="text-center">
                        <div class="flex items-baseline justify-center gap-2 text-sm text-gray-800">
                            Given this 
                            <span class="font-bold border-b border-gold px-6 min-w-[150px]">{{ now()->format('F d, Y') }}</span>
                        </div>
                        <p class="text-[11px] mt-1.5 uppercase font-medium tracking-wider text-gray-600">Date of Issue</p>
                    </div>

                    <!-- Signatures -->
                    <div class="flex justify-between items-end gap-16 text-sm font-medium">
                        <div class="flex-1 text-center">
                            <input type="text" name="coordinator_name" value="{{ $record->coordinator_name ?? '_________________________' }}" 
                                   class="border-b border-gold pb-0.5 min-w-[200px] text-center bg-transparent focus:outline-none">
                            <p class="text-[11px] mt-1.5 uppercase font-medium tracking-wider text-gray-600">Coordinator, Adult Catechesis</p>
                        </div>

                        <div class="flex-1 text-center">
                            <input type="text" name="parish_priest" value="{{ $record->minister_name ?? 'REV. FR. ELISAR CHRISTOPHER M. ITCHON' }}" 
                                   class="font-bold border-b border-gold pb-0.5 min-w-[200px] text-center bg-transparent focus:outline-none uppercase">
                            <p class="text-[11px] mt-1.5 uppercase font-medium tracking-wider text-gray-600">Parish Priest</p>
                        </div>
                    </div>
                </div>

                <!-- QR Code Section -->
                <div class="mt-4 flex justify-end">
                    <div class="flex flex-col items-center">
                        @php 
                            $qrCodeUrl = "https://quickchart.io/qr?text=" . urlencode(route('records.communion.show', $record->id)) . "&size=50&margin=0";
                        @endphp
                        <img src="{{ $qrCodeUrl }}" alt="Verify QR" width="45" height="45">
                        <p class="text-[6px] uppercase font-bold mt-0.5 text-gray-400">Scan to Verify</p>
                    </div>
                </div>

                <!-- Update Button -->
                <div class="mt-4 text-center no-print">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-2 rounded text-[10px] font-bold shadow-md uppercase tracking-wider transition-colors duration-200">
                        UPDATE RECORD
                    </button>
                </div>
            </div>
        </div>
    </form>
</body>
</html>