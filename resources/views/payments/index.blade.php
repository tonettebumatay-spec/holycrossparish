<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-xl text-gray-800 leading-tight italic uppercase tracking-tighter">
                {{ __('Payment Records') }}
            </h2>

            <div class="flex gap-2">
                <!-- ✅ Back to Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-gray-50">
                    ← Back to Dashboard
                </a>

                <a href="{{ route('payments.export', ['method' => $method, 'status' => $status]) }}" 
                   class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg text-xs font-black uppercase tracking-widest hover:bg-green-700">
                    Export CSV
                </a>
            </div>
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

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total</p>
                    <p class="text-2xl font-black text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[10px] font-black uppercase tracking-widest text-green-500">Paid</p>
                    <p class="text-2xl font-black text-green-600">{{ $stats['paid'] }}</p>
                </div>
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[10px] font-black uppercase tracking-widest text-amber-500">Pending</p>
                    <p class="text-2xl font-black text-amber-600">{{ $stats['pending'] }}</p>
                </div>
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[10px] font-black uppercase tracking-widest text-purple-500">Total Amount</p>
                    <p class="text-2xl font-black text-purple-600">₱{{ number_format($stats['total_amount'], 2) }}</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
                <form method="GET" action="{{ route('payments.index') }}" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block mb-1">Search</label>
                        <input type="text" name="search" value="{{ $search }}" 
                               placeholder="Reference number..."
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-gray-800">
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block mb-1">Method</label>
                        <select name="method" class="px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-gray-800">
                            <option value="all" {{ $method == 'all' ? 'selected' : '' }}>All</option>
                            <option value="gcash" {{ $method == 'gcash' ? 'selected' : '' }}>GCash</option>
                            <option value="cash" {{ $method == 'cash' ? 'selected' : '' }}>Cash</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block mb-1">Status</label>
                        <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-gray-800">
                            <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All</option>
                            <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="failed" {{ $status == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="archived" {{ $status == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-lg text-xs font-black uppercase tracking-widest hover:bg-gray-900">
                            Filter
                        </button>
                        <a href="{{ route('payments.index') }}" class="px-6 py-2 bg-gray-100 text-gray-600 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-gray-200">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Payments Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[30px] p-8 border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left">
                        <thead>
                            <tr class="text-[11px] text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Reference</th>
                                <th class="px-4 py-3">Amount</th>
                                <th class="px-4 py-3">Method</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allPayments as $payment)
                                <tr class="border-b border-gray-50 hover:bg-gray-50">
                                    <td class="px-4 py-4 text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($payment['created_at'])->format('M d, Y g:i A') }}
                                    </td>
                                    <td class="px-4 py-4 text-sm font-mono text-gray-800">
                                        {{ $payment['reference_number'] }}
                                    </td>
                                    <td class="px-4 py-4 text-sm font-bold text-gray-800">
                                        ₱{{ number_format($payment['amount'], 2) }}
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($payment['payment_method'] === 'gcash')
                                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-[10px] font-black uppercase">GCash</span>
                                        @else
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase">Cash</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($payment['status'] === 'paid')
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase">Paid</span>
                                        @elseif($payment['status'] === 'pending')
                                            <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-black uppercase">Pending</span>
                                        @elseif($payment['status'] === 'archived')
                                            <span class="px-3 py-1 bg-gray-200 text-gray-600 rounded-full text-[10px] font-black uppercase">Archived</span>
                                        @else
                                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-black uppercase">{{ $payment['status'] }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="inline-flex gap-2">
                                            <!-- View -->
                                            <a href="{{ route('payments.show', ['id' => $payment['id'], 'source' => $payment['source']]) }}" 
                                               class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-[10px] font-black uppercase hover:bg-gray-200">
                                                View
                                            </a>

                                            <!-- ✅ Archive -->
                                            @if($payment['status'] !== 'archived')
                                                <form action="{{ route('payments.archive', ['id' => $payment['id'], 'source' => $payment['source']]) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Archive this payment?')">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="px-3 py-1 bg-amber-100 text-amber-700 rounded-lg text-[10px] font-black uppercase hover:bg-amber-200">
                                                        Archive
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- ✅ Delete -->
                                            <form action="{{ route('payments.destroy', ['id' => $payment['id'], 'source' => $payment['source']]) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Delete this payment permanently?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-[10px] font-black uppercase hover:bg-red-200">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-14 text-center">
                                        <p class="text-gray-400 italic font-medium uppercase tracking-widest text-sm">
                                            No payments found.
                                        </p>
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