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

            <form method="POST" action="{{ route('login') }}" class="w-full space-y-6">
                @csrf

                <!-- Email/Username -->
                <div>
                    <input id="email" type="email" name="email" required autofocus 
                           placeholder="Username"
                           class="w-full px-4 py-3 bg-transparent border-t-0 border-l-0 border-r-0 border-b-2 border-[#4d290a] focus:ring-0 focus:border-[#7a4211] text-lg placeholder-[#4d290a]">
                </div>

                <!-- Password -->
                <div>
                    <input id="password" type="password" name="password" required 
                           placeholder="Password"
                           class="w-full px-4 py-3 bg-transparent border-t-0 border-l-0 border-r-0 border-b-2 border-[#4d290a] focus:ring-0 focus:border-[#7a4211] text-lg placeholder-[#4d290a]">
                </div>

                <!-- Login Button -->
                <div class="pt-4">
                    <button type="submit" 
                            class="w-full bg-[#4d290a] hover:bg-[#361d07] text-white font-bold py-4 rounded-xl text-xl transition duration-300">
                        LOGIN
                    </button>
                </div>

                <!-- Create Account Link -->
                <div class="text-center mt-6">
                    <p class="text-[#4d290a] font-medium">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="font-bold underline hover:text-[#7a4211]">
                            Create Account
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>