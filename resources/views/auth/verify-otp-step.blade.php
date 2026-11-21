<x-guest-layout>
    <div class="min-h-screen w-full flex items-center justify-center bg-cover bg-center bg-no-repeat" 
         style="background-image: url('https://lh3.googleusercontent.com/gps-cs-s/ANWiy9RY-VjQ8i7rGQqFuXRfcJUgl_QgVphhIa373BUYFUaoGX5QD2mCXjkV9C63Pq8lLEQ3oIgxUZVPR9FbSllwI9QvSlSJMgWr8HDWR0Kukf5_QGOfhnBUoG89T7xpTPdwd-XjVuk=s680-w680-h510-rw');">
        
        <!-- Tinanggal ang blur at dark overlay dito -->

        <div class="relative z-10 w-full max-w-md mx-4 p-8 bg-white/95 rounded-3xl shadow-2xl border border-white/50 text-center overflow-hidden">
            
            <div id="success-screen" class="hidden flex-col items-center justify-center py-10 animate-fade-in">
                <div class="w-24 h-24 bg-blue-50 border-2 border-blue-200 rounded-3xl flex items-center justify-center mb-6 shadow-inner animate-bounce">
                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-black text-gray-800 uppercase tracking-tighter">Verified Successfully</h2>
                <p class="text-gray-500 mt-2 text-sm font-medium">Redirecting to reset password...</p>
            </div>

            <div id="input-screen">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-black text-[#4A3728] uppercase tracking-tighter mb-2">Verify OTP</h2>
                    <p class="text-sm text-gray-500 font-medium">Enter the 6-digit code sent to your email.</p>
                </div>

                @if($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-6 text-sm rounded-r-xl font-bold text-left">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="otp-form" method="POST" action="{{ route('password.verify-otp') }}">
                    @csrf
                    
                    <input type="hidden" name="email" value="{{ $email }}">
                    <input type="hidden" name="otp" id="final-otp">

                    <div class="flex justify-center gap-2 sm:gap-3 mb-8">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-input w-12 h-14 sm:w-14 sm:h-16 text-center text-2xl font-black border-2 border-gray-200 rounded-xl focus:border-[#4A3728] focus:ring-4 focus:ring-[#4A3728]/10 transition-all bg-gray-50 text-[#4A3728]" autofocus>
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-input w-12 h-14 sm:w-14 sm:h-16 text-center text-2xl font-black border-2 border-gray-200 rounded-xl focus:border-[#4A3728] focus:ring-4 focus:ring-[#4A3728]/10 transition-all bg-gray-50 text-[#4A3728]">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-input w-12 h-14 sm:w-14 sm:h-16 text-center text-2xl font-black border-2 border-gray-200 rounded-xl focus:border-[#4A3728] focus:ring-4 focus:ring-[#4A3728]/10 transition-all bg-gray-50 text-[#4A3728]">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-input w-12 h-14 sm:w-14 sm:h-16 text-center text-2xl font-black border-2 border-gray-200 rounded-xl focus:border-[#4A3728] focus:ring-4 focus:ring-[#4A3728]/10 transition-all bg-gray-50 text-[#4A3728]">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-input w-12 h-14 sm:w-14 sm:h-16 text-center text-2xl font-black border-2 border-gray-200 rounded-xl focus:border-[#4A3728] focus:ring-4 focus:ring-[#4A3728]/10 transition-all bg-gray-50 text-[#4A3728]">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-input w-12 h-14 sm:w-14 sm:h-16 text-center text-2xl font-black border-2 border-gray-200 rounded-xl focus:border-[#4A3728] focus:ring-4 focus:ring-[#4A3728]/10 transition-all bg-gray-50 text-[#4A3728]">
                    </div>

                    <button type="submit" class="w-full bg-[#4A3728] text-white font-black py-4 rounded-2xl uppercase tracking-widest text-xs hover:bg-[#3A2A1E] hover:shadow-lg transition-all">
                        Verify OTP
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.otp-input');
            const form = document.getElementById('otp-form');
            const finalOtp = document.getElementById('final-otp');
            const inputScreen = document.getElementById('input-screen');
            const successScreen = document.getElementById('success-screen');

            inputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    if (e.target.value.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                        inputs[index - 1].focus();
                    }
                });
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault(); 
                
                let otpValue = '';
                inputs.forEach(input => otpValue += input.value);
                
                if (otpValue.length === 6) { 
                    finalOtp.value = otpValue;
                    
                    inputScreen.style.display = 'none';
                    successScreen.style.display = 'flex';

                    setTimeout(() => {
                        form.submit(); 
                    }, 1500);
                } else {
                    alert("Please enter all 6 digits.");
                }
            });
        });
    </script>
</x-guest-layout>