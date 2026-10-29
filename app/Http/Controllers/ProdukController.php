<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\KategoriProduk;
use App\Models\MerekProduk;
use App\Models\SatuanProduk;

class ProdukController extends Controller
{
    public function index()
    {
        // Ambil data produk beserta relasinya
        $data = Produk::with(['kategori', 'merek', 'satuanDasar'])->orderBy('id', 'desc')->get();
        
        // Ambil data master untuk Dropdown di Form
        $kategori = KategoriProduk::orderBy('nama_kategori')->get();
        $merek = MerekProduk::orderBy('nama_merek')->get();
        $satuan = SatuanProduk::orderBy('nama_satuan')->get();

        return view('admin.produk.index', compact('data', 'kategori', 'merek', 'satuan'));
    }

    public function create() {}

    public function store(Request $request)
    {
        $request->validate([
            'kode_produk' => 'required|string|unique:produk,kode_produk',
            'nama_produk' => 'required|string|max:255',
            'kategori_produk_id' => 'required|integer',
            'merek_id' => 'required|integer',
            'satuan_dasar_id' => 'required|integer',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|numeric',
            'stok_minimum' => 'required|numeric'
        ]);

        Produk::create($request->all());
        
        return response()->json(['message' => 'Produk berhasil disimpan!']);
    }

    // Fungsi show kita siapkan untuk "Halaman Detail Produk"
    public function show(string $id)
    {
        $produk = Produk::with(['kategori', 'merek', 'satuanDasar'])->findOrFail($id);
        return view('admin.produk.show', compact('produk'));
    }

    public function edit(string $id)
    {
        $produk = Produk::find($id);
        return response()->json($produk);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'kode_produk' => 'required|string|unique:produk,kode_produk,'.$id,
            'nama_produk' => 'required|string|max:255',
            'kategori_produk_id' => 'required|integer',
            'merek_id' => 'required|integer',
            'satuan_dasar_id' => 'required|integer',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|numeric',
            'stok_minimum' => 'required|numeric'
        ]);

        $produk = Produk::find($id);
        $produk->update($request->all());
        
        return response()->json(['message' => 'Produk berhasil diubah!']);
    }

    public function destroy(string $id)
    {
        Produk::find($id)->delete();
        return response()->json(['message' => 'Produk berhasil dihapus!']);
    }
}