<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class MarketplaceController extends Controller
{
    public function index()
    {
        // Urutkan produk terbaru
        $products = Product::latest()->get();
        return view('marketplace.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer|min:1', // Validasi Stok
            'whatsapp_number' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = $request->file('image')->store('products', 'public');

        Product::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description ?? 'Tidak ada deskripsi',
            'price' => $request->price,
            'stock' => $request->stock, // Simpan Stok
            'whatsapp_number' => $request->whatsapp_number,
            'image' => $imagePath,
        ]);

        return redirect()->back()->with('success', 'Produk berhasil dijual!');
    }

    // FUNGSI BARU: UPDATE STOK CEPAT (+/-)
    public function updateStock(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        // Pastikan yang ubah adalah pemilik
        if($product->user_id != Auth::id()) {
            abort(403);
        }

        if($request->action == 'plus') {
            $product->increment('stock');
        } elseif($request->action == 'minus' && $product->stock > 0) {
            $product->decrement('stock');
        }

        return redirect()->back()->with('success', 'Stok berhasil diupdate.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if($product->user_id == Auth::id()){
            $product->delete();
            return redirect()->back()->with('success', 'Produk dihapus.');
        }
        return abort(403);
    }
    // API: Rekomendasi Produk Berdasarkan Penyakit AI
    public function getRecommendation(Request $request)
    {
        $keyword = $request->query('label'); // Contoh: "Bercak"
        
        // Cari produk yang namanya atau deskripsinya mengandung kata kunci
        // Kita ambil 1 produk terbaik (paling mahal/paling atas)
        $product = Product::where('name', 'LIKE', "%{$keyword}%")
                          ->orWhere('description', 'LIKE', "%{$keyword}%")
                          ->orWhere('name', 'LIKE', '%Obat%') // Fallback kalau gak ketemu
                          ->latest()
                          ->first();

        if ($product) {
            return response()->json([
                'found' => true,
                'name' => $product->name,
                'price' => number_format($product->price),
                'image' => asset('storage/' . $product->image),
                'link' => "https://wa.me/{$product->whatsapp_number}?text=Saya+mau+beli+{$product->name}+untuk+mengobati+tanaman+saya",
            ]);
        }

        return response()->json(['found' => false]);
    }
    // FUNGSI 1: TAMPILKAN HALAMAN EDIT
    public function edit($id)
    {
        $product = Product::findOrFail($id);

        // Keamanan: Cek apakah user ini pemilik produk?
        if($product->user_id != Auth::id()){
            abort(403); // Dilarang masuk
        }

        return view('marketplace.edit', compact('product'));
    }

    // FUNGSI 2: SIMPAN PERUBAHAN
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Keamanan lagi
        if($product->user_id != Auth::id()){
            abort(403);
        }

        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'whatsapp_number' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Image boleh kosong kalau gak mau ganti
        ]);

        // Logika Ganti Gambar
        $imagePath = $product->image; // Pakai gambar lama dulu
        
        if ($request->hasFile('image')) {
            // Kalau user upload gambar baru
            // 1. Hapus gambar lama (opsional, biar hemat storage)
            if($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)){
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
            }
            // 2. Simpan gambar baru
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Update Database
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'whatsapp_number' => $request->whatsapp_number,
            'image' => $imagePath,
        ]);

        return redirect()->route('marketplace')->with('success', 'Produk berhasil diperbarui!');
    }
}