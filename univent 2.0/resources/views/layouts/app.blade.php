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
                        class="text-sm font-bold transition-all duration-300 {{ request()->routeIs('events.browse') ? 'text-red-600' : 'text-slate-600 hover:text-red-500' }}">
                            Events
                        </a>

                        {{-- Form Submit Event (Hanya untuk EO & Admin) --}}
                        @auth
                            @if(auth()->user()->hasRole('eo') || auth()->user()->hasRole('admin'))
                                <a href="{{ route('submit-event.form') }}" 
                                class="text-sm font-bold transition-all duration-300 {{ request()->routeIs('submit-event.form') ? 'text-red-600' : 'text-slate-600 hover:text-red-500' }}">
                                Submit Event
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>

                {{-- Bagian Kanan Desktop --}}
                <div class="hidden md:flex items-center gap-4">
                    @auth
                        {{-- Tombol Upgrade Desktop --}}
                        @if(auth()->user()->hasRole('user') && !auth()->user()->hasRole('eo') && !auth()->user()->hasRole('admin'))
                            @if(auth()->user()->eo_request_status === 'none' || auth()->user()->eo_request_status === 'rejected')
                                <button @click="$dispatch('open-modal-eo')" class="flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-gradient-to-r from-red-600 to-pink-500 text-white text-xs font-extrabold shadow-lg shadow-pink-500/30 hover:scale-105 transition-all active:scale-95">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    Upgrade EO
                                </button>
                            @elseif(auth()->user()->eo_request_status === 'pending')
                                <div class="flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-slate-100 text-slate-500 text-xs font-extrabold border border-slate-200 cursor-not-allowed">
                                    <svg class="animate-spin w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Pending...
                                </div>
                            @endif
                        @endif

                        {{-- Dropdown Profile (Dipindah ke Kiri Lonceng) --}}
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
                            <div x-show="profileMenu" @click.away="profileMenu = false" x-transition x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl shadow-slate-200/50 py-2 border border-slate-100 z-50">
                                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-red-50 hover:text-red-600 font-medium">My Profile</a>
                                <a href="{{ route('user.event.history') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-red-50 hover:text-red-600 font-medium">Event History</a>
                                
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

                        {{-- 🔔 FITUR NOTIFIKASI WEB (DESKTOP) (Sekarang di Kanan) --}}
                        <div class="relative flex items-center" x-data="{ openNotif: false }">
                            <button @click="openNotif = !openNotif" class="relative p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-full transition-all focus:outline-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="absolute top-1 right-1 flex h-3 w-3">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-600 border-2 border-white"></span>
                                    </span>
                                @endif
                            </button>

                            {{-- Dropdown Notif --}}
                            <div x-show="openNotif" @click.away="openNotif = false" x-cloak
                                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 class="absolute right-0 top-12 w-80 bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 z-50 overflow-hidden cursor-default">
                                
                                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/80">
                                    <h3 class="font-bold text-slate-800 text-sm">Notifikasi</h3>
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <form action="{{ route('notifications.markAllRead') }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="text-[11px] text-red-600 hover:text-red-800 font-bold hover:underline">Tandai sudah dibaca</button>
                                        </form>
                                    @endif
                                </div>

                                <div class="max-h-[350px] overflow-y-auto">
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        @foreach(auth()->user()->unreadNotifications as $notification)
                                            <div class="p-4 border-b border-slate-50 hover:bg-slate-50 transition">
                                                <div class="flex gap-3">
                                                    <div class="flex-shrink-0 mt-1">
                                                        @if(isset($notification->data['status']) && $notification->data['status'] == 'approved')
                                                            <div class="w-8 h-8 rounded-full bg-green-100 text-green-500 flex items-center justify-center"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg></div>
                                                        @elseif(isset($notification->data['status']) && $notification->data['status'] == 'pending')
                                                            <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-500 flex items-center justify-center"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                                                        @else
                                                            <div class="w-8 h-8 rounded-full bg-red-100 text-red-500 flex items-center justify-center"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg></div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <p class="text-sm text-slate-700 leading-snug">{!! $notification->data['message'] ?? 'Ada pembaruan.' !!}</p>
                                                        <span class="text-[10px] font-bold text-slate-400 mt-1 block uppercase tracking-wider">{{ $notification->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="p-8 text-center flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 text-slate-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                            <p class="text-slate-400 text-xs font-bold">Belum ada notifikasi baru.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-pink-500 text-white text-sm font-bold shadow-lg shadow-pink-500/25 transition hover:scale-105 active:scale-95">Login</a>
                    @endauth
                </div>

                {{-- Hamburger Button & Notif Mobile --}}
                <div class="md:hidden flex items-center gap-4">
                    @auth
                        {{-- ========================================== --}}
                        {{-- 🔔 FITUR NOTIFIKASI WEB (MOBILE)           --}}
                        {{-- ========================================== --}}
                        <div class="relative flex items-center" x-data="{ openNotifMob: false }">
                            <button @click="openNotifMob = !openNotifMob" class="relative text-slate-500 hover:text-red-600 focus:outline-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="absolute top-0 right-0 flex h-2.5 w-2.5 -mt-0.5 -mr-0.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-600 border border-white"></span>
                                    </span>
                                @endif
                            </button>

                            <div x-show="openNotifMob" @click.away="openNotifMob = false" x-cloak class="absolute right-0 top-10 w-[85vw] max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden">
                                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                                    <h3 class="font-bold text-slate-800 text-sm">Notifikasi</h3>
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <form action="{{ route('notifications.markAllRead') }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="text-[11px] text-red-600 font-bold">Tandai dibaca</button>
                                        </form>
                                    @endif
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        @foreach(auth()->user()->unreadNotifications as $notification)
                                            <div class="p-4 border-b border-slate-50 hover:bg-slate-50 transition">
                                                <div class="flex gap-3">
                                                    <div class="flex-shrink-0 mt-1">
                                                        @if(isset($notification->data['status']) && $notification->data['status'] == 'approved')
                                                            <div class="w-7 h-7 rounded-full bg-green-100 text-green-500 flex items-center justify-center"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg></div>
                                                        @elseif(isset($notification->data['status']) && $notification->data['status'] == 'pending')
                                                            <div class="w-7 h-7 rounded-full bg-amber-100 text-amber-500 flex items-center justify-center"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                                                        @else
                                                            <div class="w-7 h-7 rounded-full bg-red-100 text-red-500 flex items-center justify-center"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg></div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <p class="text-sm text-slate-700 leading-snug">{!! $notification->data['message'] ?? 'Ada pembaruan.' !!}</p>
                                                        <span class="text-[10px] font-bold text-slate-400 mt-1 block">{{ $notification->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="p-6 text-center">
                                            <p class="text-slate-400 text-xs font-bold">Belum ada notifikasi.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endauth

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
            
            {{-- Form Submit Event Mobile --}}
            @auth
                @if(auth()->user()->hasRole('eo') || auth()->user()->hasRole('admin'))
                    <a href="{{ route('submit-event.form') }}" class="block text-base font-bold text-slate-700">Submit Event</a>
                @endif
            @endauth

            <hr class="border-slate-100">
            @auth
                {{-- Tombol Upgrade Mobile --}}
                @if(auth()->user()->hasRole('user') && !auth()->user()->hasRole('eo') && !auth()->user()->hasRole('admin'))
                    @if(auth()->user()->eo_request_status === 'none' || auth()->user()->eo_request_status === 'rejected')
                        <button @click="$dispatch('open-modal-eo')" class="w-full text-left flex items-center gap-2 text-base font-extrabold text-red-600 mb-4">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            Upgrade to Event Organizer
                        </button>
                    @elseif(auth()->user()->eo_request_status === 'pending')
                        <div class="w-full text-left text-base font-bold text-slate-400 mb-4">Pengajuan EO Diproses...</div>
                    @endif
                @endif

                <a href="{{ route('profile.show') }}" class="block text-base font-bold text-red-500">My Profile</a>
                <a href="{{ route('user.event.history') }}" class="block text-base font-bold text-slate-700">Event History</a>
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
                    @auth
                        @if(auth()->user()->hasRole('eo') || auth()->user()->hasRole('admin'))
                            <li><a href="{{ route('submit-event.form') }}" class="hover:text-red-400 transition">Submit Event</a></li>
                        @endif
                    @endauth
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-6 text-white uppercase tracking-wider text-xs">Categories</h4>
                <ul class="space-y-3 text-slate-400 text-sm">
                    <li><a href="/browse-events?category=Seminar" class="hover:text-red-400 transition flex items-center group">Seminars</a></li>
                    <li><a href="/browse-events?category=Workshop" class="hover:text-red-400 transition flex items-center group">Workshops</a></li>
                    <li><a href="/browse-events?category=Competition" class="hover:text-red-400 transition flex items-center group">Competitions</a></li>
                    <li><a href="/browse-events?category=Gathering" class="hover:text-red-400 transition flex items-center group">Gatherings</a></li>
                    <li><a href="/browse-events?category=Other" class="hover:text-red-400 transition flex items-center group">Others</a></li>
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

    {{-- ========================================== --}}
    {{-- MODAL FORMULIR UPGRADE EO (Light Theme)    --}}
    {{-- ========================================== --}}
    @auth
        @if(auth()->user()->hasRole('user') && !auth()->user()->hasRole('eo') && !auth()->user()->hasRole('admin'))
            <div x-data="{ openEoModal: false }" 
                 @open-modal-eo.window="openEoModal = true"
                 x-show="openEoModal" 
                 class="fixed inset-0 z-[100] flex items-center justify-center w-full h-full bg-slate-900/40 backdrop-blur-sm p-4" 
                 x-cloak>
                 
                <div @click.away="openEoModal = false" 
                     x-show="openEoModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                     class="bg-white border border-slate-100 rounded-3xl p-6 md:p-8 max-w-lg w-full shadow-2xl relative overflow-hidden text-left max-h-[90vh] overflow-y-auto scrollbar-hide">
                    
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-red-600 to-pink-500"></div>

                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-extrabold text-slate-900">Formulir Verifikasi EO</h3>
                            <p class="text-slate-500 text-xs font-medium">Lengkapi data untuk mendapatkan hak akses.</p>
                        </div>
                    </div>
                    
                    <form action="{{ route('user.request-eo') }}" method="POST" class="space-y-4">
                        @csrf
                        
                        {{-- Tipe Organisasi --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tipe Penyelenggara <span class="text-red-500">*</span></label>
                            <select name="eo_org_type" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all text-sm font-medium text-slate-700 outline-none">
                                <option value="" disabled selected>-- Pilih Tipe --</option>
                                <option value="Internal Kampus">Internal Kampus (Himpunan/UKM/BEM)</option>
                                <option value="Eksternal Publik">Eksternal Publik (Umum/Sponsor)</option>
                            </select>
                        </div>

                        {{-- Nama Organisasi --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Organisasi / Instansi <span class="text-red-500">*</span></label>
                            <input type="text" name="eo_org_name" required placeholder="Contoh: HMIF Telkom" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all text-sm font-medium text-slate-700 outline-none">
                        </div>

                        {{-- Nama PIC --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Penanggung Jawab <span class="text-red-500">*</span></label>
                            <input type="text" name="eo_pic_name" required value="{{ Auth::user()->name }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all text-sm font-medium text-slate-700 outline-none">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Nomor WA --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nomor WhatsApp <span class="text-red-500">*</span></label>
                                <input type="text" name="eo_phone" required placeholder="0812xxxx" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all text-sm font-medium text-slate-700 outline-none">
                            </div>
                            
                            {{-- Akun IG --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Akun Instagram</label>
                                <input type="text" name="eo_instagram" placeholder="@namainstagram" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all text-sm font-medium text-slate-700 outline-none">
                            </div>
                        </div>

                        <div class="pt-4 flex flex-col-reverse sm:flex-row justify-end gap-3 border-t border-slate-100 mt-6">
                            <button type="button" @click="openEoModal = false" class="w-full sm:w-auto px-6 py-2.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl font-bold transition">Batal</button>
                            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-red-600 to-pink-500 text-white rounded-xl font-bold shadow-lg shadow-pink-500/25 hover:scale-105 transition active:scale-95">Kirim Pengajuan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endauth
    
    {{-- Floating Action Button (FAB) Contact Us --}}
    <div class="fixed bottom-6 right-6 z-50" x-data="{ hover: false }">
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