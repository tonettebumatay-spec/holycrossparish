<x-app-layout>
    <div class="py-12 bg-white min-h-screen font-sans">
        <div class="max-w-[1600px] mx-auto px-10">
            
            <div class="flex justify-between items-center mb-10">
                <a href="{{ route('records.index', ['category' => 'baptism']) }}" 
                   class="border border-gray-400 rounded-md px-6 py-2 text-xs font-bold text-gray-700 hover:bg-gray-100 transition uppercase tracking-widest shadow-sm">
                    ← BACK TO VOLUMES
                </a>

                <a href="{{ route('records.create', ['category' => 'baptism', 'book_number' => $bookNumber]) }}" 
                   class="bg-[#1a202c] text-white px-8 py-3 rounded-full font-black text-xs tracking-[0.2em] hover:bg-black transition shadow-lg uppercase">
                    ADD NEW RECORD +
                </a>
            </div>

            <div class="text-center mb-16">
                <h2 class="text-7xl font-black text-[#1a202c] tracking-[0.15em] uppercase italic leading-none">HOLY CROSS ARCHIVES</h2>
                <p class="text-sm font-bold text-gray-500 uppercase tracking-[0.4em] mt-4">BAPTISM — BOOK {{ $bookNumber }}</p>
            </div>

            <div class="border border-gray-200 rounded-md overflow-hidden bg-white shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr class="text-[10px] font-black text-gray-500 uppercase tracking-tighter">
                            <th class="px-6 py-4">BK/PG/LN</th>
                            <th class="px-6 py-4">Candidate & Legitimacy</th>
                            <th class="px-6 py-4">Birth Details</th>
                            <th class="px-6 py-4">Parents</th>
                            <th class="px-6 py-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($records as $record)
                            <tr class="hover:bg-gray-50 transition italic">
                                <td class="px-6 py-4 text-xs font-bold text-gray-700">
                                    {{ $record->book_number }}/{{ $record->page_number }}/{{ $record->line_number }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-black text-gray-900 uppercase">{{ $record->first_name }} {{ $record->last_name }}</div>
                                    <div class="text-[10px] font-bold text-blue-600 uppercase">{{ $record->legitimacy }}</div>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    {{ $record->birth_date ? \Carbon\Carbon::parse($record->birth_date)->format('M d, Y') : 'N/A' }}<br>
                                    <span class="text-[10px] text-gray-400">{{ $record->birth_place }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    F: {{ $record->father_name }}<br>
                                    M: {{ $record->mother_maiden_name ?? $record->mother_name }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-4">
                                        <a href="{{ route('records.baptism.show', $record->id) }}" 
                                           class="text-[#3E2723] hover:text-black font-black text-xs uppercase tracking-wider transition">
                                            VIEW
                                        </a>

                                        <form action="{{ route('records.destroy', $record->id) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this record?');" 
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            
                                            <input type="hidden" name="category" value="baptism">
                                            
                                            <button type="submit" class="text-[#B71C1C] hover:text-red-600 font-black text-xs uppercase tracking-wider transition">
                                                DELETE
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-32 text-center text-gray-400 italic font-medium uppercase text-sm">
                                    No records found in this book.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>