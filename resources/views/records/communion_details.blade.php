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

            <div class="text-center mb-10">
                <h2 class="text-7xl font-black text-[#1a202c] tracking-[0.15em] uppercase italic">HOLY CROSS ARCHIVES</h2>
                <p class="text-sm font-bold text-gray-500 uppercase tracking-[0.4em] mt-4">COMMUNION — BOOK {{ $bookNumber }}</p>
            </div>

            <!-- Client-side Search Bar -->
            <div class="flex justify-end mb-4">
                <div class="relative">
                    <input type="text" id="record-search" placeholder="🔍 Search records..."
                           class="w-72 border border-gray-300 rounded-full pl-5 pr-5 py-2.5 text-sm italic focus:outline-none focus:border-[#4d290a] transition-all shadow-sm">
                </div>
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
                    <tbody class="divide-y divide-gray-100" id="record-tbody">
                        @forelse($records as $record)
                        <tr class="hover:bg-orange-50/30 transition record-row">
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
                                    <a href="{{ route('records.communion.show', $record->id) }}" 
                                       class="text-[10px] font-black uppercase text-[#7c2d12] hover:underline">View</a>

                                    <form action="{{ route('records.destroy', ['id' => $record->id, 'category' => 'communion', 'book_number' => $bookNumber]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?')">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-black font-black text-[10px] uppercase tracking-widest transition">DELETE</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="record-row"><td colspan="8" class="px-6 py-10 text-center text-gray-400 uppercase text-xs tracking-widest font-bold">No records found.</td></tr>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('record-search');
            const tbody = document.getElementById('record-tbody');
            if (!searchInput || !tbody) return;

            searchInput.addEventListener('input', function () {
                const term = this.value.toLowerCase().trim();
                const rows = tbody.querySelectorAll('tr.record-row');
                rows.forEach(row => {
                    if (row.querySelector('td[colspan]')) return;
                    const text = row.innerText.toLowerCase();
                    row.style.display = (term === '' || text.includes(term)) ? '' : 'none';
                });
            });
        });
    </script>
</x-app-layout>