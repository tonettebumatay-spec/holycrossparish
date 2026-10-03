<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-2xl mx-auto px-6">

            <div class="flex justify-between items-center mb-8">
                <a href="{{ route('banns.index') }}" class="inline-flex items-center px-5 py-2 bg-white border border-gray-300 rounded-full text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    ← Back to Banns
                </a>
                <h1 class="text-2xl font-black text-gray-800 uppercase">Edit Marriage Banns</h1>
                <div class="w-20"></div>
            </div>

            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-lg p-8">
                <form method="POST" action="{{ route('banns.update', $bann->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Groom's Full Name *</label>
                        <input type="text" name="groom_name" value="{{ old('groom_name', $bann->groom_name) }}" required
                               class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Bride's Full Name *</label>
                        <input type="text" name="bride_name" value="{{ old('bride_name', $bann->bride_name) }}" required
                               class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Wedding Date *</label>
                        <input type="date" name="wedding_date" value="{{ old('wedding_date', $bann->wedding_date->format('Y-m-d')) }}" required
                               class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Banns Date *</label>
                        <input type="date" name="banns_date" value="{{ old('banns_date', $bann->banns_date->format('Y-m-d')) }}" required
                               class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Status *</label>
                        <select name="status" required class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">
                            <option value="active" {{ $bann->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="expired" {{ $bann->status == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="completed" {{ $bann->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Notes</label>
                        <textarea name="notes" rows="3"
                                  class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">{{ old('notes', $bann->notes) }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <a href="{{ route('banns.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-gray-200">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-black uppercase tracking-widest">
                            Update Banns
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>