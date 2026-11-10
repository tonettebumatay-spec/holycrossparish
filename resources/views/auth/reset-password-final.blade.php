<x-guest-layout>
    <div class="max-w-md mx-auto mt-10 p-8 bg-white/90 rounded-3xl shadow-xl backdrop-blur-md border border-white/20">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-[#4A3728] uppercase tracking-wider mb-2">Set New Password</h2>
            <p class="text-sm text-gray-600">Enter your new password below.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 text-sm rounded">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update-otp') }}">
            @csrf
            <div class="mb-4">
                <label for="password" class="block text-xs font-black text-[#4A3728] uppercase tracking-widest mb-2">New Password</label>
                <input id="password" type="password" name="password" required
                       class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:border-[#4A3728] bg-white text-gray-800"
                       placeholder="Enter new password">
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-xs font-black text-[#4A3728] uppercase tracking-widest mb-2">Confirm New Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:border-[#4A3728] bg-white text-gray-800"
                       placeholder="Confirm new password">
            </div>

            <button type="submit" class="w-full bg-[#4A3728] text-white font-black py-3 rounded-xl uppercase tracking-widest text-xs hover:bg-[#3A2A1E] transition-colors">
                Save New Password
            </button>
        </form>
    </div>
</x-guest-layout>