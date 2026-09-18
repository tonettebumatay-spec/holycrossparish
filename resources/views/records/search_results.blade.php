<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-6">

            <!-- Header -->
            <div class="flex justify-between items-center mb-10">
                <a href="{{ route('records.index') }}"
                   class="border-2 border-gray-200 rounded-full px-8 py-2.5 text-xs font-black uppercase tracking-widest text-gray-700 hover:bg-gray-50 transition shadow-sm bg-white">
                    ← Back to Main Shelf
                </a>

                <h1 class="text-3xl font-black text-gray-800 tracking-tighter uppercase italic">
                    Search Results
                </h1>

                <div class="w-32"></div>
            </div>

            <!-- Search Bar (for refining) -->
            <div class="max-w-2xl mx-auto mb-10">
                <form method="GET" action="{{ route('records.search') }}" class="flex gap-3">
                    <input type="text" name="q" value="{{ $query }}"
                           placeholder="Search by name..."
                           class="flex-1 border-2 border-gray-300 rounded-full px-6 py-3 text-sm focus:outline-none focus:border-[#4d290a]">
                    <button type="submit"
                            class="bg-[#4d290a] hover:bg-[#3a1f07] text-white font-black text-xs uppercase tracking-widest px-8 py-3 rounded-full transition">
                        Search
                    </button>
                </form>

                @if($category)
                    <p class="text-center text-xs text-gray-500 mt-3 uppercase tracking-wider">
                        Filtering: <strong>{{ ucfirst($category) }}</strong> records only
                    </p>
                @endif
            </div>

            <!-- Results Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                @if($query === '')
                    <div class="text-center py-20">
                        <div class="inline-flex p-6 bg-purple-50 rounded-full text-purple-600 mb-4">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-700">Type a Name to Search</h2>
                        <p class="text-gray-400 mt-2">Search across all sacraments or filter to a specific book.</p>
                    </div>
                @elseif($results->isEmpty())
                    <div class="text-center py-20">
                        <div class="inline-flex p-6 bg-gray-100 rounded-full text-gray-500 mb-4">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-700">No Results Found</h2>
                        <p class="text-gray-400 mt-2">No records matched "<strong>{{ $query }}</strong>".</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="bg-gray-100 text-xs font-semibold uppercase text-gray-500 border-b">
                                <tr>
                                    <th class="px-6 py-4">#</th>
                                    <th class="px-6 py-4">Sacrament</th>
                                    <th class="px-6 py-4">Name</th>
                                    <th class="px-6 py-4">Book/Page/Line</th>
                                    <th class="px-6 py-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($results as $index => $row)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 font-medium text-gray-400">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-bold uppercase
                                                @if($row['category_slug'] === 'baptism') bg-blue-100 text-blue-800
                                                @elseif($row['category_slug'] === 'communion') bg-green-100 text-green-800
                                                @elseif($row['category_slug'] === 'confirmation') bg-purple-100 text-purple-800
                                                @elseif($row['category_slug'] === 'wedding') bg-pink-100 text-pink-800
                                                @elseif($row['category_slug'] === 'funeral') bg-gray-100 text-gray-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ $row['type'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900 uppercase">{{ $row['name'] }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-700">
                                            {{ $row['book_number'] ?? '—' }} / {{ $row['page_number'] ?? '—' }} / {{ $row['line_number'] ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $viewRoute = 'records.' . $row['category_slug'] . '.show';
                                            @endphp
                                            @if(Route::has($viewRoute))
                                                <a href="{{ route($viewRoute, $row['id']) }}"
                                                   class="text-[#3E2723] hover:text-black font-black text-xs uppercase tracking-wider">
                                                    VIEW
                                                </a>
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 text-sm text-gray-500 border-t">
                        Total: {{ $results->count() }} result(s)
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>