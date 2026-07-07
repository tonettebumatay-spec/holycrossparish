<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-6">

            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-5 py-2 bg-white border border-gray-300 rounded-full text-sm font-semibold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    ← Back to Dashboard
                </a>
                <h1 class="text-3xl font-black text-gray-800 tracking-tight uppercase underline decoration-purple-600 underline-offset-4">
                    Appointment Management
                </h1>
                <div class="w-20"></div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Main Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                @if($appointments->isEmpty())
                    <div class="text-center py-20">
                        <div class="inline-flex p-6 bg-purple-50 rounded-full text-purple-600 mb-4">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-700">No Appointments Yet</h2>
                        <p class="text-gray-400 mt-2">Start by adding a new booking for the parish.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="bg-gray-100 text-xs font-semibold uppercase text-gray-500 border-b">
                                <tr>
                                    <th class="px-6 py-4">#</th>
                                    <th class="px-6 py-4">Type</th>
                                    <th class="px-6 py-4">Name</th>
                                    <th class="px-6 py-4">Date</th>
                                    <th class="px-6 py-4">Category</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4">Created</th>
                                    <th class="px-6 py-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($appointments as $index => $app)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 font-medium text-gray-400">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-bold uppercase
                                                @if($app->type == 'Baptism') bg-blue-100 text-blue-800
                                                @elseif($app->type == 'Communion') bg-green-100 text-green-800
                                                @elseif($app->type == 'Confirmation') bg-purple-100 text-purple-800
                                                @elseif($app->type == 'Wedding') bg-pink-100 text-pink-800
                                                @elseif($app->type == 'Funeral') bg-gray-100 text-gray-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ $app->type }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $app->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">
                                            @if($app->appointment_date)
                                                {{ \Carbon\Carbon::parse($app->appointment_date)->format('M d, Y') }}
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">{{ $app->category ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">
                                            @php
                                                $statusColor = match($app->status ?? 'pending') {
                                                    'confirmed' => 'bg-green-100 text-green-800',
                                                    'canceled'  => 'bg-red-100 text-red-800',
                                                    default     => 'bg-yellow-100 text-yellow-800',
                                                };
                                            @endphp
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold uppercase {{ $statusColor }}">
                                                {{ $app->status ?? 'pending' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-400 text-sm">
                                            {{ \Carbon\Carbon::parse($app->created_at)->diffForHumans() }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center space-x-2 flex-wrap gap-1">
                                                @if(($app->status ?? 'pending') !== 'confirmed')
                                                    <form action="{{ route('appointments.update-status', ['type' => strtolower($app->type), 'id' => $app->id]) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button type="submit" class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-xs rounded-full transition" onclick="return confirm('Confirm this appointment?')">
                                                            Confirm
                                                        </button>
                                                    </form>
                                                @endif
                                                @if(($app->status ?? 'pending') !== 'canceled')
                                                    <form action="{{ route('appointments.update-status', ['type' => strtolower($app->type), 'id' => $app->id]) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="canceled">
                                                        <button type="submit" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-full transition" onclick="return confirm('Cancel this appointment?')">
                                                            Cancel
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('appointments.destroy', ['type' => strtolower($app->type), 'id' => $app->id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs rounded-full transition" onclick="return confirm('Delete this appointment permanently?')">
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
                    <div class="px-6 py-4 bg-gray-50 text-sm text-gray-500 border-t">
                        Total: {{ $appointments->count() }} appointment(s)
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>