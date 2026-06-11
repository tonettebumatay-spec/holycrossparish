<nav class="bg-[#4d290a] border-b border-[#361d07] shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/parishlogo.png') }}" class="block h-12 w-auto" alt="Parish Logo">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:text-gray-200 border-white">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    <a href="#" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-300 hover:text-white hover:border-gray-300 transition duration-150 ease-in-out uppercase tracking-widest">
                        Index of Books
                    </a>
                </div>
            </div>

            <!-- User Settings & Logout -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <div class="flex items-center gap-4">
                    <span class="text-white font-medium italic text-sm">{{ Auth::user()->name }}</span>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase transition duration-300 border border-white/20">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>