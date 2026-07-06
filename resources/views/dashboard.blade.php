<x-app-layout>
    @php
    // Use the global DB helper or fully qualified namespace to avoid 'use' errors
        $bookCount = 5; 
        $massScheduleCount = \Illuminate\Support\Facades\DB::table('schedules')->count();
        $pendingCertificatesCount = \Illuminate\Support\Facades\DB::table('certificates')->where('status', 'pending')->count();
    
    // If this specific line causes a 500 error, it means the table "appointments" does not exist.
    // Try changing 'appointments' to 'booking' or whatever table stores your appointments.
        $appointmentCount = \Illuminate\Support\Facades\DB::table('appointments')->exists() 
                        ? \Illuminate\Support\Facades\DB::table('appointments')->count() 
                        : 0;
                        
        $inventoryCount = \Illuminate\Support\Facades\DB::table('inventories')->count();
        $onlineViewingCount = \Illuminate\Support\Facades\DB::table('viewing')->count();
    @endphp

    <div class="relative min-h-[calc(100vh-140px)]">
        <div
            class="fixed inset-0 -z-10 bg-cover bg-center"
            style="background-image: url('https://seepangasinan.com/wp-content/uploads/2022/08/1499243164_explora-holy-cross-parish-reliquary-2-1536x863.jpg');"
        ></div>

        <div class="fixed inset-0 -z-10 bg-white/30"></div>

        <main class="relative">
            <div class="max-w-[1500px] mx-auto px-6 py-12">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 justify-items-center">

                    {{-- Indexed Books --}}
                    <a href="{{ Route::has('records.index') ? route('records.index') : '#' }}" class="block w-full bg-white rounded-3xl shadow-lg p-8 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">Indexed Books</h4>
                            <p class="text-7xl font-black text-blue-600 mb-6 tracking-tighter">{{ $bookCount }}</p>
                        </div>
                        <div><div class="h-1 w-12 bg-blue-100 mb-6"></div><span class="text-xs font-black text-blue-500 uppercase tracking-widest flex items-center">Open Records →</span></div>
                    </a>

                    {{-- Mass Schedules --}}
                    <a href="{{ Route::has('schedules.index') ? route('schedules.index') : '#' }}" class="block w-full bg-white rounded-3xl shadow-lg p-8 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">Mass Schedules</h4>
                            <p class="text-7xl font-black text-green-600 mb-6 tracking-tighter">{{ $massScheduleCount }}</p>
                        </div>
                        <div><div class="h-1 w-12 bg-green-100 mb-6"></div><span class="text-xs font-black text-green-500 uppercase tracking-widest flex items-center">View Schedules →</span></div>
                    </a>

                    {{-- Certificates --}}
                    <a href="{{ Route::has('certificates.index') ? route('certificates.index') : '#' }}" class="block w-full bg-white rounded-3xl shadow-lg p-8 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">Certificates</h4>
                            <p class="text-7xl font-black text-amber-500 mb-6 tracking-tighter">{{ $pendingCertificatesCount }}</p>
                        </div>
                        <div><div class="h-1 w-12 bg-amber-100 mb-6"></div><span class="text-xs font-black text-amber-500 uppercase tracking-widest flex items-center">Pending Requests →</span></div>
                    </a>

                    {{-- Appointments --}}
                    <a href="{{ Route::has('appointments.index') ? route('appointments.index') : '#' }}" class="block w-full bg-white rounded-3xl shadow-lg p-8 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">Appointments</h4>
                            <p class="text-7xl font-black text-purple-600 mb-6 tracking-tighter">{{ $appointmentCount }}</p>
                        </div>
                        <div><div class="h-1 w-12 bg-purple-100 mb-6"></div><span class="text-xs font-black text-purple-500 uppercase tracking-widest flex items-center">Manage Bookings →</span></div>
                    </a>

                    {{-- Inventory --}}
                    <a href="{{ Route::has('inventory.index') ? route('inventory.index') : '#' }}" class="block w-full bg-white rounded-3xl shadow-lg p-8 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">Inventory</h4>
                            <p class="text-7xl font-black text-[#5D4037] mb-6 tracking-tighter">{{ $inventoryCount }}</p>
                        </div>
                        <div><div class="h-1 w-12 bg-orange-100 mb-6"></div><span class="text-xs font-black text-[#5D4037] uppercase tracking-widest flex items-center">Check Items →</span></div>
                    </a>

                    {{-- Online Viewing --}}
                    <a href="{{ Route::has('viewing.index') ? route('viewing.index') : '#' }}" class="block w-full bg-white rounded-3xl shadow-lg p-8 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">Online Viewing</h4>
                            <p class="text-7xl font-black text-indigo-600 mb-6 tracking-tighter">{{ $onlineViewingCount }}</p>
                        </div>
                        <div><div class="h-1 w-12 bg-indigo-100 mb-6"></div><span class="text-xs font-black text-indigo-500 uppercase tracking-widest flex items-center">View →</span></div>
                    </a>

                </div>
            </div>
        </main>
    </div>
</x-app-layout>