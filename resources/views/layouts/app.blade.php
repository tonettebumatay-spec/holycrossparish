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
</body>
</html>


