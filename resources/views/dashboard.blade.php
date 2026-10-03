<x-app-layout>
    @php
        // Hardcoded to 5 since you have 5 core sacramental books
        $bookCount = $bookCount ?? 5;

        // Temporary fallbacks so the other cards don't break:
        $massScheduleCount = $massScheduleCount ?? 0;
        $pendingCertificatesCount = $pendingCertificatesCount ?? 0;
        $appointmentCount = $appointmentCount ?? 0;
        $paymentCount = $paymentCount ?? 0;
        $onlineViewingCount = $onlineViewingCount ?? 0;
        $calendarEventCount = $calendarEventCount ?? 0;
    @endphp

    <div class="relative min-h-[calc(100vh-140px)]">
        <div
            class="fixed inset-0 -z-10 bg-cover bg-center"
            style="background-image: url('https://seepangasinan.com/wp-content/uploads/2022/08/1499243164_explora-holy-cross-parish-reliquary-2-1536x863.jpg');"
        ></div>

        <div class="fixed inset-0 -z-10 bg-white/30"></div>

        <main class="relative">
            <div class="max-w-[1500px] mx-auto px-6 py-12">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-6 justify-items-center">

                    <!-- Indexed Books -->
                    <a
                        href="{{ Route::has('records.index') ? route('records.index') : '#' }}"
                        class="block w-full bg-white rounded-3xl shadow-lg p-8 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between"
                    >
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">
                                Indexed Books
                            </h4>
                            <p class="text-7xl font-black text-blue-600 mb-6 tracking-tighter">
                                {{ $bookCount }}
                            </p>
                        </div>
                        <div>
                            <div class="h-1 w-12 bg-blue-100 mb-6"></div>
                            <span class="text-xs font-black text-blue-500 uppercase tracking-widest flex items-center">
                                Open Records <span class="ml-2">→</span>
                            </span>
                        </div>
                    </a>

                    <!-- Mass Schedules -->
                    <a
                        href="{{ Route::has('schedules.index') ? route('schedules.index') : '#' }}"
                        class="block w-full bg-white rounded-3xl shadow-lg p-8 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between"
                    >
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">
                                Mass Schedules
                            </h4>
                            <p class="text-7xl font-black text-green-600 mb-6 tracking-tighter">
                                {{ $massScheduleCount }}
                            </p>
                        </div>
                        <div>
                            <div class="h-1 w-12 bg-green-100 mb-6"></div>
                            <span class="text-xs font-black text-green-500 uppercase tracking-widest flex items-center">
                                View Schedules <span class="ml-2">→</span>
                            </span>
                        </div>
                    </a>

                    <!-- Certificates -->
                    <a
                        href="{{ Route::has('certificates.index') ? route('certificates.index') : '#' }}"
                        class="block w-full bg-white rounded-3xl shadow-lg p-8 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between"
                    >
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">
                                Certificates
                            </h4>
                            <p class="text-7xl font-black text-amber-500 mb-6 tracking-tighter">
                                {{ $pendingCertificatesCount }}
                            </p>
                        </div>
                        <div>
                            <div class="h-1 w-12 bg-amber-100 mb-6"></div>
                            <span class="text-xs font-black text-amber-500 uppercase tracking-widest flex items-center">
                                Pending Requests <span class="ml-2">→</span>
                            </span>
                        </div>
                    </a>

                    <!-- Appointments -->
                    <a
                        href="{{ Route::has('appointments.index') ? route('appointments.index') : '#' }}"
                        class="block w-full bg-white rounded-3xl shadow-lg p-8 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between"
                    >
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">
                                Appointments
                            </h4>
                            <p class="text-7xl font-black text-purple-600 mb-6 tracking-tighter">
                                {{ $appointmentCount }}
                            </p>
                        </div>
                        <div>
                            <div class="h-1 w-12 bg-purple-100 mb-6"></div>
                            <span class="text-xs font-black text-purple-500 uppercase tracking-widest flex items-center">
                                Manage Bookings <span class="ml-2">→</span>
                            </span>
                        </div>
                    </a>

                    <!-- Payment Records -->
                    <a
                        href="{{ Route::has('payments.index') ? route('payments.index') : '#' }}"
                        class="block w-full bg-white rounded-3xl shadow-lg p-8 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between"
                    >
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">
                                Payment Records
                            </h4>
                            <p class="text-7xl font-black text-[#5D4037] mb-6 tracking-tighter">
                                {{ $paymentCount }}
                            </p>
                        </div>
                        <div>
                            <div class="h-1 w-12 bg-orange-100 mb-6"></div>
                            <span class="text-xs font-black text-[#5D4037] uppercase tracking-widest flex items-center">
                                View Payments <span class="ml-2">→</span>
                            </span>
                        </div>
                    </a>

                    <!-- Online Viewing -->
                    <a
                        href="{{ Route::has('viewing.index') ? route('viewing.index') : '#' }}"
                        class="block w-full bg-white rounded-3xl shadow-lg p-8 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between"
                    >
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">
                                Online Viewing
                            </h4>
                            <p class="text-7xl font-black text-indigo-600 mb-6 tracking-tighter">
                                {{ $onlineViewingCount }}
                            </p>
                        </div>
                        <div>
                            <div class="h-1 w-12 bg-indigo-100 mb-6"></div>
                            <span class="text-xs font-black text-indigo-500 uppercase tracking-widest flex items-center">
                                View <span class="ml-2">→</span>
                            </span>
                        </div>
                    </a>

                    <!-- ✅ Calendar (BAGO) -->
                    <a
                        href="{{ Route::has('calendar.index') ? route('calendar.index') : '#' }}"
                        class="block w-full bg-white rounded-3xl shadow-lg p-8 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between"
                    >
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">
                                Calendar
                            </h4>
                            <p class="text-7xl font-black text-cyan-600 mb-6 tracking-tighter">
                                {{ $calendarEventCount }}
                            </p>
                        </div>
                        <div>
                            <div class="h-1 w-12 bg-cyan-100 mb-6"></div>
                            <span class="text-xs font-black text-cyan-500 uppercase tracking-widest flex items-center">
                                View Calendar <span class="ml-2">→</span>
                            </span>
                        </div>
                    </a>

                </div>
            </div>
        </main>
    </div>
</x-app-layout>