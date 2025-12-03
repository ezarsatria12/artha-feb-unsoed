@extends('layouts.app')

@section('title', 'Buat Pesanan')

@section('content')

<div class="sticky top-0 bg-white z-30 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
    <div class="px-6 pt-8 pb-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-5 tracking-tight"">Pesanan</h2>

        {{-- 1. Search Bar & Filter (Sejajar) --}}
        <form method=" GET" class="flex gap-3">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="ph ph-magnifying-glass text-gray-400 text-xl"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full bg-white border border-gray-200 text-gray-700 text-sm rounded-2xl pl-11 pr-4 py-3.5 focus:outline-none focus:border-[#37967D] focus:ring-1 focus:ring-[#37967D] transition-all placeholder-gray-400 shadow-sm"
                    placeholder="Cari Menu">
            </div>
            <button type="button" class="bg-white border border-gray-200 text-gray-600 rounded-2xl w-[52px] flex items-center justify-center hover:bg-gray-50 transition-colors shadow-sm">
                <i class="ph ph-sliders-horizontal text-xl"></i>
            </button>
            </form>
    </div>

    {{-- 2. Toggle Segment (Pesanan Baru / Riwayat) --}}
    <div class="px-6 mb-2">
        <div class="flex bg-gray-100 p-1.5 rounded-2xl">
            {{-- Tab Pesanan Baru (Aktif) --}}
            <div class="flex-1 text-center  py-3.5 rounded-2xl text-[15px] font-medium bg-[#37967D] text-white shadow-sm cursor-default">
                Pesanan Baru
            </div>

            {{-- Tab Riwayat (Link) --}}
            <a href="{{ route('orders.index') }}" class="flex-1 text-center py-3.5 rounded-2xl text-[15px] font-medium text-gray-500 hover:text-gray-700 hover:bg-white/60 transition-all">
                Riwayat Pesanan
            </a>
        </div>
    </div>

    {{-- 3. Kategori Menu --}}
    <div class="pl-6 pb-2 pt-2">
        <div class="flex gap-3 overflow-x-auto pr-6 pb-2 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:'none'] [scrollbar-width:'none']">

            @php
            // Fungsi menentukan class aktif/non-aktif
                function getCatClass($isActive) {
                    return $isActive
                    ? 'bg-[#37967D]/10 border-[#37967D] text-[#37967D] shadow-sm'
                    : 'bg-white border-gray-200 text-gray-400 hover:border-gray-300';
                }

                $categories = [
                    ['label' => 'Semua', 'icon' => 'ph-chef-hat', 'val' => null],
                    ['label' => 'Nasi', 'icon' => 'ph-bowl-food', 'val' => 'Nasi'],
                    ['label' => 'Minuman', 'icon' => 'ph-coffee', 'val' => 'Minuman'],
                    ['label' => 'Mie', 'icon' => 'ph-bowl-steam', 'val' => 'Mie'],
                    ['label' => 'Jajanan', 'icon' => 'ph-cookie', 'val' => 'Jajanan'],
                    ['label' => 'Lainnya', 'icon' => 'ph-dots-three', 'val' => 'Lainnya'],
                ];

                $currentCat = request('category');
            @endphp

            @foreach($categories as $cat)
                @php
                    $isActive = ($currentCat == $cat['val']) || ($cat['val'] === null && !$currentCat);
                    $classes = getCatClass($isActive);
                @endphp

                <a href="{{ route('orders.create', ['category' => $cat['val']]) }}">
                    <div class="{{ getCatClass($isActive) }} w-[88px] h-[92px] border rounded-2xl flex flex-col items-center justify-center gap-2 transition-all duration-300">
                        <i class="ph {{ $cat['icon'] }} text-3xl mb-1""></i>
                        <span class="text-[12px] font-medium {{ $isActive ? 'text-[#37967D] font-bold' : 'text-gray-500' }}">{{ $cat['label'] }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>

<div class="px-6 pt-2 pb-32 bg-white min-h-screen">
    <div class="grid grid-cols-2 gap-4">
        @forelse($products as $product)
        <div class="bg-white p-3 rounded-2xl border border-gray-200 shadow-[0_2px_8px_rgba(0,0,0,0.04)] flex flex-col h-full group transition-all hover:border-[#37967D]/50 product-card"
            data-id="{{ $product->id }}">

            <div class="relative w-full aspect-square mb-3 overflow-hidden rounded-xl bg-gray-50">
                @if($product->image_url)
                <img src="{{ asset('storage/' . $product->image_url) }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                @else
                <div class="w-full h-full flex items-center justify-center text-gray-300">
                    <i class="ph ph-image text-3xl"></i>
                </div>
                @endif
            </div>

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

            <div class="flex items-center justify-between mt-auto pt-0.5 border-t border-dashed border-gray-100">
                <span class="font-bold text-sm text-gray-900">Rp{{ number_format($product->harga_jual, 0, ',', '.') }}</span>

                <div class="flex items-center gap-2">
                    <button type="button" class="btn-minus hidden w-8 h-8 rounded-xl border border-gray-200 flex items-center justify-center text-gray-500 active:bg-gray-100 hover:border-[#37967D] hover:text-[#37967D] transition-all">
                        <i class="ph-bold ph-minus text-xs"></i>
                    </button>

                    <span class="qty-display hidden text-sm font-bold text-gray-800 w-5 text-center">0</span>

                    <button type="button" class="btn-plus w-8 h-8 rounded-xl bg-[#37967D] text-white flex items-center justify-center shadow-lg shadow-[#37967D]/20 active:scale-90 hover:bg-[#2f826c] transition-all">
                        <i class="ph-bold ph-plus text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-2 text-center py-12">
            <div class="bg-gray-50 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                <i class="ph ph-bowl-food text-gray-300 text-4xl"></i>
            </div>
            <h3 class="text-gray-800 font-semibold mb-1">Menu tidak ditemukan</h3>
            <p class="text-gray-400 text-sm">Coba cari dengan kata kunci lain.</p>
        </div>
        @endforelse
    </div>
</div>

<form id="checkoutForm" action="{{ route('orders.detail') }}" method="POST">
    @csrf
    <input type="hidden" name="cart" id="cartInput">

    <div id="checkoutBar" class="invisible fixed bottom-[90px] left-0 w-full px-6 z-40 max-w-[480px] mx-auto left-0 right-0 transform translate-y-10 transition-all duration-300">
        <button type="button" onclick="submitToDetail()" class="w-full bg-[#37967D] text-white font-bold py-4 rounded-2xl shadow-2xl shadow-[#37967D]/40 flex justify-between items-center px-6 active:scale-98 transition-transform hover:bg-[#2f826c] border border-white/10 backdrop-blur-sm">
            <div class="flex flex-col items-start">
                <span class="text-[10px] uppercase tracking-wider opacity-80 font-medium">Total Pesanan</span>
                <span id="barTotalPrice" class="text-lg font-bold">Rp0</span>
            </div>

            <div class="flex items-center gap-3 bg-black/10 px-4 py-2 rounded-xl">
                <span class="text-sm font-bold">Lanjut</span>
                <span id="totalItems" class="bg-white text-[#37967D] px-2 py-0.5 rounded-md text-[10px] font-bold">0</span>
                <i class="ph-bold ph-arrow-right"></i>
            </div>
        </button>
    </div>
</form>

<div id="product-data" data-products="{!! htmlspecialchars(json_encode($products->keyBy('id')), ENT_QUOTES, 'UTF-8') !!}" class="hidden"></div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Ambil data produk dengan aman
        const productDataElement = document.getElementById('product-data');
        const dbProducts = JSON.parse(productDataElement.getAttribute('data-products'));

        let cart = {};

        const productCards = document.querySelectorAll('.product-card');
        const checkoutBar = document.getElementById('checkoutBar');
        const totalItemsSpan = document.getElementById('totalItems');
        const barTotalPriceSpan = document.getElementById('barTotalPrice');

        // Logic Tombol Plus/Minus
        productCards.forEach(card => {
            const id = card.dataset.id;
            const btnPlus = card.querySelector('.btn-plus');
            const btnMinus = card.querySelector('.btn-minus');

            btnPlus.addEventListener('click', () => {
                cart[id] = (cart[id] || 0) + 1;
                updateCardUI(card, id);
                updateFloatingBar();
            });

            btnMinus.addEventListener('click', () => {
                if (cart[id] > 0) {
                    cart[id]--;
                    if (cart[id] === 0) delete cart[id];
                }
                updateCardUI(card, id);
                updateFloatingBar();
            });
        });

        // Update UI Kartu Produk
        function updateCardUI(card, id) {
            const qty = cart[id] || 0;
            const btnMinus = card.querySelector('.btn-minus');
            const qtyDisplay = card.querySelector('.qty-display');

            qtyDisplay.innerText = qty;

            if (qty > 0) {
                btnMinus.classList.remove('hidden');
                qtyDisplay.classList.remove('hidden');
                card.classList.add('border-[#37967D]', 'bg-green-50/30');
            } else {
                btnMinus.classList.add('hidden');
                qtyDisplay.classList.add('hidden');
                card.classList.remove('border-[#37967D]', 'bg-green-50/30');
            }
        }

        // Update Floating Bar Bawah
        function updateFloatingBar() {
            let totalQty = 0;
            let totalPrice = 0;

            for (const [id, qty] of Object.entries(cart)) {
                if (dbProducts[id]) {
                    totalQty += qty;
                    totalPrice += dbProducts[id].harga_jual * qty;
                }
            }

            totalItemsSpan.innerText = totalQty;
            const formattedPrice = 'Rp' + totalPrice.toLocaleString('id-ID');
            barTotalPriceSpan.innerText = formattedPrice;

            // Munculkan Bar jika ada item
            if (totalQty > 0) {
                checkoutBar.classList.remove('invisible', 'translate-y-10');
            } else {
                checkoutBar.classList.add('invisible', 'translate-y-10');
            }
        }

        // Fungsi Submit ke Halaman Detail
        window.submitToDetail = function() {
            const cartData = [];
            for (const [id, qty] of Object.entries(cart)) {
                cartData.push({
                    id: id,
                    qty: qty
                });
            }

            // Simpan data ke input hidden dan submit
            document.getElementById('cartInput').value = JSON.stringify(cartData);
            document.getElementById('checkoutForm').submit();
        }
    });
</script>

@endsection