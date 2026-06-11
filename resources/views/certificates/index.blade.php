<x-app-layout>
    <div x-data="{ openModal: false }">
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <h2 class="font-black text-xl text-gray-800 leading-tight italic uppercase tracking-tighter">
                    {{ __('Certificate Requests') }}
                </h2>

                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-gray-50 transition-all shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
            </div>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Back to Dashboard -->
                <div class="mb-6">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-200 text-gray-600 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-gray-50 transition-all shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Dashboard
                    </a>
                </div>

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 font-black uppercase text-[10px] tracking-widest rounded-r-xl">
                        {{ session('success') }}
                    </div>
                @endif


                <div class="bg-white overflow-hidden shadow-sm sm:rounded-[30px] p-10 border border-gray-100">
                    <div class="flex items-center justify-between mb-8 border-b border-gray-50 pb-6">
                        <div>
                            <h3 class="text-lg font-black text-gray-800 uppercase tracking-widest">Certificate Requests</h3>
                            <p class="text-xs text-gray-400 font-bold uppercase mt-1">Admin Panel</p>
                        </div>
                        <span class="px-4 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-tighter">Live System</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left">
                            <thead>
                                <tr class="text-[11px] text-gray-400 uppercase tracking-widest">
                                    <th class="px-4 py-3">Full Name</th>
                                    <th class="px-4 py-3">Type</th>
                                    <th class="px-4 py-3">Request Date</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($certificates as $certificate)
                                    <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-4 font-medium text-gray-800">{{ $certificate->full_name }}</td>
                                        <td class="px-4 py-4 text-gray-600">{{ $certificate->certificate_type }}</td>
                                        <td class="px-4 py-4 text-gray-600">{{ \Carbon\Carbon::parse($certificate->request_date)->format('M d, Y') }}</td>
                                        <td class="px-4 py-4">
                                            @if($certificate->status === 'pending')
                                                <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-[10px] font-black uppercase tracking-tighter">Pending</span>
                                            @elseif($certificate->status === 'completed')
                                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-[10px] font-black uppercase tracking-tighter">Completed</span>
                                            @else
                                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-[10px] font-black uppercase tracking-tighter">Cancelled</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <div class="inline-flex gap-2">
                                                @if($certificate->status === 'pending')
                                                    <form action="{{ route('certificates.complete', $certificate->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-[11px] font-black uppercase tracking-widest">
                                                            Complete
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('certificates.cancel', $certificate->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-[11px] font-black uppercase tracking-widest">
                                                            Cancel
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-[11px] text-gray-400 font-black uppercase">No actions</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-14 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="bg-gray-50 p-5 rounded-full mb-4">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <p class="text-gray-400 italic font-medium uppercase tracking-widest text-sm">No certificate requests yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

