@extends('layouts.app')

@section('title', 'Event History - Univent')

@section('content')
<div class="min-h-screen bg-slate-50/50 pt-28 pb-20 px-4 relative overflow-hidden">
    {{-- Glow Background Effects --}}
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-red-100 rounded-full blur-3xl opacity-50 -z-10"></div>
    <div class="absolute top-1/2 -right-24 w-80 h-80 bg-pink-100 rounded-full blur-3xl opacity-40 -z-10"></div>

    <div class="max-w-5xl mx-auto relative">
        {{-- Header Section --}}
        <div class="mb-12 text-center md:text-left">
            <h1 class="text-3xl font-extrabold text-slate-900 mb-2 italic tracking-tight">Event History</h1>
            <p class="text-slate-500 font-medium">Pantau status pendaftaran dan daftar event yang pernah kamu ikuti.</p>
        </div>

        @if ($registrations->isEmpty())
            {{-- Modern Empty State --}}
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 p-16 text-center">
                <div class="w-24 h-24 bg-red-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-red-500">
                    <x-heroicon-o-calendar class="w-12 h-12" />
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Belum ada riwayat pendaftaran</h3>
                <p class="text-slate-500 mb-8 max-w-sm mx-auto font-medium">Sepertinya kamu belum mendaftar ke event apa pun. Yuk, cari event menarik!</p>
                <a href="{{ route('events.browse') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-red-600 to-pink-500 text-white font-bold rounded-2xl shadow-lg shadow-pink-500/25 hover:scale-105 transition transform active:scale-95">
                    Browse Events Now
                </a>
            </div>
        @else
            {{-- Registration List --}}
            <div class="space-y-4">
                @foreach ($registrations as $registration)
                    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6 hover:border-red-100 transition-colors group">
                        <div class="flex items-center gap-6 w-full md:w-auto">
                            {{-- Poster Preview --}}
                        <div class="h-20 w-16 bg-slate-100 rounded-xl overflow-hidden shadow-sm flex-shrink-0 flex items-center justify-center text-slate-300">
                            @if($registration->event->event_poster)
                                <img src="data:image/jpeg;base64,{{ $registration->event->event_poster }}" class="w-full h-full object-cover">
                            @else
                                {{-- Gunakan ikon Photo (o-photo) atau SVG manual jika o-image tidak ketemu --}}
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 00-2 2z" />
                                </svg>
                            @endif
                        </div>
                            
                            <div class="space-y-1">
                                <h3 class="text-lg font-black text-slate-900 italic group-hover:text-red-500 transition-colors line-clamp-1">
                                    {{ $registration->event->event_title ?? 'Unknown Event' }}
                                </h3>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs font-bold text-slate-400">
                                    <div class="flex items-center gap-1.5 uppercase tracking-tighter">
                                        <x-heroicon-o-calendar class="w-3.5 h-3.5" />
                                        {{ $registration->created_at->format('d M Y') }}
                                    </div>
                                    <div class="flex items-center gap-1.5 uppercase tracking-tighter">
                                        <x-heroicon-o-map-pin class="w-3.5 h-3.5" />
                                        {{ $registration->event->event_location ?? 'Online' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between w-full md:w-auto md:justify-end gap-6 border-t md:border-none pt-4 md:pt-0">
                            {{-- Status Badge --}}
                            <div class="flex flex-col md:items-end">
                                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest mb-1">Status</span>
                                <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest
                                    {{ $registration->status === 'approved' ? 'bg-green-100 text-green-600' : 
                                       ($registration->status === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600') }}">
                                    {{ $registration->status }}
                                </span>
                            </div>

                            {{-- Actions --}}
                            <a href="{{ route('registration.show', $registration->id) }}" 
                                class="px-6 py-3 bg-slate-900 text-white text-xs font-black rounded-xl hover:bg-red-500 transition-all shadow-lg shadow-slate-200 uppercase tracking-widest">
                                Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination Support --}}
            @if(method_exists($registrations, 'links'))
                <div class="mt-12">
                    {{ $registrations->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection