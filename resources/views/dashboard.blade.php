<x-app-layout>
    @php
        $bookCount = $bookCount ?? 5;
        $massScheduleCount = $massScheduleCount ?? 0;
        $pendingCertificatesCount = $pendingCertificatesCount ?? 0;
        $appointmentCount = $appointmentCount ?? 0;
        $paymentCount = $paymentCount ?? 0;
        $calendarEventCount = $calendarEventCount ?? 0;
        $supportCount = $supportCount ?? 0;
        $bannsCount = $bannsCount ?? 0;
    @endphp

    <div class="relative min-h-[calc(100vh-140px)]">

        <!-- Background -->
        <div
            class="fixed inset-0 -z-10 bg-cover bg-center"
            style="background-image: url('https://seepangasinan.com/wp-content/uploads/2022/08/1499243164_explora-holy-cross-parish-reliquary-2-1536x863.jpg');"
        ></div>

        <div class="fixed inset-0 -z-10 bg-white/30"></div>

        <main class="relative">
            <div class="max-w-[1600px] mx-auto px-6 py-12">

                <!-- DASHBOARD CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-8 gap-4 justify-items-center">


                    <!-- ========================= -->
                    <!-- Indexed Books -->
                    <!-- ========================= -->
                    <a
                        href="{{ Route::has('records.index') ? route('records.index') : '#' }}"
                        class="block w-full bg-white rounded-3xl shadow-lg p-6 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between overflow-hidden"
                    >
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6 leading-tight">
                                Indexed Books
                            </h4>
                            <p class="text-6xl font-black text-blue-600 mb-6 tracking-tighter">
                                {{ $bookCount }}
                            </p>
                        </div>
                        <div>
                            <div class="h-1 w-12 bg-blue-100 mb-6"></div>
                            <span class="text-xs font-black text-blue-500 uppercase tracking-widest flex items-center leading-tight">
                                Open Records <span class="ml-2 flex-shrink-0">→</span>
                            </span>
                        </div>
                    </a>


                    <!-- ========================= -->
                    <!-- Mass Schedules -->
                    <!-- ========================= -->
                    <a
                        href="{{ Route::has('schedules.index') ? route('schedules.index') : '#' }}"
                        class="block w-full bg-white rounded-3xl shadow-lg p-6 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between overflow-hidden"
                    >
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6 leading-tight">
                                Mass Schedules
                            </h4>
                            <p class="text-6xl font-black text-green-600 mb-6 tracking-tighter">
                                {{ $massScheduleCount }}
                            </p>
                        </div>
                        <div>
                            <div class="h-1 w-12 bg-green-100 mb-6"></div>
                            <span class="text-xs font-black text-green-500 uppercase tracking-[0.08em] flex items-center leading-tight">
                                View Schedules <span class="ml-2 flex-shrink-0">→</span>
                            </span>
                        </div>
                    </a>


                    <!-- ========================= -->
                    <!-- Certificates -->
                    <!-- ========================= -->
                    <a
                        href="{{ Route::has('certificates.index') ? route('certificates.index') : '#' }}"
                        class="block w-full bg-white rounded-3xl shadow-lg p-6 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between overflow-hidden"
                    >
                        <div>
                            <h4 class="text-[11px] font-black text-gray-500 uppercase tracking-[0.08em] mb-6 leading-tight text-center">
                                Certificates
                            </h4>
                            <p class="text-6xl font-black text-amber-500 mb-6 tracking-tighter">
                                {{ $pendingCertificatesCount }}
                            </p>
                        </div>
                        <div>
                            <div class="h-1 w-12 bg-amber-100 mb-6"></div>
                            <span class="text-xs font-black text-amber-500 uppercase tracking-[0.05em] flex items-center leading-tight">
                                Pending Requests <span class="ml-1 flex-shrink-0">→</span>
                            </span>
                        </div>
                    </a>


                    <!-- ========================= -->
                    <!-- Appointments -->
                    <!-- ========================= -->
                    <a
                        href="{{ Route::has('appointments.index') ? route('appointments.index') : '#' }}"
                        class="block w-full bg-white rounded-3xl shadow-lg p-6 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between overflow-hidden"
                    >
                        <div>
                            <h4 class="text-[11px] font-black text-gray-500 uppercase tracking-[0.06em] mb-6 leading-tight text-center">
                                Appointments
                            </h4>
                            <p class="text-6xl font-black text-purple-600 mb-6 tracking-tighter">
                                {{ $appointmentCount }}
                            </p>
                        </div>
                        <div>
                            <div class="h-1 w-12 bg-purple-100 mb-6"></div>
                            <span class="text-xs font-black text-purple-500 uppercase tracking-[0.04em] flex items-center leading-tight">
                                Manage Bookings <span class="ml-1 flex-shrink-0">→</span>
                            </span>
                        </div>
                    </a>


                    <!-- ========================= -->
                    <!-- Payment Records -->
                    <!-- ========================= -->
                    <a
                        href="{{ Route::has('payments.index') ? route('payments.index') : '#' }}"
                        class="block w-full bg-white rounded-3xl shadow-lg p-6 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between overflow-hidden"
                    >
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.12em] mb-6 leading-tight">
                                Payment Records
                            </h4>
                            <p class="text-6xl font-black text-[#5D4037] mb-6 tracking-tighter">
                                {{ $paymentCount }}
                            </p>
                        </div>
                        <div>
                            <div class="h-1 w-12 bg-orange-100 mb-6"></div>
                            <span class="text-xs font-black text-[#5D4037] uppercase tracking-[0.08em] flex items-center leading-tight">
                                View Payments <span class="ml-1 flex-shrink-0">→</span>
                            </span>
                        </div>
                    </a>


                    <!-- ========================= -->
                    <!-- Calendar -->
                    <!-- ========================= -->
                    <a
                        href="{{ Route::has('calendar.index') ? route('calendar.index') : '#' }}"
                        class="block w-full bg-white rounded-3xl shadow-lg p-6 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between overflow-hidden"
                    >
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6 leading-tight">
                                Calendar
                            </h4>
                            <p class="text-6xl font-black text-cyan-600 mb-6 tracking-tighter">
                                {{ $calendarEventCount }}
                            </p>
                        </div>
                        <div>
                            <div class="h-1 w-12 bg-cyan-100 mb-6"></div>
                            <span class="text-xs font-black text-cyan-500 uppercase tracking-[0.07em] flex items-center">
                                View Calendar <span class="ml-1 flex-shrink-0">→</span>
                            </span>
                        </div>
                    </a>


                    <!-- ========================= -->
                    <!-- Customer Support -->
                    <!-- ========================= -->
                    <a
                        href="{{ Route::has('support.index') ? route('support.index') : '#' }}"
                        class="block w-full bg-white rounded-3xl shadow-lg p-6 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between overflow-hidden"
                    >
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.10em] mb-6 leading-tight">
                                Customer Support
                            </h4>
                            <p class="text-6xl font-black text-pink-600 mb-6 tracking-tighter">
                                💬
                            </p>
                        </div>
                        <div>
                            <div class="h-1 w-12 bg-pink-100 mb-6"></div>
                            <span class="text-xs font-black text-pink-500 uppercase tracking-[0.05em] flex items-center leading-tight">
                                View Messages <span class="ml-1 flex-shrink-0">→</span>
                            </span>
                        </div>
                    </a>


                    <!-- ========================= -->
                    <!-- Marriage Banns -->
                    <!-- ========================= -->
                    <a
                        href="{{ Route::has('banns.index') ? route('banns.index') : '#' }}"
                        class="block w-full bg-white rounded-3xl shadow-lg p-6 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between overflow-hidden"
                    >
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.10em] mb-6 leading-tight">
                                Marriage Banns
                            </h4>
                            <p class="text-6xl font-black text-rose-600 mb-6 tracking-tighter">
                                💍
                            </p>
                        </div>
                        <div>
                            <div class="h-1 w-12 bg-rose-100 mb-6"></div>
                            <span class="text-xs font-black text-rose-500 uppercase tracking-[0.07em] flex items-center">
                                View Banns <span class="ml-1 flex-shrink-0">→</span>
                            </span>
                        </div>
                    </a>

                </div>
            </div>
        </main>
    </div>
</x-app-layout>