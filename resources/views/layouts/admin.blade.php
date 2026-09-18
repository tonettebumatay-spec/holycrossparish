<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin - Holy Cross Parish')</title>

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Flatpickr CSS (para sa calendar) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .admin-header {
            background: #4d290a;
            color: white;
            padding: 15px 0;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .admin-header h1 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }
        .admin-nav a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            margin-right: 20px;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        .admin-nav a:hover {
            color: white;
            text-decoration: underline;
        }
        .container {
            max-width: 1140px;
        }

        /* Flatpickr custom styling (cyan/teal highlight) */
        .flatpickr-day.selected,
        .flatpickr-day.selected:focus,
        .flatpickr-day.selected:hover {
            background: #4FD1C5 !important;
            border-color: #4FD1C5 !important;
            color: #1A202C !important;
            font-weight: bold;
        }
        .flatpickr-months .flatpickr-month {
            background: #2D3748 !important;
        }
        .flatpickr-months .flatpickr-prev-month,
        .flatpickr-months .flatpickr-next-month,
        .flatpickr-current-month .flatpickr-monthDropdown-months,
        .flatpickr-current-month input.cur-year {
            color: #ffffff !important;
            fill: #ffffff !important;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h1>Holy Cross Parish — Admin</h1>
                <nav class="admin-nav d-flex align-items-center flex-wrap">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <a href="{{ route('admin.availability.index') }}">Availability</a>
                    <a href="{{ url('/appointments') }}">Appointments</a>
                    <a href="{{ url('/records') }}">Records</a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline mb-0 ms-2">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light">Logout</button>
                    </form>
                </nav>
            </div>
        </div>
    </div>

    <main>
        @yield('content')
    </main>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Flatpickr JS (para sa calendar) -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Stack para sa child page scripts (e.g. inline calendar initialization) -->
    @stack('scripts')
</body>
</html>