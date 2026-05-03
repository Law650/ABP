@extends('layouts.auth')
@section('title', 'Sign Up')
@section('header', 'Create Account')
@section('subtitle')
    Already have an account? <a href="{{ route('login') }}" class="font-bold text-red-500 hover:text-pink-500 transition-colors">Log In</a>
@endsection

@section('content')
<form action="{{ route('register') }}" method="POST" class="space-y-5">
    @csrf
    
    {{-- Email Field --}}
    <div>
        <label for="email" class="block text-sm font-bold text-gray-700 mb-1.5">Email Address</label>
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-red-500 transition-colors">
                <x-heroicon-o-envelope class="w-5 h-5" />
            </div>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 transition-all placeholder:text-gray-400 shadow-sm"
                placeholder="name@example.com">
        </div>
    </div>

    {{-- Password Field --}}
    <div x-data="{ show: false }">
        <label for="password" class="block text-sm font-bold text-gray-700 mb-1.5">Password</label>
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

    {{-- Confirm Password Field --}}
    <div x-data="{ showConf: false }">
        <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-1.5">Confirm Password</label>
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

    {{-- Button: Red to Pink Gradient --}}
    <div>
        <button type="submit" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-pink-500/25 text-sm font-bold text-white bg-gradient-to-r from-red-600 to-pink-500 hover:from-red-500 hover:to-pink-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-400 transition-all transform active:scale-[0.98]">
            Daftar Akun Baru
        </button>
    </div>
</form>

<div class="mt-6">
    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-100"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-400 font-medium italic">Or sign up with</span>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('google.redirect') }}" class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-gray-200 rounded-xl bg-white text-sm font-bold text-gray-700 hover:bg-pink-50 hover:border-pink-200 hover:text-pink-600 transition-all shadow-sm">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EB4335"/>
            </svg>
            <span>Google Account</span>
        </a>
    </div>
</div>
@endsection