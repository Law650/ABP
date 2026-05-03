@extends('layouts.app')

@section('title', $event->event_title . ' - Univent')

@section('content')
<div class="min-h-screen bg-slate-50/50 pt-28 pb-20 px-4 relative overflow-hidden">
    {{-- Glow Effect Background --}}
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-red-100 rounded-full blur-3xl opacity-50 -z-10"></div>
    <div class="absolute top-1/2 -right-24 w-80 h-80 bg-pink-100 rounded-full blur-3xl opacity-40 -z-10"></div>

    <div class="max-w-6xl mx-auto relative">
        {{-- Back Button --}}
        <a href="/browse-events" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-600 font-bold rounded-2xl hover:bg-slate-50 transition-all mb-10 shadow-sm text-sm">
            <x-heroicon-o-arrow-left class="w-4 h-4" />
            <span>Back to events</span>
        </a>

        <div class="flex flex-col lg:flex-row gap-12">
            
            {{-- Main Content: Poster & Description --}}
                    <div class="lg:w-2/3 space-y-10">
                        
                        {{-- Event Poster - Ukuran diperkecil dengan max-w-md dan mx-auto --}}
                        <div class="flex justify-left">
                            <div class="group relative max-w-md w-full rounded-[2.5rem] overflow-hidden bg-slate-900 shadow-2xl shadow-slate-200/50 cursor-zoom-in" onclick="openModal()">
                                <img src="data:image/jpeg;base64,{{ $event->event_poster }}" alt="Event Poster" 
                                    class="w-full h-auto object-cover group-hover:scale-105 transition-transform duration-700">
                                <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors"></div>
                            </div>
                        </div>

                        {{-- Event Content Card --}}
                        <div class="bg-white rounded-[2.5rem] p-8 md:p-12 border border-slate-100 shadow-xl shadow-slate-200/50">
                            <div class="flex flex-wrap gap-3 mb-6">
                                <span class="px-4 py-1.5 bg-green-100 text-green-600 text-[11px] font-black uppercase tracking-wider rounded-lg">
                                    {{ $event->event_category }}
                                </span>
                                <span class="px-4 py-1.5 bg-red-100 text-red-600 text-[11px] font-black uppercase tracking-wider rounded-lg">
                                    {{ ucwords(str_replace('_', ' ', $event->organizer_type)) }}
                                </span>
                            </div>

                            <h1 class="text-3xl md:text-5xl font-black text-slate-900 mb-4 leading-tight italic">{{ $event->event_title }}</h1>
                            
                            <div class="flex items-center gap-3 text-slate-500 font-bold mb-8">
                                <div class="p-2 bg-slate-100 rounded-xl">
                                    <x-heroicon-s-users class="w-5 h-5 text-slate-600" />
                                </div>
                                <span class="text-sm tracking-wide">Organized by <span class="text-slate-900">{{ $event->organizer_name }}</span></span>
                            </div>

                            <div class="h-px w-full bg-slate-100 mb-10"></div>

                            <h2 class="text-xl font-black text-slate-900 mb-6 uppercase tracking-widest flex items-center gap-3">
                                <span class="w-8 h-1 bg-red-500 rounded-full"></span>
                                About Event
                            </h2>
                            <div class="text-slate-600 leading-relaxed space-y-4 font-medium">
                                {!! nl2br(e($event->event_description)) !!}
                            </div>
                        </div>
                    </div>

            {{-- Sidebar: Info Cards --}}
            <div class="lg:w-1/3 space-y-6">
                <div class="sticky top-28 space-y-6">
                    {{-- Schedule Card --}}
                    <div class="bg-white rounded-[2rem] p-8 border border-slate-100 shadow-xl shadow-slate-200/50 space-y-8">
                        <div class="flex items-start gap-4">
                            <div class="p-3 bg-red-50 rounded-2xl text-red-500">
                                <x-heroicon-s-calendar class="w-6 h-6" />
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-400 uppercase tracking-tighter mb-1">Start Date</span>
                                <p class="text-slate-800 font-black italic">{{ date('D, d M Y, H:i', strtotime($event->start_date . ' ' . $event->start_time)) }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="p-3 bg-red-50 rounded-2xl text-red-500">
                                <x-heroicon-s-calendar-days class="w-6 h-6" />
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-400 uppercase tracking-tighter mb-1">End Date</span>
                                <p class="text-slate-800 font-black italic">{{ date('D, d M Y, H:i', strtotime($event->end_date . ' ' . $event->end_time)) }}</p>
                            </div>
                        </div>

                        <div class="h-px w-full bg-slate-50"></div>

                        <div class="flex items-start gap-4">
                            <div class="p-3 bg-pink-50 rounded-2xl text-pink-500">
                                <x-heroicon-s-map-pin class="w-6 h-6" />
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-400 uppercase tracking-tighter mb-1">Location</span>
                                <p class="text-slate-800 font-black italic">{{ $event->event_location }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="p-3 bg-slate-100 rounded-2xl text-slate-600">
                                <x-heroicon-s-phone class="w-6 h-6" />
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-400 uppercase tracking-tighter mb-1">Contact Person</span>
                                <p class="text-slate-800 font-black italic">{{ $event->contact_person }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Register Button --}}
                    <a href="{{ $event->registration_link ?? '#' }}" target="_blank" 
                        class="block w-full text-center py-5 bg-gradient-to-r from-red-600 to-pink-500 text-white font-black rounded-[2rem] shadow-2xl shadow-pink-500/30 hover:scale-105 transition transform active:scale-95 uppercase tracking-widest text-sm">
                        Register Event Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Image Modal --}}
<div id="imageModal" class="fixed inset-0 z-[100] bg-slate-900/90 backdrop-blur-xl hidden flex items-center justify-center p-4 cursor-zoom-out" onclick="closeModal()">
    <button class="absolute top-8 right-8 text-white hover:rotate-90 transition-transform duration-300">
        <x-heroicon-o-x-mark class="w-10 h-10" />
    </button>
    <img src="data:image/jpeg;base64,{{ $event->event_poster }}" class="max-w-full max-h-full rounded-2xl shadow-2xl border-4 border-white/10">
</div>

<script>
    function openModal() { document.getElementById('imageModal').classList.remove('hidden'); }
    function closeModal() { document.getElementById('imageModal').classList.add('hidden'); }
</script>
@endsection