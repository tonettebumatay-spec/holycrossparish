<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baptism Certificate - {{ $record->first_name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <style>
        @media print {
            @page { size: letter; margin: 0 !important; }
            .no-print { display: none !important; }
            body { background: white !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .certificate-container { margin: 0 !important; box-shadow: none !important; height: 100vh !important; width: 100vw !important; page-break-after: avoid !important; page-break-inside: avoid !important; }
        }
        .corner-decoration { position: absolute; width: 70px; height: 70px; z-index: 10; }
        .top-left { top: 10px; left: 10px; border-left: 2px double #C5A059; border-top: 2px double #C5A059; }
        .top-right { top: 10px; right: 10px; border-right: 2px double #C5A059; border-top: 2px double #C5A059; }
        .bottom-left { bottom: 10px; left: 10px; border-left: 2px double #C5A059; border-bottom: 2px double #C5A059; }
        .bottom-right { bottom: 10px; right: 10px; border-right: 2px double #C5A059; border-bottom: 2px double #C5A059; }
        .inner-border { border: 1px solid #C5A059; margin: 4px; height: calc(100% - 8px); }
        
        input, select {
            transition: all 0.2s ease;
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
<body class="bg-gray-200 min-h-screen antialiased flex flex-col justify-between">

    <div class="py-2 print:py-0 w-full my-auto">
        <!-- Back Button -->
        <div class="max-w-4xl mx-auto mb-4 no-print">
            <div class="flex justify-between items-center">
                <a href="{{ route('records.index', ['category' => 'baptism', 'book_number' => $record->book_number ?? 1]) }}" 
                   class="border border-gray-400 rounded-md px-5 py-2 text-xs font-bold uppercase tracking-widest shadow-sm hover:bg-gray-100 transition bg-white">
                    ← BACK TO RECORDS
                </a>
                <button type="button" onclick="window.print()" class="bg-[#431407] text-white px-6 py-2 rounded-full font-black text-xs tracking-widest hover:bg-[#7c2d12] transition uppercase shadow-lg no-print">
                    PRINT CERTIFICATE
                </button>
            </div>
        </div>
        
        <div class="certificate-container max-w-4xl mx-auto bg-white p-4 shadow-2xl relative border-[10px] border-double border-[#C5A059] flex flex-col justify-between" style="height: 980px; max-height: 98vh;">
            
            <div class="corner-decoration top-left"></div>
            <div class="corner-decoration top-right"></div>
            <div class="corner-decoration bottom-left"></div>
            <div class="corner-decoration bottom-right"></div>

            <div class="inner-border p-6 flex flex-col justify-between relative">
                
                <div class="w-full">
                    <div class="flex justify-between items-center px-6 mb-2">
                        <!-- LEFT: Parish Logo (holylogo.png) -->
                        <div class="w-16 h-16 flex items-center justify-center">
                            <img src="{{ asset('images/holylogo.png') }}" alt="Parish Logo" class="object-contain max-h-full max-w-full rounded-full">
                        </div>
                        
                        <!-- CENTER: Header Text -->
                        <div class="text-center">
                            <p class="text-[9px] italic font-serif text-gray-700 tracking-wider">Roman Catholic Diocese of Urdaneta</p>
                            <h1 class="text-2xl font-bold tracking-widest text-gray-900 my-0.5" style="font-family: 'Cinzel', serif;">HOLY CROSS PARISH</h1>
                            <p class="text-[9px] tracking-wide text-gray-600 font-serif">Alcala, Pangasinan 2425</p>
                        </div>
                        
                        <!-- RIGHT: Diocese Logo (dialogo.png) -->
                        <div class="w-16 h-16 flex items-center justify-center">
                            <img src="{{ asset('images/diologo.png') }}" alt="Diocese Logo" class="object-contain max-h-full max-w-full rounded-full">
                        </div>
                    </div>

                    <div class="text-center mb-4">
                        <h2 class="text-3xl text-gray-800 font-normal my-1" style="font-family: 'Playfair Display', Georgia, serif; font-style: italic;">Certificate of Baptism</h2>
                        <p class="text-[11px] text-gray-600 font-serif">This is to certify that</p>
                    </div>

                    <form action="{{ route('records.baptism.update', $record->id) }}" method="POST" class="space-y-4">
                        @csrf @method('PUT')

                        <div class="text-center mb-2 max-w-xl mx-auto border-b border-black pb-0">
                            <input type="text" name="name" value="{{ $record->first_name }} {{ $record->middle_name }} {{ $record->last_name }}" 
                                   class="text-xl font-bold text-center focus:outline-none w-full bg-transparent uppercase tracking-wider text-gray-900 font-serif border-none p-0 pb-0.5 leading-none focus:ring-0">
                        </div>

                        <p class="text-center text-xs mb-4 font-serif text-gray-800 italic font-semibold leading-tight">
                            was Solemnly Baptized According to the<br>
                            <span class="text-sm font-bold not-italic tracking-wide text-gray-900">Rite of the Roman Catholic Church</span>
                        </p>

                        <div class="max-w-md mx-auto space-y-3.5 text-xs font-serif px-2">
                            @foreach([
                                'Father\'s Name' => 'father_name',
                                'Mother\'s Name' => 'mother_maiden_name',
                                'Date of Birth' => 'birth_date',
                                'Place of Birth' => 'birth_place',
                                'Date of Baptism' => 'baptism_date',
                                'Name of Minister' => 'minister_name'
                            ] as $label => $field)
                            <div class="flex items-baseline">
                                <span class="w-32 text-gray-700 font-medium shrink-0">{{ $label }}:</span>
                                <input type="text" name="{{ $field }}" value="{{ ($field == 'birth_date' || $field == 'baptism_date') ? \Carbon\Carbon::parse($record->$field)->format('F d, Y') : $record->$field }}" 
                                       class="flex-1 border-b border-gray-400 border-t-0 border-l-0 border-r-0 focus:outline-none focus:ring-0 uppercase px-1 py-0 m-0 bg-transparent text-xs text-gray-900 font-sans tracking-wide leading-none">
                            </div>
                            @endforeach
                            
                            <div class="flex items-start">
                                <span class="w-32 text-gray-700 font-medium pt-0.5 shrink-0">Godparents:</span>
                                <div class="flex-1 flex flex-col space-y-3.5">
                                    <input type="text" name="godfather" value="{{ $record->godfather }}" class="w-full border-b border-gray-400 border-t-0 border-l-0 border-r-0 focus:outline-none focus:ring-0 uppercase py-0 m-0 bg-transparent text-xs text-gray-900 font-sans tracking-wide leading-none">
                                    <input type="text" name="godmother" value="{{ $record->godmother }}" class="w-full border-b border-gray-400 border-t-0 border-l-0 border-r-0 focus:outline-none focus:ring-0 uppercase py-0 m-0 bg-transparent text-xs text-gray-900 font-sans tracking-wide leading-none">
                                </div>
                            </div>
                        </div>
                </div>

                <div class="w-full mt-auto">
                    <div class="text-center text-[11px] font-serif text-gray-800 space-y-0.5">
                        <p>These data are taken from the Canonical Book of Baptism,</p>
                        <p class="font-semibold">
                            Book No. <span class="border-b border-black font-bold px-2 mx-0.5">{{ $record->book_number }}</span> 
                            Page No. <span class="border-b border-black font-bold px-2 mx-0.5">{{ $record->page_number }}</span> 
                            Line No. <span class="border-b border-black font-bold px-2 mx-0.5">{{ $record->line_number }}</span>
                        </p>
                        <p class="pt-0.5">Issued on <span class="border-b border-black font-bold px-2 mx-0.5">{{ now()->format('jS') }}</span> day of <span class="border-b border-black font-bold px-2 mx-0.5">{{ now()->format('F, Y') }}</span></p>
                    </div>

                    <div class="mt-8 text-center flex flex-col items-center">
                        <div class="relative w-[400px]"> 
                            <input type="text" name="parish_priest" value="REV. FR. ELISAR CHRISTOPHER M. ITCHON" 
                                   class="font-sans font-bold border-b border-black pb-0.5 uppercase text-xs text-center focus:outline-none focus:ring-0 bg-transparent w-full text-gray-900 tracking-wide border-t-0 border-l-0 border-r-0 leading-none">
                            <p class="text-[10px] mt-0.5 font-sans font-medium text-gray-600">Parish Priest</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between items-end px-2">
                        <div class="flex-1 flex items-baseline space-x-0.5">
                            <p class="text-[9px] uppercase font-bold tracking-wider text-gray-800 shrink-0">REMARKS:</p>
                            <span class="text-[9px] italic font-serif text-gray-900">"</span>
                            <input type="text" name="remarks" value="{{ $record->remarks ?? 'for GENERAL purposes' }}" 
                                   class="text-[10px] italic font-serif border-b border-gray-300 border-t-0 border-l-0 border-r-0 focus:outline-none focus:ring-0 bg-transparent px-1 w-1/2 text-gray-900 font-medium py-0 m-0 leading-none">
                            <span class="text-[9px] italic font-serif text-gray-900">"</span>
                        </div>
                        
                        <div class="flex flex-col items-center mb-1">
                            @php 
                                $qrCodeUrl = "https://quickchart.io/qr?text=" . urlencode(route('records.baptism.show', $record->id)) . "&size=65&margin=0";
                            @endphp
                            <div class="border border-[#C5A059]/40 p-0.5 bg-white shadow-sm rounded">
                                <img src="{{ $qrCodeUrl }}" alt="Verify QR" class="w-12 h-12">
                            </div>
                            <p class="text-[7px] uppercase font-sans font-bold mt-0.5 tracking-wider text-gray-400">Verify Record</p>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-center gap-3 no-print pb-0">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1 rounded text-[10px] font-bold shadow-md uppercase tracking-wider transition-colors duration-200">Update Record</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>