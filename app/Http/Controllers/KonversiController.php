<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterKonversi; 
use App\Models\SatuanProduk;   

class KonversiController extends Controller
{
    public function index()
    {
        $data = MasterKonversi::with(['satuanAsal', 'satuanTujuan'])
            ->withCount('konversi')
            ->orderBy('id', 'desc')
            ->get();
        
        $satuan = SatuanProduk::orderBy('nama_satuan')->get();
        
        return view('admin.konversi.index', compact('data', 'satuan'));
    }

    public function create() {}

    public function store(Request $request)
    {
        $request->validate([
            'satuan_asal_id' => 'required|exists:satuan_produk,id',
            'satuan_tujuan_id' => 'required|exists:satuan_produk,id|different:satuan_asal_id',
            'nilai_konversi' => 'required|numeric|min:0.01'
        ], [
            'satuan_tujuan_id.different' => 'Satuan tujuan tidak boleh sama dengan satuan asal!'
        ]);

        MasterKonversi::create($request->all());
        
        return response()->json(['message' => 'Rumus konversi berhasil disimpan!']);
    }

    public function show(string $id) {}

    public function edit(string $id)
    {
        $konversi = MasterKonversi::findOrFail($id);
        $konversi->is_used = $konversi->konversi()->exists();
        return response()->json($konversi);
    }

    public function update(Request $request, string $id)
    {
        $konversi = MasterKonversi::findOrFail($id);

        if ($konversi->konversi()->exists()) {
            $request->validate([
                'nilai_konversi' => 'required|numeric|min:0.01'
            ]);

            $konversi->update([
                'nilai_konversi' => $request->nilai_konversi
            ]);

            return response()->json(['message' => 'Nilai rumus konversi berhasil diubah!']);
        }

        $request->validate([
            'satuan_asal_id' => 'required|exists:satuan_produk,id',
            'satuan_tujuan_id' => 'required|exists:satuan_produk,id|different:satuan_asal_id',
            'nilai_konversi' => 'required|numeric|min:0.01'
        ]);

        $konversi->update($request->all());
        
        return response()->json(['message' => 'Rumus konversi berhasil diubah!']);
    }

    public function destroy(string $id)
    {
        $konversi = MasterKonversi::findOrFail($id);

        if ($konversi->konversi()->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Rumus konversi ini tidak dapat dihapus karena sudah memiliki riwayat transaksi konversi produk!'
            ], 422);
        }

        $konversi->delete();
        return response()->json(['message' => 'Rumus konversi berhasil dihapus!']);
    }
}
