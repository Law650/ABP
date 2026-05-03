@props(['events', 'showActions' => false, 'showRevert' => false])

<div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden mb-10">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50/50 border-b border-slate-100">
                <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-slate-400">No</th>
                <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-slate-400">Event Info</th>
                <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-slate-400 text-center">Status</th>
                <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-slate-400 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse ($events as $index => $event)
                <tr class="hover:bg-slate-50/30 transition-colors group">
                    {{-- No --}}
                    <td class="px-6 py-4 text-sm font-bold text-slate-400">{{ $index + 1 }}</td>

                    {{-- Event Info (Poster & Title) --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <img src="data:image/jpeg;base64,{{ $event->event_poster }}" 
                                class="w-12 h-16 object-cover rounded-xl shadow-sm border border-slate-100 transition-transform group-hover:scale-105">
                            <div>
                                <p class="text-sm font-black text-slate-900 line-clamp-1 italic tracking-tight">{{ $event->event_title }}</p>
                                <p class="text-[10px] font-bold text-red-500 uppercase tracking-tighter">by {{ $event->organizer_name }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Status Badge --}}
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-full 
                            {{ $event->status === 'approved' ? 'bg-green-100 text-green-600' : 
                               ($event->status === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600') }}">
                            {{ $event->status }}
                        </span>
                    </td>

                    {{-- Aksi (Buttons) --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            
                            {{-- Logic: Accept / Reject Buttons --}}
                            @if ($showActions || $showRevert)
                                {{-- Tombol Accept (Jika status pending atau status rejected di mode revert) --}}
                                @if (($showActions && $event->status === 'pending') || ($showRevert && $event->status === 'rejected'))
                                    <form action="{{ route('admin.events.approve', $event->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2.5 bg-green-100 text-green-600 rounded-xl hover:bg-green-600 hover:text-white transition-all shadow-sm" title="Accept">
                                            <x-heroicon-s-check class="w-4 h-4" />
                                        </button>
                                    </form>
                                @endif

                                {{-- Tombol Reject (Jika status pending atau status approved di mode revert) --}}
                                @if (($showActions && $event->status === 'pending') || ($showRevert && $event->status === 'approved'))
                                    <form action="{{ route('admin.events.reject', $event->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2.5 bg-red-100 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Reject">
                                            <x-heroicon-s-x-mark class="w-4 h-4" />
                                        </button>
                                    </form>
                                @endif
                            @endif

                            {{-- View Detail (Selalu tampil) --}}
                            <a href="{{ route('admin.events.detail', $event->id) }}" 
                                class="p-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-900 hover:text-white transition-all shadow-sm" title="View Detail">
                                <x-heroicon-s-eye class="w-4 h-4" />
                            </a>

                            {{-- Delete Button --}}
                            <form action="{{ route('admin.events.delete', $event->id) }}" method="POST" class="inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="p-2.5 bg-white border border-slate-200 text-slate-400 rounded-xl hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all shadow-sm btn-delete-sw" title="Delete">
                                    <x-heroicon-s-trash class="w-4 h-4" />
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-20 text-center text-slate-400 font-bold italic text-sm">
                        Tidak ada event dengan status ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>