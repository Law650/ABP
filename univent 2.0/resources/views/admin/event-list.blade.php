@extends('layouts.app')

@section('title', 'Event List Management - Admin')

@section('content')
<div class="min-h-screen bg-slate-50/50 pt-28 pb-20 px-4 relative overflow-hidden">
    {{-- Glow Effects --}}
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-red-100 rounded-full blur-3xl opacity-50 -z-10"></div>
    <div class="absolute top-1/2 -right-24 w-80 h-80 bg-pink-100 rounded-full blur-3xl opacity-40 -z-10"></div>

    <div class="max-w-7xl mx-auto relative">
        {{-- Header --}}
        <div class="mb-8 text-center md:text-left">
            <h1 class="text-3xl font-extrabold text-slate-900 mb-2 italic tracking-tight">Admin Workspace</h1>
            <p class="text-slate-500 font-medium">Kelola seluruh ekosistem Univent dari panel ini.</p>
        </div>

        {{-- KODE BARU: Menu Pilihan Utama (Switcher) --}}
        <div class="flex p-1.5 mb-10 bg-slate-200/50 rounded-2xl w-fit border border-slate-200/50">
            {{-- Tombol Event List (Aktif) --}}
            <a href="{{ route('admin.event-list') }}" class="px-8 py-3 rounded-xl text-sm font-bold bg-white text-red-600 shadow-sm border border-slate-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Event List
            </a>
            {{-- Tombol EO Requests (Tidak Aktif) --}}
            <a href="{{ route('admin.eo-requests') }}" class="px-8 py-3 rounded-xl text-sm font-bold text-slate-500 hover:text-slate-700 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                EO Requests
            </a>
        </div>

        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-600 rounded-2xl font-bold text-sm flex items-center gap-3 animate-pulse">
                <x-heroicon-s-check-circle class="w-5 h-5" />
                {{ session('success') }}
            </div>
        @endif

        {{-- Filter Bar Horizontal (Kode Aslimu) --}}
        <div class="flex flex-col md:flex-row items-center justify-start gap-2.5 mb-8 bg-white p-4 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40">
            <div class="w-full md:w-[400px] relative group">
                <div class="absolute left-4 top-3">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4 text-slate-400 group-focus-within:text-red-500 transition-colors" />
                </div>
                <input type="text" placeholder="Search Events" class="w-full pl-11 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm text-slate-600 focus:outline-none focus:border-red-300 transition-all shadow-sm">
            </div>

            {{-- Dropdown Category --}}
            <div class="relative w-full md:w-48" x-data="{ open: false, selected: 'All Categories' }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 shadow-sm hover:border-slate-300 transition-all">
                    <span x-text="selected" class="truncate font-medium text-slate-600"></span>
                    <span :class="open ? 'rotate-180' : ''" class="transition-transform duration-200 flex items-center">
                        <x-heroicon-s-chevron-down class="w-4 h-4 text-slate-400" />
                    </span>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak class="absolute z-30 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-xl py-1 overflow-hidden">
                    @foreach(['All Categories', 'Seminar', 'Workshop', 'Competition', 'Gathering', 'Other'] as $cat)
                        <button @click="selected = '{{ $cat }}'; open = false" class="w-full text-left px-4 py-2 text-sm text-slate-600 hover:bg-red-50 transition-colors">{{ $cat }}</button>
                    @endforeach
                </div>
            </div>

            {{-- Dropdown Organizer --}}
            <div class="relative w-full md:w-48" x-data="{ open: false, selected: 'All Organizers' }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 shadow-sm hover:border-slate-300 transition-all">
                    <span x-text="selected" class="truncate font-medium text-slate-600"></span>
                    <span :class="open ? 'rotate-180' : ''" class="transition-transform duration-200 flex items-center">
                        <x-heroicon-s-chevron-down class="w-4 h-4 text-slate-400" />
                    </span>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak class="absolute z-30 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-xl py-1 overflow-hidden">
                    @foreach(['All Organizers', 'Student Association', 'Lecturer', 'External'] as $org)
                        <button @click="selected = '{{ $org }}'; open = false" class="w-full text-left px-4 py-2 text-sm text-slate-600 hover:bg-red-50 transition-colors">{{ $org }}</button>
                    @endforeach
                </div>
            </div>

            <button class="w-full md:w-auto px-6 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50 transition-all flex items-center justify-center gap-2 shadow-sm">
                <x-heroicon-o-arrow-path class="w-4 h-4" />
                <span>Clear</span>
            </button>
        </div>

        {{-- Nav Tabs (Kode Aslimu) --}}
        <div class="flex flex-wrap gap-2 mb-8 bg-slate-200/50 p-1.5 rounded-2xl w-fit border border-slate-200/30">
            <button onclick="showTab(this, 'pending')" id="tab-pending" class="tab-btn px-6 py-2.5 rounded-xl text-sm font-bold transition-all bg-white text-red-600 shadow-sm border border-slate-200">
                Menunggu <span class="ml-1 opacity-50">({{ $pendingEvents->count() ?? 0 }})</span>
            </button>
            <button onclick="showTab(this, 'approved')" id="tab-approved" class="tab-btn px-6 py-2.5 rounded-xl text-sm font-bold transition-all text-slate-500 hover:text-slate-700">
                Disetujui <span class="ml-1 opacity-50">({{ $approvedEvents->count() ?? 0 }})</span>
            </button>
            <button onclick="showTab(this, 'rejected')" id="tab-rejected" class="tab-btn px-6 py-2.5 rounded-xl text-sm font-bold transition-all text-slate-500 hover:text-slate-700">
                Ditolak <span class="ml-1 opacity-50">({{ $rejectedEvents->count() ?? 0 }})</span>
            </button>
            <button onclick="showTab(this, 'all')" id="tab-all" class="tab-btn px-6 py-2.5 rounded-xl text-sm font-bold transition-all text-slate-500 hover:text-slate-700">
                Semua <span class="ml-1 opacity-50">({{ $allEvents->count() ?? 0 }})</span>
            </button>
        </div>

        {{-- Tab Contents --}}
        <div id="pending" class="tab-content block">
            @include('partials.event_table', ['events' => $pendingEvents ?? [], 'showActions' => true])
        </div>
        <div id="approved" class="tab-content hidden">
            @include('partials.event_table', ['events' => $approvedEvents ?? [], 'showActions' => false, 'showRevert' => true])
        </div>
        <div id="rejected" class="tab-content hidden">
            @include('partials.event_table', ['events' => $rejectedEvents ?? [], 'showActions' => false, 'showRevert' => true])
        </div>
        <div id="all" class="tab-content hidden">
            @include('partials.event_table', ['events' => $allEvents ?? [], 'showActions' => false, 'showRevert' => true])
        </div>
    </div>
</div>

<script>
    function showTab(element, tabId) {
        document.querySelectorAll('.tab-content').forEach(c => { c.classList.add('hidden'); c.classList.remove('block'); });
        document.getElementById(tabId).classList.remove('hidden');
        document.getElementById(tabId).classList.add('block');
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('bg-white', 'text-red-600', 'shadow-sm', 'border', 'border-slate-200');
            b.classList.add('text-slate-500');
        });
        element.classList.add('bg-white', 'text-red-600', 'shadow-sm', 'border', 'border-slate-200');
        element.classList.remove('text-slate-500');
    }
</script>
@endsection