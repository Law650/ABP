<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Univent') - Campus Event Portal</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('images/univent-logo3.png') }}" type="image/png">
    
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    {{-- Navbar --}}
    <nav x-data="{ mobileMenu: false, profileMenu: false }" class="fixed top-0 inset-x-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center gap-8">
                    <a href="/" class="flex-shrink-0 transition hover:scale-105">
                        <img src="{{ asset('images/univent-logo.png') }}" alt="Univent" class="h-10 w-auto">
                    </a>
                    {{-- Desktop Menu --}}
                    <div class="hidden md:flex items-center gap-8">
                        {{-- Beranda / Home --}}
                        <a href="{{ route('dashboard') }}" 
                        class="text-sm font-bold transition-all duration-300 {{ request()->routeIs('dashboard') ? 'text-red-600' : 'text-slate-600 hover:text-red-500' }}">
                            Home
                        </a>

                        {{-- Jelajah Event --}}
                        <a href="{{ route('events.browse') }}" 
                        class="text-sm font-bold transition-all duration-300 {{ request()->routeIs('browse-events') ? 'text-red-600' : 'text-slate-600 hover:text-red-500' }}">
                            Events
                        </a>

                        {{-- Form Submit Event --}}
                        <a href="{{ route('submit-event.form') }}" 
                        class="text-sm font-bold transition-all duration-300 {{ request()->routeIs('submit-event.form') ? 'text-red-600' : 'text-slate-600 hover:text-red-500' }}">
                        Submit Event
                        </a>
                    </div>
                </div>

                <div class="hidden md:flex items-center gap-4">
                    @auth
                        {{-- Dropdown Profile --}}
                        <div class="relative">
                            <button @click="profileMenu = !profileMenu" class="flex items-center gap-3 p-1 rounded-full hover:bg-slate-100 transition focus:outline-none">
                                <span class="text-sm font-bold text-slate-700 ml-2">{{ Auth::user()->name }}</span>
                                <div class="h-9 w-9 rounded-full ring-2 ring-red-500/20 overflow-hidden bg-slate-200">
                                    @if(Auth::user()->avatar)
                                        <img src="data:image/jpeg;base64,{{ Auth::user()->avatar }}" class="h-full w-full object-cover">
                                    @else
                                        <img src="{{ asset('images/default-avatar.svg') }}" class="h-full w-full object-cover">
                                    @endif
                                </div>
                            </button>
                            <div x-show="profileMenu" @click.away="profileMenu = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl shadow-slate-200/50 py-2 border border-slate-100">
                                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-red-50 hover:text-red-600 font-medium">My Profile</a>
                                @if(Auth::user()->isAdmin())
                                    <a href="/admin/event-list" class="block px-4 py-2 text-sm text-slate-600 hover:bg-red-50 hover:text-red-600 font-medium">Admin Panel</a>
                                @endif
                                <hr class="my-1 border-slate-100">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-bold">Sign Out</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-pink-500 text-white text-sm font-bold shadow-lg shadow-pink-500/25 transition hover:scale-105 active:scale-95">Login</a>
                    @endauth
                </div>

                {{-- Hamburger Button --}}
                <div class="md:hidden flex items-center">
                    <button @click="mobileMenu = !mobileMenu" class="text-slate-600 hover:text-red-500 focus:outline-none">
                        <svg x-show="!mobileMenu" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                        <svg x-show="mobileMenu" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenu" x-cloak class="md:hidden bg-white border-t border-slate-100 px-4 py-6 space-y-4 shadow-xl">
            <a href="/" class="block text-base font-bold text-slate-700">Home</a>
            <a href="/browse-events" class="block text-base font-bold text-slate-700">Events</a>
            <a href="/submit-event" class="block text-base font-bold text-slate-700">Submit Event</a>
            <hr class="border-slate-100">
            @auth
                <a href="{{ route('profile.show') }}" class="block text-base font-bold text-red-500">My Profile</a>
                <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="text-base font-bold text-slate-400">Logout</button></form>
            @else
                <a href="{{ route('login') }}" class="block text-center py-3 rounded-xl bg-red-500 text-white font-bold">Login</a>
            @endauth
        </div>
    </nav>

    <main class="pt-20">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-slate-900 text-white py-16 mt-20">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="space-y-4">
                <img src="{{ asset('images/univent-logo2.png') }}" class="h-12">
                <p class="text-slate-400 text-sm leading-relaxed">Connecting Telkom University Purwokerto students through incredible events.</p>
            </div>
            <div>
                <h4 class="font-bold mb-6">Quick Links</h4>
                <ul class="space-y-3 text-slate-400 text-sm">
                    <li><a href="/browse-events" class="hover:text-red-400 transition">Browse Events</a></li>
                    <li><a href="/submit-event" class="hover:text-red-400 transition">Submit Event</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-6 text-white uppercase tracking-wider text-xs">Categories</h4>
                <ul class="space-y-3 text-slate-400 text-sm">
                    <li>
                        <a href="/browse-events?category=Seminar" class="hover:text-red-400 transition flex items-center group">
                            Seminars
                        </a>
                    </li>
                    <li>
                        <a href="/browse-events?category=Workshop" class="hover:text-red-400 transition flex items-center group">
                            Workshops
                        </a>
                    </li>
                    <li>
                        <a href="/browse-events?category=Competition" class="hover:text-red-400 transition flex items-center group">
                            Competitions
                        </a>
                    </li>
                    <li>
                        <a href="/browse-events?category=Gathering" class="hover:text-red-400 transition flex items-center group">
                            Gatherings
                        </a>
                    </li>
                    <li>
                        <a href="/browse-events?category=Other" class="hover:text-red-400 transition flex items-center group">
                            Others
                        </a>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-6">Contact Us</h4>
                <p class="text-slate-400 text-sm">Jl. DI Panjaitan No.128, Purwokerto</p>
                <a href="mailto:univenttelkom@gmail.com" class="text-red-400 font-bold block mt-2">univenttelkom@gmail.com</a>
            </div>
        </div>
    </footer>

    @include('partials.sweetalert')
    @stack('scripts')

    {{-- Floating Action Button (FAB) --}}
<div class="fixed bottom-6 right-6 z-50" x-data="{ hover: false }">
    {{-- Tooltip yang muncul pas di-hover --}}
    <div x-show="hover" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="absolute bottom-full mb-3 right-0 whitespace-nowrap bg-slate-900 text-white text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-lg shadow-xl" x-cloak>
        Need Help? Contact Us
    </div>

    {{-- Tombol Utama --}}
    <a href="{{ route('contact') }}" 
   @mouseenter="hover = true" 
   @mouseleave="hover = false"
   class="flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-red-600 to-pink-500 text-white shadow-lg shadow-pink-500/40 transition-all duration-300 hover:scale-110 hover:-rotate-12 active:scale-95 group">
    
    <svg class="w-7 h-7 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
    </svg>
</a>
</div>
</body>
</html>