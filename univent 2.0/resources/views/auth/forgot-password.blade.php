@extends('layouts.auth')
@section('title', 'Forgot Password')
@section('header', 'Reset Password')
@section('subtitle')
    Enter your email and we'll send you a 6-digit code to reset your password.
@endsection

@section('content')
<form action="{{ route('password.email') }}" method="POST" class="space-y-6">
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

    {{-- Button: Red to Pink Gradient --}}
    <div>
        <button type="submit" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-pink-500/25 text-sm font-bold text-white bg-gradient-to-r from-red-600 to-pink-500 hover:from-red-500 hover:to-pink-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-400 transition-all transform active:scale-[0.98]">
            Send Reset Code
        </button>
    </div>
</form>

<div class="mt-8 text-center">
    <a href="{{ route('login') }}" class="inline-flex items-center text-sm font-bold text-red-500 hover:text-pink-500 transition-colors group">
        <x-heroicon-o-arrow-left class="w-4 h-4 mr-2 transition-transform group-hover:-translate-x-1" />
        Back to Login
    </a>
</div>
@endsection