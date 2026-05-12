@extends('layouts.app')

@section('content')
{{-- Hero Section --}}
<section class="relative overflow-hidden pt-16 pb-20 px-4">
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-red-100 rounded-full blur-3xl opacity-50"></div>
    <div class="max-w-7xl mx-auto text-center relative">
        <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 leading-tight">
            Discover Campus Events at <br>
            <span class="bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-pink-500">
                Telkom University Purwokerto
            </span>
        </h1>
        <p class="mt-6 text-lg text-slate-600 max-w-2xl mx-auto font-medium">
            Stay connected with seminars, workshops, and gatherings organized by student associations and lecturers.
        </p>
        <div class="mt-10 flex flex-wrap justify-center gap-4">
            <a href="/browse-events" class="px-8 py-4 bg-gradient-to-r from-red-600 to-pink-500 text-white font-bold rounded-2xl shadow-xl shadow-pink-500/25 hover:scale-105 transition transform active:scale-95">
                Browse Events
            </a>
            
            {{-- Tombol Submit Event di Hero (Hanya untuk EO & Admin) --}}
            @auth
                @if(auth()->user()->hasRole('eo') || auth()->user()->hasRole('admin'))
                    <a href="{{ route('submit-event.form') }}" class="px-8 py-4 bg-white text-slate-900 font-bold rounded-2xl border border-slate-200 shadow-sm hover:bg-slate-50 transition transform active:scale-95">
                        Submit Event
                    </a>
                @endif
            @endauth
        </div>
    </div>
</section>

{{-- ========================================== --}}
{{-- SECTION TRENDING DI DASHBOARD --}}
{{-- ========================================== --}}
@if(isset($trendingEvents) && $trendingEvents->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-12">
        <div class="flex items-end justify-between mb-8">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <h2 class="text-3xl font-extrabold text-slate-900">🌟 Sedang Trending</h2>
                    <span class="px-3 py-1 bg-orange-100 text-orange-600 text-xs font-bold rounded-full">Paling Diminati</span>
                </div>
                <div class="h-1.5 w-20 bg-gradient-to-r from-orange-400 to-red-500 rounded-full mt-2"></div>
            </div>
            <a href="{{ route('events.browse') }}" class="text-sm font-bold text-orange-500 hover:text-red-500 flex items-center gap-1 transition">
                Lihat Semua <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($trendingEvents as $event)
                <div class="group bg-white rounded-3xl border-2 border-orange-100 overflow-hidden shadow-lg shadow-orange-100/40 hover:shadow-2xl hover:shadow-orange-200/50 transition-all duration-300 transform hover:-translate-y-2">
                    {{-- Poster --}}
                    <div class="h-56 w-full relative overflow-hidden bg-slate-200">
                        <img src="data:image/jpeg;base64,{{ $event->event_poster }}" class="h-full w-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute top-4 left-4 flex gap-2">
                            <span class="px-3 py-1 bg-gradient-to-r from-orange-500 to-red-500 text-white text-[10px] font-extrabold uppercase rounded-lg shadow-sm">Trending</span>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-6">
                        <h3 class="text-xl font-extrabold text-slate-900 mb-2 group-hover:text-orange-500 transition line-clamp-1 text-ellipsis">{{ $event->event_title }}</h3>
                        
                        <div class="space-y-3 mb-8 mt-4">
                            {{-- Jumlah Pendaftar --}}
                            <div class="flex items-center gap-3 text-slate-500">
                                <div class="p-2 bg-orange-50 rounded-lg text-orange-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </div>
                                <span class="text-xs font-bold">{{ $event->registrations_count ?? 0 }} Orang Mendaftar</span>
                            </div>
                            {{-- Tanggal --}}
                            <div class="flex items-center gap-3 text-slate-500">
                                <div class="p-2 bg-red-50 rounded-lg text-red-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="text-xs font-bold">{{ date('D, M d, Y', strtotime($event->start_date)) }}</span>
                            </div>
                        </div>

                        <a href="{{ route('events.show', $event->id) }}" class="block w-full text-center py-4 bg-slate-900 text-white rounded-2xl text-sm font-bold hover:bg-orange-500 transition shadow-lg shadow-slate-200">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Upcoming Events Section (Semua Event Biasa) --}}
