<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marriage Banns - Holy Cross Parish</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F4F1EA] min-h-screen">

    <!-- Header -->
    <header class="bg-[#4A3728] py-8">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <img src="{{ asset('images/parishlogo.png') }}" class="w-20 h-20 mx-auto mb-4 rounded-full" alt="Parish Logo">
            <h1 class="text-3xl font-black text-white">Holy Cross Parish</h1>
            <p class="text-white/70 mt-2">Marriage Banns</p>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-6 py-12">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-2xl font-black text-gray-800 mb-2">💍 Marriage Banns</h2>
            <p class="text-gray-500 text-sm mb-8">
                Ang mga sumusunod na banns ay naka-post para sa 3 linggo.
                Kung may nalalaman kayong impediment, mangyaring makipag-ugnayan sa parish office.
            </p>

            @if($banns->isEmpty())
                <div class="text-center py-16">
                    <p class="text-gray-400 italic">No marriage banns at the moment.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($banns as $bann)
                        <div class="border-l-4 border-[#4A3728] bg-gray-50 p-6 rounded-r-2xl">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">
                                        {{ $bann->groom_name }} & {{ $bann->bride_name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-2">
                                        <strong>Wedding Date:</strong> {{ $bann->wedding_date->format('F d, Y') }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <strong>Banns Posted:</strong> {{ $bann->banns_date->format('F d, Y') }}
                                    </p>
                                    @if($bann->notes)
                                        <p class="text-sm text-gray-500 mt-2 italic">{{ $bann->notes }}</p>
                                    @endif
                                </div>
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-black uppercase">
                                    Active
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <p class="text-center text-xs text-gray-400 mt-8">
            © {{ date('Y') }} Holy Cross Parish. All rights reserved.
        </p>
    </main>
</body>
</html>