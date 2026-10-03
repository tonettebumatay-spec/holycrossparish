<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-xl text-gray-800 leading-tight italic uppercase tracking-tighter">
                {{ __('Payment Details') }}
            </h2>

            <a href="{{ route('payments.index') }}" 
               class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-gray-50">
                ← Back to Payments
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[30px] p-8 border border-gray-100">

                <div class="flex items-center justify-between mb-6 pb-6 border-b border-gray-100">
                    <div>
                        <h3 class="text-2xl font-black text-gray-800">₱{{ number_format($payment->amount, 2) }}</h3>
                        <p class="text-xs text-gray-400 font-mono mt-1">{{ $payment->reference_number ?? 'N/A' }}</p>
                    </div>

                    <div>
                        @if($payment->status === 'paid')
                            <span class="px-4 py-2 bg-green-100 text-green-700 rounded-full text-xs font-black uppercase">Paid</span>
                        @elseif($payment->status === 'pending')
                            <span class="px-4 py-2 bg-amber-100 text-amber-700 rounded-full text-xs font-black uppercase">Pending</span>
                        @else
                            <span class="px-4 py-2 bg-red-100 text-red-700 rounded-full text-xs font-black uppercase">{{ $payment->status }}</span>
                        @endif
                    </div>
                </div>

                <!-- Details -->
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Payment Method</p>
                        <p class="text-sm font-bold text-gray-800 uppercase">{{ $payment->payment_method }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Date</p>
                        <p class="text-sm font-bold text-gray-800">
                            {{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y g:i A') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Amount</p>
                        <p class="text-sm font-bold text-gray-800">₱{{ number_format($payment->amount, 2) }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Reference</p>
                        <p class="text-sm font-mono text-gray-800">{{ $payment->reference_number ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- QR Code (kung PayMongo) -->
                @if($source === 'paymongo' && $payment->qr_code_url)
                    <div class="mb-8">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">QR Code</p>
                        <img src="{{ $payment->qr_code_url }}" alt="QR Code" class="w-48 h-48 border border-gray-200 rounded-lg p-2">
                    </div>
                @endif

                <!-- Actions -->
                @if($source === 'payments' && $payment->status === 'pending')
                    <form action="{{ route('payments.markPaid', $payment->id) }}" method="POST" class="mt-6">
                        @csrf
                        <button type="submit" 
                                class="w-full py-4 bg-green-600 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-green-700">
                            ✅ Mark as Paid
                        </button>
                    </form>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>