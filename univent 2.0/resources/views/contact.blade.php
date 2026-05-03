@extends('layouts.app')

@section('title', 'Contact Us - Univent')

@section('content')
<div class="min-h-screen pt-28 pb-20 px-4">
    <div class="max-w-5xl mx-auto bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden flex flex-col md:flex-row">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-red-100 rounded-full blur-3xl opacity-80 -z-10"></div>
    <div class="absolute top-1/2 -right-24 w-80 h-80 bg-pink-100 rounded-full blur-3xl opacity-60 -z-10"></div>

        {{-- Left: Contact Form --}}
        <div class="flex-[1.5] p-8 md:p-12 border-b md:border-b-0 md:border-r border-slate-50">
            <h2 class="text-3xl font-extrabold text-slate-900 mb-2">Contact Us</h2>
            <p class="text-slate-500 mb-10 font-medium">Have questions or feedback? We'd love to hear from you!</p>

            <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="name" class="text-sm font-bold text-slate-700">Your Name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" required 
                            value="{{ old('name', Auth::user()->name) }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 transition-all outline-none"
                            placeholder="Enter Your Name">
                    </div>
                    <div class="space-y-2">
                        <label for="email" class="text-sm font-bold text-slate-700">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" required 
                            value="{{ old('email', Auth::user()->email) }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 transition-all outline-none"
                            placeholder="Enter Your Email">
                    </div>
                </div>
                <div class="space-y-2">
                    <label for="message" class="text-sm font-bold text-slate-700">Message <span class="text-red-500">*</span></label>
                    <textarea id="message" name="message" rows="5" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 transition-all outline-none resize-none"
                        placeholder="Write your message here">{{ old('message') }}</textarea>
                </div>
                
                <button type="submit" class="w-full py-4 bg-gradient-to-r from-red-600 to-pink-500 text-white font-bold rounded-2xl shadow-lg shadow-pink-500/25 transition transform hover:scale-[1.01] active:scale-95">
                    Send Message
                </button>
            </form>
        </div>

        {{-- Right: Info Section --}}
        <div class="flex-1 bg-slate-50/50 p-8 md:p-12">
            <h3 class="text-xl font-extrabold text-slate-900 mb-8">Get In Touch</h3>
            
            <div class="space-y-8">
                {{-- Address --}}
                <div class="flex gap-4 group">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors shadow-sm shadow-red-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 mb-1">Address</h4>
                        <p class="text-xs font-semibold text-red-500 leading-relaxed">Jl. D.I. Panjaitan No. 128, Purwokerto, Banyumas, Jawa Tengah</p>
                    </div>
                </div>

                {{-- WhatsApp --}}
                <a href="https://wa.me/6287824253298" target="_blank" class="flex gap-4 group">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors shadow-sm shadow-green-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 mb-1">WhatsApp</h4>
                        <p class="text-xs font-semibold text-red-500">087824253298</p>
                    </div>
                </a>

                {{-- Email --}}
                <a href="mailto:univenttelkom@gmail.com" class="flex gap-4 group">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-pink-100 flex items-center justify-center text-pink-600 group-hover:bg-pink-600 group-hover:text-white transition-colors shadow-sm shadow-pink-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 mb-1">Email Address</h4>
                        <p class="text-xs font-semibold text-red-500 underline decoration-red-200">univenttelkom@gmail.com</p>
                    </div>
                </a>
            </div>

            {{-- Support Note --}}
            <div class="mt-12 p-4 bg-white border border-slate-100 rounded-2xl">
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest text-center">Support Hours: 08:00 - 17:00 WIB</p>
            </div>
        </div>
    </div>
</div>
@endsection