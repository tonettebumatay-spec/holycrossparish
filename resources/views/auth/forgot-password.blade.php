<x-guest-layout>
    <div class="max-w-md mx-auto mt-10 p-8 bg-white/90 rounded-3xl shadow-xl backdrop-blur-md border border-white/20">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-[#4A3728] uppercase tracking-wider mb-2">Forgot Password</h2>
            <p class="text-sm text-gray-600">Enter your email and we'll send you an OTP code to reset your password.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 text-sm rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 text-sm rounded">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-6">
                <label for="email" class="block text-xs font-black text-[#4A3728] uppercase tracking-widest mb-2">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:border-[#4A3728] bg-white text-gray-800"
                       placeholder="Enter your email">
            </div>

            <div class="flex flex-col gap-3">
                <button type="submit" class="w-full bg-[#4A3728] text-white font-black py-3 rounded-xl uppercase tracking-widest text-xs hover:bg-[#3A2A1E] transition-colors">
                    Send OTP Code
                </button>

                <a href="{{ route('login') }}" class="text-center text-sm text-gray-500 hover:text-[#4A3728] font-semibold transition-colors">
                    Back to Login
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>