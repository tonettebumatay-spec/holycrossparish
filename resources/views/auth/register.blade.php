<x-guest-layout>
    <div class="relative min-h-screen flex items-center justify-end pr-10 md:pr-24 overflow-hidden">
        
        <!-- Background Image -->
        <img src="{{ asset('images/frontchurch.png') }}" 
             class="absolute inset-0 w-full h-full object-cover z-0" 
             alt="Church Background">

        <!-- Registration Card -->
        <div class="relative z-10 w-full max-w-md bg-white/80 backdrop-blur-md p-10 rounded-[60px] shadow-2xl flex flex-col items-center">
            
            <!-- Logo Circle -->
            <div class="w-20 h-20 bg-[#4d290a] rounded-full flex items-center justify-center mb-4">
                <img src="{{ asset('images/parishlogo.png') }}" alt="Logo" class="w-14">
            </div>

            <h2 class="text-2xl font-bold text-[#4d290a] mb-6 tracking-wider uppercase text-center">Create Admin Account</h2>

            <form method="POST" action="{{ route('register') }}" class="w-full space-y-4">
                @csrf

                <!-- Name -->
                <div>
                    <input id="name" type="text" name="name" :value="old('name')" required autofocus 
                           placeholder="Full Name"
                           class="w-full px-4 py-2 bg-transparent border-t-0 border-l-0 border-r-0 border-b-2 border-[#4d290a] focus:ring-0 focus:border-[#7a4211] text-md placeholder-[#4d290a]">
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div>
                    <input id="email" type="email" name="email" :value="old('email')" required 
                           placeholder="Email Address"
                           class="w-full px-4 py-2 bg-transparent border-t-0 border-l-0 border-r-0 border-b-2 border-[#4d290a] focus:ring-0 focus:border-[#7a4211] text-md placeholder-[#4d290a]">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <input id="password" type="password" name="password" required 
                           placeholder="Password"
                           class="w-full px-4 py-2 bg-transparent border-t-0 border-l-0 border-r-0 border-b-2 border-[#4d290a] focus:ring-0 focus:border-[#7a4211] text-md placeholder-[#4d290a]">
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <input id="password_confirmation" type="password" name="password_confirmation" required 
                           placeholder="Confirm Password"
                           class="w-full px-4 py-2 bg-transparent border-t-0 border-l-0 border-r-0 border-b-2 border-[#4d290a] focus:ring-0 focus:border-[#7a4211] text-md placeholder-[#4d290a]">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Actions -->
                <div class="pt-4 flex flex-col items-center gap-4">
                    <button type="submit" 
                            class="w-full bg-[#4d290a] hover:bg-[#361d07] text-white font-bold py-3 rounded-xl text-lg transition duration-300 uppercase">
                        Register
                    </button>

                    <a class="text-sm text-[#4d290a] font-bold underline hover:text-[#7a4211]" href="{{ route('login') }}">
                        Already registered? Log in
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>