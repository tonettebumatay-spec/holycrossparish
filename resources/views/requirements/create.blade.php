<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-2xl mx-auto px-6">

            <div class="flex justify-between items-center mb-8">
                <a href="{{ route('requirements.index') }}" class="inline-flex items-center px-5 py-2 bg-white border border-gray-300 rounded-full text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    ← Back to Requirements
                </a>
                <h1 class="text-2xl font-black text-gray-800 uppercase">Add Requirement</h1>
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
                <form method="POST" action="{{ route('requirements.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Sacrament *</label>
                        <select name="sacrament_type" required class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">
                            <option value="">Select Sacrament</option>
                            <option value="baptism" {{ old('sacrament_type') == 'baptism' ? 'selected' : '' }}>Baptism</option>
                            <option value="communion" {{ old('sacrament_type') == 'communion' ? 'selected' : '' }}>Communion</option>
                            <option value="confirmation" {{ old('sacrament_type') == 'confirmation' ? 'selected' : '' }}>Confirmation</option>
                            <option value="wedding" {{ old('sacrament_type') == 'wedding' ? 'selected' : '' }}>Wedding</option>
                            <option value="funeral" {{ old('sacrament_type') == 'funeral' ? 'selected' : '' }}>Funeral</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Requirement Name *</label>
                        <input type="text" name="requirement_name" value="{{ old('requirement_name') }}" required placeholder="e.g. Birth Certificate (PSA)"
                               class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Description</label>
                        <textarea name="description" rows="3" placeholder="Optional details..."
                                  class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">{{ old('description') }}</textarea>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_required" id="is_required" value="1" {{ old('is_required', true) ? 'checked' : '' }}
                               class="w-5 h-5 text-purple-600 rounded">
                        <label for="is_required" class="text-sm font-semibold text-gray-700">Required</label>
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                               class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <a href="{{ route('requirements.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-gray-200">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-black uppercase tracking-widest">
                            Save Requirement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>