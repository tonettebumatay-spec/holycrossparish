<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-6">

            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-5 py-2 bg-white border border-gray-300 rounded-full text-sm font-semibold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    ← Back to Dashboard
                </a>
                <h1 class="text-3xl font-black text-gray-800 uppercase tracking-tight">
                    💍 Marriage Banns
                </h1>
                <a href="{{ route('banns.create') }}" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-full text-xs font-black uppercase tracking-widest">
                    + Add Banns
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total</p>
                    <p class="text-2xl font-black text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[10px] font-black uppercase tracking-widest text-green-500">Active</p>
                    <p class="text-2xl font-black text-green-600">{{ $stats['active'] }}</p>
                </div>
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[10px] font-black uppercase tracking-widest text-amber-500">Expired</p>
                    <p class="text-2xl font-black text-amber-600">{{ $stats['expired'] }}</p>
                </div>
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[10px] font-black uppercase tracking-widest text-blue-500">Completed</p>
                    <p class="text-2xl font-black text-blue-600">{{ $stats['completed'] }}</p>
                </div>
            </div>

            <!-- Filter -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
                <form method="GET" action="{{ route('banns.index') }}" class="flex gap-3 items-center">
                    <select name="status" class="border-2 border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:border-purple-500">
                        <option value="">All Status</option>
                        <option value="active" {{ $statusFilter == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="expired" {{ $statusFilter == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="completed" {{ $statusFilter == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-full text-xs font-black uppercase">
                        Filter
                    </button>
                    <a href="{{ route('banns.index') }}" class="bg-gray-100 text-gray-600 px-6 py-2 rounded-full text-xs font-black uppercase">
                        Reset
                    </a>
                </form>
            </div>

            <!-- Banns Table -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-100 text-xs font-semibold uppercase text-gray-500 border-b">
                            <tr>
                                <th class="px-6 py-4">#</th>
                                <th class="px-6 py-4">Groom</th>
                                <th class="px-6 py-4">Bride</th>
                                <th class="px-6 py-4">Wedding Date</th>
                                <th class="px-6 py-4">Banns Date</th>
                                <th class="px-6 py-4">Expires</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($banns as $index => $bann)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-gray-400">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $bann->groom_name }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $bann->bride_name }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $bann->wedding_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $bann->banns_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $bann->expires_at ? $bann->expires_at->format('M d, Y') : 'N/A' }}</td>
                                    <td class="px-6 py-4">
                                        @if($bann->status === 'active')
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase">Active</span>
                                        @elseif($bann->status === 'expired')
                                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-black uppercase">Expired</span>
                                        @else
                                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-[10px] font-black uppercase">Completed</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex gap-2">
                                            <a href="{{ route('banns.edit', $bann->id) }}" class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-[10px] font-black uppercase hover:bg-blue-200">
                                                Edit
                                            </a>
                                            <form action="{{ route('banns.destroy', $bann->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this bann?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-[10px] font-black uppercase hover:bg-red-200">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-14 text-center">
                                        <p class="text-gray-400 italic">No marriage banns yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>