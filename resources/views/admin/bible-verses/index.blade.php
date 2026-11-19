<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen font-sans">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Header Section -->
            <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Manage Bible Verses</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Add, schedule, and manage the Bible Verse of the Day displayed on the mobile app.
                    </p>
                </div>
                
                <!-- 👇 BACK BUTTON DITO 👇 -->
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-600 rounded-full text-xs font-bold uppercase tracking-widest hover:bg-gray-50 transition shadow-sm self-start md:self-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>

            <!-- Flash Message -->
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Add New Verse Form -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Add New Verse</h2>
                    
                    <form method="POST" action="{{ route('admin.bible-verses.store') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Date to Display</label>
                            <input type="date" name="date_to_display" class="w-full text-sm border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" required>
                            @error('date_to_display')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Bible Verse</label>
                            <textarea name="verse_text" rows="4" class="w-full text-sm border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" placeholder="Type the verse here..." required></textarea>
                            @error('verse_text')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Reference (Optional)</label>
                            <input type="text" name="reference" class="w-full text-sm border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" placeholder="e.g. John 3:16">
                        </div>

                        <button type="submit" class="w-full bg-purple-700 hover:bg-purple-800 text-white font-bold py-3 px-4 rounded-lg text-sm transition shadow-md">
                            Add Verse
                        </button>
                    </form>
                </div>

                <!-- List of Verses -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 lg:col-span-2">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Scheduled Verses</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="bg-gray-100 text-xs font-semibold uppercase text-gray-500 border-b">
                                <tr>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3 w-1/2">Verse</th>
                                    <th class="px-4 py-3">Reference</th>
                                    <th class="px-4 py-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($verses as $verse)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-4 font-bold text-purple-700 whitespace-nowrap">
                                            {{ $verse->date_to_display->format('M d, Y') }}
                                        </td>
                                        <td class="px-4 py-4 italic text-gray-800">
                                            "{{ $verse->verse_text }}"
                                        </td>
                                        <td class="px-4 py-4 text-gray-500 whitespace-nowrap">
                                            {{ $verse->reference ?? '-' }}
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <form action="{{ route('admin.bible-verses.destroy', $verse->id) }}" method="POST" onsubmit="return confirm('Delete this verse?')">
                                                @csrf 
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs uppercase tracking-wider">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                            No verses scheduled yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</x-app-layout>