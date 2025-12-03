@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="bg-white min-h-screen pb-24">

    {{-- Header Sticky --}}
    <header class="sticky top-0 z-20 bg-white/90 backdrop-blur-md border-b border-gray-50 px-6 pt-8 pb-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ url()->previous() }}" class="text-gray-900 hover:text-[#37967D] transition p-1 -ml-1">
                <i class="ph-bold ph-caret-left text-2xl"></i>
            </a>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">Notifikasi</h1>
        </div>

        {{-- Badge Belum Dibaca --}}
        @php
            // Pastikan variable $notifications tersedia dari controller (bisa array/collection)
            $notifCollection = collect($notifications);
            $unreadCount = $notifCollection->where('is_read', false)->count();
        @endphp
        
        @if($unreadCount > 0)
            <span class="bg-red-50 text-red-500 text-[10px] font-bold px-2.5 py-1 rounded-full border border-red-100 shadow-sm">
                {{ $unreadCount }} Baru
            </span>
        @endif
    </header>

    {{-- List Notifikasi --}}
    <div class="flex flex-col">
        @forelse($notifications as $notif)
            <a href="{{ route('notifications.show', $notif['id']) }}" 
               class="relative w-full px-6 py-5 border-b border-gray-50 flex gap-4 transition-all duration-200 group
                      {{ !$notif['is_read'] ? 'bg-[#37967D]/[0.03]' : 'bg-white hover:bg-gray-50' }}">
                
                {{-- Indikator Belum Dibaca (Dot Merah) --}}
                @if(!$notif['is_read'])
                    <div class="absolute top-6 left-2 w-1.5 h-1.5 bg-red-500 rounded-full"></div>
                @endif

                {{-- Icon Type --}}
                <div class="flex-shrink-0">
                    <div class="w-11 h-11 rounded-full flex items-center justify-center 
                        {{ !$notif['is_read'] ? 'bg-[#37967D]/10 text-[#37967D]' : 'bg-gray-50 text-gray-400' }}">
                        
                        @if(!$notif['is_read'])
                            <i class="ph-fill ph-bell-ringing text-xl"></i>
                        @else
                            <i class="ph-fill ph-bell text-xl"></i>
                        @endif
                    </div>
                </div>
                
                {{-- Konten Teks --}}
                <div class="flex-1 min-w-0"> {{-- min-w-0 untuk truncate text --}}
                    <div class="flex justify-between items-start mb-1">
                        <h3 class="text-[14px] leading-tight truncate pr-2 {{ !$notif['is_read'] ? 'font-bold text-gray-900' : 'font-medium text-gray-600' }}">
                            {{ $notif['title'] }}
                        </h3>
                        
                        <span class="text-[10px] text-gray-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($notif['created_at'])->locale('id')->diffForHumans() }}
                        </span>
                    </div>
                    
                    <p class="text-xs text-gray-500 leading-relaxed line-clamp-2">
                        {{ $notif['body'] }}
                    </p>
                </div>
                
                {{-- Chevron --}}
                <div class="flex items-center text-gray-300 group-hover:text-[#37967D] transition-colors">
                    <i class="ph-bold ph-caret-right text-sm"></i>
                </div>
            </a>
        @empty
            {{-- State Kosong --}}
            <div class="flex flex-col items-center justify-center pt-24 text-center px-6">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-4 border border-gray-100">
                    <i class="ph-duotone ph-bell-slash text-4xl"></i>
                </div>
                <h3 class="text-gray-900 font-bold text-base mb-1">Tidak ada notifikasi</h3>
                <p class="text-gray-400 text-xs max-w-[200px] leading-relaxed">
                    Saat ini belum ada pemberitahuan baru yang masuk.
                </p>
            </div>
        @endforelse
    </div>
</div>
@endsection