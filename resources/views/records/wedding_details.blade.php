<x-app-layout>
    <div class="py-12 bg-white min-h-screen" x-data="{ openWedding: false }">
        <div class="max-w-[1600px] mx-auto px-10">
            
            <div class="flex justify-between items-center mb-10">
                <a href="{{ route('records.index', ['category' => 'Wedding']) }}" 
                   class="border border-gray-400 rounded-md px-6 py-2 text-xs font-bold uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
                    ← VOLUMES
                </a>
                
                <a href="{{ route('records.create', ['category' => 'Wedding', 'book_number' => request('book_number')]) }}"
                   class="bg-[#1a202c] text-white px-8 py-2.5 rounded-full font-black text-xs tracking-widest hover:bg-black transition uppercase shadow-lg">
                    REGISTER NEW WEDDING +
                </a>
            </div>

            <div class="text-center mb-16">
                <h2 class="text-7xl font-black text-[#1a202c] tracking-[0.15em] uppercase italic">HOLY CROSS ARCHIVES</h2>
            </div>

            <!-- FUNCTIONAL SEARCH BAR -->
            <div class="flex justify-center mb-8">
                <form action="{{ url()->current() }}" method="GET" class="flex items-center">
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    <input type="hidden" name="book_number" value="{{ request('book_number') }}">
                    <div class="relative w-full max-w-md flex items-center">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search records..."
                               class="w-80 pl-6 pr-14 py-2.5 border border-gray-200 rounded-full text-sm italic text-gray-500 focus:ring-0 focus:border-gray-300 transition-all shadow-sm">
                        <button type="submit" class="absolute right-0 h-full px-5 bg-[#5D4037] text-white rounded-r-full hover:bg-[#4E342E] transition-colors flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                    @if(request('search'))
                        <a href="{{ url()->current() }}" class="ml-3 text-[10px] font-bold text-red-400 hover:text-red-600 uppercase tracking-tighter">Clear Search</a>
                    @endif
                </form>
            </div>

            <div class="text-center mb-16">
                <p class="text-sm font-bold text-gray-500 uppercase tracking-[0.4em] mt-4">WEDDING – BOOK {{ request('book_number') }}</p>
            </div>

            <!-- Table matches the columns in the image_1caa94.png -->
            <div class="border border-gray-200 rounded-sm overflow-hidden bg-white shadow-sm">
                <table class="w-full text-left border-collapse italic">
                    <thead class="bg-gray-50 border-b border-gray-200 uppercase text-[10px] font-black text-gray-400 tracking-widest">
                        <tr>
                            <th class="px-4 py-5">No.</th>
                            <th class="px-4 py-5">Year</th>
                            <th class="px-4 py-5">Month/Day</th>
                            <th class="px-6 py-5">Groom's Name</th>
                            <th class="px-6 py-5">Bride's Name</th>
                            <th class="px-4 py-5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($records as $record)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-4 py-4 text-xs font-bold text-gray-500">{{ $record->line_number }}</td>
                            <td class="px-4 py-4 text-xs text-gray-600 uppercase">{{ $record->year }}</td>
                            <td class="px-4 py-4 text-xs text-gray-600 uppercase">{{ $record->month_day }}</td>
                            <td class="px-6 py-4 font-black text-blue-900 uppercase text-sm">
                                {{ $record->groom_name }} 
                                <div class="font-normal text-gray-400 text-[10px] mt-1">{{ $record->groom_residence }}</div>
                            </td>
                            <td class="px-6 py-4 font-black text-red-900 uppercase text-sm">
                                {{ $record->bride_name }}
                                <div class="font-normal text-gray-400 text-[10px] mt-1">{{ $record->bride_residence }}</div>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-4">
                                    <!-- FIXED: View button now links to wedding certificate -->
                                    <a href="{{ route('records.wedding.show', $record->id) }}" 
                                       class="text-[10px] font-black uppercase text-[#7c2d12] hover:underline">View</a>

                                    <form action="{{ route('records.destroy', ['id' => $record->id, 'category' => 'wedding', 'book_number' => $bookNumber]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-black font-black text-[10px] uppercase tracking-widest transition">
                                            DELETE
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-300 uppercase text-xs tracking-widest font-bold">No records found for this volume.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL -->
        <div x-show="openWedding" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" x-transition x-cloak>
            <div class="bg-white w-full max-w-6xl p-10 rounded-sm shadow-2xl overflow-y-auto max-h-[95vh]" @click.away="openWedding = false">
                
                <div class="flex justify-between items-center mb-8 border-b border-gray-100 pb-5">
                    <h3 class="text-3xl font-black italic tracking-tighter text-[#1a202c]">NEW WEDDING ENTRY</h3>
                    <button @click="openWedding = false" class="text-gray-300 hover:text-black transition text-3xl font-light">&times;</button>
                </div>

                <form action="{{ route('records.store') }}" method="POST" class="space-y-8">
                    @csrf
                    <input type="hidden" name="category" value="Wedding">
                    <input type="hidden" name="book_number" value="{{ $bookNumber }}">

                    <div class="grid grid-cols-4 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2 tracking-widest">Year</label>
                            <input type="text" name="year" required class="w-full border-gray-200 text-sm focus:ring-[#1a202c]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2 tracking-widest">Month-Day</label>
                            <input type="text" name="month_day" required class="w-full border-gray-200 text-sm italic focus:ring-[#1a202c]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2 tracking-widest">Page No.</label>
                            <input type="number" name="page_number" required class="w-full border-gray-200 text-sm focus:ring-[#1a202c]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2 tracking-widest">Line No.</label>
                            <input type="number" name="line_number" required class="w-full border-gray-200 text-sm focus:ring-[#1a202c]">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-12">
                        <!-- Groom -->
                        <div class="space-y-5 p-6 border-l-4 border-blue-600 bg-gray-50/50">
                            <p class="text-[11px] font-black uppercase text-blue-700 tracking-[0.3em]">The Groom</p>
                            <input type="text" name="groom_name" placeholder="FULL NAME" required class="w-full border-gray-200 text-sm uppercase italic">
                            
                            <div class="grid grid-cols-2 gap-4">
                                <input type="number" name="groom_age" placeholder="AGE" required class="w-full border-gray-200 text-sm">
                                <select name="groom_status" class="w-full border-gray-200 text-sm italic">
                                    <option value="Single">Single</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Separated">Separated</option>
                                </select>
                            </div>
                            
                            <input type="text" name="groom_residence" placeholder="CURRENT RESIDENCE" required class="w-full border-gray-200 text-sm uppercase italic">
                            <input type="text" name="groom_parents" placeholder="PARENTS (NAMES & SURNAME)" required class="w-full border-gray-200 text-sm uppercase italic">
                            <input type="text" name="groom_parents_residence" placeholder="PARENTS' RESIDENCE" required class="w-full border-gray-200 text-sm uppercase italic">
                        </div>

                        <!-- Bride -->
                        <div class="space-y-5 p-6 border-l-4 border-red-600 bg-gray-50/50">
                            <p class="text-[11px] font-black uppercase text-red-700 tracking-[0.3em]">The Bride</p>
                            <input type="text" name="bride_name" placeholder="FULL NAME" required class="w-full border-gray-200 text-sm uppercase italic">
                            
                            <div class="grid grid-cols-2 gap-4">
                                <input type="number" name="bride_age" placeholder="AGE" required class="w-full border-gray-200 text-sm">
                                <select name="bride_status" class="w-full border-gray-200 text-sm italic">
                                    <option value="Single">Single</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Separated">Separated</option>
                                </select>
                            </div>

                            <input type="text" name="bride_residence" placeholder="CURRENT RESIDENCE" required class="w-full border-gray-200 text-sm uppercase italic">
                            <input type="text" name="bride_parents" placeholder="PARENTS (NAMES & SURNAME)" required class="w-full border-gray-200 text-sm uppercase italic">
                            <input type="text" name="bride_parents_residence" placeholder="PARENTS' RESIDENCE" required class="w-full border-gray-200 text-sm uppercase italic">
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 mt-10">
                        <button type="button" @click="openWedding = false" class="text-[10px] font-black uppercase tracking-widest text-gray-400">Cancel</button>
                        <button type="submit" class="bg-[#1a202c] text-white px-12 py-3 text-[10px] font-black uppercase tracking-widest shadow-xl">Save Wedding Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>