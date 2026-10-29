<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MerekProduk;

class MerekProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = MerekProduk::orderBy('id', 'desc')->get();
        // Nanti kita buat file view-nya di folder admin/merek/
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
        // Ambil data merek beserta produk-produk di dalamnya
        $merek = MerekProduk::with('produk')->findOrFail($id);
        return view('admin.merek.show', compact('merek'));
    }
    public function edit(string $id)
    {
        $merek = MerekProduk::find($id);
        return response()->json($merek);
    }
    public function update(Request $request, string $id)
    {
        $request->validate(['nama_merek' => 'required|string|max:255']);
        $merek = MerekProduk::find($id);
        $merek->update(['nama_merek' => $request->nama_merek]);

        return response()->json(['message' => 'Merek berhasil diubah!']);
    }
    public function destroy(string $id)
    {
        MerekProduk::find($id)->delete();
        return response()->json(['message' => 'Merek berhasil dihapus!']);
    }
}