<section class="max-w-7xl mx-auto px-4 py-8 mb-20">
    <div class="flex items-end justify-between mb-12">
        <div class="space-y-2">
            <h2 class="text-3xl font-extrabold text-slate-900">Upcoming Events</h2>
            <div class="h-1.5 w-20 bg-gradient-to-r from-red-500 to-pink-500 rounded-full"></div>
        </div>
        @if(isset($events) && $events->count() > 0)
        <a href="/browse-events" class="text-sm font-bold text-red-500 hover:text-pink-500 flex items-center gap-1 transition">
            See All Events <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
        </a>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @if(isset($events))
            @forelse ($events as $event)
                <div class="group bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm hover:shadow-2xl hover:shadow-slate-200/50 transition-all duration-300 transform hover:-translate-y-2">
                    {{-- Card Image --}}
                    <div class="h-56 w-full relative overflow-hidden bg-slate-200">
                        <img src="data:image/jpeg;base64,{{ $event->event_poster }}" alt="Poster" class="h-full w-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute top-4 left-4 flex gap-2">
                            @if ($event->tags)
                                @foreach (explode(',', $event->tags) as $tag)
                                    <span class="px-3 py-1 bg-white/90 backdrop-blur-md text-[10px] font-extrabold uppercase tracking-widest text-red-600 rounded-lg shadow-sm">{{ $tag }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    
                    {{-- Card Body --}}
                    <div class="p-6">
                        <h3 class="text-xl font-extrabold text-slate-900 mb-2 group-hover:text-red-500 transition line-clamp-1 text-ellipsis">{{ $event->event_title }}</h3>
                        <p class="text-sm text-slate-500 line-clamp-2 leading-relaxed mb-6">{{ $event->event_description }}</p>
                        
                        <div class="space-y-3 mb-8">
                            <div class="flex items-center gap-3 text-slate-500">
                                <div class="p-2 bg-red-50 rounded-lg text-red-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="text-xs font-bold">{{ date('D, M d, Y', strtotime($event->start_date)) }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-slate-500">
                                <div class="p-2 bg-pink-50 rounded-lg text-pink-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <span class="text-xs font-bold line-clamp-1">{{ $event->event_location }}</span>
                            </div>
                        </div>

                        <a href="{{ route('events.show', $event->id) }}" class="block w-full text-center py-4 bg-slate-900 text-white rounded-2xl text-sm font-bold hover:bg-red-500 transition shadow-lg shadow-slate-200">
                            View Details
                        </a>
                    </div>
                </div>
            @empty
                {{-- Status Kosong: Tampil jika $events tidak ada data --}}
                <div class="col-span-full py-20 flex flex-col items-center justify-center text-center">
                    <div class="relative mb-8">
                        {{-- Glow Effect --}}
                        <div class="absolute inset-0 bg-red-100 rounded-full scale-150 blur-3xl opacity-60"></div>
                        
                        {{-- Status Icon --}}
                        <div class="relative w-24 h-24 bg-gradient-to-br from-red-500 to-pink-500 rounded-[2rem] flex items-center justify-center shadow-2xl shadow-pink-500/30 rotate-6 group-hover:rotate-0 transition-transform duration-500">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z opacity-40"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2 2 4-4" />
                            </svg>
                        </div>
                    </div>

                    <h3 class="text-2xl font-extrabold text-slate-900 mb-2">Belum ada event untuk saat ini</h3>
                    <p class="text-slate-500 max-w-sm mx-auto mb-10 font-medium">
                        Sepertinya semua sedang bersiap. Yuk, jadi yang pertama membuat event seru di kampus!
                    </p>

                    {{-- Tombol Mulai Buat Event (Hanya untuk EO & Admin) --}}
                    @auth
                        @if(auth()->user()->hasRole('eo') || auth()->user()->hasRole('admin'))
                            <a href="{{ route('submit-event.form') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-white border-2 border-red-500 text-red-500 font-bold rounded-2xl hover:bg-red-500 hover:text-white transition-all duration-300 shadow-lg shadow-red-500/10 active:scale-95 group">
                                <svg class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Mulai Buat Event
                            </a>
                        @endif
                    @endauth
                </div>
            @endforelse
        @endif
    </div>
</section>
@endsection