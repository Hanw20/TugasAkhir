<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MerekProduk;

class MerekProdukController extends Controller
{
    
    public function index()
    {
        $data = MerekProduk::withCount('produk')->orderBy('id', 'desc')->get();
        return view('admin.merek.index', compact('data'));
    }
    public function create()
    {
    }
    public function store(Request $request)
    {
        $request->validate(['nama_merek' => 'required|string|max:255']);
        MerekProduk::create(['nama_merek' => $request->nama_merek]);

        return response()->json(['message' => 'Merek berhasil disimpan!']);
    }
    public function show(string $id)
    {
        $merek = MerekProduk::with('produk')->findOrFail($id);
        return view('admin.merek.show', compact('merek'));
    }
    public function edit(string $id)
    {
        $merek = MerekProduk::findOrFail($id);
        $merek->is_used = $merek->produk()->exists();
        return response()->json($merek);
    }
    public function update(Request $request, string $id)
    {
        $merek = MerekProduk::findOrFail($id);

        if ($merek->produk()->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => "Merek '{$merek->nama_merek}' tidak dapat diubah karena sedang digunakan oleh produk aktif!"
            ], 422);
        }

        $request->validate(['nama_merek' => 'required|string|max:255']);
        $merek->update(['nama_merek' => $request->nama_merek]);

        return response()->json(['message' => 'Merek berhasil diubah!']);
    }
    public function destroy(string $id)
    {
        $merek = MerekProduk::findOrFail($id);

        if ($merek->produk()->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => "Merek '{$merek->nama_merek}' tidak dapat dihapus karena masih digunakan oleh produk aktif!"
            ], 422);
        }

        $merek->delete();
        return response()->json(['message' => 'Merek berhasil dihapus!']);
    }
}
