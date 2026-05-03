@extends('layouts.auth')
@section('title', 'Email Verification')
@section('header', 'Verify Your Email')
@section('subtitle')
    We've sent a code to <span class="font-bold text-gray-900">{{ $email ?? 'your.email@example.com' }}</span>
@endsection

@section('content')
<div x-data="otpHandler()" class="space-y-6">
    <form action="{{ route('verification.verify') }}" method="POST" id="verify-form">
        @csrf
        <input type="hidden" name="email" value="{{ request()->query('email', $email ?? '') }}">
        {{-- Input hidden untuk menampung gabungan 6 digit OTP --}}
        <input type="hidden" name="otp" x-model="otpValue">

        <div class="form-group">
            <label class="block text-sm font-bold text-center text-gray-700 mb-4">Enter 6-Digit Code</label>
            
            {{-- Kontainer 6 Kotak OTP --}}
            <div class="flex justify-between gap-2 sm:gap-3" @paste.prevent="handlePaste($event)">
                <template x-for="i in 6" :key="i">
                    <input type="text" 
                        maxlength="1" 
                        :id="'code-' + i"
                        class="w-12 h-14 sm:w-14 sm:h-16 text-center text-2xl font-extrabold border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-400 focus:border-red-400 transition-all bg-slate-50 shadow-sm"
                        @input="handleInput($event, i)"
                        @keydown.backspace="handleBackspace($event, i)"
                        required>
                </template>
            </div>
            <p class="mt-4 text-xs text-center text-gray-500">Check your spam folder if you don't see the email.</p>
        </div>

        <div class="mt-8">
            <button type="submit" 
                class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-pink-500/25 text-sm font-bold text-white bg-gradient-to-r from-red-600 to-pink-500 hover:from-red-500 hover:to-pink-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-400 transition-all transform active:scale-[0.98]">
                Verify Account
            </button>
        </div>
    </form>

    {{-- Fitur Resend Code dengan Timer --}}
    <div class="text-center">
        <form action="{{ route('verification.resend') }}" method="POST" > 
            @csrf
            <input type="hidden" name="email" value="{{ $email ?? '' }}">
            <p class="text-sm text-gray-600">
                Didn't receive the code? 
                <button type="submit" 
                    class="font-bold text-red-500 hover:text-pink-500 disabled:text-gray-400 disabled:cursor-not-allowed transition-colors"
                    :disabled="!canResend">
                    <span x-show="canResend">Resend Code</span>
                    <span x-show="!canResend" x-text="'Resend in ' + timer + 's'"></span>
                </button>
            </p>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function otpHandler() {
        return {
            otpValue: '',
            timer: 60,           // Tambahkan ini
            canResend: false,    // Tambahkan ini

            // Fungsi ini otomatis dijalankan oleh Alpine.js saat halaman dibuka
            init() {
                let countdown = setInterval(() => {
                    if (this.timer > 0) {
                        this.timer--;
                    } else {
                        this.canResend = true;
                        clearInterval(countdown); // Hentikan timer kalau sudah 0
                    }
                }, 1000);
            },

            handleInput(e, index) {
                const input = e.target;
                // Hanya izinkan angka
                input.value = input.value.replace(/[^0-9]/g, '');
                
                this.updateOtpValue();

                // Pindah ke kotak berikutnya jika terisi
                if (input.value && index < 6) {
                    document.getElementById('code-' + (index + 1)).focus();
                }
            },
            handleBackspace(e, index) {
                // Pindah ke kotak sebelumnya jika dihapus
                if (!e.target.value && index > 1) {
                    setTimeout(() => document.getElementById('code-' + (index - 1)).focus(), 10);
                }
                this.updateOtpValue();
            },
            handlePaste(e) {
                const pasteData = e.clipboardData.getData('text').slice(0, 6).split('');
                pasteData.forEach((char, i) => {
                    const el = document.getElementById('code-' + (i + 1));
                    if (el) el.value = char;
                });
                this.updateOtpValue();
            },
            updateOtpValue() {
                let val = '';
                for (let i = 1; i <= 6; i++) {
                    val += document.getElementById('code-' + i).value;
                }
                this.otpValue = val;
            }
        }
    }
</script>
@endpush
@endsection