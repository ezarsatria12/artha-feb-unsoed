@extends('layouts.app')

@section('title', 'Riwayat Pesanan')

@section('content')

<div class="sticky top-0 bg-white z-20 shadow-[0_1px_2px_rgba(0,0,0,0.02)]">
    
    <div class="px-6 pt-8 pb-2">
        <h2 class="text-2xl font-bold text-gray-900 mb-5 tracking-tight">Pemesanan</h2>
        
        {{-- Search Bar --}}
        <form action="{{ route('orders.index') }}" method="GET" class="relative mb-4">
            <input type="hidden" name="date_filter" value="{{ request('date_filter') }}">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="ph ph-magnifying-glass text-gray-400 text-xl"></i>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" 
                class="w-full bg-gray-50 border border-gray-100 text-gray-700 text-sm rounded-2xl pl-11 pr-4 py-3.5 focus:outline-none focus:border-[#37967D] focus:ring-1 focus:ring-[#37967D] transition-all placeholder-gray-400" 
                placeholder="Cari ID Pesanan atau Nama...">
        </form>

        {{-- Tab Navigasi --}}
        <div class="flex bg-gray-100 p-1.5 rounded-2xl mb-4">
            {{-- Tab Pesanan Baru (Link ke Create) --}}
            <a href="{{ route('orders.create') }}" class="flex-1 text-center py-2.5 rounded-xl text-sm font-medium text-gray-500 hover:text-gray-700 transition-all">
                Pesanan Baru
            </a>
            
            {{-- Tab Riwayat (Aktif) --}}
            <div class="flex-1 text-center py-2.5 rounded-xl text-sm font-bold bg-white text-[#37967D] shadow-sm ring-1 ring-gray-200 transition-all cursor-default">
                Riwayat Pesanan
            </div>
        </div>

        {{-- FILTER DROPDOWN & DOWNLOAD --}}
        <div class="flex justify-end items-center gap-3 mb-2">
            
            {{-- Form Khusus Filter Tanggal --}}
            <form action="{{ route('orders.index') }}" method="GET" id="filterForm">
                {{-- Keep search param if exists --}}
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <div class="relative">
                    <select name="date_filter" onchange="document.getElementById('filterForm').submit()" 
                        class="appearance-none bg-white border border-gray-200 text-gray-700 text-xs font-bold py-2 pl-4 pr-8 rounded-xl focus:outline-none focus:border-[#37967D] focus:ring-1 focus:ring-[#37967D] cursor-pointer shadow-sm">
                        <option value="today" {{ $dateFilter == 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="week" {{ $dateFilter == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="month" {{ $dateFilter == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="all" {{ $dateFilter == 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                        <i class="ph-bold ph-caret-down text-xs"></i>
                    </div>
                </div>
            </form>

            {{-- Tombol Download (Placeholder Link) --}}
            <a href="{{ route('reports.export') }}" class="w-8 h-8 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-[#37967D] hover:bg-gray-50 shadow-sm transition-colors">
                <i class="ph-bold ph-download-simple text-lg"></i>
            </a>
        </div>
    </div>
</div>

<div class="px-6 pt-2 pb-32 bg-white min-h-screen">
    @forelse($orders as $order)
        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-[0_2px_10px_rgba(0,0,0,0.02)] mb-3 hover:border-[#37967D]/30 transition-all relative group">
            
            <div class="flex justify-between items-start mb-3 pb-3 border-b border-dashed border-gray-100">
                <div>
                    <h4 class="font-bold text-gray-800 text-sm">{{ $order->nama_pemesan }}</h4>
                    <p class="text-[10px] text-gray-400 mt-0.5 font-medium">
                        {{ $order->items_count }} Produk
                    </p>
                </div>
                <div class="text-right">
                    <span class="block text-sm font-bold text-gray-900">Rp{{ number_format($order->total_uang_masuk, 0, ',', '.') }}</span>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $order->created_at->format('d M, H:i') }} WIB</p>
                </div>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-[10px] text-gray-400">
                    ID: <span class="font-mono text-gray-600">{{ $order->order_code }}</span>
                </span>
                
                <a href="{{ route('orders.show', $order->id) }}" class="bg-[#EBF8F5] text-[#37967D] text-[10px] font-bold px-3 py-1.5 rounded-lg hover:bg-[#37967D] hover:text-white transition-colors">
                    Lihat Detail
                </a>
            </div>
        </div>
    @empty
        <div class="text-center py-12">
            <div class="bg-gray-50 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 border border-gray-100">
                <i class="ph ph-receipt text-gray-300 text-4xl"></i>
            </div>
            <h3 class="text-gray-800 font-semibold mb-1">Belum ada riwayat</h3>
            <p class="text-gray-400 text-xs">Transaksi pada periode ini tidak ditemukan.</p>
            
            @if($dateFilter != 'all')
                <a href="{{ route('orders.index', ['date_filter' => 'all']) }}" class="inline-block mt-4 text-[#37967D] text-xs font-bold hover:underline">
                    Lihat Semua Riwayat
                </a>
            @endif
        </div>
    @endforelse
    
    {{-- Pagination Link --}}
    <div class="mt-4">
        {{ $orders->links('pagination::tailwind') }}
    </div>
</div>

@endsection