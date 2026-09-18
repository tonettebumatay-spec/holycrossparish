<x-app-layout>
    <div class="py-12 bg-white min-h-screen" x-data="{ openConfirmation: false }">
        <div class="max-w-[1600px] mx-auto px-10">
            
            <div class="flex justify-between items-center mb-10">
                <a href="{{ route('records.index', ['category' => 'Confirmation']) }}" 
                   class="border border-gray-400 rounded-md px-6 py-2 text-xs font-bold uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
                    ← VOLUMES
                </a>
                
                <a href="{{ route('records.create', ['category' => 'Confirmation', 'book_number' => request('book_number')]) }}"
                   class="bg-[#1a202c] text-white px-8 py-2.5 rounded-full font-black text-xs tracking-widest hover:bg-black transition uppercase shadow-lg">
                    REGISTER NEW CONFIRMATION +
                </a>
            </div>

            <div class="text-center mb-10">
                <h2 class="text-7xl font-black text-[#1a202c] tracking-[0.15em] uppercase italic">HOLY CROSS ARCHIVES</h2>
                <p class="text-sm font-bold text-gray-500 uppercase tracking-[0.4em] mt-4">CONFIRMATION — BOOK {{ $bookNumber }}</p>
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
                            <th class="px-6 py-5">Confirmand Name</th>
                            <th class="px-4 py-5">Age</th>
                            <th class="px-6 py-5">Parents</th>
                            <th class="px-6 py-5">Sponsors</th>
                            <th class="px-6 py-5">Minister</th>
                            <th class="px-4 py-5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="record-tbody">
                        @forelse($records as $record)
                        <tr class="hover:bg-gray-50/50 transition record-row">
                            <td class="px-4 py-4 text-xs font-bold text-gray-500">{{ $record->line_number }}</td>
                            <td class="px-4 py-4 text-xs text-gray-600 uppercase">{{ $record->year }}</td>
                            <td class="px-4 py-4 text-xs text-gray-600 uppercase">{{ $record->month_day }}</td>
                            <td class="px-6 py-4 font-black text-gray-900 uppercase text-sm">
                                {{ $record->first_name }} {{ $record->last_name }}
                                <span class="block text-[10px] font-normal text-gray-400 italic lowercase tracking-tight">born in {{ $record->birthplace }}</span>
                            </td>
                            <td class="px-4 py-4 text-xs text-gray-600">{{ $record->age }}</td>
                            <td class="px-6 py-4 text-xs text-gray-500 uppercase leading-relaxed">
                                <span class="font-bold text-gray-700">F:</span> {{ $record->father_name }} <br>
                                <span class="font-bold text-gray-700">M:</span> {{ $record->mother_name }}
                            </td>
                            <td class="px-6 py-4 text-[11px] text-gray-500 uppercase truncate max-w-[200px]">
                                {{ $record->sponsors }}
                            </td>
                            <td class="px-6 py-4 text-xs font-medium uppercase italic">{{ $record->minister_name }}</td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-4">
                                    <a href="{{ route('records.confirmation.show', $record->id) }}" 
                                       class="text-[10px] font-black uppercase text-[#1a202c] hover:underline">
                                        VIEW
                                    </a>
                                    <form action="{{ route('records.destroy', ['id' => $record->id, 'category' => 'confirmation']) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?')">
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
                        <tr class="record-row">
                            <td colspan="9" class="px-6 py-10 text-center text-gray-400 uppercase text-xs tracking-widest font-bold">No records found for this volume.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Registration Modal Layer -->
        <div x-show="openConfirmation" 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
             x-transition
             x-cloak>
            
            <div class="bg-white w-full max-w-4xl p-10 rounded-sm shadow-2xl overflow-y-auto max-h-[95vh]" 
                 @click.away="openConfirmation = false">
                
                <div class="flex justify-between items-center mb-8 border-b border-gray-100 pb-5">
                    <h3 class="text-3xl font-black italic tracking-tighter text-[#1a202c]">NEW CONFIRMATION ENTRY</h3>
                    <button @click="openConfirmation = false" class="text-gray-300 hover:text-black transition text-3xl font-light">&times;</button>
                </div>

                <form action="{{ route('records.store') }}" method="POST" class="space-y-8">
                    @csrf
                    <input type="hidden" name="category" value="Confirmation">
                    <input type="hidden" name="book_number" value="{{ $bookNumber }}">

                    <div class="bg-gray-50/50 p-6 rounded-md border border-gray-100">
                        <div class="grid grid-cols-4 gap-6">
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">No.</label>
                                <input type="number" name="line_number" required class="w-full border-gray-200 text-sm focus:ring-[#1a202c]">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Year</label>
                                <input type="text" name="year" required class="w-full border-gray-200 text-sm focus:ring-[#1a202c]">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Month & Day</label>
                                <input type="text" name="month_day" required class="w-full border-gray-200 text-sm focus:ring-[#1a202c]">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Page No.</label>
                                <input type="number" name="page_number" required class="w-full border-gray-200 text-sm focus:ring-[#1a202c]">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <h4 class="text-[11px] font-black uppercase text-[#1a202c] tracking-widest border-l-4 border-[#1a202c] pl-3">Candidate</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <input type="text" name="first_name" placeholder="First Name" required class="w-full border-gray-200 text-sm uppercase italic">
                                <input type="text" name="last_name" placeholder="Surname" required class="w-full border-gray-200 text-sm uppercase italic">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <input type="number" name="age" placeholder="Age" required class="w-full border-gray-200 text-sm">
                                <input type="text" name="birthplace" placeholder="Birthplace" required class="w-full border-gray-200 text-sm uppercase">
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h4 class="text-[11px] font-black uppercase text-[#1a202c] tracking-widest border-l-4 border-[#1a202c] pl-3">Parents</h4>
                            <input type="text" name="father_name" placeholder="Father's Name" required class="w-full border-gray-200 text-sm uppercase">
                            <input type="text" name="mother_name" placeholder="Mother's Name" required class="w-full border-gray-200 text-sm uppercase">
                            <textarea name="parents_residence" placeholder="Residence" rows="2" class="w-full border-gray-200 text-sm italic"></textarea>
                        </div>
                    </div>

                    <div class="bg-[#1a202c] p-8 rounded-sm shadow-inner">
                        <div class="grid grid-cols-2 gap-10">
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 mb-3 tracking-[0.2em]">Sponsors (Godparents)</label>
                                <textarea name="sponsors" rows="3" class="w-full bg-white/5 border border-white/10 rounded-sm text-xs text-white p-3 focus:border-white/40 italic placeholder-white/20" placeholder="List sponsors..."></textarea>
                            </div>
                            <div class="flex flex-col justify-center">
                                <label class="block text-[10px] font-black uppercase text-gray-400 mb-3 tracking-[0.2em]">Officiating Minister</label>
                                <input type="text" name="minister_name" required class="w-full bg-white/5 border border-white/10 rounded-sm text-sm text-white py-4 px-4 uppercase font-black italic tracking-widest">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-6 pt-4">
                        <button type="button" @click="openConfirmation = false" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-black transition">Cancel Entry</button>
                        <button type="submit" class="bg-[#1a202c] text-white px-12 py-4 text-[10px] font-black uppercase tracking-widest hover:bg-black transition shadow-xl">Save to Archives</button>
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