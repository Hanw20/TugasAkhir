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
        $data = Produk::with(['kategori', 'merek', 'satuanDasar'])
            ->withCount(['detailPenjualan', 'detailPembelian', 'konversiAsal', 'konversiTujuan', 'detailStokOpname'])
            ->orderBy('id', 'desc')
            ->get();
        
        $kategori = KategoriProduk::orderBy('nama_kategori')->get();
        $merek = MerekProduk::orderBy('nama_merek')->get();
        $satuan = SatuanProduk::orderBy('nama_satuan')->get();

        return view('admin.produk.index', compact('data', 'kategori', 'merek', 'satuan'));
    }

    public function create() {}

    public function store(Request $request)
    {
        $request->validate([
            'kode_produk' => 'required|string|max:50|unique:produk,kode_produk',
            'nama_produk' => 'required|string|max:100',
            'kategori_produk_id' => 'required|exists:kategori_produk,id',
            'merek_id' => 'required|exists:merek_produk,id',
            'satuan_dasar_id' => 'required|exists:satuan_produk,id',
            'metode_jual' => 'required|in:satuan,nominal_timbang',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|numeric|min:0',
            'stok_minimum' => 'required|numeric|min:0'
        ]);

        Produk::create($request->all());
        
        return response()->json(['message' => 'Produk berhasil disimpan!']);
    }

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
            'harga_jual' => 'required|numeric|min:0',
            'stok_minimum' => 'required|numeric|min:0'
        ]);

        $produk = Produk::findOrFail($id);
        $produk->update([
            'harga_jual' => $request->harga_jual,
            'stok_minimum' => $request->stok_minimum,
        ]);
        
        return response()->json(['message' => 'Produk berhasil diubah!']);
    }

    public function destroy(string $id)
    {
        $produk = Produk::findOrFail($id);

        $adaTransaksi = $produk->detailPenjualan()->exists()
            || $produk->detailPembelian()->exists()
            || $produk->konversiAsal()->exists()
            || $produk->konversiTujuan()->exists()
            || $produk->detailStokOpname()->exists();

        if ($adaTransaksi) {
            return response()->json([
                'status'  => 'error',
                'message' => "Produk '{$produk->nama_produk}' tidak dapat dihapus karena sudah memiliki riwayat transaksi!"
            ], 422);
        }

        $produk->supplier()->detach();
        $produk->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil dihapus!'
        ]);
    }
}
