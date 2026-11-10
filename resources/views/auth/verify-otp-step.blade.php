<x-guest-layout>
    <div class="max-w-md mx-auto mt-10 p-8 bg-white/90 rounded-3xl shadow-xl backdrop-blur-md border border-white/20">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-[#4A3728] uppercase tracking-wider mb-2">Verify OTP</h2>
            <p class="text-sm text-gray-600">Enter the 6-digit code sent to your email.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 text-sm rounded">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.verify-otp') }}">
            @csrf
            
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="mb-6">
                <!-- ✅ INALIS ANG tracking-[0.5em] AT GINAWANG text-xl -->
                <input id="otp" type="text" name="otp" required
                       class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:border-[#4A3728] bg-white text-gray-800 text-center text-xl font-bold"
                       placeholder="000000" maxlength="6" value="{{ old('otp') }}">
            </div>

            <button type="submit" class="w-full bg-[#4A3728] text-white font-black py-3 rounded-xl uppercase tracking-widest text-xs hover:bg-[#3A2A1E] transition-colors">
                Verify OTP
            </button>
        </form>
    </div>
</x-guest-layout>