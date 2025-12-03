<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // Tambahkan ini

class ProductController extends Controller
{
    /**
     * Menampilkan daftar produk (Index)
     * Otomatis difilter by user_id karena Global Scope di Model Product
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Logika Pencarian
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_produk', 'like', "%{$keyword}%")
                  ->orWhere('kategori', 'like', "%{$keyword}%");
            });
        }

        // Ambil data terbaru
        $products = $query->latest()->get();

        return view('menu.index', compact('products'));
    }

    /**
     * Menampilkan Form Tambah Produk
     */
    public function create()
    {
        return view('menu.create');
    }

    /**
     * Menyimpan Produk Baru ke Database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi'   => 'required|string',
            'kategori'    => 'required|string',
            'stock'       => 'required|integer|min:0',
            'harga_modal' => 'required|numeric|min:0',
            'persen_keuntungan' => 'required|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // Hitung harga jual otomatis
        $validated['harga_jual'] = $validated['harga_modal'] + 
            ($validated['harga_modal'] * ($validated['persen_keuntungan'] / 100));

        // Upload Gambar jika ada
        if ($request->hasFile('image')) {
            $validated['image_url'] = $request->file('image')->store('products', 'public');
        }

        // Hapus field 'image' temp
        unset($validated['image']);

        // Tambahkan user_id manual (opsional jika boot model gagal, tapi aman ditambah)
        $validated['user_id'] = Auth::id();

        Product::create($validated);

        // Redirect kembali ke halaman menu dengan pesan sukses
        return redirect()->route('menu.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    /**
     * Menampilkan Form Edit Produk
     */
    public function edit(string $id)
    {
        // FindOrFail otomatis cek user_id (404 jika punya user lain)
        $product = Product::findOrFail($id);
        return view('menu.edit', compact('product'));
    }

    /**
     * Memperbarui Produk (Update)
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi'   => 'required|string',
            'kategori'    => 'required|string',
            'stock'       => 'required|integer|min:0',
            'harga_modal' => 'required|numeric|min:0',
            'persen_keuntungan' => 'required|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // Hitung ulang harga jual
        $validated['harga_jual'] = $validated['harga_modal'] + 
            ($validated['harga_modal'] * ($validated['persen_keuntungan'] / 100));

        // Cek jika ada upload gambar baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama dari storage jika ada
            if ($product->image_url) {
                Storage::disk('public')->delete($product->image_url);
            }
            // Simpan gambar baru
            $validated['image_url'] = $request->file('image')->store('products', 'public');
        }

        unset($validated['image']);

        $product->update($validated);

        return redirect()->route('menu.index')
            ->with('success', 'Produk berhasil diperbarui!');
    }

    /**
     * Menghapus Produk (Destroy)
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        // Hapus gambar dari storage
        if ($product->image_url) {
            Storage::disk('public')->delete($product->image_url);
        }

        $product->delete();

        return redirect()->route('menu.index')
            ->with('success', 'Produk berhasil dihapus!');
    }
}