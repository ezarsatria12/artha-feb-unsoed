@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="bg-gray-50 min-h-screen pb-28">

    {{-- 1. Header Hijau Gradient --}}
    <div class="bg-gradient-to-b from-[#37967D] to-[#2a7561] pb-24 rounded-b-[40px] px-6 pt-8 text-white relative overflow-hidden">
        {{-- === 1. TARUH CODE GAMBAR DISINI === --}}
        <img src="{{ asset('images/asset-background-home.png') }}" 
             alt="Pattern"
             class="absolute inset-0 w-full h-full object-cover opacity-50 pointer-events-none z-0">
        
        {{-- Dekorasi Latar (Bulatan Cahaya) --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none z-0"></div>
        
        {{-- Dekorasi Latar --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

        {{-- Top Bar --}}
        <div class="flex justify-between items-center relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-white/20 flex items-center justify-center border border-white/30 backdrop-blur-sm">
                    <i class="ph-fill ph-user text-xl text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-green-100 font-medium opacity-90">{{ Auth::user()->name ?? 'Owner' }}</p>
                    <h1 class="text-lg font-bold leading-tight">Hai, Selamat Pagi</h1>
                </div>
            </div>
            
            <a href="{{ route('notifications.index') }}" class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition backdrop-blur-sm relative">
                <i class="ph ph-bell text-xl"></i>
                <span class="absolute top-2.5 right-3 w-2 h-2 bg-red-400 rounded-full border border-white/20"></span>
            </a>
        </div>

        {{-- Banner Text --}}
        <div class="mt-8 relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <h2 class="text-xl font-bold">Bagaimana Hari Ini?</h2>
                <span class="text-xl">🤔</span>
            </div>
            <p class="text-sm text-green-50 opacity-90 font-light leading-relaxed max-w-[90%]">
                Jangan khawatir, kami bantu Anda mengelola penjualan dan keuangan harian secara otomatis.
            </p>
        </div>
    </div>

    {{-- 2. Statistik Cards (Grid 2x2) --}}
    <div class="px-6 -mt-16 relative z-10">
        <div class="grid grid-cols-2 gap-3">
            
            {{-- Helper Component untuk Badge Persentase --}}
            @php
                function renderBadge($val) {
                    $isUp = $val >= 0;
                    $color = $isUp ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600';
                    $icon = $isUp ? 'ph-caret-up' : 'ph-caret-down';
                    return '
                    <div class="flex items-center gap-1 px-2 py-1 rounded-full '.$color.'">
                        <span class="text-[10px] font-bold">'.number_format(abs($val), 0).'%</span>
                        <i class="ph-fill '.$icon.' text-[10px]"></i>
                    </div>';
                }
            @endphp

            {{-- Card 1: Total Tunai --}}
            <div class="bg-white p-4 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-50 flex flex-col justify-between h-32">
                <p class="text-xs text-gray-500 font-medium">Total Tunai</p>
                <h3 class="text-xl font-bold text-gray-900 mt-1">Rp{{ number_format($data['tunai'] / 1000, 0) }}rb</h3>
                <div class="flex justify-between items-center mt-auto pt-2">
                    <span class="text-[9px] text-gray-400">Dari hari ini</span>
                    {!! renderBadge($percentages['tunai']) !!}
                </div>
            </div>

            {{-- Card 2: Total QRIS --}}
            <div class="bg-white p-4 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-50 flex flex-col justify-between h-32">
                <p class="text-xs text-gray-500 font-medium">Total QRIS</p>
                <h3 class="text-xl font-bold text-gray-900 mt-1">Rp{{ number_format($data['qris'] / 1000, 0) }}rb</h3>
                <div class="flex justify-between items-center mt-auto pt-2">
                    <span class="text-[9px] text-gray-400">Dari hari ini</span>
                    {!! renderBadge($percentages['qris']) !!}
                </div>
            </div>

            {{-- Card 3: Total Pemasukan --}}
            <div class="bg-white p-4 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-50 flex flex-col justify-between h-32">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-1.5 h-4 bg-green-500 rounded-full"></div>
                    <p class="text-xs text-gray-500 font-medium">Total Pemasukan</p>
                </div>
                <h3 class="text-2xl font-black text-gray-900">Rp{{ number_format($data['revenue'] / 1000, 0) }}rb</h3>
                <div class="flex justify-between items-center mt-auto">
                    <span class="text-[9px] text-gray-400">Dari hari ini</span>
                    {!! renderBadge($percentages['revenue']) !!}
                </div>
            </div>

            {{-- Card 4: Total Keuntungan --}}
            <div class="bg-white p-4 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-50 flex flex-col justify-between h-32">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-1.5 h-4 bg-blue-500 rounded-full"></div>
                    <p class="text-xs text-gray-500 font-medium">Total Keuntungan</p>
                </div>
                <h3 class="text-2xl font-black text-gray-900">Rp{{ number_format($data['profit'] / 1000, 0) }}rb</h3>
                <div class="flex justify-between items-center mt-auto">
                    <span class="text-[9px] text-gray-400">Dari hari ini</span>
                    {!! renderBadge($percentages['profit']) !!}
                </div>
            </div>

        </div>
    </div>

    {{-- 3. Menu Paling Laris --}}
    <div class="px-6 mt-8">
        <div class="flex justify-between items-end mb-4">
            <h3 class="font-bold text-gray-900">Menu Paling Laris</h3>
            <span class="text-xs text-gray-400">Hari Ini</span>
        </div>

        <div class="bg-white rounded-3xl p-5 shadow-[0_2px_15px_rgba(0,0,0,0.02)] border border-gray-50">
            <div class="flex justify-between text-xs text-gray-400 mb-4 px-2">
                <span>Jenis</span>
                <span>Total dibeli</span>
            </div>

            <div class="space-y-5">
                @forelse($topProducts as $item)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        {{-- Foto Bulat --}}
                        <div class="w-12 h-12 rounded-full bg-gray-100 overflow-hidden border border-gray-100 shrink-0">
                            @if($item->product->image_url)
                                <img src="{{ asset('storage/' . $item->product->image_url) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <i class="ph-fill ph-image text-lg"></i>
                                </div>
                            @endif
                        </div>
                        
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm">{{ $item->product->nama_produk }}</h4>
                            <p class="text-xs text-gray-500 font-medium">Rp. {{ number_format($item->product->harga_jual, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    
                    <span class="font-bold text-gray-900 text-sm">{{ $item->total_sold }}</span>
                </div>
                @empty
                <div class="text-center py-6 text-gray-400 text-xs">
                    Belum ada penjualan hari ini.
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection