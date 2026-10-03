<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-6">

            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-5 py-2 bg-white border border-gray-300 rounded-full text-sm font-semibold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    ← Back to Dashboard
                </a>
                <h1 class="text-3xl font-black text-gray-800 tracking-tight uppercase">
                    📅 Calendar View
                </h1>
                <div class="w-20"></div>
            </div>

            <!-- Legend -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-4 items-center">
                <span class="text-xs font-black uppercase tracking-widest text-gray-500">Legend:</span>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full" style="background: #3B82F6;"></span>
                    <span class="text-xs font-semibold text-gray-700">Mass Schedules</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full" style="background: #10B981;"></span>
                    <span class="text-xs font-semibold text-gray-700">Approved Appointments</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full" style="background: #F59E0B;"></span>
                    <span class="text-xs font-semibold text-gray-700">Pending Appointments</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full" style="background: #EF4444;"></span>
                    <span class="text-xs font-semibold text-gray-700">Cancelled</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full" style="background: #6B7280;"></span>
                    <span class="text-xs font-semibold text-gray-700">Expired</span>
                </div>
            </div>

            <!-- Calendar -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

    <!-- Calendar Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                },
                buttonText: {
                    today: 'Today',
                    month: 'Month',
                    week: 'Week',
                    day: 'Day',
                    list: 'List'
                },
                height: 'auto',
                events: '{{ route('calendar.events') }}',
                eventClick: function(info) {
                    info.jsEvent.preventDefault();

                    const props = info.event.extendedProps;

                    let details = '<div style="text-align: left;">';
                    details += '<p><strong>Type:</strong> ' + props.type + '</p>';

                    if (props.type === 'appointment') {
                        details += '<p><strong>Service:</strong> ' + (props.service_type || 'N/A') + '</p>';
                        details += '<p><strong>Name:</strong> ' + (props.user_name || 'N/A') + '</p>';
                        details += '<p><strong>Contact:</strong> ' + (props.contact_number || 'N/A') + '</p>';
                        details += '<p><strong>Status:</strong> ' + (props.status || 'N/A') + '</p>';
                    } else {
                        details += '<p><strong>Description:</strong> ' + (props.description || 'N/A') + '</p>';
                    }

                    details += '<p><strong>Time:</strong> ' + (props.time || 'N/A') + '</p>';
                    details += '</div>';

                    alert(info.event.title + '\n\n' + details.replace(/<[^>]*>/g, ''));
                },
                eventDidMount: function(info) {
                    info.el.title = info.event.title;
                }
            });

            calendar.render();
        });
    </script>
</x-app-layout>