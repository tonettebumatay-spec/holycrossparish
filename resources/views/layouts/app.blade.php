<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Holy Cross Parish Portal</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js CDN (Essential for the button to work) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- ========================================== -->
    <!-- FLATPICKR CSS (Para sa Calendar Design)    -->
    <!-- ========================================== -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Flatpickr Dark Theme -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">

    <!-- Custom CSS para sa Cyan/Teal na bilog (Katulad ng screenshot mo) -->
    <style>
        /* Palitan ang kulay ng selected date */
        .flatpickr-day.selected, 
        .flatpickr-day.startRange, 
        .flatpickr-day.endRange, 
        .flatpickr-day.selected.inRange, 
        .flatpickr-day.startRange.inRange, 
        .flatpickr-day.endRange.inRange, 
        .flatpickr-day.selected:focus, 
        .flatpickr-day.startRange:focus, 
        .flatpickr-day.endRange:focus, 
        .flatpickr-day.selected:hover, 
        .flatpickr-day.startRange:hover, 
        .flatpickr-day.endRange:hover {
            background: #4FD1C5 !important; /* Cyan/Teal color */
            border-color: #4FD1C5 !important;
            color: #1A202C !important; /* Dark text */
            font-weight: bold;
        }

        /* Palitan ang kulay ng header (September 2026) */
        .flatpickr-months .flatpickr-month {
            background: #2D3748 !important;
        }
        
        /* Palitan ang kulay ng arrows at month text */
        .flatpickr-months .flatpickr-prev-month, 
        .flatpickr-months .flatpickr-next-month,
        .flatpickr-current-month .flatpickr-monthDropdown-months,
        .flatpickr-current-month input.cur-year {
            color: #ffffff !important;
            fill: #ffffff !important;
        }
    </style>
</head>
<body class="font-sans antialiased bg-[#f3f4f6]">
    <div class="min-h-screen">
        <header class="bg-white pt-10 pb-6 px-10 border-b border-gray-100">
            <div class="max-w-7xl mx-auto flex justify-between items-start">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center border-4 border-[#4d290a] shadow-sm overflow-hidden">
                        <img src="{{ asset('images/parishlogo.png') }}" class="w-full h-full object-cover" alt="Parish Logo">
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900 tracking-tight">Holy Cross Parish Portal</h1>
                        <p class="text-gray-500 text-lg mt-2 font-medium">Welcome back, {{ Auth::user()->name }}</p>
                    </div>
                </div>
                <div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-[#e11d48] hover:bg-[#be123c] text-white px-8 py-2 rounded-full font-semibold transition shadow-md uppercase text-sm tracking-wider">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>
    </div>

    <!-- ========================================== -->
    <!-- FLATPICKR JS (Para gumana ang Calendar)    -->
    <!-- ========================================== -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // I-initialize ang Datepicker sa lahat ng input na may class na "flatpickr-date"
            // Siguraduhin na ang input sa modal mo ay may class="flatpickr-date"
            flatpickr(".flatpickr-date", {
                dateFormat: "Y-m-d", // Format na ise-save sa database (e.g., 2026-09-09)
                defaultDate: "today",
                theme: "dark", // Gamitin ang dark theme
                allowInput: false, // Bawal i-type manually para iwas error
            });

            // Optional: Para sa Time input kung gusto mo rin palitan
            // Siguraduhin na ang input sa modal mo ay may class="flatpickr-time"
            flatpickr(".flatpickr-time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "h:i K", // Format: 09:00 AM
                time_24hr: false,
                theme: "dark",
                allowInput: false,
            });
        });
    </script>
</body>
</html>