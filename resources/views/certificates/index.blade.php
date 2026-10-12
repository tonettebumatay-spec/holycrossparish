<x-app-layout>
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

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 font-black uppercase text-[10px] tracking-widest rounded-r-xl">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 font-black uppercase text-[10px] tracking-widest rounded-r-xl">
                    {{ session('error') }}
                </div>
            @endif

            {{-- ✅ Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total</p>
                    <p class="text-2xl font-black text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[10px] font-black uppercase tracking-widest text-amber-500">Pending</p>
                    <p class="text-2xl font-black text-amber-600">{{ $stats['pending'] }}</p>
                </div>
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[10px] font-black uppercase tracking-widest text-green-500">Completed</p>
                    <p class="text-2xl font-black text-green-600">{{ $stats['completed'] }}</p>
                </div>
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[10px] font-black uppercase tracking-widest text-red-500">Cancelled</p>
                    <p class="text-2xl font-black text-red-600">{{ $stats['cancelled'] }}</p>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ACTIVE CERTIFICATES (Pending)                                --}}
            {{-- ============================================================ --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[30px] p-8 border border-gray-100 mb-8">
                <div class="flex items-center justify-between mb-6 border-b border-gray-50 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold uppercase bg-amber-100 text-amber-800">
                            Active
                        </span>
                        <h3 class="text-lg font-black text-gray-800 uppercase tracking-widest">Pending Requests</h3>
                    </div>
                    <span class="text-xs text-gray-500 font-semibold">Total: {{ $activeCertificates->count() }}</span>
                </div>

                @if($activeCertificates->isEmpty())
                    <div class="text-center py-14">
                        <div class="bg-gray-50 p-5 rounded-full inline-flex mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-gray-400 italic font-medium uppercase tracking-widest text-sm">No pending certificate requests.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left">
                            <thead>
                                <tr class="text-[11px] text-gray-400 uppercase tracking-widest">
                                    <th class="px-4 py-3">Full Name</th>
                                    <th class="px-4 py-3">Type</th>
                                    <th class="px-4 py-3">Contact</th>
                                    <th class="px-4 py-3">Scheduled</th>
                                    <th class="px-4 py-3">Request Date</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeCertificates as $certificate)
                                    <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-4">
                                            <div class="font-medium text-gray-800">{{ $certificate->full_name }}</div>
                                            @if($certificate->email)
                                                <div class="text-[11px] text-gray-400">{{ $certificate->email }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-gray-600">{{ $certificate->certificate_type }}</td>
                                        <td class="px-4 py-4 text-gray-600 text-sm">
                                            {{ $certificate->contact_number ?? '—' }}
                                        </td>
                                        <td class="px-4 py-4">
                                            @if($certificate->appointment_date)
                                                <div class="flex flex-col text-xs">
                                                    <span class="font-semibold text-gray-900">
                                                        {{ \Carbon\Carbon::parse($certificate->appointment_date)->format('M d, Y') }}
                                                    </span>
                                                    @if($certificate->appointment_time)
                                                        <span class="text-gray-600">
                                                            {{ \Carbon\Carbon::parse($certificate->appointment_time)->format('g:i A') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs italic text-gray-400">Not scheduled</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-gray-600 text-sm">{{ \Carbon\Carbon::parse($certificate->request_date)->format('M d, Y') }}</td>
                                        <td class="px-4 py-4 text-right">
                                            <div class="inline-flex gap-2">
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
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- ============================================================ --}}
            {{-- ARCHIVED CERTIFICATES (Completed + Cancelled)               --}}
            {{-- ============================================================ --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[30px] p-8 border border-gray-100">
                <div class="flex items-center justify-between mb-6 border-b border-gray-50 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold uppercase bg-gray-200 text-gray-700">
                            Archive
                        </span>
                        <h3 class="text-lg font-black text-gray-800 uppercase tracking-widest">Past Requests</h3>
                    </div>
                    <span class="text-xs text-gray-500 font-semibold">Total: {{ $archivedCertificates->count() }}</span>
                </div>

                @if($archivedCertificates->isEmpty())
                    <div class="text-center py-14">
                        <div class="bg-gray-50 p-5 rounded-full inline-flex mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                        </div>
                        <p class="text-gray-400 italic font-medium uppercase tracking-widest text-sm">No archived certificates yet.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left">
                            <thead>
                                <tr class="text-[11px] text-gray-400 uppercase tracking-widest">
                                    <th class="px-4 py-3">Full Name</th>
                                    <th class="px-4 py-3">Type</th>
                                    <th class="px-4 py-3">Contact</th>
                                    <th class="px-4 py-3">Request Date</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($archivedCertificates as $certificate)
                                    <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors opacity-80">
                                        <td class="px-4 py-4">
                                            <div class="font-medium text-gray-800">{{ $certificate->full_name }}</div>
                                            @if($certificate->email)
                                                <div class="text-[11px] text-gray-400">{{ $certificate->email }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-gray-600">{{ $certificate->certificate_type }}</td>
                                        <td class="px-4 py-4 text-gray-600 text-sm">
                                            {{ $certificate->contact_number ?? '—' }}
                                        </td>
                                        <td class="px-4 py-4 text-gray-600 text-sm">{{ \Carbon\Carbon::parse($certificate->request_date)->format('M d, Y') }}</td>
                                        <td class="px-4 py-4">
                                            @if($certificate->status === 'completed')
                                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-[10px] font-black uppercase tracking-tighter">Completed</span>
                                            @else
                                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-[10px] font-black uppercase tracking-tighter">Cancelled</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <form action="{{ route('certificates.destroy', $certificate->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this certificate permanently? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-[11px] font-black uppercase tracking-widest">
                                                    🗑 Delete
                                                </button>
                                            </form>
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