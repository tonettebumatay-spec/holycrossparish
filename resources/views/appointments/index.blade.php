<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-10">
            
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
                                    <th class="p-4">Type</th>
                                    <th class="p-4">Primary Name</th>
                                    <th class="p-4">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments as $app)
                                    <tr class="border-b hover:bg-gray-50 transition">
                                        <td class="p-4 font-black text-gray-700">{{ $app->category ?? 'N/A' }}</td>
                                        <td class="p-4 font-medium text-gray-600">
                                            {{ $app->first_name ?? $app->candidate_name ?? $app->groom_name ?? $app->deceased_name ?? 'N/A' }}
                                        </td>
                                        <td class="p-4 text-gray-500">
                                            {{ $app->baptism_date ?? $app->communion_date ?? $app->confirmation_date ?? $app->burial_date ?? ($app->year . '-' . $app->month_day) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>