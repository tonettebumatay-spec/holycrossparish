<x-guest-layout>
    <div class="min-h-screen w-full flex items-center justify-center bg-cover bg-center bg-no-repeat" 
         style="background-image: url('https://lh3.googleusercontent.com/gps-cs-s/ANWiy9RY-VjQ8i7rGQqFuXRfcJUgl_QgVphhIa373BUYFUaoGX5QD2mCXjkV9C63Pq8lLEQ3oIgxUZVPR9FbSllwI9QvSlSJMgWr8HDWR0Kukf5_QGOfhnBUoG89T7xpTPdwd-XjVuk=s680-w680-h510-rw');">
        
        <div class="relative z-10 w-full max-w-md mx-4 p-8 bg-white/95 rounded-3xl shadow-2xl border border-white/50">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-black text-[#4A3728] uppercase tracking-tighter mb-2">Enter OTP</h2>
                <p class="text-sm text-gray-500 font-medium">We sent a 6-digit code to your email. It expires in 15 minutes.</p>
            </div>

            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 text-sm rounded-r-xl font-bold">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update-otp') }}">
                @csrf

                <!-- OTP Code -->
                <div class="mb-6">
                    <label for="otp" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2 ml-1 text-center">OTP Code</label>
                    <input id="otp" type="text" name="otp" required
                           class="w-full border-2 border-gray-200 rounded-2xl px-5 py-3.5 focus:outline-none focus:ring-4 focus:ring-[#4A3728]/10 focus:border-[#4A3728] bg-gray-50 text-gray-800 text-center text-2xl tracking-[0.5em] font-black"
                           placeholder="000000" maxlength="6">
                </div>

                <!-- New Password -->
                <div class="mb-4" x-data="{ show: false }">
                    <label for="password" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">New Password</label>
                    <div class="relative">
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required
                               class="w-full border-2 border-gray-200 rounded-2xl px-5 py-3.5 pr-12 focus:outline-none focus:ring-4 focus:ring-[#4A3728]/10 focus:border-[#4A3728] bg-gray-50 text-gray-800 transition-all font-medium"
                               placeholder="Enter new password">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-[#4A3728] focus:outline-none transition-colors">
                            <!-- Eye Icon -->
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <!-- Eye Slash Icon -->
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-6" x-data="{ show: false }">
                    <label for="password_confirmation" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Confirm New Password</label>
                    <div class="relative">
                        <input id="password_confirmation" :type="show ? 'text' : 'password'" name="password_confirmation" required
                               class="w-full border-2 border-gray-200 rounded-2xl px-5 py-3.5 pr-12 focus:outline-none focus:ring-4 focus:ring-[#4A3728]/10 focus:border-[#4A3728] bg-gray-50 text-gray-800 transition-all font-medium"
                               placeholder="Confirm new password">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-[#4A3728] focus:outline-none transition-colors">
                            <!-- Eye Icon -->
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <!-- Eye Slash Icon -->
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <button type="submit" class="w-full bg-[#4A3728] text-white font-black py-4 rounded-2xl uppercase tracking-widest text-xs hover:bg-[#3A2A1E] hover:shadow-lg transition-all">
                        Change Password
                    </button>

                    <a href="{{ route('login') }}" class="text-center text-xs text-gray-500 hover:text-[#4A3728] font-bold uppercase tracking-widest transition-colors mt-2">
                        ← Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>