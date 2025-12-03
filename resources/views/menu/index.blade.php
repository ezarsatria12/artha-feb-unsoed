@extends('layouts.app')

@section('title', 'Menu')

@section('content')

{{-- 
    HEADER SECTION
    Menggunakan bg-[#F7F7F7] agar warnanya MENYATU dengan background body (base layer).
    Ditambah z-30 agar tetap di atas saat scroll.
--}}
<div class="sticky top-0 bg-white z-30 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
    
    <div class="px-6 pt-8 pb-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-5 tracking-tight">Menu</h2>
        
        <form action="{{ route('menu.index') }}" method="GET" class="flex gap-3">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="ph ph-magnifying-glass text-gray-400 text-xl"></i>
                </div>
                {{-- Input Search background putih agar kontras dengan base layer --}}
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="w-full bg-white border border-gray-200 text-gray-700 text-sm rounded-2xl pl-11 pr-4 py-3.5 focus:outline-none focus:border-[#37967D] focus:ring-1 focus:ring-[#37967D] transition-all placeholder-gray-400 shadow-sm" 
                    placeholder="Cari Menu">
            </div>
            {{-- Tombol Filter --}}
            <button type="button" class="bg-white border border-gray-200 text-gray-600 rounded-2xl w-[52px] flex items-center justify-center hover:bg-gray-50 transition-colors shadow-sm">
                <i class="ph ph-sliders-horizontal text-xl"></i>
            </button>
        </form>
    </div>

    <div class="px-6 mb-2 ">
        <a href="{{ route('menu.create') }}" 
           class="flex items-center justify-center gap-2 w-full bg-[#37967D] text-white font-semibold text-[15px] py-3.5 rounded-2xl shadow-[0_8px_20px_rgba(55,150,125,0.25)] active:scale-98 transition-all hover:bg-[#2f826c]">
           <i class="ph ph-plus text-lg"></i>
           Tambah Produk
        </a>
    </div>

    {{-- List Kategori --}}
    <div class="pl-6 pb-2 pt-2">
        <div class="flex gap-3 overflow-x-auto pr-6 pb-2 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:'none'] [scrollbar-width:'none']">
            
            @php
                function getCatClass($isActive) {
                    return $isActive 
                        ? 'bg-[#37967D]/10 border-[#37967D] text-[#37967D] shadow-sm' 
                        : 'bg-white border-gray-200 text-gray-400 hover:border-gray-300';
                }
                
                $categories = [
                    ['name' => 'Semua', 'icon' => 'ph-chef-hat', 'val' => null],
                    ['name' => 'Nasi', 'icon' => 'ph-bowl-food', 'val' => 'Nasi'],
                    ['name' => 'Minuman', 'icon' => 'ph-coffee', 'val' => 'Minuman'],
                    ['name' => 'Mie', 'icon' => 'ph-bowl-steam', 'val' => 'Mie'],
                    ['name' => 'Jajanan', 'icon' => 'ph-cookie', 'val' => 'Jajanan'],
                    ['name' => 'Lainnya', 'icon' => 'ph-dots-three', 'val' => 'Lainnya'],
                ];
            @endphp

            @foreach($categories as $cat)
                @php 
                    $isActive = (request('search') == $cat['val']) || ($cat['val'] == null && !request('search'));
                @endphp
                
                <a href="{{ route('menu.index', ['search' => $cat['val']]) }}">
                    <div class="{{ getCatClass($isActive) }} w-[88px] h-[92px] border rounded-2xl flex flex-col items-center justify-center gap-2 transition-all duration-300">
                        <i class="ph {{ $cat['icon'] }} text-3xl mb-1"></i>
                        <span class="text-[12px] font-medium {{ $isActive ? 'text-[#37967D] font-bold' : 'text-gray-500' }}">
                            {{ $cat['name'] }}
                        </span>
                    </div>
                </a>
            @endforeach

        </div>
    </div>
</div>

{{-- GRID PRODUK --}}
<div class="px-6 pt-2 pb-32 bg-white min-h-screen">
    <div class="grid grid-cols-2 gap-4 ">
        @forelse($products as $product)
            {{-- 
               CARD STYLE:
               - bg-white: Warna dasar putih
               - rounded-[20px]: Sudut tumpul sesuai gambar
               - border border-gray-200: Stroke/Garis tepi tipis (Dikit aja)
               - shadow-sm: Bayangan halus agar tidak terlalu floating
            --}}
            <div class="bg-white p-3 rounded-[20px] border border-gray-200 shadow-sm flex flex-col h-full hover:border-[#37967D]/50 transition-all duration-300">
                
                {{-- Gambar Persegi (Aspect Square) --}}
                <div class="w-full aspect-square rounded-xl overflow-hidden bg-gray-50 mb-3 relative group">
                    @if($product->image_url)
                        <img src="{{ asset('storage/' . $product->image_url) }}" 
                             class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500" 
                             alt="{{ $product->nama_produk }}">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                            <i class="ph-fill ph-image text-3xl mb-1"></i>
                        </div>
                    @endif
                </div>

                {{-- Informasi Produk (Jarak teks dirapatkan) --}}
                <div class="flex flex-col flex-1">
                    {{-- Judul --}}
                    <h4 class="font-bold text-gray-900 text-[14px] leading-tight mb-0.5 line-clamp-1">
                        {{ $product->nama_produk }}
                    </h4>
                    
                    {{-- Kategori & Deskripsi --}}
                    <p class="text-[11px] text-gray-400 font-medium mb-1 leading-snug line-clamp-1">
                        {{ $product->deskripsi }}
                    </p>

                    {{-- Stok --}}
                    <p class="text-[11px] text-gray-400 mb-3">
                        Stok: <span class="text-gray-600 font-bold">{{ $product->stock }}</span>
                    </p>

                    {{-- Footer: Harga & Tombol Aksi (Edit + Hapus di bawah) --}}
                    <div class="mt-auto flex items-center justify-between pt-0.5 border-t border-dashed border-gray-100">
                        {{-- Harga --}}
                        <span class="font-bold text-sm text-gray-900">Rp{{ number_format($product->harga_jual, 0, ',', '.') }}</span>
                        
                        {{-- Group Tombol (Balik ke style awal) --}}
                        <div class="flex gap-1.5">
                            {{-- Tombol Edit --}}
                            <a href="{{ route('menu.edit', $product->id) }}" 
                               class="bg-[#EBF8F5] text-[#37967D] border border-[#37967D]/20 text-[10px] px-2.5 py-1.5 rounded-lg font-bold hover:bg-[#37967D] hover:text-white transition-colors">
                                Edit
                            </a>
                            
                            {{-- Tombol Hapus --}}
                            <form action="{{ route('menu.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-50 text-red-500 border border-red-100 text-[10px] w-7 h-[27px] rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors">
                                    <i class="ph-bold ph-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 py-12 flex flex-col items-center justify-center text-center">
                <div class="bg-white border border-gray-100 rounded-full w-24 h-24 flex items-center justify-center mb-4 shadow-sm">
                    <i class="ph-fill ph-bowl-food text-gray-300 text-5xl"></i>
                </div>
                <h3 class="text-gray-900 font-bold text-lg mb-1">Belum ada produk</h3>
                <p class="text-gray-500 text-sm max-w-[200px]">Silakan tambah produk baru melalui tombol di atas.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection