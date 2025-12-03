@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')

<form action="{{ route('orders.store') }}" method="POST" id="orderForm" class="min-h-screen pb-32">
    @csrf
    
    <input type="hidden" name="cart" id="cartInput">
    <input type="hidden" name="order_code" value="{{ $orderCode }}">

    <div class="p-4 space-y-5">

        <div class="flex items-center gap-4">
            <a href="{{ url()->previous() }}" class="text-gray-600 text-xl">
                <i class="ph ph-arrow-left"></i>
            </a>
            <h1 class="font-semibold text-lg text-gray-800">Detail Pesanan</h1>
            <button type="button" class="ml-auto text-gray-500 text-xl">
                <i class="ph ph-info"></i>
            </button>
        </div>

        <div>
            <h2 class="font-semibold text-base text-gray-800">Informasi Pesanan</h2>
            <p class="text-xs text-gray-500">Order ID: {{ $orderCode }}</p>
            <p class="text-xs text-gray-500">Tanggal: {{ now()->translatedFormat('d M Y') }}</p>
            <p class="text-xs text-gray-500">Jam: {{ now()->format('H:i') }} WIB</p>
        </div>

        <div class="bg-gray-100 p-1 rounded-2xl flex">
            <input type="hidden" name="tipe_pesanan" id="orderType" value="makan_ditempat">
            
            <button type="button" id="makanBtn" onclick="setOrderType('makan_ditempat')"
                class="flex-1 px-3 py-2 text-sm font-semibold rounded-xl bg-[#37967D] text-white shadow-sm flex items-center justify-center gap-2 transition-all">
                <i class="ph ph-bowl-food"></i> Makan Ditempat
            </button>

            <button type="button" id="bungkusBtn" onclick="setOrderType('bungkus')"
                class="flex-1 px-3 py-2 text-sm font-medium text-gray-500 rounded-xl flex items-center justify-center gap-2 transition-all hover:bg-gray-200">
                <i class="ph ph-shopping-bag"></i> Bungkus
            </button>
        </div>

        <div>
            <label class="text-sm text-gray-600 mb-1 block">Nama Pemesan</label>
            <input type="text" name="nama_pemesan" required
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#37967D] focus:ring-1 focus:ring-[#37967D]"
                placeholder="Masukkan nama">
        </div>

        <div class="border border-gray-200 rounded-2xl p-4 space-y-4 bg-white shadow-sm">
            <div class="flex justify-between text-sm text-gray-500 pb-2 border-b border-gray-50">
                <span>Menu Pesanan</span>
                <span id="totalCountBadge">{{ count($details) }} Produk</span>
            </div>

            <div id="cartListContainer" class="space-y-4">
                @foreach ($details as $index => $item)
                <div class="flex gap-3 cart-item" id="item-row-{{ $index }}" data-index="{{ $index }}" data-price="{{ $item['price'] }}">
                    
                    <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 shrink-0 border border-gray-100">
                        @if ($item['image_url'])
                            <img src="{{ $item['image_url'] }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center w-full h-full text-gray-300">
                                <i class="ph ph-image text-3xl"></i>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 flex flex-col justify-between">
                        <div>
                            <p class="font-semibold text-sm text-gray-800 line-clamp-1">{{ $item['name'] }}</p>
                            <p class="text-gray-500 text-xs mt-0.5">
                                Rp{{ number_format($item['price'], 0, ',', '.') }} × <span class="qty-text">{{ $item['qty'] }}</span>
                            </p>
                        </div>

                        <div class="flex items-center text-sm gap-2 mt-2 text-gray-500">
                            <button type="button" class="btn-action px-2 py-1 border rounded-lg border-gray-300 hover:bg-gray-50 transition-colors" data-action="decrease">
                                <i class="ph ph-minus pointer-events-none"></i>
                            </button>

                            <span class="text-gray-800 font-bold w-4 text-center item-qty">{{ $item['qty'] }}</span>

                            <button type="button" class="btn-action px-2 py-1 border rounded-lg border-gray-300 hover:bg-gray-50 transition-colors" data-action="increase">
                                <i class="ph ph-plus pointer-events-none"></i>
                            </button>

                            <button type="button" class="btn-action text-red-500 text-lg ml-auto hover:text-red-700 transition-colors" data-action="remove">
                                <i class="ph ph-trash pointer-events-none"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col justify-end">
                        <p class="font-semibold text-sm text-[#37967D] item-subtotal">
                            Rp{{ number_format($item['subtotal'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-2 text-sm text-gray-600">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span class="text-gray-800 font-medium" id="summarySubtotal">Rp{{ number_format($totalPrice, 0, ',', '.') }}</span>
            </div>

            <div class="flex justify-between">
                <span>Biaya lainnya</span>
                <span class="text-gray-800 font-medium">Rp0</span>
            </div>

            <hr class="border-gray-200 my-1">

            <div class="flex justify-between font-semibold text-base text-gray-800">
                <span>Total Pembayaran</span>
                <span class="text-[#37967D]" id="summaryTotal">Rp{{ number_format($totalPrice, 0, ',', '.') }}</span>
            </div>
        </div>

        <div>
            <p class="text-sm font-medium mb-2 text-gray-800">Metode Pembayaran</p>
            <input type="hidden" name="payment_method" id="paymentMethod" value="tunai">

            <div class="flex gap-4">
                <button type="button" id="cashBtn" onclick="setPayment('tunai')"
                    class="border border-[#37967D] bg-[#37967D]/10 px-4 py-3 rounded-xl flex flex-col items-center w-28 text-sm font-semibold text-[#37967D] transition-all">
                    <i class="ph-fill ph-money text-lg"></i>
                    <span>Cash</span>
                </button>

                <button type="button" id="qrisBtn" onclick="setPayment('qris')"
                    class="border border-gray-300 px-4 py-3 rounded-xl flex flex-col items-center w-28 text-sm text-gray-500 transition-all hover:bg-gray-50">
                    <i class="ph-fill ph-qr-code text-lg"></i>
                    <span>QRIS</span>
                </button>
            </div>
        </div>

        <button type="submit" class="bg-[#37967D] text-white w-full py-3.5 rounded-2xl font-semibold shadow-md active:scale-95 transition-transform hover:bg-[#2f826c]">
            Proses Pesanan
        </button>

    </div>
</form>

<div id="init-data" 
     data-cart="{{ json_encode(array_values($details)) }}"
     class="hidden">
</div>

{{-- SCRIPT LOGIC (CLEAN) --}}
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Ambil data dari atribut HTML (Solusi Error Merah)
        const initData = document.getElementById('init-data');
        let cart = JSON.parse(initData.getAttribute('data-cart'));
        
        const formatRp = (num) => 'Rp' + num.toLocaleString('id-ID');

        // Fungsi Render Ulang Tampilan
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
                        row.querySelector('.qty-text').innerText = item.qty; // Update text "Rp... x Qty"
                        row.querySelector('.item-subtotal').innerText = formatRp(subtotal);
                    }
                    
                    cartDataForInput.push({ id: item.id, qty: item.qty });
                } else {
                    if (row) row.style.display = 'none';
                }
            });

            // Update Total Bawah
            document.getElementById('summarySubtotal').innerText = formatRp(total);
            document.getElementById('summaryTotal').innerText = formatRp(total);
            document.getElementById('totalCountBadge').innerText = count + ' Produk';

            // Update Hidden Input (PENTING!)
            document.getElementById('cartInput').value = JSON.stringify(cartDataForInput);

            if (count === 0) {
                alert('Keranjang kosong, kembali ke menu.');
                window.location.href = "{{ route('orders.create') }}";
            }
        }

        // Event Listener Global (Delegation)
        document.getElementById('cartListContainer').addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-action');
            if (!btn) return;

            const row = btn.closest('.cart-item');
            const index = parseInt(row.getAttribute('data-index'));
            const action = btn.getAttribute('data-action');

            if (action === 'increase') {
                cart[index].qty++;
            } 
            else if (action === 'decrease') {
                if (cart[index].qty > 1) {
                    cart[index].qty--;
                } else {
                    if(confirm('Hapus menu ini?')) cart[index].qty = 0;
                }
            } 
            else if (action === 'remove') {
                if(confirm('Hapus menu ini dari pesanan?')) {
                    cart[index].qty = 0;
                }
            }

            renderCart();
        });

        // --- Logic Toggle Makan/Bungkus ---
        window.setOrderType = function(type) {
            const makanBtn = document.getElementById('makanBtn');
            const bungkusBtn = document.getElementById('bungkusBtn');
            document.getElementById('orderType').value = type;

            const activeClass = "bg-[#37967D] text-white font-semibold shadow-sm";
            const inactiveClass = "text-gray-500 font-medium hover:bg-gray-200";

            // Reset base class
            const baseClass = "flex-1 px-3 py-2 text-sm rounded-xl flex items-center justify-center gap-2 transition-all";

            if (type === 'makan_ditempat') {
                makanBtn.className = `${baseClass} ${activeClass}`;
                bungkusBtn.className = `${baseClass} ${inactiveClass}`;
            } else {
                bungkusBtn.className = `${baseClass} ${activeClass}`;
                makanBtn.className = `${baseClass} ${inactiveClass}`;
            }
        }

        // --- Logic Toggle Pembayaran ---
        window.setPayment = function(type) {
            const cashBtn = document.getElementById('cashBtn');
            const qrisBtn = document.getElementById('qrisBtn');
            document.getElementById('paymentMethod').value = type;

            const activeClass = "border-[#37967D] bg-[#37967D]/10 text-[#37967D] font-semibold";
            const inactiveClass = "border-gray-300 text-gray-500 font-medium hover:bg-gray-50";
            
            const baseClass = "border px-4 py-3 rounded-xl flex flex-col items-center w-28 text-sm transition-all";

            if (type === 'tunai') {
                cashBtn.className = `${baseClass} ${activeClass}`;
                qrisBtn.className = `${baseClass} ${inactiveClass}`;
            } else {
                qrisBtn.className = `${baseClass} ${activeClass}`;
                cashBtn.className = `${baseClass} ${inactiveClass}`;
            }
        }

        // Init data saat load
        renderCart();
    });
</script>

@endsection