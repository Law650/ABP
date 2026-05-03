@extends('layouts.app')

@section('title', 'Registration Details - ' . ($registration->event->event_title ?? 'Detail'))

@section('content')
<div class="min-h-screen bg-slate-50/50 pt-28 pb-20 px-4 relative overflow-hidden">
    {{-- Glow Background Effects --}}
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-red-100 rounded-full blur-3xl opacity-50 -z-10"></div>
    <div class="absolute top-1/2 -right-24 w-80 h-80 bg-pink-100 rounded-full blur-3xl opacity-40 -z-10"></div>

    <div class="max-w-4xl mx-auto relative">
        {{-- Action Header --}}
        <div class="flex items-center justify-between mb-10">
            <a href="{{ route('user.event.history') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-600 font-bold rounded-2xl hover:bg-slate-50 transition-all shadow-sm text-sm">
                <x-heroicon-o-arrow-left class="w-4 h-4" />
                <span>Back to history</span>
            </a>

            @if (Auth::id() == $registration->user_id && strtolower($registration->event->status) === 'pending')
                <a href="{{ route('submit-event.form', ['edit' => $registration->event->id]) }}" 
                    class="px-6 py-2.5 bg-slate-900 text-white font-bold rounded-xl hover:bg-red-600 transition-all shadow-lg text-sm flex items-center gap-2">
                    <x-heroicon-s-pencil-square class="w-4 h-4" />
                    Edit Submission
                </a>
            @endif
        </div>

        {{-- Main Detail Card --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
            {{-- Status Header --}}
            <div class="px-8 md:px-12 py-6 bg-slate-50 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 italic tracking-tight uppercase">Registration Details</h1>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Submission ID: #REG-{{ $registration->id }}</p>
                </div>
                <div class="flex items-center gap-3 bg-white px-5 py-2.5 rounded-2xl shadow-sm border border-slate-100">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Status:</span>
                    <span class="px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest
                        {{ $registration->status === 'approved' ? 'bg-green-100 text-green-600' : 
                           ($registration->status === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600') }}">
                        {{ $registration->status }}
                    </span>
                </div>
            </div>

            <div class="p-8 md:p-12">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    
                    {{-- Left Column: Event Core Info --}}
                    <div class="space-y-10">
                        {{-- Poster Preview --}}
                        @if ($registration->event->event_poster)
                            <div class="group relative rounded-3xl overflow-hidden bg-slate-900 shadow-lg aspect-video md:aspect-[3/4]">
                                <img src="data:image/jpeg;base64,{{ $registration->event->event_poster }}" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                        @endif

                        <div class="space-y-6">
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Event Title</label>
                                <h2 class="text-xl font-black text-slate-900 leading-tight italic">{{ $registration->event->event_title ?? 'N/A' }}</h2>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Category</label>
                                    <span class="inline-block px-3 py-1 bg-green-50 text-green-600 text-[10px] font-bold rounded-lg capitalize">{{ $registration->event->event_category ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Org. Type</label>
                                    <span class="inline-block px-3 py-1 bg-red-50 text-red-600 text-[10px] font-bold rounded-lg uppercase">{{ str_replace('_', ' ', $registration->event->organizer_type) ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Detailed Fields --}}
                    <div class="space-y-8">
                        {{-- Organizer Info --}}
                        <div class="p-6 bg-slate-50/50 rounded-3xl border border-slate-100">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 block">Organizer Details</label>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-red-500 shadow-sm border border-slate-100">
                                    <x-heroicon-s-users class="w-6 h-6" />
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 italic">{{ $registration->event->organizer_name ?? 'N/A' }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Event Holder</p>
                                </div>
                            </div>
                        </div>

                        {{-- Schedule Grid --}}
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Event Schedule</label>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                    <span class="text-[9px] font-black text-slate-300 uppercase block mb-1">Starts</span>
                                    <p class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($registration->event->start_date)->format('d M Y') }}</p>
                                    <p class="text-[10px] font-black text-red-500">{{ \Carbon\Carbon::parse($registration->event->start_time)->format('H:i A') }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                    <span class="text-[9px] font-black text-slate-300 uppercase block mb-1">Ends</span>
                                    <p class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($registration->event->end_date)->format('d M Y') }}</p>
                                    <p class="text-[10px] font-black text-red-500">{{ \Carbon\Carbon::parse($registration->event->end_time)->format('H:i A') }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Metadata List --}}
                        <div class="space-y-6 pt-4">
                            <div class="flex items-start gap-4">
                                <x-heroicon-s-map-pin class="w-5 h-5 text-red-400 mt-0.5" />
                                <div>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Location</span>
                                    <p class="text-sm font-bold text-slate-700 italic">{{ $registration->event->event_location ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <x-heroicon-s-link class="w-5 h-5 text-red-400 mt-0.5" />
                                <div class="w-full">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Registration Link</span>
                                    <a href="{{ $registration->event->registration_link }}" target="_blank" class="text-sm font-bold text-blue-500 hover:text-red-500 transition-colors break-all italic underline decoration-blue-200 underline-offset-4">
                                        {{ $registration->event->registration_link ?? 'N/A' }}
                                    </a>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <x-heroicon-s-phone class="w-5 h-5 text-red-400 mt-0.5" />
                                <div>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Contact Person</span>
                                    <p class="text-sm font-bold text-slate-700 italic">{{ $registration->event->contact_person ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Description Full Width --}}
                    <div class="col-span-full border-t border-slate-100 pt-10">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 block">Event Description</label>
                        <div class="text-slate-600 leading-relaxed font-medium">
                            {!! nl2br(e($registration->event->event_description ?? 'Tidak ada deskripsi.')) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection