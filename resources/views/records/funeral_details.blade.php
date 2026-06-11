<x-app-layout>
    <div class="py-12 bg-white min-h-screen font-sans">
        <div class="max-w-[1600px] mx-auto px-10">
            
            <div class="flex justify-between items-center mb-10">
                <a href="{{ route('records.index', ['category' => 'funeral']) }}" 
                   class="border border-gray-400 rounded-md px-6 py-2 text-xs font-bold text-gray-700 hover:bg-gray-100 transition uppercase tracking-widest shadow-sm">
                    ← BACK TO VOLUMES
                </a>

                <a href="{{ route('records.create', ['category' => 'funeral', 'book_number' => $bookNumber]) }}" 
                   class="bg-[#1a202c] text-white px-8 py-3 rounded-full font-black text-xs tracking-[0.2em] hover:bg-black transition shadow-lg uppercase">
                    ADD NEW RECORD +
                </a>
            </div>

            <div class="text-center mb-16">
                <h2 class="text-7xl font-black text-[#1a202c] tracking-[0.15em] uppercase italic leading-none">HOLY CROSS ARCHIVES</h2>
                <p class="text-sm font-bold text-gray-500 uppercase tracking-[0.4em] mt-4">FUNERAL — BOOK {{ $bookNumber }}</p>
            </div>

            <div class="border border-gray-200 rounded-md overflow-hidden bg-white shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr class="text-[10px] font-black text-gray-500 uppercase tracking-tighter">
                            <th class="px-6 py-4">BK/PG/LN</th>
                            <th class="px-6 py-4">Deceased Name</th>
                            <th class="px-6 py-4">Residence</th>
                            <th class="px-6 py-4">Age</th>
                            <th class="px-6 py-4">Death Details</th>
                            <th class="px-6 py-4">Last Sacraments</th>
                            <th class="px-6 py-4">Place of Burial</th>
                            <th class="px-6 py-4">Minister</th>
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
                                    <div class="text-sm font-black text-gray-900 uppercase">{{ $record->deceased_name ?? 'N/A' }}</div>
                                    @if($record->spouse_name)
                                        <div class="text-[10px] font-bold text-gray-500 uppercase">of {{ $record->spouse_name }}</div>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    {{ $record->residence ?? 'N/A' }}
                                </td>
                                
                                <td class="px-6 py-4 text-xs font-bold text-gray-700">
                                    {{ $record->age_at_death ?? 'N/A' }} yrs
                                </td>
                                
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    <div>Died: {{ $record->death_date ? \Carbon\Carbon::parse($record->death_date)->format('M d, Y') : 'N/A' }}</div>
                                    <div>Buried: {{ $record->burial_date ? \Carbon\Carbon::parse($record->burial_date)->format('M d, Y') : 'N/A' }}</div>
                                    <div class="text-[10px] text-gray-400 mt-1">{{ $record->cause_of_death ?? 'SENILITY WITHOUT MENTION OF PSYCHOSIS' }}</div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    @if($record->sacraments_received)
                                        <span class="inline-block bg-green-100 text-green-800 text-[10px] font-black px-2 py-1 rounded uppercase tracking-wider">
                                            YES
                                        </span>
                                        <div class="text-[9px] text-gray-500 mt-1">{{ $record->sacraments_received }}</div>
                                    @else
                                        <span class="inline-block bg-red-100 text-red-800 text-[10px] font-black px-2 py-1 rounded uppercase tracking-wider">
                                            NO
                                        </span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    {{ $record->cemetery_name ?? 'ALCALÁ MUNICIPAL CEMETERY' }}
                                </td>
                                
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    {{ $record->minister_name ?? 'REV. FR. NUMERIANO A. GABOT JR.' }}
                                </td>
                                
                                <!-- FIXED ACTION COLUMN -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-4">
                                        <a href="{{ route('records.funeral.show', $record->id) }}" 
                                           class="text-[#3E2723] hover:text-black font-black text-xs uppercase tracking-wider transition">
                                            VIEW
                                        </a>

                                        <form action="{{ route('records.destroy', ['id' => $record->id, 'category' => 'funeral', 'book_number' => $bookNumber]) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this funeral record?');" 
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-[#B71C1C] hover:text-red-600 font-black text-xs uppercase tracking-wider transition">
                                                DELETE
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-32 text-center text-gray-400 italic font-medium uppercase text-sm">
                                    No funeral records found in this book.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Footer with Signature and Date -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider">
                            <span class="font-black">SIGNATURE:</span> ____________________
                        </p>
                        <p class="text-xs text-gray-500 uppercase tracking-wider mt-2">
                            <span class="font-black">DATE ISSUED:</span> {{ date('F d, Y') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">
                            <span class="font-black">BOOK NO.:</span> {{ $bookNumber }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            <span class="font-black">TOTAL RECORDS:</span> {{ $records->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>