<x-guest-layout>
    <div class="min-h-screen w-full flex items-center justify-center bg-cover bg-center bg-no-repeat" 
         style="background-image: url('https://lh3.googleusercontent.com/gps-cs-s/ANWiy9RY-VjQ8i7rGQqFuXRfcJUgl_QgVphhIa373BUYFUaoGX5QD2mCXjkV9C63Pq8lLEQ3oIgxUZVPR9FbSllwI9QvSlSJMgWr8HDWR0Kukf5_QGOfhnBUoG89T7xpTPdwd-XjVuk=s680-w680-h510-rw');">
        
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>

        <div class="relative z-10 w-full max-w-md mx-4 p-8 bg-white/95 rounded-3xl shadow-2xl border border-white/50">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-black text-[#4A3728] uppercase tracking-tighter mb-2">Set New Password</h2>
                <p class="text-sm text-gray-500 font-medium">Enter your new password below.</p>
            </div>

            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 text-sm rounded-r-xl font-bold">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update-otp') }}">
                @csrf
                
                <div class="mb-4">
                    <label for="password" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">New Password</label>
                    <input id="password" type="password" name="password" required
                           class="w-full border-2 border-gray-200 rounded-2xl px-5 py-3.5 focus:outline-none focus:ring-4 focus:ring-[#4A3728]/10 focus:border-[#4A3728] bg-gray-50 text-gray-800 transition-all font-medium"
                           placeholder="Enter new password">
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Confirm New Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="w-full border-2 border-gray-200 rounded-2xl px-5 py-3.5 focus:outline-none focus:ring-4 focus:ring-[#4A3728]/10 focus:border-[#4A3728] bg-gray-50 text-gray-800 transition-all font-medium"
                           placeholder="Confirm new password">
                </div>

                <div class="flex flex-col gap-4">
                    <button type="submit" class="w-full bg-[#4A3728] text-white font-black py-4 rounded-2xl uppercase tracking-widest text-xs hover:bg-[#3A2A1E] hover:shadow-lg transition-all">
                        Save New Password
                    </button>

                    <a href="{{ route('login') }}" class="text-center text-xs text-gray-500 hover:text-[#4A3728] font-bold uppercase tracking-widest transition-colors mt-2">
                        ← Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>