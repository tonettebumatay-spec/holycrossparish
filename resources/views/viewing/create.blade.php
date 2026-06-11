<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-10">
            <div class="flex justify-between items-center mb-10 gap-6">
                <a href="{{ route('dashboard') }}" class="border-2 border-gray-200 rounded-full px-6 py-2 text-xs font-black uppercase tracking-widest text-gray-700 hover:bg-gray-100 transition shadow-sm bg-white inline-flex items-center gap-2">
                    ← Back to Dashboard
                </a>

                <h1 class="text-4xl font-black text-gray-800 tracking-tighter italic uppercase underline decoration-indigo-600 decoration-4 underline-offset-8 text-center">
                    Add Viewing
                </h1>

                <div class="w-32"></div>
            </div>

            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 p-10">
                <form method="POST" action="{{ route('viewing.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div>
                            <div class="mb-5">
                                <label class="block text-xs font-black uppercase tracking-widest text-gray-600 mb-2" for="title">Title</label>
                                <input
                                    id="title"
                                    name="title"
                                    type="text"
                                    value="{{ old('title') }}"
                                    class="w-full px-5 py-3 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="e.g., Holy Week Program"
                                    required
                                />
                                @error('title')
                                    <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-5">
                                <label class="block text-xs font-black uppercase tracking-widest text-gray-600 mb-2" for="description">Description</label>
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="6"
                                    class="w-full px-5 py-3 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Write a short description..."
                                    required
                                >{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            @if($errors->any())
                                <div class="mb-5 p-4 bg-red-50 border border-red-100 rounded-2xl">
                                    <p class="text-xs font-black uppercase tracking-widest text-red-700">Please fix the errors below:</p>
                                    <ul class="mt-2 text-sm text-red-600 font-medium">
                                        @foreach($errors->all() as $error)
                                            <li>• {{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div>
                            <div class="mb-5">
                                <label class="block text-xs font-black uppercase tracking-widest text-gray-600 mb-2" for="image">Image Upload</label>
                                <input
                                    id="image"
                                    name="image"
                                    type="file"
                                    accept="image/*"
                                    class="w-full px-5 py-3 border border-gray-200 rounded-2xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    required
                                />
                                <p class="mt-2 text-[11px] text-gray-400 font-bold uppercase tracking-widest">
                                    Stored as Base64 in the database.
                                </p>
                                @error('image')
                                    <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="bg-indigo-50 border border-indigo-100 rounded-[28px] p-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-full bg-indigo-600 text-white flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z" />
                                            <path d="m7 15 2-2 3 3 4-4 2 2" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Preview Tip</h3>
                                        <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">After saving, the image shows as a card preview.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 flex items-center justify-end gap-4">
                        <a href="{{ route('viewing.index') }}" class="px-6 py-3 rounded-2xl border border-gray-200 text-gray-600 text-xs font-black uppercase tracking-widest hover:bg-gray-50 transition shadow-sm">
                            Cancel
                        </a>

                        <button type="submit" class="px-8 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase tracking-widest transition shadow-sm">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

