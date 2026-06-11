<x-app-layout>
    <div class="py-12 bg-white min-h-screen" x-data="{ openCommunion: false }">
        <div class="max-w-[1600px] mx-auto px-10">
            
            <div class="flex justify-between items-center mb-10">
                <a href="{{ route('records.index', ['category' => 'communion']) }}" 
                   class="border border-gray-400 rounded-md px-6 py-2 text-xs font-bold uppercase tracking-widest shadow-sm">
                    ← VOLUMES
                </a>
                
                <button @click="openCommunion = true" 
                        class="bg-[#431407] text-white px-8 py-2.5 rounded-full font-black text-xs tracking-widest hover:bg-[#7c2d12] transition uppercase shadow-lg">
                    REGISTER NEW COMMUNION +
                </button>
            </div>

            <div class="text-center mb-16">
                <h2 class="text-7xl font-black text-[#1a202c] tracking-[0.15em] uppercase italic">HOLY CROSS ARCHIVES</h2>
                <p class="text-sm font-bold text-gray-500 uppercase tracking-[0.4em] mt-4">COMMUNION — BOOK {{ $bookNumber }}</p>
            </div>

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
                            <a href="{{ url()->current() }}" class="ml-3 text-[10px] font-bold text-red-400 hover:text-red-600 uppercase tracking-tighter">Clear</a>
                        @endif
                </form>
            </div>

            <div class="border border-gray-200 rounded-sm overflow-hidden bg-white shadow-sm">
                <table class="w-full text-left border-collapse italic">
                    <thead class="bg-gray-50 border-b border-gray-200 uppercase text-[10px] font-black text-gray-400 tracking-widest">
                        <tr>
                            <th class="px-4 py-5">No.</th>
                            <th class="px-4 py-5">Year</th>
                            <th class="px-4 py-5">Month/Day</th>
                            <th class="px-6 py-5">Name of Communicant</th>
                            <th class="px-6 py-5">Domicile</th>
                            <th class="px-6 py-5">Minister</th>
                            <th class="px-6 py-5">Baptismal Data</th>
                            <th class="px-4 py-5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($records as $record)
                        <tr class="hover:bg-orange-50/30 transition">
                            <td class="px-4 py-4 text-xs font-bold text-gray-500">{{ $record->line_number }}</td>
                            <td class="px-4 py-4 text-xs text-gray-600 uppercase">{{ \Carbon\Carbon::parse($record->communion_date)->format('Y') }}</td>
                            <td class="px-4 py-4 text-xs text-gray-600 uppercase">{{ \Carbon\Carbon::parse($record->communion_date)->format('M d') }}</td>
                            <td class="px-6 py-4 font-black text-gray-900 uppercase text-sm">{{ $record->first_name }} {{ $record->last_name }}</td>
                            <td class="px-6 py-4 text-xs text-gray-500 uppercase">{{ $record->residence }}</td>
                            <td class="px-6 py-4 text-xs font-medium uppercase">{{ $record->minister_name }}</td>
                            <td class="px-6 py-4 text-xs text-gray-400 uppercase italic">
                                {{ \Carbon\Carbon::parse($record->baptism_date)->format('M d, Y') }} / {{ $record->place_of_baptism }}
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-4">
                                    <!-- FIXED: Changed from records.show to records.communion.show -->
                                    <a href="{{ route('records.communion.show', $record->id) }}" 
                                       class="text-[10px] font-black uppercase text-[#7c2d12] hover:underline">View</a>

                                    <!-- FIXED: Added book_number parameter to delete route -->
                                    <form action="{{ route('records.destroy', ['id' => $record->id, 'category' => 'communion', 'book_number' => $bookNumber]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?')">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-black font-black text-[10px] uppercase tracking-widest transition">DELETE</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-6 py-10 text-center text-gray-400 uppercase text-xs tracking-widest font-bold">No records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="openCommunion" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" x-transition x-cloak>
            <div class="bg-white w-full max-w-2xl p-10 rounded-sm shadow-2xl overflow-y-auto max-h-[90vh]" @click.away="openCommunion = false">
                <div class="flex justify-between items-center mb-8 border-b border-gray-100 pb-5">
                    <h3 class="text-3xl font-black italic tracking-tighter text-[#1a202c]">NEW COMMUNION ENTRY</h3>
                    <button @click="openCommunion = false" class="text-gray-300 hover:text-black transition text-3xl font-light">&times;</button>
                </div>
                <form action="{{ route('records.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="category" value="communion">
                    <input type="hidden" name="book_number" value="{{ $bookNumber }}">
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div><label class="block text-[10px] font-black uppercase text-gray-400 mb-2 tracking-widest">Page Number</label><input type="number" name="page_number" required class="w-full border-gray-200 text-sm focus:ring-[#7c2d12]"></div>
                        <div><label class="block text-[10px] font-black uppercase text-gray-400 mb-2 tracking-widest">Line No.</label><input type="number" name="line_number" required class="w-full border-gray-200 text-sm focus:ring-[#7c2d12]"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div><label class="block text-[10px] font-black uppercase text-gray-400 mb-2 tracking-widest">First Name</label><input type="text" name="first_name" required class="w-full border-gray-200 text-sm uppercase italic focus:ring-[#7c2d12]"></div>
                        <div><label class="block text-[10px] font-black uppercase text-gray-400 mb-2 tracking-widest">Last Name</label><input type="text" name="last_name" required class="w-full border-gray-200 text-sm uppercase italic focus:ring-[#7c2d12]"></div>
                    </div>
                    <div><label class="block text-[10px] font-black uppercase text-gray-400 mb-2 tracking-widest">Date of Holy Communion</label><input type="date" name="communion_date" required class="w-full border-gray-200 text-sm focus:ring-[#7c2d12]"></div>
                    <div><label class="block text-[10px] font-black uppercase text-gray-400 mb-2 tracking-widest">Domicile (Residence)</label><input type="text" name="residence" required class="w-full border-gray-200 text-sm uppercase italic focus:ring-[#7c2d12]"></div>
                    <div><label class="block text-[10px] font-black uppercase text-gray-400 mb-2 tracking-widest">Minister</label><input type="text" name="minister_name" required class="w-full border-gray-200 text-sm uppercase italic focus:ring-[#7c2d12]"></div>
                    <div class="pt-6 mt-6 border-t border-dashed border-gray-200">
                        <p class="text-[10px] font-black uppercase text-[#7c2d12] mb-4 tracking-[0.2em]">Baptismal Reference</p>
                        <div class="grid grid-cols-2 gap-6">
                            <div><label class="block text-[10px] font-black uppercase text-gray-400 mb-2 tracking-widest">Baptism Date</label><input type="date" name="baptism_date" required class="w-full border-gray-200 text-sm focus:ring-[#7c2d12]"></div>
                            <div><label class="block text-[10px] font-black uppercase text-gray-400 mb-2 tracking-widest">Place of Baptism</label><input type="text" name="place_of_baptism" required class="w-full border-gray-200 text-sm uppercase italic focus:ring-[#7c2d12]"></div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-4 mt-10">
                        <button type="button" @click="openCommunion = false" class="px-8 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-black">Cancel</button>
                        <button type="submit" class="bg-[#1a202c] text-white px-12 py-3 text-[10px] font-black uppercase tracking-widest hover:bg-[#7c2d12] transition shadow-xl">Save Archive Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>