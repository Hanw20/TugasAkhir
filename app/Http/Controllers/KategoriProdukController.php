<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriProduk; 

class KategoriProdukController extends Controller
{
    public function index()
    {
        $data = KategoriProduk::orderBy('id', 'desc')->get();
        return view('admin.kategori.index', compact('data'));
    }

    public function create()
    {
        // Kosongkan saja untuk AJAX
    }

    public function store(Request $request)
    {
        $request->validate(['nama_kategori' => 'required|string|max:255']);
        KategoriProduk::create(['nama_kategori' => $request->nama_kategori]);
        
        return response()->json(['message' => 'Kategori berhasil disimpan!']);
    }

    public function show(string $id)
    {
        // Ambil data kategori, sekaligus "tarik" data produk yang terhubung dengannya
        $kategori = KategoriProduk::with('produk')->findOrFail($id);
        return view('admin.kategori.show', compact('kategori'));
    }

    public function edit(string $id)
    {
        $kategori = KategoriProduk::find($id);
        return response()->json($kategori); // Mengirim data lama ke Form Modal
    }

    public function update(Request $request, string $id)
    {
        $request->validate(['nama_kategori' => 'required|string|max:255']);
        $kategori = KategoriProduk::find($id);
        $kategori->update(['nama_kategori' => $request->nama_kategori]);
        
        return response()->json(['message' => 'Kategori berhasil diubah!']);
    }

    public function destroy(string $id)
    {
        KategoriProduk::find($id)->delete();
        return response()->json(['message' => 'Kategori berhasil dihapus!']);
    }
}