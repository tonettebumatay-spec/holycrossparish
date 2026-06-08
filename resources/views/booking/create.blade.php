<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Centralized Booking</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body class="bg-[#f3f4f6]">

<div class="min-h-screen">
    <header class="bg-white pt-10 pb-6 px-5 sm:px-10 border-b border-gray-100">
        <div class="max-w-3xl mx-auto">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center border-4 border-[#4d290a] shadow-sm overflow-hidden">
                    <img src="{{ asset('images/parishlogo.png') }}" class="w-full h-full object-cover" alt="Parish Logo">
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Centralized Booking</h1>
                    <p class="text-gray-500 text-sm sm:text-base mt-1 font-medium">Book a date for church services</p>
                </div>
            </div>
        </div>
    </header>

    <main class="px-5 sm:px-10 py-8">
        <div class="max-w-3xl mx-auto">
            @if(session('success'))
                <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800 text-sm">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-7">
                <form method="POST" action="{{ route('booking.store') }}" class="space-y-5" id="bookingForm">
                    @csrf

                    <div>
                        <label for="service_type" class="block text-sm font-semibold text-gray-800 mb-1">
                            Service
                        </label>
                        <div class="relative">
                            <select id="service_type" name="service_type" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#4d290a]/30 focus:border-[#4d290a]" required>
                                <option value="" selected disabled>Select a service</option>
                                <option value="baptism">Baptism (Binyag)</option>
                                <option value="wedding">Wedding (Kasal)</option>
                                <option value="communion">Communion (Komunyon)</option>
                                <option value="confirmation">Confirmation (Kumpil)</option>
                                <option value="funeral">Funeral (Libing)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="user_name" class="block text-sm font-semibold text-gray-800 mb-1">User Name</label>
                            <input id="user_name" name="user_name" type="text" value="{{ old('user_name') }}"
                                   class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#4d290a]/30 focus:border-[#4d290a]" required>
                        </div>

                        <div>
                            <label for="contact_number" class="block text-sm font-semibold text-gray-800 mb-1">Contact Number</label>
                            <input id="contact_number" name="contact_number" type="text" inputmode="tel" value="{{ old('contact_number') }}"
                                   class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#4d290a]/30 focus:border-[#4d290a]" required>
                        </div>
                    </div>

                    <div>
                        <label for="appointment_date" class="block text-sm font-semibold text-gray-800 mb-1">Appointment Date</label>
                        <input id="appointment_date" name="appointment_date" type="text" value="{{ old('appointment_date') }}"
                               class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#4d290a]/30 focus:border-[#4d290a]" placeholder="Select a date" required>
                        <p class="mt-2 text-xs text-gray-500">Mondays and full dates (3+ bookings) are disabled.</p>
                    </div>

                    <div>
                        <label for="details" class="block text-sm font-semibold text-gray-800 mb-1">Details (Optional)</label>
                        <textarea id="details" name="details" rows="4"
                                  class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#4d290a]/30 focus:border-[#4d290a]" placeholder="Add any important notes...">{{ old('details') }}</textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="w-full rounded-xl bg-[#4d290a] hover:bg-[#361d07] text-white font-bold py-3 shadow-sm transition focus:outline-none focus:ring-2 focus:ring-[#4d290a]/30">
                            Submit Booking Request
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center text-xs text-gray-500">
                    If you have questions, please contact the parish office.
                </div>
            </div>
        </div>
    </main>
</div>


<div id="full-dates-data" data-dates="{!! json_encode(array_values($fullDates ?? [])) !!}" class="hidden"></div>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Babasahin ang data mula sa HTML div nang ligtas
    const dataContainer = document.getElementById('full-dates-data');
    const fullDates = dataContainer ? JSON.parse(dataContainer.getAttribute('data-dates')) : [];

    // Disable Mondays + full dates
    const disabledDatesSet = new Set(fullDates);

    const appointmentDateInput = document.getElementById('appointment_date');

    if (appointmentDateInput) {
        flatpickr(appointmentDateInput, {
            dateFormat: 'Y-m-d',
            minDate: 'today',
            disable: [
                function(date) {
                    // I-disable ang lahat ng Lunes (Mondays)
                    if (date.getDay() === 1) return true;

                    // I-disable ang mga petsang puno na
                    const yyyy = date.getFullYear();
                    const mm = String(date.getMonth() + 1).padStart(2, '0');
                    const dd = String(date.getDate()).padStart(2, '0');
                    const key = `${yyyy}-${mm}-${dd}`;
                    return disabledDatesSet.has(key);
                }
            ] 
        }); 
    }
</script>