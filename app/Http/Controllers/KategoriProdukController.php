<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriProduk; 

class KategoriProdukController extends Controller
{
    public function index()
    {
        $data = KategoriProduk::withCount('produk')->orderBy('id', 'desc')->get();
        return view('admin.kategori.index', compact('data'));
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        $request->validate(['nama_kategori' => 'required|string|max:255']);
        KategoriProduk::create(['nama_kategori' => $request->nama_kategori]);
        
        return response()->json(['message' => 'Kategori berhasil disimpan!']);
    }

    public function show(string $id)
    {
        $kategori = KategoriProduk::with('produk')->findOrFail($id);
        return view('admin.kategori.show', compact('kategori'));
    }

    public function edit(string $id)
    {
        $kategori = KategoriProduk::findOrFail($id);
        $kategori->is_used = $kategori->produk()->exists();
        return response()->json($kategori);
    }

    public function update(Request $request, string $id)
    {
        $kategori = KategoriProduk::findOrFail($id);

        if ($kategori->produk()->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => "Kategori '{$kategori->nama_kategori}' tidak dapat diubah karena sedang digunakan oleh produk aktif!"
            ], 422);
        }

        $request->validate(['nama_kategori' => 'required|string|max:255']);
        $kategori->update(['nama_kategori' => $request->nama_kategori]);
        
        return response()->json(['message' => 'Kategori berhasil diubah!']);
    }

    public function destroy(string $id)
    {
        $kategori = KategoriProduk::findOrFail($id);

        if ($kategori->produk()->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => "Kategori '{$kategori->nama_kategori}' tidak dapat dihapus karena masih digunakan oleh produk aktif!"
            ], 422);
        }

        $kategori->delete();
        return response()->json(['message' => 'Kategori berhasil dihapus!']);
    }
}
