<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-6">

            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-5 py-2 bg-white border border-gray-300 rounded-full text-sm font-semibold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    ← Back to Dashboard
                </a>
                <h1 class="text-3xl font-black text-gray-800 uppercase tracking-tight">
                    Booking Requirements
                </h1>
                <a href="{{ route('requirements.create') }}" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-full text-xs font-black uppercase tracking-widest">
                    + Add Requirement
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filter -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
                <form method="GET" action="{{ route('requirements.index') }}" class="flex gap-3 items-center">
                    <label class="text-xs font-black uppercase tracking-widest text-gray-500">Filter by Sacrament:</label>
                    <select name="sacrament" class="border-2 border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:border-purple-500">
                        <option value="">All Sacraments</option>
                        <option value="baptism" {{ $sacramentFilter == 'baptism' ? 'selected' : '' }}>Baptism</option>
                        <option value="communion" {{ $sacramentFilter == 'communion' ? 'selected' : '' }}>Communion</option>
                        <option value="confirmation" {{ $sacramentFilter == 'confirmation' ? 'selected' : '' }}>Confirmation</option>
                        <option value="wedding" {{ $sacramentFilter == 'wedding' ? 'selected' : '' }}>Wedding</option>
                        <option value="funeral" {{ $sacramentFilter == 'funeral' ? 'selected' : '' }}>Funeral</option>
                    </select>
                    <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-full text-xs font-black uppercase">
                        Filter
                    </button>
                    <a href="{{ route('requirements.index') }}" class="bg-gray-100 text-gray-600 px-6 py-2 rounded-full text-xs font-black uppercase">
                        Reset
                    </a>
                </form>
            </div>

            <!-- Requirements by Sacrament -->
            @forelse($requirements as $sacrament => $items)
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-6">
                    <div class="px-6 py-4 bg-gray-50 border-b flex items-center justify-between">
                        <h2 class="text-lg font-black text-gray-800 uppercase tracking-tight">
                            {{ ucfirst($sacrament) }}
                        </h2>
                        <span class="text-xs text-gray-500 font-semibold">Total: {{ $items->count() }}</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-100 text-xs font-semibold uppercase text-gray-500 border-b">
                                <tr>
                                    <th class="px-6 py-4">#</th>
                                    <th class="px-6 py-4">Requirement</th>
                                    <th class="px-6 py-4">Description</th>
                                    <th class="px-6 py-4">Required?</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($items as $index => $req)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-gray-400">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 font-bold text-gray-900">{{ $req->requirement_name }}</td>
                                        <td class="px-6 py-4 text-gray-600 text-xs">{{ $req->description ?? '—' }}</td>
                                        <td class="px-6 py-4">
                                            @if($req->is_required)
                                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-black uppercase">Required</span>
                                            @else
                                                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-[10px] font-black uppercase">Optional</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($req->is_active)
                                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase">Active</span>
                                            @else
                                                <span class="px-3 py-1 bg-gray-200 text-gray-600 rounded-full text-[10px] font-black uppercase">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="inline-flex gap-2">
                                                <a href="{{ route('requirements.edit', $req->id) }}" class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-[10px] font-black uppercase hover:bg-blue-200">
                                                    Edit
                                                </a>
                                                <form action="{{ route('requirements.toggle', $req->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-[10px] font-black uppercase hover:bg-yellow-200">
                                                        {{ $req->is_active ? 'Deactivate' : 'Activate' }}
                                                    </button>
                                                </form>
                                                <form action="{{ route('requirements.destroy', $req->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this requirement?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-[10px] font-black uppercase hover:bg-red-200">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                    <h3 class="text-lg font-bold text-gray-700">No Requirements Yet</h3>
                    <p class="text-gray-400 mt-2">Add your first requirement to get started.</p>
                    <a href="{{ route('requirements.create') }}" class="inline-block mt-4 px-6 py-2 bg-purple-600 text-white rounded-full text-xs font-black uppercase">
                        + Add Requirement
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>