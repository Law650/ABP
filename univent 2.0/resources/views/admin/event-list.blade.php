@extends('layouts.app')

@section('title', 'Event List Management - Admin')

@section('content')
<div class="min-h-screen bg-slate-50/50 pt-28 pb-20 px-4 relative overflow-hidden">
    {{-- Glow Effects --}}
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-red-100 rounded-full blur-3xl opacity-50 -z-10"></div>
    <div class="absolute top-1/2 -right-24 w-80 h-80 bg-pink-100 rounded-full blur-3xl opacity-40 -z-10"></div>

    <div class="max-w-7xl mx-auto relative">
        {{-- Header --}}
        <div class="mb-10 text-center md:text-left">
            <h1 class="text-3xl font-extrabold text-slate-900 mb-2 italic tracking-tight">Event List Management</h1>
            <p class="text-slate-500 font-medium">Kelola event yang disubmit oleh pengguna dan tentukan status penayangannya.</p>
        </div>

        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-600 rounded-2xl font-bold text-sm flex items-center gap-3 animate-pulse">
                <x-heroicon-s-check-circle class="w-5 h-5" />
                {{ session('success') }}
            </div>
        @endif

        {{-- Filter Bar Horizontal --}}
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

        {{-- Nav Tabs --}}
        <div class="flex flex-wrap gap-2 mb-8 bg-slate-200/50 p-1.5 rounded-2xl w-fit border border-slate-200/30">
            <button onclick="showTab(this, 'pending')" id="tab-pending" class="tab-btn px-6 py-2.5 rounded-xl text-sm font-bold transition-all bg-white text-red-600 shadow-sm border border-slate-200">
                Menunggu <span class="ml-1 opacity-50">({{ $pendingEvents->count() }})</span>
            </button>
            <button onclick="showTab(this, 'approved')" id="tab-approved" class="tab-btn px-6 py-2.5 rounded-xl text-sm font-bold transition-all text-slate-500 hover:text-slate-700">
                Disetujui <span class="ml-1 opacity-50">({{ $approvedEvents->count() }})</span>
            </button>
            <button onclick="showTab(this, 'rejected')" id="tab-rejected" class="tab-btn px-6 py-2.5 rounded-xl text-sm font-bold transition-all text-slate-500 hover:text-slate-700">
                Ditolak <span class="ml-1 opacity-50">({{ $rejectedEvents->count() }})</span>
            </button>
            <button onclick="showTab(this, 'all')" id="tab-all" class="tab-btn px-6 py-2.5 rounded-xl text-sm font-bold transition-all text-slate-500 hover:text-slate-700">
                Semua <span class="ml-1 opacity-50">({{ $allEvents->count() }})</span>
            </button>
        </div>

        {{-- Tab Contents --}}
        <div id="pending" class="tab-content block">
            @include('partials.event_table', ['events' => $pendingEvents, 'showActions' => true])
        </div>
        <div id="approved" class="tab-content hidden">
            @include('partials.event_table', ['events' => $approvedEvents, 'showActions' => false, 'showRevert' => true])
        </div>
        <div id="rejected" class="tab-content hidden">
            @include('partials.event_table', ['events' => $rejectedEvents, 'showActions' => false, 'showRevert' => true])
        </div>
        <div id="all" class="tab-content hidden">
            @include('partials.event_table', ['events' => $allEvents, 'showActions' => false, 'showRevert' => true])
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