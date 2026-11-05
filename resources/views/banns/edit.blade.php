<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-6">

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
                <form method="POST" action="{{ route('banns.update', $bann->id) }}" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Dates Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Wedding Date *</label>
                            <input type="date" name="wedding_date" value="{{ old('wedding_date', $bann->wedding_date->format('Y-m-d')) }}" required class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Banns Date *</label>
                            <input type="date" name="banns_date" value="{{ old('banns_date', $bann->banns_date->format('Y-m-d')) }}" required class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Status</label>
                            <select name="status" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">
                                <option value="active" {{ $bann->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="expired" {{ $bann->status == 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="completed" {{ $bann->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                    </div>

                    <!-- Publication Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Publication Start</label>
                            <input type="date" name="publication_start" value="{{ old('publication_start', $bann->publication_start?->format('Y-m-d')) }}" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Publication End</label>
                            <input type="date" name="publication_end" value="{{ old('publication_end', $bann->publication_end?->format('Y-m-d')) }}" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">
                        </div>
                    </div>

                    <!-- GROOM & BRIDE DETAILS -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <!-- GROOM COLUMN -->
                        <div class="space-y-4 border-r border-gray-100 pr-0 md:pr-8">
                            <h3 class="text-lg font-black text-[#4A3728] uppercase border-b pb-2">Groom's Details</h3>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Full Name *</label>
                                <input type="text" name="groom_name" value="{{ old('groom_name', $bann->groom_name) }}" required class="w-full border-2 border-gray-300 rounded-xl px-4 py-2 text-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Date of Birth</label>
                                    <input type="text" name="groom_dob" value="{{ old('groom_dob', $bann->groom_dob) }}" class="w-full border-2 border-gray-300 rounded-xl px-4 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Place of Birth</label>
                                    <input type="text" name="groom_pob" value="{{ old('groom_pob', $bann->groom_pob) }}" class="w-full border-2 border-gray-300 rounded-xl px-4 py-2 text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Father's Name</label>
                                <input type="text" name="groom_father" value="{{ old('groom_father', $bann->groom_father) }}" class="w-full border-2 border-gray-300 rounded-xl px-4 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Mother's Maiden Name</label>
                                <input type="text" name="groom_mother" value="{{ old('groom_mother', $bann->groom_mother) }}" class="w-full border-2 border-gray-300 rounded-xl px-4 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Address</label>
                                <input type="text" name="groom_address" value="{{ old('groom_address', $bann->groom_address) }}" class="w-full border-2 border-gray-300 rounded-xl px-4 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Picture (2x2)</label>
                                @if($bann->groom_picture)
                                    <img src="{{ asset('storage/' . $bann->groom_picture) }}" class="h-16 w-16 object-cover rounded mb-2">
                                @endif
                                <input type="file" name="groom_picture" accept="image/*" class="w-full text-sm text-gray-500">
                            </div>
                        </div>

                        <!-- BRIDE COLUMN -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-black text-[#4A3728] uppercase border-b pb-2">Bride's Details</h3>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Full Name *</label>
                                <input type="text" name="bride_name" value="{{ old('bride_name', $bann->bride_name) }}" required class="w-full border-2 border-gray-300 rounded-xl px-4 py-2 text-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Date of Birth</label>
                                    <input type="text" name="bride_dob" value="{{ old('bride_dob', $bann->bride_dob) }}" class="w-full border-2 border-gray-300 rounded-xl px-4 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Place of Birth</label>
                                    <input type="text" name="bride_pob" value="{{ old('bride_pob', $bann->bride_pob) }}" class="w-full border-2 border-gray-300 rounded-xl px-4 py-2 text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Father's Name</label>
                                <input type="text" name="bride_father" value="{{ old('bride_father', $bann->bride_father) }}" class="w-full border-2 border-gray-300 rounded-xl px-4 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Mother's Maiden Name</label>
                                <input type="text" name="bride_mother" value="{{ old('bride_mother', $bann->bride_mother) }}" class="w-full border-2 border-gray-300 rounded-xl px-4 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Address</label>
                                <input type="text" name="bride_address" value="{{ old('bride_address', $bann->bride_address) }}" class="w-full border-2 border-gray-300 rounded-xl px-4 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Picture (2x2)</label>
                                @if($bann->bride_picture)
                                    <img src="{{ asset('storage/' . $bann->bride_picture) }}" class="h-16 w-16 object-cover rounded mb-2">
                                @endif
                                <input type="file" name="bride_picture" accept="image/*" class="w-full text-sm text-gray-500">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Notes (Optional)</label>
                        <textarea name="notes" rows="3" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">{{ old('notes', $bann->notes) }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
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