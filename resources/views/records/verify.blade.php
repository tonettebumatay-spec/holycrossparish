<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification - Holy Cross Parish</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .serif { font-family: 'Playfair Display', Georgia, serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-100 to-gray-200 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl overflow-hidden">

        @if(isset($notFound) && $notFound)

            {{-- RECORD NOT FOUND --}}
            <div class="bg-red-600 text-white px-6 py-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white bg-opacity-20 rounded-full mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold tracking-wide">CERTIFICATE NOT FOUND</h1>
            </div>
            <div class="px-6 py-8 text-center text-gray-700">
                <p class="text-sm">The certificate you are trying to verify does not exist in our records, or the link is invalid.</p>
                <p class="text-xs text-gray-500 mt-4">If you believe this is an error, please contact Holy Cross Parish directly.</p>
            </div>

        @else

            {{-- SUCCESSFUL VERIFICATION --}}
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 text-white px-6 py-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white bg-opacity-25 rounded-full mb-3">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold tracking-wide">CERTIFICATE VERIFIED</h1>
                <p class="text-emerald-50 text-xs mt-2">This certificate is authentic and issued by Holy Cross Parish.</p>
            </div>

            <div class="px-6 py-6 space-y-4">

                <div class="border-b border-gray-100 pb-3">
                    <p class="text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1">Certificate Type</p>
                    <p class="text-base font-bold text-gray-900">{{ $type }} Certificate</p>
                </div>

                <div class="border-b border-gray-100 pb-3">
                    <p class="text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1">Name on Record</p>
                    <p class="text-base font-bold text-gray-900 uppercase">{{ $name }}</p>
                </div>

                <div class="grid grid-cols-3 gap-3 border-b border-gray-100 pb-3">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1">Book No.</p>
                        <p class="text-base font-bold text-gray-900">{{ $bookNumber ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1">Page No.</p>
                        <p class="text-base font-bold text-gray-900">{{ $pageNumber ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1">Line No.</p>
                        <p class="text-base font-bold text-gray-900">{{ $lineNumber ?? '—' }}</p>
                    </div>
                </div>

                <div class="border-b border-gray-100 pb-3">
                    <p class="text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1">Date Issued</p>
                    <p class="text-base font-bold text-gray-900">{{ \Carbon\Carbon::parse($issuedAt)->format('F j, Y') }}</p>
                </div>

                <div class="pt-2">
                    <p class="text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1">Verified On</p>
                    <p class="text-sm font-medium text-gray-700">{{ $verifiedAt->format('F j, Y g:i A') }}</p>
                </div>

                <div class="pt-4 border-t border-gray-200 text-center">
                    <p class="text-[10px] text-gray-400 italic">This verification page confirms that the certificate bearing this QR code is valid and was issued by Holy Cross Parish, Alcala, Pangasinan.</p>
                </div>

            </div>

        @endif

    </div>

</body>
</html>