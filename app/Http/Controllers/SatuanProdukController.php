<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SatuanProduk;

class SatuanProdukController extends Controller
{
    public function index()
    {
        $data = SatuanProduk::orderBy('id', 'desc')->get();
        // Nanti kita buat file view-nya di folder admin/satuan/
        return view('admin.satuan.index', compact('data')); 
    }

    public function create() {}

    public function store(Request $request)
    {
        $request->validate(['nama_satuan' => 'required|string|max:255']);
        SatuanProduk::create(['nama_satuan' => $request->nama_satuan]);
        
        return response()->json(['message' => 'Satuan berhasil disimpan!']);
    }

    public function show(string $id) {}

    public function edit(string $id)
    {
        $satuan = SatuanProduk::find($id);
        return response()->json($satuan);
    }

    public function update(Request $request, string $id)
    {
        $request->validate(['nama_satuan' => 'required|string|max:255']);
        $satuan = SatuanProduk::find($id);
        $satuan->update(['nama_satuan' => $request->nama_satuan]);
        
        return response()->json(['message' => 'Satuan berhasil diubah!']);
    }

    public function destroy(string $id)
    {
        SatuanProduk::find($id)->delete();
        return response()->json(['message' => 'Satuan berhasil dihapus!']);
    }
}