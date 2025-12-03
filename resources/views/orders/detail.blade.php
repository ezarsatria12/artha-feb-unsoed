@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')

{{-- Form Pembungkus Utama --}}
<form action="{{ route('orders.store') }}" method="POST" id="orderForm" class="min-h-screen bg-white relative">
    @csrf
    
    {{-- Input Hidden --}}
    <input type="hidden" name="cart" id="cartInput">
    <input type="hidden" name="order_code" value="{{ $orderCode }}">
    <input type="hidden" name="tipe_pesanan" id="orderType" value="makan_ditempat">
    <input type="hidden" name="payment_method" id="paymentMethod" value="tunai">

    {{-- HEADER (Sticky Top) --}}
    <div class="sticky top-0 bg-white z-[50] px-6 pt-8 pb-4 border-b border-gray-50 flex items-center justify-between">
        <a href="{{ route('orders.create') }}" class="text-gray-900 hover:text-[#37967D] transition-colors p-1 -ml-1">
            <i class="ph-bold ph-caret-left text-2xl"></i>
        </a>
        <h1 class="font-bold text-lg text-gray-900 tracking-tight">{{ $orderCode }}</h1>
        <button type="button" class="text-gray-900 hover:text-[#37967D] transition-colors p-1 -mr-1">
            <i class="ph-bold ph-info text-2xl"></i>
        </button>
    </div>

    {{-- KONTEN SCROLLABLE --}}
    {{-- pb-40 memberikan ruang agar konten paling bawah tidak tertutup tombol submit --}}
    <div class="p-6 space-y-6 pb-40">

        {{-- 1. Info Tanggal --}}
        <div>
            <h2 class="font-bold text-xl text-gray-900 mb-1">Detail Pesanan</h2>
            <p class="text-xs text-gray-400 leading-relaxed">
                Tanggal: {{ now()->translatedFormat('l, d F Y') }}<br>
                Jam: {{ now()->format('H:i') }} WIB
            </p>
        </div>

        {{-- 2. Tipe Pesanan --}}
        <input type="hidden" name="tipe_pesanan" id="orderType" value="makan_ditempat">
        <div class="bg-gray-50 p-1.5 rounded-2xl flex">
            <button type="button" id="makanBtn" onclick="setOrderType('makan_ditempat')"
                class="flex-1 py-3 text-sm font-medium rounded-xl bg-[#37967D] text-white shadow-md transition-all flex items-center justify-center gap-2">
                Makan Ditempat
            </button>

            <button type="button" id="bungkusBtn" onclick="setOrderType('bungkus')"
                class="flex-1 py-3 text-sm font-medium text-gray-400 rounded-xl flex items-center justify-center gap-2 transition-all hover:text-gray-600">
                Bungkus
            </button>
        </div>

        {{-- 3. Nama Pemesan --}}
        <div>
            <label class="text-sm font-bold text-gray-900 mb-2 block">Nama Pemesan</label>
            <input type="text" name="nama_pemesan" required
                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:border-[#37967D] focus:ring-1 focus:ring-[#37967D] transition-all placeholder-gray-400 font-medium"
                placeholder="Masukkan nama pelanggan...">
        </div>

        {{-- 4. List Menu --}}
        <div>
            <div class="flex justify-between items-end mb-4">
                <h3 class="font-bold text-gray-900 text-sm">Menu Pesanan</h3>
                <span class="text-xs text-gray-400">{{ count($details) }} Produk</span>
            </div>

            <div id="cartListContainer" class="space-y-6">
                @foreach ($details as $index => $item)
                <div class="flex gap-4 cart-item border-b border-dashed border-gray-100 pb-6 last:border-0 last:pb-0" 
                     id="item-row-{{ $index }}" 
                     data-index="{{ $index }}" 
                     data-price="{{ $item['price'] }}">
                    
                    {{-- Gambar --}}
                    <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-100 shrink-0 border border-gray-100 shadow-sm">
                        @if ($item['image_url'])
                            <img src="{{ asset('storage/' . $item['image_url']) }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center w-full h-full text-gray-300">
                                <i class="ph-fill ph-image text-2xl"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Info & Kontrol --}}
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-1">
                            <h4 class="font-bold text-gray-900 text-sm leading-tight">{{ $item['name'] }}</h4>
                            <span class="font-bold text-sm text-gray-900 item-subtotal">
                                Rp{{ number_format($item['subtotal'], 0, ',', '.') }}
                            </span>
                        </div>
                        
                        <p class="text-xs text-gray-500 font-medium mb-3">
                            RP. {{ number_format($item['price'], 0, ',', '.') }} × <span class="qty-text">{{ $item['qty'] }}</span>
                        </p>

                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-3">
                                <button type="button" class="btn-action w-6 h-6 rounded-full border border-gray-300 flex items-center justify-center text-gray-600 active:bg-gray-100 transition-all" data-action="decrease">
                                    <i class="ph-bold ph-minus text-[10px]"></i>
                                </button>
                                
                                <span class="text-sm font-bold text-gray-900 w-3 text-center item-qty">{{ $item['qty'] }}</span>

                                <button type="button" class="btn-action w-6 h-6 rounded-full bg-[#1F2937] text-white flex items-center justify-center shadow-md active:scale-90 transition-all" data-action="increase">
                                    <i class="ph-bold ph-plus text-[10px]"></i>
                                </button>
                            </div>

                            <button type="button" class="btn-action text-red-400 hover:text-red-600 transition-colors bg-red-50 p-1.5 rounded-lg" data-action="remove">
                                <i class="ph-bold ph-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- 5. Rincian Biaya --}}
        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 mt-6">
            <h3 class="font-bold text-gray-900 text-sm mb-3">Rincian Pembayaran</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-gray-500">
                    <span>Subtotal</span>
                    <span class="font-bold text-gray-900" id="summarySubtotal">Rp{{ number_format($totalPrice, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span>Pajak / Biaya Layanan</span>
                    <span class="font-bold text-gray-900">Rp0</span>
                </div>
                <div class="border-t border-dashed border-gray-200 my-2"></div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-900 font-bold">Total Akhir</span>
                    <span class="text-lg font-black text-[#37967D]" id="summaryTotal">Rp{{ number_format($totalPrice, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- 6. Metode Pembayaran --}}
        <div class="mt-6">
            <h3 class="font-bold text-gray-900 text-sm mb-3">Metode Pembayaran</h3>
            <div class="flex gap-3">
                <button type="button" id="cashBtn" onclick="setPayment('tunai')"
                    class="flex-1 border border-[#37967D] bg-[#EBF8F5] py-3.5 rounded-xl flex flex-col items-center gap-1 transition-all group">
                    <i class="ph-fill ph-money text-xl text-[#37967D]"></i>
                    <span class="text-xs font-bold text-[#37967D]">Tunai (Cash)</span>
                </button>

                <button type="button" id="qrisBtn" onclick="setPayment('qris')"
                    class="flex-1 border border-gray-200 bg-white py-3.5 rounded-xl flex flex-col items-center gap-1 transition-all hover:bg-gray-50 group">
                    <i class="ph-fill ph-qr-code text-xl text-gray-400 group-hover:text-gray-600"></i>
                    <span class="text-xs font-bold text-gray-400 group-hover:text-gray-600">QRIS Scan</span>
                </button>
            </div>
        </div>

    </div>

    {{-- 
        7. BUTTON SUBMIT (FIXED BOTTOM)
        - z-[999] agar berada di atas Bottom Nav Bar (jika ada)
        - Background putih solid agar tidak transparan
    --}}
    <div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-100 p-5 z-[999] max-w-[480px] mx-auto right-0 shadow-[0_-4px_20px_rgba(0,0,0,0.05)]">
        <button type="submit" class="w-full bg-[#37967D] text-white py-4 rounded-2xl font-medium text-[16px] shadow-lg shadow-[#37967D]/30 active:scale-98 transition-transform hover:bg-[#2f826c] flex items-center justify-center gap-2">
            <span>Proses Pesanan</span>
            <i class="ph-bold ph-arrow-right"></i>
        </button>
    </div>

</form>

{{-- Data Cart untuk JS --}}
<div id="init-data" data-cart="{{ json_encode(array_values($details)) }}" class="hidden"></div>

{{-- SCRIPT LOGIC --}}
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const initData = document.getElementById('init-data');
        let cart = JSON.parse(initData.getAttribute('data-cart'));
        const formatRp = (num) => 'Rp' + num.toLocaleString('id-ID');

        function renderCart() {
            let total = 0;
            let count = 0;
            let cartDataForInput = [];

            cart.forEach((item, index) => {
                const row = document.getElementById(`item-row-${index}`);
                
                if (item.qty > 0) {
                    const subtotal = item.price * item.qty;
                    total += subtotal;
                    count++;

                    if (row) {
                        row.style.display = 'flex';
                        row.querySelector('.item-qty').innerText = item.qty;
                        row.querySelector('.qty-text').innerText = item.qty;
                        row.querySelector('.item-subtotal').innerText = formatRp(subtotal);
                    }
                    cartDataForInput.push({ id: item.id, qty: item.qty });
                } else {
                    if (row) row.remove();
                }
            });

            document.getElementById('summarySubtotal').innerText = formatRp(total);
            document.getElementById('summaryTotal').innerText = formatRp(total);
            document.getElementById('cartInput').value = JSON.stringify(cartDataForInput);

            if (count === 0) {
                alert('Keranjang kosong, pesanan dibatalkan.');
                window.location.href = "{{ route('orders.create') }}";
            }
        }

        document.getElementById('cartListContainer').addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-action');
            if (!btn) return;

            const row = btn.closest('.cart-item');
            const index = parseInt(row.getAttribute('data-index'));
            const action = btn.getAttribute('data-action');

            if (action === 'increase') {
                cart[index].qty++;
            } else if (action === 'decrease') {
                if (cart[index].qty > 1) {
                    cart[index].qty--;
                } else {
                    if(confirm('Hapus menu ini?')) cart[index].qty = 0;
                }
            } else if (action === 'remove') {
                if(confirm('Hapus menu ini dari pesanan?')) cart[index].qty = 0;
            }
            renderCart();
        });

        window.setOrderType = function(type) {
            document.getElementById('orderType').value = type;
            const makanBtn = document.getElementById('makanBtn');
            const bungkusBtn = document.getElementById('bungkusBtn');
            
            const activeClasses = ['bg-[#37967D]', 'text-white', 'shadow-md'];
            const inactiveClasses = ['bg-transparent', 'text-gray-400', 'hover:text-gray-600'];

            if (type === 'makan_ditempat') {
                makanBtn.classList.add(...activeClasses);
                makanBtn.classList.remove(...inactiveClasses);
                bungkusBtn.classList.remove(...activeClasses);
                bungkusBtn.classList.add(...inactiveClasses);
            } else {
                bungkusBtn.classList.add(...activeClasses);
                bungkusBtn.classList.remove(...inactiveClasses);
                makanBtn.classList.remove(...activeClasses);
                makanBtn.classList.add(...inactiveClasses);
            }
        }

        window.setPayment = function(type) {
            document.getElementById('paymentMethod').value = type;
            const cashBtn = document.getElementById('cashBtn');
            const qrisBtn = document.getElementById('qrisBtn');

            const activeClass = "border-[#37967D] bg-[#EBF8F5]";
            const activeText = "text-[#37967D]";
            const inactiveClass = "border-gray-200 bg-white";
            const inactiveText = "text-gray-400";

            // Fungsi reset style tombol
            const resetBtn = (btn, isCash) => {
                const icon = btn.querySelector('i');
                const text = btn.querySelector('span');
                btn.className = `flex-1 border ${inactiveClass} py-3.5 rounded-xl flex flex-col items-center gap-1 transition-all hover:bg-gray-50 group`;
                icon.className = `ph-fill ${isCash ? 'ph-money' : 'ph-qr-code'} text-xl ${inactiveText} group-hover:text-gray-600`;
                text.className = `text-xs font-bold ${inactiveText} group-hover:text-gray-600`;
            }
            
            // Fungsi set active style
            const setActive = (btn, isCash) => {
                const icon = btn.querySelector('i');
                const text = btn.querySelector('span');
                btn.className = `flex-1 border ${activeClass} py-3.5 rounded-xl flex flex-col items-center gap-1 transition-all`;
                icon.className = `ph-fill ${isCash ? 'ph-money' : 'ph-qr-code'} text-xl ${activeText}`;
                text.className = `text-xs font-bold ${activeText}`;
            }

            if (type === 'tunai') {
                setActive(cashBtn, true);
                resetBtn(qrisBtn, false);
            } else {
                setActive(qrisBtn, false);
                resetBtn(cashBtn, true);
            }
        }

        renderCart();
    });
</script>

@endsection