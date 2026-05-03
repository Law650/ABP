@extends('layouts.app')

@section('title', 'Edit Profile - Univent')

@section('content')
<div class="min-h-screen bg-slate-50/50 pt-28 pb-20 px-4 relative overflow-hidden">
    {{-- Glow Background Page --}}
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-red-100 rounded-full blur-3xl opacity-50 -z-10"></div>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden flex flex-col md:flex-row">
                
                {{-- Sidebar: Identity & Photo --}}
                <div class="md:w-1/3 p-8 md:p-12 border-b md:border-b-0 md:border-r border-slate-50 flex flex-col items-center text-center">
                    
                    {{-- Avatar Container with Outer Glow (Revisi image_7ea064.png) --}}
                    <div class="relative mb-8">
                        {{-- Glow Effect OUTSIDE the photo --}}
                        <div class="absolute inset-0 bg-gradient-to-tr from-red-500 to-pink-500 rounded-full blur-2xl opacity-30 scale-110"></div>
                        
                        <div class="relative w-44 h-44 rounded-full p-1 bg-white shadow-sm">
                            <img id="avatar-preview" 
                                src="{{ $user->avatar ? 'data:image/jpeg;base64,'.$user->avatar : asset('images/default-avatar.svg') }}" 
                                class="w-full h-full rounded-full object-cover border-4 border-white shadow-inner"
                                data-default="{{ asset('images/default-avatar.svg') }}">
                        </div>
                    </div>

                    <h3 class="text-xl font-black text-slate-900 mb-1 italic">{{ $user->name }}</h3>
                    
                    {{-- Fixed "Update Photo" Button (Revisi logic klik) --}}
                    <div class="flex flex-col items-center gap-2">
                        <label for="avatar-upload-input" class="text-[10px] font-black text-slate-400 uppercase tracking-widest cursor-pointer hover:text-red-500 transition-colors">
                            Update Photo
                        </label>
                        <input type="file" id="avatar-upload-input" name="avatar_raw" class="hidden" accept="image/*">
                        
                        {{-- Hidden Input untuk Base64 --}}
                        <input type="hidden" name="new_avatar_temp" id="new_avatar_temp">

                        @if($user->avatar)
                            <button type="button" id="btn-remove-photo" class="text-[9px] font-bold text-red-400 hover:text-red-600 uppercase tracking-tighter transition-colors">
                                Remove Photo
                            </button>
                            <input type="hidden" name="remove_avatar" id="remove-avatar-input" value="0">
                        @endif
                    </div>
                </div>

                {{-- Main Content: Form Fields (Sesuai image_7ea064.png) --}}
                <div class="md:w-2/3 p-8 md:p-12 space-y-10">
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 mb-2 italic tracking-tight uppercase">Edit Personal Information</h2>
                        <p class="text-sm font-medium text-slate-500">Sesuaikan profilmu agar orang lain lebih mudah mengenalimu.</p>
                    </div>

                    <div class="space-y-6">
                        {{-- Full Name --}}
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Full Name</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-red-500 transition-colors">
                                    <x-heroicon-s-user class="w-5 h-5" />
                                </div>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full pl-14 pr-6 py-4 bg-slate-50/50 border border-slate-100 rounded-[1.25rem] text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-red-500/5 focus:border-red-200 outline-none transition-all">
                            </div>
                        </div>

                        {{-- Birthday --}}
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Birthday</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-pink-500 transition-colors">
                                    <x-heroicon-s-cake class="w-5 h-5" />
                                </div>
                                <input type="date" name="birthday" 
                                    value="{{ old('birthday', $user->profile?->birthday ? \Carbon\Carbon::parse($user->profile->birthday)->format('Y-m-d') : '') }}"
                                    class="w-full pl-14 pr-6 py-4 bg-slate-50/50 border border-slate-100 rounded-[1.25rem] text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-pink-500/5 focus:border-pink-200 outline-none transition-all">
                            </div>
                        </div>

                        {{-- Phone Number --}}
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Phone Number</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-slate-900 transition-colors">
                                    <x-heroicon-s-phone class="w-5 h-5" />
                                </div>
                                <input type="tel" name="phone" value="{{ old('phone', $user->profile?->phone) }}" placeholder="08xxxxxx"
                                    class="w-full pl-14 pr-6 py-4 bg-slate-50/50 border border-slate-100 rounded-[1.25rem] text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-slate-500/5 focus:border-slate-300 outline-none transition-all">
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions (Sesuai image_7ea064.png) --}}
                    <div class="flex flex-col md:flex-row items-center justify-end gap-6 pt-10 border-t border-slate-50">
                        <a href="{{ route('profile.show') }}" class="text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="w-full md:w-auto px-12 py-4 bg-gradient-to-r from-red-600 to-pink-500 text-white font-black rounded-2xl shadow-xl shadow-pink-500/30 hover:scale-105 transition transform active:scale-95 uppercase tracking-widest text-xs">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Logic: Photo Preview & Base64 Converter
    const uploadInput = document.getElementById('avatar-upload-input');
    const previewImg = document.getElementById('avatar-preview');
    const tempInput = document.getElementById('new_avatar_temp');
    const removeBtn = document.getElementById('btn-remove-photo');
    const removeInput = document.getElementById('remove-avatar-input');

    uploadInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                tempInput.value = e.target.result; // Simpan base64 ke hidden input
                if(removeInput) removeInput.value = "0";
            }
            reader.readAsDataURL(file);
        }
    });

    if(removeBtn) {
        removeBtn.addEventListener('click', function() {
            previewImg.src = previewImg.dataset.default;
            tempInput.value = "";
            removeInput.value = "1";
            uploadInput.value = "";
        });
    }
</script>
@endpush
@endsection