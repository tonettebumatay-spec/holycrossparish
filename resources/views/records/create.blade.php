@extends('layouts.index')

@section('content')
<div class="w-full min-h-screen bg-[#f8fafc] py-12 px-4 flex flex-col items-center justify-start font-sans">
    
    <div class="w-full max-w-4xl bg-white rounded-lg shadow-[0_10px_30px_rgba(0,0,0,0.08)] border border-gray-100 p-6 md:p-10 space-y-8 mt-4">
        
        <!-- BACK BUTTON -->
        <div class="mb-4">
            <a href="{{ route('records.index', ['category' => request('category'), 'book_number' => request('book_number')]) }}" 
               class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 text-xs font-bold uppercase tracking-wider transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                BACK TO RECORDS
            </a>
        </div>
        
        @if(strtolower(request('category')) === 'baptism')
            
            <div class="w-full bg-[#242b35] text-black text-center py-3.5 rounded-sm uppercase font-bold text-sm tracking-[0.2em] shadow-sm">
                ADD BAPTISMAL RECORD
            </div>

            <form action="{{ route('records.store') }}" method="POST" class="space-y-8">

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-800 rounded px-4 py-3">
                        <div class="font-bold text-sm mb-2">Please fix the following:</div>
                        <ul class="list-disc pl-5 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @csrf
                <input type="hidden" name="category" value="Baptism">
                <input type="hidden" name="book_number" value="{{ request('book_number') }}">

                <!-- I. REGISTRY REFERENCES -->
                <div class="bg-gray-50 p-5 rounded-sm border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Book Number</label>
                            <input type="text" value="{{ request('book_number') }}" readonly class="text-sm w-full bg-gray-100 border border-gray-300 rounded px-3 py-2.5 text-gray-600">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Page Number</label>
                            <input type="number" name="page_number" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Line Number</label>
                            <input type="number" name="line_number" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                    </div>
                </div>

               <!-- I. BAPTISMAL INFORMATION -->
                <div>
                    <h3 class="text-md font-bold text-gray-800 border-l-4 border-[#6c4b3e] pl-3 mb-5 uppercase tracking-wide">I. BAPTISMAL INFORMATION</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Date of Baptism</label>
                            <input type="date" name="baptism_date" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Full Name of Child</label>
                            <input type="text" name="candidate_name" placeholder="First Name M.I. Surname" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 placeholder-gray-400 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- II. BIRTH DETAILS -->
                <div>
                    <h3 class="text-md font-bold text-gray-800 border-l-4 border-[#6c4b3e] pl-3 mb-5 uppercase tracking-wide">II. BIRTH DETAILS</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Date of Birth</label>
                            <input type="date" name="birth_date" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Place of Birth</label>
                            <input type="text" name="birth_place" placeholder="City/Municipality, Province" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 placeholder-gray-400 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- III. PARENTS' INFORMATION -->
                <div>
                    <h3 class="text-md font-bold text-gray-800 border-l-4 border-[#6c4b3e] pl-3 mb-5 uppercase tracking-wide">III. PARENTS' INFORMATION</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Father's Name & Surname</label>
                            <input type="text" name="father_name" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Father's Birthplace</label>
                            <input type="text" name="father_birthplace" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Mother's Name & Surname</label>
                            <input type="text" name="mother_name" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Mother's Birthplace</label>
                            <input type="text" name="mother_birthplace" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Legitimacy</label>
                            <select name="legitimacy" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                                <option value="Legitimate">Legitimate (Leg.)</option>
                                <option value="Illegitimate">Illegitimate (Illeg.)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- IV. SPONSORS & MINISTER -->
                <div>
                    <h3 class="text-md font-bold text-gray-800 border-l-4 border-[#6c4b3e] pl-3 mb-5 uppercase tracking-wide">IV. SPONSORS & MINISTER</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Godfather (Name & Residence)</label>
                            <input type="text" name="godfather" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Godmother (Name & Residence)</label>
                            <input type="text" name="godmother" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Name of Minister</label>
                            <input type="text" name="minister_name" placeholder="Rev. Fr. Name of Priest" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 placeholder-gray-400 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Remarks</label>
                            <textarea name="remarks" rows="3" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 resize-none transition-all" placeholder="Additional remarks..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex flex-col items-center justify-center space-y-4 border-t border-gray-200">
                    <button type="submit" class="w-full md:w-auto bg-[#242b35] hover:bg-slate-800 text-black text-xs font-bold tracking-[0.2em] uppercase px-12 py-3.5 rounded shadow-sm transition-all duration-200">
                        SAVE RECORD TO BAPTISM BOOK
                    </button>
                    <a href="{{ route('records.index', ['category' => request('category'), 'book_number' => request('book_number')]) }}" class="text-xs text-gray-500 hover:text-gray-700 underline tracking-wider font-medium transition-colors">
                        Cancel Registration
                    </a>
                </div>
            </form>

        @elseif(strtolower(request('category')) === 'communion')

            <div class="w-full bg-[#242b35] text-black text-center py-3.5 rounded-sm uppercase font-bold text-sm tracking-[0.2em] shadow-sm">
                ADD HOLY COMMUNION RECORD
            </div>

            <form action="{{ route('records.store') }}" method="POST" class="space-y-8">
                @csrf
                <input type="hidden" name="category" value="Communion">
                <input type="hidden" name="book_number" value="{{ request('book_number', 1) }}">

                <!-- I. REGISTRY REFERENCES -->
                <div class="bg-gray-50 p-5 rounded-sm border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Book Number</label>
                            <input type="text" value="{{ request('book_number', 1) }}" readonly class="text-sm w-full bg-gray-100 border border-gray-300 rounded px-3 py-2.5 text-gray-600">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Page Number</label>
                            <input type="number" name="page_number" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Line Number</label>
                            <input type="number" name="line_number" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- I. COMMUNICANT INFORMATION -->
                <div>
                    <h3 class="text-md font-bold text-gray-800 border-l-4 border-[#6c4b3e] pl-3 mb-5 uppercase tracking-wide">I. COMMUNICANT INFORMATION</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">First Name</label>
                            <input type="text" name="first_name" required placeholder="First Name" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Last Name</label>
                            <input type="text" name="last_name" required placeholder="Last Name / Surname" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Residence (Address)</label>
                            <input type="text" name="residence" placeholder="Street, Barangay, City/Municipality" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- II. SACRAMENTAL DETAILS -->
                <div>
                    <h3 class="text-md font-bold text-gray-800 border-l-4 border-[#6c4b3e] pl-3 mb-5 uppercase tracking-wide">II. SACRAMENTAL DETAILS</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Date of First Communion</label>
                            <input type="date" name="communion_date" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Name of Minister</label>
                            <input type="text" name="minister_name" placeholder="Rev. Fr. Name of Priest" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 placeholder-gray-400 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Date of Baptism</label>
                            <input type="date" name="baptism_date" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Place of Baptism</label>
                            <input type="text" name="place_of_baptism" placeholder="Parish Church, Town/City" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex flex-col items-center justify-center space-y-4 border-t border-gray-200">
                    <button type="submit" class="w-full md:w-auto bg-[#242b35] hover:bg-slate-800 text-black text-xs font-bold tracking-[0.2em] uppercase px-12 py-3.5 rounded shadow-sm transition-all duration-200">
                        SAVE RECORD TO COMMUNION BOOK
                    </button>
                    <a href="{{ route('records.index', ['category' => request('category'), 'book_number' => request('book_number')]) }}" class="text-xs text-gray-500 hover:text-gray-700 underline tracking-wider font-medium transition-colors">
                        Cancel Registration
                    </a>
                </div>
            </form>

        @elseif(strtolower(request('category')) === 'confirmation')

            <div class="w-full bg-[#242b35] text-black text-center py-3.5 rounded-sm uppercase font-bold text-sm tracking-[0.2em] shadow-sm">
                ADD CONFIRMATION RECORD
            </div>

            <form action="{{ route('records.store') }}" method="POST" class="space-y-8">
                @csrf
                <input type="hidden" name="category" value="Confirmation">
                <input type="hidden" name="book_number" value="{{ request('book_number', 1) }}">

                <!-- I. REGISTRY REFERENCES -->
                <div class="bg-gray-50 p-5 rounded-sm border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Book Number</label>
                            <input type="text" value="{{ request('book_number', 1) }}" readonly class="text-sm w-full bg-gray-100 border border-gray-300 rounded px-3 py-2.5 text-gray-600">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Page Number</label>
                            <input type="number" name="page_number" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Line Number</label>
                            <input type="number" name="line_number" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Year</label>
                            <input type="text" name="year" placeholder="e.g. 2026" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Month & Day</label>
                            <input type="text" name="month_day" placeholder="e.g. May 21" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- I. CONFIRMAND INFORMATION -->
                <div>
                    <h3 class="text-md font-bold text-gray-800 border-l-4 border-[#6c4b3e] pl-3 mb-5 uppercase tracking-wide">I. CONFIRMAND INFORMATION</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">First Name</label>
                            <input type="text" name="first_name" required placeholder="First Name" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Last Name</label>
                            <input type="text" name="last_name" required placeholder="Last Name / Surname" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Age</label>
                            <input type="number" name="age" required placeholder="Age" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Birthplace</label>
                            <input type="text" name="birthplace" placeholder="City/Municipality, Province" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- II. PARENTS' INFORMATION -->
                <div>
                    <h3 class="text-md font-bold text-gray-800 border-l-4 border-[#6c4b3e] pl-3 mb-5 uppercase tracking-wide">II. PARENTS' INFORMATION</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Father's Full Name</label>
                            <input type="text" name="father_name" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Mother's Full Name (Maiden)</label>
                            <input type="text" name="mother_name" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Parents' Residence</label>
                            <input type="text" name="parents_residence" placeholder="Street, Barangay, City/Municipality" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- III. CHURCH DETAILS -->
                <div>
                    <h3 class="text-md font-bold text-gray-800 border-l-4 border-[#6c4b3e] pl-3 mb-5 uppercase tracking-wide">III. CHURCH DETAILS</h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Name of Minister (Bishop / Priest)</label>
                            <input type="text" name="minister_name" placeholder="Most Rev. / Rev. Fr. Name" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Sponsor Name</label>
                            <input type="text" name="sponsor_name" placeholder="Name of Sponsor / Godparent" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex flex-col items-center justify-center space-y-4 border-t border-gray-200">
                    <button type="submit" class="w-full md:w-auto bg-[#242b35] hover:bg-slate-800 text-black text-xs font-bold tracking-[0.2em] uppercase px-12 py-3.5 rounded shadow-sm transition-all duration-200">
                        SAVE RECORD TO CONFIRMATION BOOK
                    </button>
                    <a href="{{ route('records.index', ['category' => request('category'), 'book_number' => request('book_number')]) }}" class="text-xs text-gray-500 hover:text-gray-700 underline tracking-wider font-medium transition-colors">
                        Cancel Registration
                    </a>
                </div>
            </form>

        @elseif(strtolower(request('category')) === 'wedding')

            <div class="w-full bg-[#242b35] text-black text-center py-3.5 rounded-sm uppercase font-bold text-sm tracking-[0.2em] shadow-sm">
                ADD WEDDING RECORD
            </div>

            <form action="{{ route('records.store') }}" method="POST" class="space-y-8">
                @csrf
                <input type="hidden" name="category" value="Wedding">
                <input type="hidden" name="book_number" value="{{ request('book_number', 1) }}">

                <!-- I. REGISTRY REFERENCES -->
                <div class="bg-gray-50 p-5 rounded-sm border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Book Number</label>
                            <input type="text" value="{{ request('book_number', 1) }}" readonly class="text-sm w-full bg-gray-100 border border-gray-300 rounded px-3 py-2.5 text-gray-600">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Page Number</label>
                            <input type="number" name="page_number" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Line Number</label>
                            <input type="number" name="line_number" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Year</label>
                            <input type="text" name="year" placeholder="e.g. 2026" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Month & Day</label>
                            <input type="text" name="month_day" placeholder="e.g. May 21" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- I. GROOM DETAILS -->
                <div>
                    <h3 class="text-md font-bold text-gray-800 border-l-4 border-[#6c4b3e] pl-3 mb-5 uppercase tracking-wide">I. GROOM DETAILS</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Full Name of Groom</label>
                            <input type="text" name="groom_name" required placeholder="First Name M.I. Surname" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Age</label>
                            <input type="number" name="groom_age" required placeholder="Groom's Age" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Civil Status</label>
                            <input type="text" name="groom_status" placeholder="Single / Widower" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Residence</label>
                            <input type="text" name="groom_residence" placeholder="Street, Barangay, City/Municipality" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Groom's Parents</label>
                            <input type="text" name="groom_parents" placeholder="Father & Mother Names" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Parents' Residence</label>
                            <input type="text" name="groom_parents_residence" placeholder="Parents' Residence" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- II. BRIDE DETAILS -->
                <div>
                    <h3 class="text-md font-bold text-gray-800 border-l-4 border-[#6c4b3e] pl-3 mb-5 uppercase tracking-wide">II. BRIDE DETAILS</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Full Name of Bride</label>
                            <input type="text" name="bride_name" required placeholder="First Name M.I. Maiden Surname" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Age</label>
                            <input type="number" name="bride_age" required placeholder="Bride's Age" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Civil Status</label>
                            <input type="text" name="bride_status" placeholder="Single / Widow" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Residence</label>
                            <input type="text" name="bride_residence" placeholder="Street, Barangay, City/Municipality" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bride's Parents</label>
                            <input type="text" name="bride_parents" placeholder="Father & Mother Names" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Parents' Residence</label>
                            <input type="text" name="bride_parents_residence" placeholder="Parents' Residence" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- III. MINISTER & REMARKS -->
                <div>
                    <h3 class="text-md font-bold text-gray-800 border-l-4 border-[#6c4b3e] pl-3 mb-5 uppercase tracking-wide">III. MINISTER & REMARKS</h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Name of Minister</label>
                            <input type="text" name="minister_name" placeholder="Rev. Fr. Name of Priest" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Remarks</label>
                            <textarea name="remarks" rows="3" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 resize-none transition-all" placeholder="Additional remarks..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex flex-col items-center justify-center space-y-4 border-t border-gray-200">
                    <button type="submit" class="w-full md:w-auto bg-[#242b35] hover:bg-slate-800 text-black text-xs font-bold tracking-[0.2em] uppercase px-12 py-3.5 rounded shadow-sm transition-all duration-200">
                        SAVE RECORD TO WEDDING BOOK
                    </button>
                    <a href="{{ route('records.index', ['category' => request('category'), 'book_number' => request('book_number')]) }}" class="text-xs text-gray-500 hover:text-gray-700 underline tracking-wider font-medium transition-colors">
                        Cancel Registration
                    </a>
                </div>
            </form>

        @elseif(strtolower(request('category')) === 'funeral')

            <div class="w-full bg-[#242b35] text-black text-center py-3.5 rounded-sm uppercase font-bold text-sm tracking-[0.2em] shadow-sm">
                ADD FUNERAL RECORD
            </div>

            <form action="{{ route('records.store') }}" method="POST" class="space-y-8">
                @csrf
                <input type="hidden" name="category" value="Funeral">
                <input type="hidden" name="book_number" value="{{ request('book_number') }}">

                <!-- I. REGISTRY REFERENCES -->
                <div class="bg-gray-50 p-5 rounded-sm border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Book Number</label>
                            <input type="text" value="{{ request('book_number') }}" readonly class="text-sm w-full bg-gray-100 border border-gray-300 rounded px-3 py-2.5 text-gray-600">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Page Number</label>
                            <input type="number" name="page_number" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Line Number</label>
                            <input type="number" name="line_number" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- I. DECEASED INFORMATION -->
                <div>
                    <h3 class="text-md font-bold text-gray-800 border-l-4 border-[#6c4b3e] pl-3 mb-5 uppercase tracking-wide">I. DECEASED INFORMATION</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Full Name of Deceased</label>
                            <div class="grid grid-cols-3 gap-3">
                                <input type="text" name="first_name" placeholder="First Name" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                                <input type="text" name="middle_name" placeholder="M.I" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                                <input type="text" name="last_name" placeholder="Surname" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Age at Death</label>
                            <input type="number" name="age" placeholder="Years Old" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Civil Status</label>
                            <select name="civil_status" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Spouse Name (if married)</label>
                            <input type="text" name="spouse_name" placeholder="Name of spouse" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Residence</label>
                            <input type="text" name="residence" placeholder="Barrio, Municipality, Province" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- II. DEATH & BURIAL DETAILS -->
                <div>
                    <h3 class="text-md font-bold text-gray-800 border-l-4 border-[#6c4b3e] pl-3 mb-5 uppercase tracking-wide">II. DEATH & BURIAL DETAILS</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Date of Death</label>
                            <input type="date" name="death_date" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Date of Burial</label>
                            <input type="date" name="burial_date" required class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Cause of Death</label>
                            <input type="text" name="cause_of_death" placeholder="Cause of death" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Place of Burial</label>
                            <input type="text" name="burial_place" value="Municipal Cemetery of Alcala" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- III. MINISTER & SACRAMENTS -->
                <div>
                    <h3 class="text-md font-bold text-gray-800 border-l-4 border-[#6c4b3e] pl-3 mb-5 uppercase tracking-wide">III. MINISTER & SACRAMENTS</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Name of Minister</label>
                            <input type="text" name="minister_name" placeholder="Rev. Fr. Name of Priest" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Received Sacraments?</label>
                            <select name="sacrament_before_death" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 transition-all">
                                <option value="0">Not able to receive Sacraments</option>
                                <option value="1">Received Sacraments before death</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Remarks</label>
                            <textarea name="remarks" rows="3" class="text-sm w-full bg-white border border-gray-300 rounded px-3 py-2.5 text-gray-800 focus:outline-none focus:border-gray-500 resize-none transition-all" placeholder="Additional notes..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex flex-col items-center justify-center space-y-4 border-t border-gray-200">
                    <button type="submit" class="w-full md:w-auto bg-[#242b35] hover:bg-slate-800 text-black text-xs font-bold tracking-[0.2em] uppercase px-12 py-3.5 rounded shadow-sm transition-all duration-200">
                        SAVE RECORD TO FUNERAL BOOK
                    </button>
                    <a href="{{ route('records.index', ['category' => request('category'), 'book_number' => request('book_number')]) }}" class="text-xs text-gray-500 hover:text-gray-700 underline tracking-wider font-medium transition-colors">
                        Cancel Registration
                    </a>
                </div>
            </form>

        @else
            
            <div class="text-center my-4">
                <h2 class="text-2xl font-bold tracking-wider text-gray-800 uppercase">REGISTER NEW RECORD</h2>
                <p class="text-[11px] text-gray-400 tracking-widest uppercase mt-1">{{ request('category') }} — BOOK {{ request('book_number', '1') }}</p>
            </div>

            <form action="{{ route('records.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="category" value="{{ request('category') }}">
                <input type="hidden" name="book_number" value="{{ request('book_number') }}">
                <input type="hidden" name="page_number" value="1">
                <input type="hidden" name="line_number" value="1">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-500 tracking-wider mb-1.5">Reference Location</label>
                        <input type="text" name="reference_location" class="text-sm border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:border-gray-500 font-semibold text-gray-700 transition-all" placeholder="e.g. BK-PG-LN" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-500 tracking-wider mb-1.5">Candidate Name</label>
                        <input type="text" name="candidate_name" class="text-sm border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:border-gray-500 font-semibold text-gray-700 transition-all" placeholder="Full Name" required>
                    </div>
                </div>

                <div class="pt-6 flex flex-col items-center justify-center space-y-4 border-t border-gray-100">
                    <button type="submit" class="bg-black hover:bg-gray-900 text-white text-xs font-bold tracking-widest uppercase px-14 py-3 rounded shadow transition-all duration-200">
                        SAVE {{ strtoupper(request('category')) }} RECORD
                    </button>
                    <a href="{{ route('records.index', ['category' => request('category'), 'book_number' => request('book_number')]) }}" class="text-xs text-gray-400 hover:text-gray-600 underline tracking-wide font-medium">
                        Cancel Registration
                    </a>
                </div>
            </form>

        @endif

    </div>
</div>
@endsection