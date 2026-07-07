<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-10">

            <!-- ===== DEBUG SECTION (remove after fixing) ===== -->
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded-r-lg" role="alert">
                <p class="font-bold">🐞 Debug Info:</p>
                <p>Number of appointments passed to view: <span class="font-mono bg-yellow-200 px-2 py-1 rounded">{{ $appointments->count() }}</span></p>
                @if($appointments->isNotEmpty())
                    <p>First appointment: <span class="font-mono">{{ $appointments->first()->name ?? 'N/A' }}</span> 
                       ({{ $appointments->first()->type ?? 'N/A' }}) 
                       – Date: {{ $appointments->first()->appointment_date ?? 'N/A' }}</p>
                    <p>Last appointment: <span class="font-mono">{{ $appointments->last()->name ?? 'N/A' }}</span> 
                       ({{ $appointments->last()->type ?? 'N/A' }}) 
                       – Date: {{ $appointments->last()->appointment_date ?? 'N/A' }}</p>
                @else
                    <p>No appointments found in the collection.</p>
                @endif
            </div>
            <!-- ===== END DEBUG ===== -->

            <div class="flex justify-between items-center mb-10">
                <a href="{{ route('dashboard') }}" class="border-2 border-gray-200 rounded-full px-6 py-2 text-xs font-black uppercase tracking-widest text-gray-700 hover:bg-gray-100 transition shadow-sm bg-white">
                    ← Back to Dashboard
                </a>
                <h1 class="text-4xl font-black text-gray-800 tracking-tighter italic uppercase underline decoration-purple-600 decoration-4 underline-offset-8">
                    Appointment Management
                </h1>
                <div class="w-32"></div>
            </div>

            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 p-12">
                @if($appointments->isEmpty())
                    <div class="text-center py-20">
                        <div class="mb-6 inline-flex p-6 bg-purple-50 rounded-full text-purple-600">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-black text-gray-800 uppercase mb-2">No Appointments Yet</h2>
                        <p class="text-gray-400 font-medium mb-8 uppercase tracking-widest text-xs">Start by adding a new booking for the parish.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-xs uppercase text-gray-500 border-b">
                                    <th class="p-4 font-semibold">#</th>
                                    <th class="p-4 font-semibold">Type</th>
                                    <th class="p-4 font-semibold">Name</th>
                                    <th class="p-4 font-semibold">Date</th>
                                    <th class="p-4 font-semibold">Category</th>
                                    <th class="p-4 font-semibold">Remarks</th>
                                    <th class="p-4 font-semibold">Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments as $index => $app)
                                    <tr class="border-b hover:bg-gray-50 transition">
                                        <td class="p-4 text-gray-400">{{ $index + 1 }}</td>
                                        <td class="p-4 font-semibold">
                                            <span class="px-2 py-1 rounded-full text-xs font-bold uppercase whitespace-nowrap
                                                @if($app->type == 'Baptism') bg-blue-100 text-blue-800
                                                @elseif($app->type == 'Communion') bg-green-100 text-green-800
                                                @elseif($app->type == 'Confirmation') bg-purple-100 text-purple-800
                                                @elseif($app->type == 'Wedding') bg-pink-100 text-pink-800
                                                @elseif($app->type == 'Funeral') bg-gray-100 text-gray-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ $app->type ?? 'Unknown' }}
                                            </span>
                                        </td>
                                        <td class="p-4 font-medium">{{ $app->name ?? 'N/A' }}</td>
                                        <td class="p-4">
                                            @if($app->appointment_date)
                                                {{ \Carbon\Carbon::parse($app->appointment_date)->format('M d, Y') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="p-4">{{ $app->category ?? 'N/A' }}</td>
                                        <td class="p-4 text-gray-500 max-w-xs truncate">{{ $app->remarks ?? '' }}</td>
                                        <td class="p-4 text-gray-400 text-sm">
                                            @if($app->created_at)
                                                {{ \Carbon\Carbon::parse($app->created_at)->diffForHumans() }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 text-sm text-gray-500 text-center">
                        Total: {{ $appointments->count() }} appointment(s)
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>