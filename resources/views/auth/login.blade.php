<x-guest-layout>
    <div class="relative min-h-screen flex items-center justify-end pr-10 md:pr-24 overflow-hidden">
        
        <!-- Background Image -->
        <img src="{{ asset('images/frontchurch.png') }}" 
             class="absolute inset-0 w-full h-full object-cover z-0" 
             alt="Church Background">

        <!-- Login Card -->
        <div class="relative z-10 w-full max-w-md bg-white/80 backdrop-blur-md p-10 rounded-[60px] shadow-2xl flex flex-col items-center">
            
            <!-- Logo Circle -->
            <div class="w-24 h-24 bg-[#4d290a] rounded-full flex items-center justify-center mb-4">
                <img src="{{ asset('images/parishlogo.png') }}" alt="Logo" class="w-16">
            </div>

            <h2 class="text-3xl font-bold text-[#4d290a] mb-8 tracking-wider uppercase">Login</h2>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 text-center">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Error Messages -->
            @if($errors->any())
                <div class="mb-4 font-medium text-sm text-red-600 text-center w-full">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="w-full space-y-6">
                @csrf

                <!-- Email/Username -->
                <div>
                    <input id="email" type="email" name="email" required autofocus 
                           placeholder="Username"
                           class="w-full px-4 py-3 bg-transparent border-t-0 border-l-0 border-r-0 border-b-2 border-[#4d290a] focus:ring-0 focus:border-[#7a4211] text-lg placeholder-[#4d290a]">
                </div>

                <!-- Password with Eye Icon -->
                <div class="relative" x-data="{ show: false }">
                    <!-- 👇 DEFAULT TYPE AY 'password' 👇 -->
                    <input id="password" :type="show ? 'text' : 'password'" name="password" required 
                           placeholder="Password"
                           class="w-full px-4 py-3 pr-12 bg-transparent border-t-0 border-l-0 border-r-0 border-b-2 border-[#4d290a] focus:ring-0 focus:border-[#7a4211] text-lg placeholder-[#4d290a]">
                    
                    <!-- Eye Toggle Button -->
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-2 flex items-center text-[#4d290a]/50 hover:text-[#4d290a] focus:outline-none transition-colors">
                        <!-- Eye Icon (Naka-hide) -->
                        <svg x-show="!show" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <!-- Eye Slash Icon (Naka-show) -->
                        <svg x-show="show" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                        </svg>
                    </button>
                </div>

                <!-- ✅ FORGOT PASSWORD LINK -->
                <div class="flex justify-end">
                    <a href="{{ route('password.request') }}" class="text-sm font-bold text-[#4d290a]/70 hover:text-[#4d290a] transition duration-300">
                        Forgot Password?
                    </a>
                </div>

                <!-- Login Button -->
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full bg-[#4d290a] hover:bg-[#361d07] text-white font-bold py-4 rounded-xl text-xl transition duration-300">
                        LOGIN
                    </button>
                </div>

                <!-- Admin-managed notice -->
                <div class="text-center mt-6">
                    <p class="text-[#4d290a]/70 text-sm font-medium">
                        Admin-managed accounts only.
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>