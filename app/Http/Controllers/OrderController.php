<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Tambahkan ini untuk akses user ID
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Query otomatis difilter by user_id lewat Global Scope di Model
        $query = Order::withCount('items')->latest();

        // 1. Filter Pencarian
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('order_code', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_pemesan', 'like', '%' . $request->search . '%');
            });
        }

        // 2. Filter Waktu
        $dateFilter = $request->query('date_filter', 'today');

        switch ($dateFilter) {
            case 'week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year);
                break;
            case 'all':
                break;
            case 'today':
            default:
                $query->whereDate('created_at', Carbon::today());
                break;
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('orders.index', compact('orders', 'dateFilter'));
    }

    public function create(Request $request)
    {
        // Global Scope di Model Product juga otomatis memfilter produk milik user ini saja
        $query = Product::query();

        if ($request->filled('category') && $request->category !== 'Semua') {
            $query->where('kategori', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        $products = $query->where('stock', '>', 0)->latest()->get();

        return view('orders.create', compact('products'));
    }

    public function detail(Request $request)
    {
        if ($request->method() !== 'POST') {
            return redirect()->route('orders.create');
        }

        $cartData = json_decode($request->cart, true);
        
        if (!$cartData || count($cartData) == 0) {
            return redirect()->route('orders.create')->with('error', 'Keranjang kosong!');
        }

        $details = [];
        $totalPrice = 0;

        foreach ($cartData as $item) {
            // Find otomatis memfilter produk milik user ini
            $product = Product::find($item['id']);
            
            if ($product) {
                $subtotal = $product->harga_jual * $item['qty'];
                $totalPrice += $subtotal;
                
                $details[] = [
                    'id' => $product->id,
                    'name' => $product->nama_produk,
                    'qty' => $item['qty'],
                    'price' => $product->harga_jual,
                    'subtotal' => $subtotal,
                    'image_url' => $product->image_url
                ];
            }
        }

        // Generate Order Code (Hitung jumlah order milik user ini saja untuk nomor urut)
        $today = date('Ymd');
        $count = Order::whereDate('created_at', now())->count() + 1; 
        $orderCode = 'ORD-' . $today . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        return view('orders.detail', compact('details', 'totalPrice', 'orderCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|string',
            'nama_pemesan' => 'required|string',
            'tipe_pesanan' => 'required|in:makan_ditempat,bungkus',
            'payment_method' => 'required|in:tunai,qris',
        ]);

        $cartItems = json_decode($request->cart, true);

        if (empty($cartItems)) {
            return back()->with('error', 'Keranjang kosong!');
        }

        try {
            $order = DB::transaction(function () use ($request, $cartItems) {
                $today = date('Ymd');
                
                // Hitung nomor urut berdasarkan data user ini saja
                $lastOrder = Order::whereDate('created_at', now())->lockForUpdate()->count();
                $count = $lastOrder + 1;
                $orderCode = 'ORD-' . $today . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

                // Create Order
                // NOTE: 'user_id' akan otomatis terisi oleh Model Boot (langkah sebelumnya)
                // Tapi jika ingin eksplisit: 'user_id' => Auth::id(),
                $order = Order::create([
                    'order_code' => $orderCode,
                    'nama_pemesan' => $request->nama_pemesan,
                    'tipe_pesanan' => $request->tipe_pesanan,
                    'payment_method' => $request->payment_method,
                    'total_uang_masuk' => 0,
                    'total_modal' => 0,
                    'total_profit' => 0,
                    'status' => 'selesai'
                ]);

                $total_uang = 0;
                $total_modal = 0;
                $total_profit = 0;

                foreach ($cartItems as $item) {
                    // Cari produk & kunci row (Pastikan produk milik user ini)
                    $product = Product::lockForUpdate()->findOrFail($item['id']);

                    if ($product->stock < $item['qty']) {
                        throw new \Exception("Stok {$product->nama_produk} habis!");
                    }

                    $subtotal = $product->harga_jual * $item['qty'];
                    $modal = $product->harga_modal * $item['qty'];
                    $profit = ($product->harga_jual - $product->harga_modal) * $item['qty'];

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'jumlah' => $item['qty'],
                        'harga_modal' => $product->harga_modal,
                        'harga_jual' => $product->harga_jual,
                        'subtotal' => $subtotal,
                        'profit' => $profit,
                    ]);

                    $product->decrement('stock', $item['qty']);

                    $total_uang += $subtotal;
                    $total_modal += $modal;
                    $total_profit += $profit;
                }

                $order->update([
                    'total_uang_masuk' => $total_uang,
                    'total_modal' => $total_modal,
                    'total_profit' => $total_profit,
                ]);

                return $order;
            });

            return redirect()->route('orders.show', $order->id)->with('success', 'Pesanan berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        // FindOrFail otomatis mengecek user_id berkat Global Scope
        // Jika user A mencoba akses ID order user B, akan error 404 (Aman)
        $order = Order::with('items.product')->findOrFail($id);
        return view('orders.show', compact('order'));
    }
}