@extends('layouts.app')

@section('title', 'Detail Notifikasi')

@section('content')
<div class="bg-white min-h-screen pb-24">

    {{-- Header --}}
    <header class="sticky top-0 z-20 bg-white/90 backdrop-blur-md border-b border-gray-50 px-6 pt-8 pb-4 flex items-center gap-3">
        <a href="{{ route('notifications.index') }}" class="text-gray-900 hover:text-[#37967D] transition p-1 -ml-1">
            <i class="ph-bold ph-caret-left text-2xl"></i>
        </a>
        <h1 class="text-xl font-bold text-gray-900 tracking-tight">Detail Pesan</h1>
    </header>

    <div class="px-6 py-6">
        {{-- Tanggal & Kategori --}}
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2 text-gray-400">
                <i class="ph-fill ph-calendar-blank"></i>
                <span class="text-xs font-medium">
                    {{ \Carbon\Carbon::parse($notification['created_at'])->translatedFormat('l, d F Y • H:i') }}
                </span>
            </div>

            {{-- Label Tipe (Warna Warni) --}}
            @php
                $type = $notification['type'] ?? 'info';
                $colors = [
                    'order'   => 'text-blue-600 bg-blue-50 border-blue-100',
                    'stock'   => 'text-red-600 bg-red-50 border-red-100',
                    'payment' => 'text-green-600 bg-green-50 border-green-100',
                    'system'  => 'text-gray-600 bg-gray-50 border-gray-100',
                    'info'    => 'text-purple-600 bg-purple-50 border-purple-100',
                ];
                $style = $colors[$type] ?? $colors['info'];
            @endphp
            <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg border {{ $style }}">
                {{ $type }}
            </span>
        </div>

        {{-- Judul Besar --}}
        <h2 class="text-2xl font-bold text-gray-900 leading-tight mb-6">
            {{ $notification['title'] }}
        </h2>

        {{-- Garis Pemisah --}}
        <hr class="border-gray-100 mb-6 border-dashed">

        {{-- Isi Konten --}}
        <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed text-[14px]">
            <p>{{ $notification['body'] }}</p>
        </div>
    </div>

    {{-- Tombol Aksi (Kondisional) --}}
    @if(($notification['type'] ?? '') === 'stock')
        <div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-100 p-5 z-30 max-w-[480px] mx-auto right-0">
            <a href="{{ route('products.index') }}" {{-- Asumsi route ke stok/produk --}}
               class="flex items-center justify-center gap-2 w-full bg-[#37967D] text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-[#37967D]/25 active:scale-98 transition-transform hover:bg-[#2f826c]">
               <i class="ph-bold ph-package"></i>
               <span>Cek Stok Sekarang</span>
            </a>
        </div>
    @elseif(($notification['type'] ?? '') === 'order')
        <div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-100 p-5 z-30 max-w-[480px] mx-auto right-0">
            <a href="{{ route('orders.index') }}" 
               class="flex items-center justify-center gap-2 w-full bg-[#37967D] text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-[#37967D]/25 active:scale-98 transition-transform hover:bg-[#2f826c]">
               <i class="ph-bold ph-receipt"></i>
               <span>Lihat Pesanan</span>
            </a>
        </div>
    @endif

</div>
@endsection