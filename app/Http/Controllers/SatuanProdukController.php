<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SatuanProduk;

class SatuanProdukController extends Controller
{
    public function index()
    {
        $data = SatuanProduk::withCount(['produk', 'masterKonversiAsal', 'masterKonversiTujuan'])
            ->orderBy('id', 'desc')
            ->get();
        return view('admin.satuan.index', compact('data')); 
    }

    public function create() {}

    public function store(Request $request)
    {
        $request->validate(['nama_satuan' => 'required|string|max:50']);
        SatuanProduk::create(['nama_satuan' => $request->nama_satuan]);
        
        return response()->json(['message' => 'Satuan berhasil disimpan!']);
    }

    public function show(string $id) {}

    public function edit(string $id)
    {
        $satuan = SatuanProduk::findOrFail($id);
        $satuan->is_used = $satuan->produk()->exists() 
            || $satuan->masterKonversiAsal()->exists() 
            || $satuan->masterKonversiTujuan()->exists();
        return response()->json($satuan);
    }

    public function update(Request $request, string $id)
    {
        $satuan = SatuanProduk::findOrFail($id);
        $isUsed = $satuan->produk()->exists() 
            || $satuan->masterKonversiAsal()->exists() 
            || $satuan->masterKonversiTujuan()->exists();

        if ($isUsed) {
            return response()->json([
                'status'  => 'error',
                'message' => "Satuan '{$satuan->nama_satuan}' tidak dapat diubah karena sedang digunakan oleh produk atau master rumus konversi!"
            ], 422);
        }

        $request->validate(['nama_satuan' => 'required|string|max:50']);
        $satuan->update(['nama_satuan' => $request->nama_satuan]);
        
        return response()->json(['message' => 'Satuan berhasil diubah!']);
    }

    public function destroy(string $id)
    {
        $satuan = SatuanProduk::findOrFail($id);
        $isUsed = $satuan->produk()->exists() 
            || $satuan->masterKonversiAsal()->exists() 
            || $satuan->masterKonversiTujuan()->exists();

        if ($isUsed) {
            return response()->json([
                'status'  => 'error',
                'message' => "Satuan '{$satuan->nama_satuan}' tidak dapat dihapus karena sedang digunakan oleh produk atau master rumus konversi!"
            ], 422);
        }

        $satuan->delete();
        return response()->json(['message' => 'Satuan berhasil dihapus!']);
    }
}
