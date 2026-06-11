<x-app-layout>
    <style>
        /* Hide User Settings & Logout Section */
        .hidden.sm\:flex.sm\:items-center.sm\:ml-6 {
            display: none !important;
        }
        
        header {
            display: none !important;
        }
        
        .py-12:first-of-type {
            padding-top: 1rem !important;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            .py-12 {
                padding: 0 !important;
            }
            .max-w-4xl {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            #certificate-content {
                border: 2px solid #8B4513 !important;
                box-shadow: none !important;
                margin: 0 !important;
            }
            input, select {
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                background: transparent !important;
                font-weight: bold !important;
            }
            @page {
                size: letter;
                margin: 0.3in;
            }
        }
        
        .certificate-container {
            position: relative;
        }
        
        .corner-decoration {
            position: absolute;
            width: 50px;
            height: 50px;
            z-index: 10;
        }
        
        .top-left {
            top: 8px;
            left: 8px;
            border-left: 2px double #C5A059;
            border-top: 2px double #C5A059;
        }
        
        .top-right {
            top: 8px;
            right: 8px;
            border-right: 2px double #C5A059;
            border-top: 2px double #C5A059;
        }
        
        .bottom-left {
            bottom: 8px;
            left: 8px;
            border-left: 2px double #C5A059;
            border-bottom: 2px double #C5A059;
        }
        
        .bottom-right {
            bottom: 8px;
            right: 8px;
            border-right: 2px double #C5A059;
            border-bottom: 2px double #C5A059;
        }
        
        .inner-border {
            border: 1px solid #C5A059;
            margin: 3px;
        }
        
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
    
    <div class="py-8 bg-white min-h-screen">
        <div class="max-w-3xl mx-auto px-4">
            
            <!-- Action Buttons - Hidden when printing -->
            <div class="flex justify-between items-center mb-6 no-print">
                <a href="{{ route('records.index', ['category' => 'funeral', 'book_number' => $record->book_number ?? 1]) }}" 
                   class="border border-gray-400 rounded-md px-4 py-1.5 text-xs font-bold uppercase tracking-widest shadow-sm hover:bg-gray-100 transition bg-white">
                    ← BACK
                </a>
                
                <div class="flex gap-3">
                    <button onclick="window.print()" 
                            class="bg-[#431407] text-white px-6 py-1.5 rounded-full font-black text-xs tracking-widest hover:bg-[#7c2d12] transition uppercase shadow-lg">
                        PRINT
                    </button>
                </div>
            </div>

            <!-- Funeral Certificate - Single Coupon -->
            <form action="{{ route('records.funeral.update', $record->id) }}" method="POST">
                @csrf 
                @method('PUT')
                
                <div class="certificate-container relative border-[8px] border-double border-[#C5A059] bg-white" id="certificate-content">
                    
                    <div class="corner-decoration top-left"></div>
                    <div class="corner-decoration top-right"></div>
                    <div class="corner-decoration bottom-left"></div>
                    <div class="corner-decoration bottom-right"></div>

                    <div class="inner-border">
                        <div class="p-5">
                            
                            <!-- Header with Logos -->
                            <div class="w-full">
                                <div class="flex justify-between items-center px-2 mb-2">
                                    <!-- LEFT: Parish Logo (holylogo.png) -->
                                    <div class="w-12 h-12 flex items-center justify-center">
                                        <img src="{{ asset('images/holylogo.png') }}" alt="Parish Logo" class="object-contain max-h-full max-w-full rounded-full" onerror="this.src='/baprec.png'">
                                    </div>
                                    
                                    <!-- CENTER: Header Text -->
                                    <div class="text-center">
                                        <p class="text-[8px] italic font-serif text-gray-600 tracking-wider">Roman Catholic Diocese of Urdaneta</p>
                                        <h1 class="text-lg font-bold tracking-widest text-gray-900" style="font-family: 'Cinzel', serif;">HOLY CROSS PARISH</h1>
                                        <p class="text-[8px] text-gray-500">Alcala, Pangasinan 2425</p>
                                    </div>
                                    
                                    <!-- RIGHT: Diocese Logo (dialogo.png) -->
                                    <div class="w-12 h-12 flex items-center justify-center">
                                        <img src="{{ asset('images/diologo.png') }}" alt="Diocese Logo" class="object-contain max-h-full max-w-full rounded-full" onerror="this.style.display='none'">
                                    </div>
                                </div>
                            </div>

                            <!-- Title -->
                            <div class="text-center mb-3">
                                <h2 class="text-md font-bold uppercase tracking-wider border-b border-[#8B4513] inline-block pb-0.5 px-4">
                                    CERTIFICATE OF FUNERAL
                                </h2>
                            </div>

                            <!-- Certificate Body - Compact -->
                            <div class="space-y-2 text-justify font-serif text-xs leading-relaxed">
                                <p class="font-semibold">This is to certify that:</p>
                                
                                <!-- Deceased Name -->
                                <div class="text-center my-2">
                                    <input type="text" name="deceased_name" value="{{ strtoupper($record->deceased_name ?? '________________________') }}" 
                                           class="text-base font-bold uppercase text-center focus:outline-none w-full max-w-sm mx-auto block bg-transparent border-b border-[#8B4513] px-2 py-0.5">
                                </div>
                                
                                <p>
                                    a resident of barrio 
                                    <input type="text" name="barrio" value="{{ $record->barrio ?? $record->residence_barrio ?? '_________________' }}" 
                                           class="font-semibold border-b border-gray-400 inline-block w-[100px] text-center bg-transparent px-1">
                                    , Municipality of 
                                    <input type="text" name="municipality" value="{{ $record->municipality ?? '_____________________' }}" 
                                           class="font-semibold border-b border-gray-400 inline-block w-[80px] text-center bg-transparent px-1">
                                    , Province of 
                                    <input type="text" name="province" value="{{ $record->province ?? 'Pangasinan' }}" 
                                           class="font-semibold border-b border-gray-400 inline-block w-[90px] text-center bg-transparent px-1">
                                    , 
                                    <input type="text" name="civil_status" value="{{ $record->civil_status ?? '_____________________' }}" 
                                           class="font-semibold border-b border-gray-400 inline-block w-[70px] text-center bg-transparent px-1">
                                    , 
                                    <input type="text" name="relationship_note" value="{{ $record->relationship_note ?? '____________________-' }}" 
                                           class="font-semibold border-b border-gray-400 inline-block w-[65px] text-center bg-transparent px-1">
                                    of 
                                    <input type="text" name="spouse_name" value="{{ $record->spouse_name ?? '____________________-' }}" 
                                           class="font-semibold border-b border-gray-400 inline-block w-[80px] text-center bg-transparent px-1">
                                    , died on the 
                                    <input type="text" name="death_day" value="{{ $record->death_date ? \Carbon\Carbon::parse($record->death_date)->format('jS') : '____' }}" 
                                           class="font-semibold border-b border-gray-400 inline-block w-[40px] text-center bg-transparent px-1">
                                    day of 
                                    <input type="text" name="death_month" value="{{ $record->death_date ? \Carbon\Carbon::parse($record->death_date)->format('F') : '__________' }}" 
                                           class="font-semibold border-b border-gray-400 inline-block w-[80px] text-center bg-transparent px-1">
                                    , 
                                    <input type="text" name="death_year" value="{{ $record->death_date ? \Carbon\Carbon::parse($record->death_date)->format('Y') : '____' }}" 
                                           class="font-semibold border-b border-gray-400 inline-block w-[50px] text-center bg-transparent px-1">
                                    , at the age 
                                    <input type="text" name="age" value="{{ $record->age ?? '____' }}" 
                                           class="font-semibold border-b border-gray-400 inline-block w-[40px] text-center bg-transparent px-1">
                                    years old and was buried in the Roman Catholic Cemetery 
                                    <input type="text" name="burial_place" value="{{ $record->burial_place ?? 'Municipal Cemetery of Alcala' }}" 
                                           class="font-semibold border-b border-gray-400 inline-block w-[180px] text-center bg-transparent px-1">
                                    on the 
                                    <input type="text" name="burial_day" value="{{ $record->burial_date ? \Carbon\Carbon::parse($record->burial_date)->format('jS') : '____' }}" 
                                           class="font-semibold border-b border-gray-400 inline-block w-[40px] text-center bg-transparent px-1">
                                    day of 
                                    <input type="text" name="burial_month" value="{{ $record->burial_date ? \Carbon\Carbon::parse($record->burial_date)->format('F') : '__________' }}" 
                                           class="font-semibold border-b border-gray-400 inline-block w-[80px] text-center bg-transparent px-1">
                                    , 
                                    <input type="text" name="burial_year" value="{{ $record->burial_date ? \Carbon\Carbon::parse($record->burial_date)->format('Y') : '____' }}" 
                                           class="font-semibold border-b border-gray-400 inline-block w-[50px] text-center bg-transparent px-1">
                                    .
                                </p>
                                
                                <p>
                                    The cause of death was 
                                    <input type="text" name="cause_of_death" value="{{ $record->cause_of_death ?? '_______________________________________________________________________________________________--' }}" 
                                           class="font-semibold border-b border-gray-400 inline-block w-[180px] text-center bg-transparent px-1">
                                    .
                                    <select name="sacrament_before_death" class="border-b border-gray-400 bg-transparent font-semibold text-xs ml-1">
                                        <option value="1" {{ $record->sacrament_before_death ? 'selected' : '' }}>Received Sacraments</option>
                                        <option value="0" {{ !$record->sacrament_before_death ? 'selected' : '' }}>Not able to receive Sacraments</option>
                                    </select>
                                </p>
                                
                                <p>
                                    This is a true copy of the original record as it appears in the Register of Death, 
                                    Book No. 
                                    <input type="text" name="book_number" value="{{ $record->book_number ?? '___' }}" 
                                           class="font-semibold border-b border-gray-400 inline-block w-[40px] text-center bg-transparent px-1">
                                    Folio No. 
                                    <input type="text" name="page_number" value="{{ $record->page_number ?? '___' }}" 
                                           class="font-semibold border-b border-gray-400 inline-block w-[40px] text-center bg-transparent px-1">
                                    .
                                </p>
                                
                                <p>
                                    In witness whereof I affix my signature and place the seal of Holy Cross Parish, 
                                    Alcala, Pangasinan on the 
                                    <span class="font-semibold">{{ now()->format('jS') }}</span> Day of 
                                    <span class="font-semibold">{{ now()->format('F') }}</span>, 
                                    <span class="font-semibold">{{ now()->format('Y') }}</span>.
                                </p>
                            </div>

                            <!-- Signature -->
                            <div class="mt-4 text-center">
                                <input type="text" name="minister_name" value="{{ $record->minister_name ?? '_______________________________' }}" 
                                       class="font-bold text-xs text-center focus:outline-none bg-transparent border-b border-gray-400 w-64 uppercase">
                                <p class="text-[8px] text-gray-500 mt-0.5">Parish Priest</p>
                            </div>

                            <!-- Remarks -->
                            <div class="mt-3 flex items-baseline">
                                <p class="text-[8px] uppercase font-bold text-gray-600 shrink-0">REMARKS:</p>
                                <span class="text-[8px] italic">"</span>
                                <input type="text" name="remarks" value="{{ $record->remarks ?? '' }}" 
                                       class="text-[8px] italic font-serif border-b border-gray-300 flex-1 bg-transparent px-1">
                                <span class="text-[8px] italic">"</span>
                            </div>

                            <!-- QR Code at Bottom Right -->
                            <div class="mt-3 flex justify-end">
                                <div class="flex flex-col items-center">
                                    @php 
                                        $qrCodeUrl = "https://quickchart.io/qr?text=" . urlencode(route('records.funeral.show', $record->id)) . "&size=45&margin=0";
                                    @endphp
                                    <img src="{{ $qrCodeUrl }}" alt="Verify QR" width="40" height="40">
                                    <p class="text-[6px] uppercase font-bold mt-0.5 text-gray-400">Scan to Verify</p>
                                </div>
                            </div>

                            <!-- Update Button -->
                            <div class="mt-3 text-center no-print">
                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-1 rounded text-[9px] font-bold shadow-md uppercase tracking-wider">
                                    UPDATE RECORD
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>