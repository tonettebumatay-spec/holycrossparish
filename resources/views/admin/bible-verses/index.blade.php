<x-app-layout>
    <div class="py-8 max-w-4xl mx-auto px-6">
        <h1 class="text-2xl font-bold mb-6">Manage Bible Verse of the Day</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">{{ session('success') }}</div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <form method="POST" action="{{ route('admin.bible-verses.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block font-semibold mb-1">Date to Display</label>
                    <input type="date" name="date_to_display" class="w-full border rounded p-2" required>
                </div>
                <div class="mb-4">
                    <label class="block font-semibold mb-1">Bible Verse</label>
                    <textarea name="verse_text" rows="3" class="w-full border rounded p-2" placeholder="For God so loved the world..." required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block font-semibold mb-1">Reference (Optional)</label>
                    <input type="text" name="reference" class="w-full border rounded p-2" placeholder="John 3:16">
                </div>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded transition">Add Verse</button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold mb-4">Scheduled Verses</h2>
            <ul>
                @forelse($verses as $verse)
                    <li class="border-b py-3 flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-purple-700">{{ $verse->date_to_display->format('M d, Y') }}</p>
                            <p class="text-gray-700 italic">"{{ $verse->verse_text }}"</p>
                            <p class="text-sm text-gray-500">- {{ $verse->reference }}</p>
                        </div>
                        <form action="{{ route('admin.bible-verses.destroy', $verse->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 font-bold">Delete</button>
                        </form>
                    </li>
                @empty
                    <p class="text-gray-500">No verses scheduled yet.</p>
                @endforelse
            </ul>
        </div>
    </div>
</x-app-layout>