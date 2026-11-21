<x-guest-layout>
    <div class="max-w-md mx-auto mt-10 p-8 bg-white/95 rounded-3xl shadow-2xl border border-white/50 backdrop-blur-md">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-black text-[#4A3728] uppercase tracking-tighter mb-2">Forgot Password</h2>
            <p class="text-sm text-gray-500 font-medium">Enter your email and we'll send you an OTP code to reset your password.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 text-sm rounded-r-xl font-bold">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 text-sm rounded-r-xl font-bold">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-6">
                <label for="email" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border-2 border-gray-200 rounded-2xl px-5 py-3.5 focus:outline-none focus:ring-4 focus:ring-[#4A3728]/10 focus:border-[#4A3728] bg-gray-50 text-gray-800 transition-all font-medium"
                       placeholder="Enter your email">
            </div>

            <div class="flex flex-col gap-4">
                <button type="submit" class="w-full bg-[#4A3728] text-white font-black py-4 rounded-2xl uppercase tracking-widest text-xs hover:bg-[#3A2A1E] hover:shadow-lg transition-all">
                    Send OTP Code
                </button>

                <a href="{{ route('login') }}" class="text-center text-xs text-gray-500 hover:text-[#4A3728] font-bold uppercase tracking-widest transition-colors mt-2">
                    ← Back to Login
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>