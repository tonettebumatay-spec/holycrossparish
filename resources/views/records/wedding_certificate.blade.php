<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding Certificate - {{ $record->groom_name }} & {{ $record->bride_name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <style>
        @media print {
            @page { size: letter; margin: 0.3in !important; }
            .no-print { display: none !important; }
            body { background: white !important; margin: 0; padding: 0; }
            .certificate-container { margin: 0 !important; box-shadow: none !important; border: 2px solid #C5A059 !important; }
            input, select { border: none !important; padding: 0 !important; background: transparent !important; font-weight: bold !important; }
            .corner-decoration { print-color-adjust: exact; }
        }
        
        .corner-decoration { position: absolute; width: 50px; height: 50px; z-index: 10; }
        .top-left { top: 8px; left: 8px; border-left: 2px double #C5A059; border-top: 2px double #C5A059; }
        .top-right { top: 8px; right: 8px; border-right: 2px double #C5A059; border-top: 2px double #C5A059; }
        .bottom-left { bottom: 8px; left: 8px; border-left: 2px double #C5A059; border-bottom: 2px double #C5A059; }
        .bottom-right { bottom: 8px; right: 8px; border-right: 2px double #C5A059; border-bottom: 2px double #C5A059; }
        .inner-border { border: 1px solid #C5A059; margin: 4px; }
        
        input, select { transition: all 0.2s ease; background: transparent; }
        input:hover, select:hover { background-color: #fef3c7; }
        input:focus, select:focus { outline: none; border-bottom-color: #8B4513; background-color: #fffbeb; }
    </style>
</head>
<body class="bg-gray-200 min-h-screen antialiased">

    <div class="py-2 print:py-0">
        <!-- Back Button -->
        <div class="max-w-3xl mx-auto mb-2 no-print">
            <div class="flex justify-between items-center">
                <a href="{{ route('records.index', ['category' => 'wedding', 'book_number' => $record->book_number ?? 1]) }}" 
                   class="border border-gray-400 rounded-md px-4 py-1.5 text-xs font-bold uppercase tracking-widest shadow-sm hover:bg-gray-100 transition bg-white">
                    ← BACK TO RECORDS
                </a>
                <button type="button" onclick="window.print()" class="bg-[#431407] text-white px-5 py-1.5 rounded-full font-black text-xs tracking-widest hover:bg-[#7c2d12] transition uppercase shadow-lg">
                    PRINT
                </button>
            </div>
        </div>

        <div class="certificate-container max-w-3xl mx-auto bg-white p-3 shadow-2xl relative border-[8px] border-double border-[#C5A059]">
            
            <div class="corner-decoration top-left"></div>
            <div class="corner-decoration top-right"></div>
            <div class="corner-decoration bottom-left"></div>
            <div class="corner-decoration bottom-right"></div>

            <div class="inner-border p-4 relative">
                
                <form action="{{ route('records.wedding.update', $record->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Header with Logos -->
                    <div class="flex justify-center items-center gap-3 mb-3">
                        <!-- LEFT: Parish Logo (holylogo.png) -->
                        <div class="w-10 h-10 flex items-center justify-center">
                            <img src="{{ asset('images/holylogo.png') }}" alt="Parish Logo" class="object-contain max-h-full max-w-full rounded-full" onerror="this.style.display='none'">
                        </div>
                        
                        <!-- CENTER: Header Text -->
                        <div class="text-center">
                            <p class="text-[9px] tracking-widest uppercase font-serif">Roman Catholic Diocese of Urdaneta</p>
                            <h1 class="text-lg font-bold tracking-widest text-gray-900" style="font-family: 'Cinzel', serif;">HOLY CROSS PARISH</h1>
                            <p class="text-[9px] tracking-wide text-gray-600">Alcala, Pangasinan 2425</p>
                        </div>
                        
                        <!-- RIGHT: Diocese Logo (dialogo.png) -->
                        <div class="w-10 h-10 flex items-center justify-center">
                            <img src="{{ asset('images/diologo.png') }}" alt="Diocese Logo" class="object-contain max-h-full max-w-full rounded-full" onerror="this.style.display='none'">
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="text-center mb-3">
                        <h2 class="text-lg font-bold uppercase tracking-wider" style="font-family: 'Playfair Display', serif;">CERTIFICATE OF MARRIAGE</h2>
                    </div>

                    <!-- Subtitle -->
                    <div class="text-center mb-2">
                        <p class="text-[11px] font-semibold">THIS IS TO CERTIFY THAT</p>
                    </div>

                    <!-- Groom Section -->
                    <div class="text-center mb-3">
                        <input type="text" name="groom_name" value="{{ strtoupper($record->groom_name) }}" 
                               class="text-base font-bold uppercase text-center focus:outline-none w-full max-w-sm mx-auto block bg-transparent border-b border-[#8B4513] px-2 py-0">
                        <div class="flex justify-center gap-1 text-[11px] mt-1">
                            <input type="text" name="groom_status" value="{{ $record->groom_status ?? 'single' }}" class="border-b border-gray-400 w-20 text-center bg-transparent lowercase">
                            <span>.</span>
                            <input type="text" name="groom_age" value="{{ $record->groom_age }}" class="border-b border-gray-400 w-12 text-center bg-transparent">
                            <span>years old.</span>
                        </div>
                        <p class="text-[10px] mt-1">Son of</p>
                        <input type="text" name="groom_parents" value="{{ $record->groom_parents }}" 
                               class="text-xs font-semibold uppercase text-center focus:outline-none w-full max-w-sm mx-auto block bg-transparent border-b border-gray-400 px-2 py-0">
                    </div>

                    <!-- AND -->
                    <div class="text-center my-1">
                        <p class="text-sm font-bold">AND</p>
                    </div>

                    <!-- Bride Section -->
                    <div class="text-center mb-3">
                        <input type="text" name="bride_name" value="{{ strtoupper($record->bride_name) }}" 
                               class="text-base font-bold uppercase text-center focus:outline-none w-full max-w-sm mx-auto block bg-transparent border-b border-[#8B4513] px-2 py-0">
                        <div class="flex justify-center gap-1 text-[11px] mt-1">
                            <input type="text" name="bride_status" value="{{ $record->bride_status ?? 'single' }}" class="border-b border-gray-400 w-20 text-center bg-transparent lowercase">
                            <span>.</span>
                            <input type="text" name="bride_age" value="{{ $record->bride_age }}" class="border-b border-gray-400 w-12 text-center bg-transparent">
                            <span>years old.</span>
                        </div>
                        <p class="text-[10px] mt-1">Daughter of</p>
                        <input type="text" name="bride_parents" value="{{ $record->bride_parents }}" 
                               class="text-xs font-semibold uppercase text-center focus:outline-none w-full max-w-sm mx-auto block bg-transparent border-b border-gray-400 px-2 py-0">
                    </div>

                    <!-- Marriage Text -->
                    <div class="text-center mb-3">
                        <p class="text-[11px]">were united in Holy Matrimony according to the</p>
                        <p class="text-sm font-bold mt-1">Rite of the Roman Catholic Church</p>
                        <p class="text-[11px]">And the laws of the country.</p>
                    </div>

                    <!-- Minister and Date -->
                    <div class="text-center mb-3">
                        <p class="text-[10px]">By</p>
                        <input type="text" name="minister_name" value="{{ $record->minister_name }}" 
                               class="text-xs font-semibold uppercase text-center focus:outline-none w-full max-w-sm mx-auto block bg-transparent border-b border-gray-400 px-2 py-0">
                        <p class="text-[10px] mt-1">on</p>
                        <input type="date" name="wedding_date" value="{{ $record->wedding_date ? \Carbon\Carbon::parse($record->wedding_date)->format('Y-m-d') : ($record->year ? $record->year . '-01-01' : '') }}" 
                               class="text-xs text-center focus:outline-none w-auto mx-auto block bg-transparent border-b border-gray-400 px-2 py-0">
                    </div>

                    <!-- Location -->
                    <div class="text-center mb-3">
                        <p class="text-[10px]">at this Parish Church of Holy Cross Parish, Alcala, Pangasinan, Philippines.</p>
                    </div>

                    <!-- Witnesses -->
                    <div class="text-center mb-3">
                        <p class="text-[10px] italic">Being witnesses of the ceremony:</p>
                        <div class="flex justify-center gap-2 mt-1">
                            <input type="text" name="witness_1" value="{{ $record->witness_1 ?? '____________________' }}" 
                                   class="border-b border-gray-400 w-40 text-center bg-transparent text-[10px]">
                            <span>and</span>
                            <input type="text" name="witness_2" value="{{ $record->witness_2 ?? '____________________' }}" 
                                   class="border-b border-gray-400 w-40 text-center bg-transparent text-[10px]">
                        </div>
                    </div>

                    <!-- Issued Date -->
                    <div class="text-center mb-2">
                        <p class="text-[10px]">Issued this <span class="font-bold">{{ now()->format('jS') }}</span> day of <span class="font-bold">{{ now()->format('F, Y') }}</span></p>
                    </div>

                    <!-- Signature and Seal - Compact -->
                    <div class="flex justify-center items-center gap-8 mt-2">
                        <div class="text-center">
                            <div class="mx-auto w-12 h-12 border-2 border-[#8B4513] rounded-full flex items-center justify-center">
                                <span class="text-[6px] text-gray-400 text-center">PARISH SEAL</span>
                            </div>
                            <p class="text-[8px] text-gray-500">Parish Seal</p>
                        </div>
                        <div class="text-center">
                            <input type="text" name="parish_priest" value="REV. FR. ELISAR CHRISTOPHER M. ITCHON" 
                                   class="font-bold border-b border-black text-[9px] text-center bg-transparent w-48 uppercase">
                            <p class="text-[8px] mt-0.5 text-gray-600">Parish Priest</p>
                        </div>
                    </div>

                    <!-- Book Reference - Compact -->
                    <div class="text-center mt-3 text-[9px] text-gray-500 flex justify-center gap-3">
                        <span>Registered in Book No. <input type="text" name="book_number" value="{{ $record->book_number }}" class="w-10 text-center border-b border-gray-300 bg-transparent"></span>
                        <span>Page No. <input type="text" name="page_number" value="{{ $record->page_number }}" class="w-10 text-center border-b border-gray-300 bg-transparent"></span>
                        <span>Line No. <input type="text" name="line_number" value="{{ $record->line_number }}" class="w-10 text-center border-b border-gray-300 bg-transparent"></span>
                    </div>

                    <!-- QR Code -->
                    <div class="mt-2 flex justify-end">
                        <div class="flex flex-col items-center">
                            @php 
                                $qrCodeUrl = "https://quickchart.io/qr?text=" . urlencode(route('records.wedding.show', $record->id)) . "&size=40&margin=0";
                            @endphp
                            <img src="{{ $qrCodeUrl }}" alt="Verify QR" width="35" height="35">
                            <p class="text-[5px] uppercase font-bold mt-0.5 text-gray-400">Verify</p>
                        </div>
                    </div>

                    <!-- Update Button -->
                    <div class="mt-2 flex justify-center no-print">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-1 rounded text-[8px] font-bold uppercase tracking-wider">
                            UPDATE RECORD
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>