<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-6">

            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-5 py-2 bg-white border border-gray-300 rounded-full text-sm font-semibold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    ← Back to Dashboard
                </a>
                <h1 class="text-3xl font-black text-gray-800 tracking-tight uppercase underline decoration-purple-600 underline-offset-4">
                    Appointment Management
                </h1>
                <div class="w-20"></div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- 🔍 Search & Filter Bar -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
                <form method="GET" action="{{ route('appointments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                    <div>
                        <input type="text" name="search" class="w-full text-sm border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" placeholder="Search by name..." value="{{ request('search') }}">
                    </div>

                    <div>
                        <select name="type" class="w-full text-sm border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="">All Types</option>
                            <option value="Baptism" {{ request('type') == 'Baptism' ? 'selected' : '' }}>Baptism</option>
                            <option value="Communion" {{ request('type') == 'Communion' ? 'selected' : '' }}>Communion</option>
                            <option value="Confirmation" {{ request('type') == 'Confirmation' ? 'selected' : '' }}>Confirmation</option>
                            <option value="Wedding" {{ request('type') == 'Wedding' ? 'selected' : '' }}>Wedding</option>
                            <option value="Funeral" {{ request('type') == 'Funeral' ? 'selected' : '' }}>Funeral</option>
                        </select>
                    </div>

                    <div>
                        <select name="status" class="w-full text-sm border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition">
                            Filter
                        </button>
                        <a href="{{ route('appointments.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm transition text-center">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Main Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                @if($appointments->isEmpty())
                    <div class="text-center py-20">
                        <div class="inline-flex p-6 bg-purple-50 rounded-full text-purple-600 mb-4">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-700">No Appointments Found</h2>
                        <p class="text-gray-400 mt-2">Try adjusting your filter or search criteria.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="bg-gray-100 text-xs font-semibold uppercase text-gray-500 border-b">
                                <tr>
                                    <th class="px-6 py-4">#</th>
                                    <th class="px-6 py-4">Type</th>
                                    <th class="px-6 py-4">Name</th>
                                    <th class="px-6 py-4">Scheduled</th>
                                    <th class="px-6 py-4">Date Submitted</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($appointments as $index => $app)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 font-medium text-gray-400">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-bold uppercase
                                                @if($app->type == 'Baptism') bg-blue-100 text-blue-800
                                                @elseif($app->type == 'Communion') bg-green-100 text-green-800
                                                @elseif($app->type == 'Confirmation') bg-purple-100 text-purple-800
                                                @elseif($app->type == 'Wedding') bg-pink-100 text-pink-800
                                                @elseif($app->type == 'Funeral') bg-gray-100 text-gray-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ $app->type }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $app->name ?? 'N/A' }}</td>

                                        <!-- ✅ Scheduled Column -->
                                        <td class="px-6 py-4">
                                            @php
                                                $scheduledDate = $app->appointment_date ?? null;
                                                $scheduledTime = $app->appointment_time ?? null;
                                                $isScheduled = !empty($scheduledDate) || !empty($scheduledTime);
                                            @endphp

                                            @if($isScheduled)
                                                <div class="flex flex-col text-xs">
                                                    @if($scheduledDate)
                                                        <span class="font-semibold text-gray-900">
                                                            {{ \Carbon\Carbon::parse($scheduledDate)->format('M d, Y') }}
                                                        </span>
                                                    @endif
                                                    @if($scheduledTime)
                                                        <span class="text-gray-600">
                                                            {{ \Carbon\Carbon::parse($scheduledTime)->format('g:i A') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs italic text-gray-400">Not scheduled</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 font-medium text-gray-700">{{ $app->submitted_at }}</td>
                                        
                                        <!-- Status Column -->
                                        <td class="px-6 py-4">
                                            @if(($app->status ?? 'pending') === 'cancelled' || ($app->status ?? 'pending') === 'canceled')
                                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold uppercase bg-red-100 text-red-800 cursor-help" 
                                                    title="Reason: {{ $app->cancellation_reason ?? 'No reason provided' }}">
                                                    Cancelled
                                                </span>
                                            @else
                                                @php
                                                    $statusColor = match($app->status ?? 'pending') {
                                                        'confirmed', 'approved' => 'bg-green-100 text-green-800',
                                                        default                 => 'bg-yellow-100 text-yellow-800',
                                                    };
                                                @endphp
                                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold uppercase {{ $statusColor }}">
                                                    {{ $app->status ?? 'pending' }}
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Actions Column -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center space-x-2 flex-wrap gap-1">
                                                @if(($app->status ?? 'pending') == 'pending')
                                                    <button
                                                        type="button"
                                                        class="schedule-btn px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded-full transition"
                                                        data-type="{{ strtolower($app->type) }}"
                                                        data-id="{{ $app->id }}"
                                                        data-date="{{ $app->appointment_date ?? '' }}"
                                                        data-time="{{ $app->appointment_time ?? '' }}">
                                                        Schedule
                                                    </button>
                                                @endif

                                                @if(($app->status ?? 'pending') !== 'cancelled' && ($app->status ?? 'pending') !== 'canceled' && !($app->is_locked ?? false))
                                                    <button type="button" 
                                                            class="cancel-btn px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-full transition"
                                                            data-type="{{ strtolower($app->type) }}"
                                                            data-id="{{ $app->id }}">
                                                        Cancel
                                                    </button>
                                                @endif

                                                <form action="{{ route('appointments.destroy', ['type' => strtolower($app->type), 'id' => $app->id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs rounded-full transition" onclick="return confirm('Delete this appointment permanently?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 text-sm text-gray-500 border-t">
                        Total: {{ $appointments->count() }} appointment(s)
                    </div>
                @endif
            </div>
        </div>

        <!-- Cancellation Modal -->
        <div id="cancelModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-2xl shadow-xl p-6 max-w-md w-full mx-4">
                <div class="flex justify-between items-center pb-3 border-b mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Cancel Appointment</h3>
                    <button type="button" id="closeModalBtn" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
                </div>

                <form id="cancelForm" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="type" id="cancelType">
                    <input type="hidden" name="id" id="cancelId">

                    <div class="mb-4">
                        <label for="cancelReason" class="block text-sm font-medium text-gray-700 mb-2">
                            Cancellation Reason
                        </label>
                        <textarea name="reason" id="cancelReason" rows="3" required class="w-full text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" placeholder="State reason for cancellation..."></textarea>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" id="cancelModalCloseBtn" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-lg transition">
                            Close
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition">
                            Confirm Cancellation
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Schedule Modal (updated with inline calendar) -->
        <div id="scheduleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
                <h2 class="text-xl font-bold mb-4">
                    Set Appointment Schedule
                </h2>

                <form id="scheduleForm" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Inline Calendar --}}
                    <div class="mb-5">
                        <label class="block text-xs font-black text-gray-600 uppercase tracking-widest mb-2">Pick a Date</label>
                        <div class="flex justify-center">
                            <div id="schedule-calendar" class="border border-gray-200 rounded-2xl p-2 bg-white inline-block"></div>
                        </div>
                        <input type="hidden" id="schedule_date" name="appointment_date" required>
                        <p class="text-xs text-gray-600 mt-3 text-center">
                            Selected: <strong id="selected-date-label" class="text-purple-700">None</strong>
                        </p>
                    </div>

                    {{-- Time field (flatpickr popup, 12-hour display) --}}
                    <div class="mb-5">
                        <label class="block text-xs font-black text-gray-600 uppercase tracking-widest mb-2">Time</label>
                        <input
                            type="text"
                            id="schedule_time"
                            name="appointment_time"
                            class="w-full border border-gray-200 rounded-2xl bg-gray-50 py-3.5 px-5 text-base text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent transition-all"
                            placeholder="Select Time.."
                            readonly
                            required>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button
                            type="button"
                            id="closeSchedule"
                            class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl text-xs font-black uppercase tracking-widest transition">
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest transition shadow-lg">
                            Save Schedule
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- JavaScript for Modals -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // ==========================================
                // CANCEL MODAL LOGIC
                // ==========================================
                const modal = document.getElementById('cancelModal');
                const cancelForm = document.getElementById('cancelForm');
                const cancelTypeInput = document.getElementById('cancelType');
                const cancelIdInput = document.getElementById('cancelId');
                const closeModalBtn = document.getElementById('closeModalBtn');
                const cancelModalCloseBtn = document.getElementById('cancelModalCloseBtn');

                document.querySelectorAll('.cancel-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const type = this.dataset.type;
                        const id = this.dataset.id;
                        
                        cancelTypeInput.value = type;
                        cancelIdInput.value = id;
                        cancelForm.action = `/appointments/${type}/${id}/cancel`;
                        
                        modal.classList.remove('hidden');
                    });
                });

                function closeModal() {
                    modal.classList.add('hidden');
                    cancelForm.reset();
                }

                if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
                if (cancelModalCloseBtn) cancelModalCloseBtn.addEventListener('click', closeModal);

                // ==========================================
                // SCHEDULE MODAL LOGIC
                // ==========================================
                const scheduleModal = document.getElementById('scheduleModal');
                const scheduleForm = document.getElementById('scheduleForm');
                const scheduleDateInput = document.getElementById('schedule_date');
                const scheduleTimeInput = document.getElementById('schedule_time');
                const selectedDateLabel = document.getElementById('selected-date-label');
                const calendarEl = document.getElementById('schedule-calendar');

                let datePicker = null;

                // Initialize Flatpickr for Time (12-hour display with AM/PM) — popup mode
                const timePicker = flatpickr(scheduleTimeInput, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "h:i K",
                    time_24hr: false,
                    theme: "dark",
                    allowInput: false,
                });

                // Initialize Flatpickr for Date — INLINE mode
                function initCalendar() {
                    if (typeof flatpickr === 'undefined' || !calendarEl) {
                        return setTimeout(initCalendar, 200);
                    }
                    if (calendarEl._flatpickr) return;

                    datePicker = flatpickr(calendarEl, {
                        inline: true,
                        dateFormat: "Y-m-d",
                        theme: "dark",
                        allowInput: false,
                        onChange: function (selectedDates, dateStr) {
                            scheduleDateInput.value = dateStr;
                            if (dateStr) {
                                const d = new Date(dateStr + 'T00:00:00');
                                const opts = { year: 'numeric', month: 'long', day: 'numeric' };
                                selectedDateLabel.textContent = d.toLocaleDateString('en-US', opts);
                            } else {
                                selectedDateLabel.textContent = 'None';
                            }
                        }
                    });
                }
                initCalendar();

                // Helper: Convert 24-hour "HH:MM" to 12-hour "h:MM AM/PM"
                function to12Hour(time24) {
                    if (!time24) return '';
                    let [hours, minutes] = time24.split(':').map(Number);
                    let period = hours >= 12 ? 'PM' : 'AM';
                    let hours12 = hours % 12;
                    if (hours12 === 0) hours12 = 12;
                    return hours12 + ':' + String(minutes).padStart(2, '0') + ' ' + period;
                }

                // Helper: Convert 12-hour "h:MM AM/PM" to 24-hour "HH:MM"
                function to24Hour(time12) {
                    if (!time12) return '';
                    let match = time12.match(/(\d{1,2}):(\d{2})\s*(AM|PM)/i);
                    if (!match) return time12;
                    let hours = parseInt(match[1], 10);
                    let minutes = match[2];
                    let period = match[3].toUpperCase();
                    if (period === 'AM' && hours === 12) hours = 0;
                    if (period === 'PM' && hours !== 12) hours += 12;
                    return String(hours).padStart(2, '0') + ':' + minutes;
                }

                document.querySelectorAll('.schedule-btn').forEach(btn => {
                    btn.addEventListener('click', function(){
                        let type = this.dataset.type;
                        let id = this.dataset.id;
                        let date = this.dataset.date;
                        let time = this.dataset.time;

                        scheduleForm.action = `/appointments/${type}/${id}/schedule`;

                        // Set date in inline calendar
                        if (date && datePicker) {
                            datePicker.setDate(date, true);
                            scheduleDateInput.value = date;
                            const d = new Date(date + 'T00:00:00');
                            const opts = { year: 'numeric', month: 'long', day: 'numeric' };
                            selectedDateLabel.textContent = d.toLocaleDateString('en-US', opts);
                        } else if (datePicker) {
                            datePicker.clear();
                            scheduleDateInput.value = '';
                            selectedDateLabel.textContent = 'None';
                        }

                        // Set time
                        if(time) {
                            let time24 = time.substring(0, 5);
                            let time12 = to12Hour(time24);
                            timePicker.setDate(time12, true);
                        } else {
                            timePicker.clear();
                        }

                        scheduleModal.classList.remove('hidden');
                    });
                });

                scheduleForm.addEventListener('submit', function() {
                    let current = scheduleTimeInput.value;
                    let converted = to24Hour(current);
                    scheduleTimeInput.value = converted;
                });

                const closeScheduleBtn = document.getElementById('closeSchedule');
                if (closeScheduleBtn) {
                    closeScheduleBtn.onclick = function(){
                        scheduleModal.classList.add('hidden');
                    };
                }
            });
        </script>

    </div>
</x-app-layout>