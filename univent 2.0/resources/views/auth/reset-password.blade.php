@extends('layouts.auth')
@section('title', 'Reset Password')
@section('header', 'Set New Password')
@section('subtitle')
    Enter the code sent to your email and create a strong new password for your account.
@endsection

@section('content')
<div x-data="resetHandler()">
    <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
        @csrf
        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
        {{-- Gabungan OTP --}}
        <input type="hidden" name="otp" x-model="otpValue">

        {{-- Section 1: OTP Input --}}
        <div class="form-group">
            <label class="block text-sm font-bold text-gray-700 mb-3">Verification Code</label>
            <div class="flex justify-between gap-2" @paste.prevent="handlePaste($event)">
                <template x-for="i in 6" :key="i">
                    <input type="text" 
                        maxlength="1" 
                        :id="'code-' + i"
                        class="w-11 h-14 text-center text-xl font-extrabold border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-400 focus:border-red-400 transition-all bg-slate-50 shadow-sm"
                        @input="handleInput($event, i)"
                        @keydown.backspace="handleBackspace($event, i)"
                        required>
                </template>
            </div>
        </div>

        {{-- Section 2: New Password --}}
        <div class="space-y-4 pt-2">
            <div x-data="{ show: false }">
                <label for="password" class="block text-sm font-bold text-gray-700 mb-1.5">New Password</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-red-500 transition-colors">
                        <x-heroicon-o-lock-closed class="w-5 h-5" />
                    </div>
                    <input :type="show ? 'text' : 'password'" id="password" name="password" required
                        class="block w-full pl-11 pr-12 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 transition-all placeholder:text-gray-400 shadow-sm"
                        placeholder="••••••••">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-pink-500 focus:outline-none transition-colors z-10">
                        <x-heroicon-o-eye x-show="!show" class="w-5 h-5" />
                        <x-heroicon-o-eye-slash x-show="show" class="w-5 h-5" x-cloak />
                    </button>
                </div>
            </div>

            <div x-data="{ showConf: false }">
                <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-1.5">Confirm New Password</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-red-500 transition-colors">
                        <x-heroicon-o-shield-check class="w-5 h-5" />
                    </div>
                    <input :type="showConf ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required
                        class="block w-full pl-11 pr-12 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 transition-all placeholder:text-gray-400 shadow-sm"
                        placeholder="••••••••">
                    <button type="button" @click="showConf = !showConf" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-pink-500 focus:outline-none transition-colors z-10">
                        <x-heroicon-o-eye x-show="!showConf" class="w-5 h-5" />
                        <x-heroicon-o-eye-slash x-show="showConf" class="w-5 h-5" x-cloak />
                    </button>
                </div>
            </div>
        </div>

        {{-- Button: Red to Pink Gradient --}}
        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-pink-500/25 text-sm font-bold text-white bg-gradient-to-r from-red-600 to-pink-500 hover:from-red-500 hover:to-pink-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-400 transition-all transform active:scale-[0.98]">
                Update Password
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function resetHandler() {
        return {
            otpValue: '',
            handleInput(e, index) {
                const input = e.target;
                input.value = input.value.replace(/[^0-9]/g, '');
                this.updateOtpValue();
                if (input.value && index < 6) {
                    document.getElementById('code-' + (index + 1)).focus();
                }
            },
            handleBackspace(e, index) {
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