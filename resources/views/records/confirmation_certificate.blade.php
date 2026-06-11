<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation Certificate - {{ $record->first_name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Cinzel:wght@400;700&family=Old+Standard+TT:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        @media print {
            @page { size: letter; margin: 0.3in !important; }
            .no-print { display: none !important; }
            body { background: white !important; margin: 0; padding: 0; }
            .certificate-container { 
                margin: 0 !important; 
                box-shadow: none !important; 
                -webkit-print-color-adjust: exact;
            }
            input, select {
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                background: transparent !important;
                font-weight: bold !important;
            }
        }

        .corner-decoration {
            position: absolute;
            width: 60px;
            height: 60px;
            border: 2px solid #C5A059;
            z-index: 10;
        }
        .top-left { top: 12px; left: 12px; border-right: none; border-bottom: none; border-radius: 10px 0 0 0; }
        .top-right { top: 12px; right: 12px; border-left: none; border-bottom: none; border-radius: 0 10px 0 0; }
        .bottom-left { bottom: 12px; left: 12px; border-right: none; border-top: none; border-radius: 0 0 0 10px; }
        .bottom-right { bottom: 12px; right: 12px; border-left: none; border-top: none; border-radius: 0 0 10px 0; }
        
        .inner-border {
            border: 1px solid #C5A059;
            margin: 6px;
            height: calc(100% - 12px);
        }

        .certificate-container {
            border: 8px double #C5A059;
            font-family: 'Old Standard TT', serif;
        }
        
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
<body class="bg-gray-200 min-h-screen antialiased">

    <div class="py-4 print:py-0">
        <!-- Back Button -->
        <div class="max-w-3xl mx-auto mb-3 no-print">
            <a href="{{ route('records.index', ['category' => 'confirmation', 'book_number' => $record->book_number ?? 1]) }}" 
               class="inline-block border border-gray-400 rounded-md px-4 py-1.5 text-xs font-bold uppercase tracking-widest shadow-sm hover:bg-gray-100 transition bg-white">
                ← BACK TO RECORDS
            </a>
        </div>
        
        <form action="{{ route('records.confirmation.update', $record->id) }}" method="POST">
            @csrf 
            @method('PUT')
            
            <div class="certificate-container max-w-3xl mx-auto bg-white p-4 shadow-2xl relative">
                
                <!-- Ornamental Border Elements -->
                <div class="corner-decoration top-left"></div>
                <div class="corner-decoration top-right"></div>
                <div class="corner-decoration bottom-left"></div>
                <div class="corner-decoration bottom-right"></div>

                <div class="inner-border p-6 flex flex-col justify-between relative">
                    
                    <div class="w-full text-center">
                        <!-- Header with Logos -->
                        <div class="flex justify-center items-center gap-3 mb-2">
                            <!-- LEFT: Parish Logo (holylogo.png) -->
                            <div class="w-10 h-10 flex items-center justify-center">
                                <img src="{{ asset('images/holylogo.png') }}" alt="Parish Logo" class="object-contain max-h-full max-w-full rounded-full" onerror="this.style.display='none'">
                            </div>
                            
                            <!-- CENTER: Header Text -->
                            <div>
                                <p class="text-[8px] italic">Roman Catholic Diocese of Urdaneta</p>
                                <h1 class="text-xl font-bold tracking-tighter text-gray-900" style="font-family: 'Cinzel', serif;">HOLY CROSS PARISH</h1>
                                <p class="text-[8px] font-bold">Alcala, Pangasinan 2425</p>
                            </div>
                            
                            <!-- RIGHT: Diocese Logo (dialogo.png) -->
                            <div class="w-10 h-10 flex items-center justify-center">
                                <img src="{{ asset('images/diologo.png') }}" alt="Diocese Logo" class="object-contain max-h-full max-w-full rounded-full" onerror="this.style.display='none'">
                            </div>
                        </div>

                        <h2 class="text-2xl font-bold mt-3 mb-2" style="font-family: 'Playfair Display', serif;">Confirmation Certificate</h2>
                        
                        <h3 class="text-md italic font-bold mb-4">This is to Certify</h3>

                        <!-- Identity Section - Compact -->
                        <div class="max-w-md mx-auto text-left space-y-2 mb-5 text-base">
                            <div class="flex items-baseline">
                                <span class="w-16 text-sm">That</span>
                                <input type="text" name="first_name" value="{{ $record->first_name }}" 
                                       class="flex-1 border-b border-black font-bold px-2 uppercase text-base bg-transparent focus:outline-none">
                                <span> </span>
                                <input type="text" name="last_name" value="{{ $record->last_name }}" 
                                       class="flex-1 border-b border-black font-bold px-2 uppercase text-base bg-transparent focus:outline-none">
                            </div>
                            <div class="flex items-baseline">
                                <span class="w-16 text-sm">Child of</span>
                                <input type="text" name="father_name" value="{{ $record->father_name }}" 
                                       class="flex-1 border-b border-black px-2 uppercase bg-transparent focus:outline-none">
                            </div>
                            <div class="flex items-baseline">
                                <span class="w-16 text-sm">And</span>
                                <input type="text" name="mother_name" value="{{ $record->mother_name }}" 
                                       class="flex-1 border-b border-black px-2 uppercase bg-transparent focus:outline-none">
                            </div>
                        </div>

                        <!-- Sacramental Context - Compact -->
                        <div class="space-y-2 mb-4">
                            <p class="text-sm font-bold tracking-widest">RECEIVED</p>
                            <p class="text-lg font-bold italic" style="font-family: 'Playfair Display', serif;">The Holy Sacrament of Confirmation</p>
                            <p class="text-xs">In the Church of</p>
                            <p class="text-md font-bold border-b border-black inline-block px-3">HOLY CROSS PARISH</p>
                            <p class="text-xs border-b border-black inline-block px-3 block w-max mx-auto">Alcala, Pangasinan</p>
                        </div>

                        <!-- Event Details - Compact -->
                        <div class="space-y-2 max-w-md mx-auto text-base">
                            <div class="flex justify-center items-baseline gap-2">
                                <span class="text-sm">On</span>
                                <input type="date" name="confirmation_date" value="{{ $record->confirmation_date ? \Carbon\Carbon::parse($record->confirmation_date)->format('Y-m-d') : ($record->year && $record->month_day ? \Carbon\Carbon::parse($record->month_day . ' ' . $record->year)->format('Y-m-d') : '') }}" 
                                       class="border-b border-black px-3 font-bold uppercase bg-transparent focus:outline-none text-sm">
                            </div>
                            <div class="flex justify-center items-baseline gap-2">
                                <span class="text-sm">By the</span>
                                <input type="text" name="minister_name" value="{{ $record->minister_name }}" 
                                       class="border-b border-black px-3 font-bold uppercase bg-transparent focus:outline-none w-48 text-sm">
                            </div>
                            <div class="text-center mt-1">
                                <span class="italic text-sm">Sponsor was:</span>
                                <input type="text" name="sponsors" value="{{ $record->sponsors ?? $record->sponsor_name }}" 
                                       class="border-b border-black px-3 uppercase font-bold bg-transparent focus:outline-none w-48 text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Footer Section - Compact -->
                    <div class="w-full mt-4">
                        <div class="flex justify-between items-end mb-4">
                            <!-- Archive Reference -->
                            <div class="text-[8px] leading-relaxed">
                                <p class="font-bold italic uppercase">Certified True Copy:</p>
                                <p>Page No. <input type="text" name="page_number" value="{{ $record->page_number }}" 
                                       class="font-bold underline w-10 text-center bg-transparent border-b border-gray-400 focus:outline-none"></p>
                                <p>Book No. <input type="text" name="book_number" value="{{ $record->book_number }}" 
                                       class="font-bold underline w-10 text-center bg-transparent border-b border-gray-400 focus:outline-none"></p>
                                <p>Line No. <input type="text" name="line_number" value="{{ $record->line_number }}" 
                                       class="font-bold underline w-10 text-center bg-transparent border-b border-gray-400 focus:outline-none"></p>
                                <p class="mt-2 italic text-[7px]">Parish Seal</p>
                            </div>

                            <!-- Date and QR -->
                            <div class="flex flex-col items-end">
                                <p class="text-sm mb-2">Date: <span class="border-b border-black px-3 font-bold">{{ now()->format('F d, Y') }}</span></p>
                                <div class="flex flex-col items-center">
                                    @php
                                        $url = route('records.confirmation.show', $record->id);
                                        $qrCodeUrl = "https://quickchart.io/qr?text=" . urlencode($url) . "&size=55&margin=0";
                                    @endphp
                                    <img src="{{ $qrCodeUrl }}" alt="Verify" class="border p-0.5 bg-white w-12 h-12 shadow-sm">
                                    <p class="text-[6px] uppercase font-black mt-0.5 text-gray-400">Verify</p>
                                </div>
                            </div>
                        </div>

                        <!-- Priest Signature - Compact -->
                        <div class="text-center">
                            <div class="inline-block border-b border-black px-6">
                                <input type="text" name="minister_signature" value="{{ $record->minister_name ?? 'REV. FR. ELISAR CHRISTOPHER M. ITCHON' }}" 
                                       class="text-sm font-bold uppercase text-center bg-transparent border-none focus:outline-none w-80">
                            </div>
                            <p class="italic text-[10px] mt-0.5 text-gray-700">Parish Priest</p>
                        </div>

                        <!-- Remarks -->
                        <div class="mt-3 flex items-baseline justify-center">
                            <p class="text-[7px] uppercase font-bold text-gray-600 shrink-0">REMARKS:</p>
                            <span class="text-[7px] italic">"</span>
                            <input type="text" name="remarks" value="{{ $record->remarks ?? '' }}" 
                                   class="text-[7px] italic font-serif border-b border-gray-300 w-64 bg-transparent px-1 focus:outline-none">
                            <span class="text-[7px] italic">"</span>
                        </div>

                        <!-- Update and Print Buttons -->
                        <div class="mt-4 flex justify-center gap-3 no-print">
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-1.5 rounded text-[9px] font-black uppercase tracking-widest transition shadow-lg">
                                UPDATE
                            </button>
                            <button type="button" onclick="window.print()" class="bg-[#1a202c] hover:bg-black text-white px-6 py-1.5 rounded text-[9px] font-black uppercase tracking-widest transition shadow-lg">
                                PRINT
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>