<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterKonversi; 
use App\Models\SatuanProduk;   

class KonversiController extends Controller
{
    public function index()
    {
        // Ambil data rumus konversi beserta nama satuannya
        $data = MasterKonversi::with(['satuanAsal', 'satuanTujuan'])->orderBy('id', 'desc')->get();
        
        // Ambil data satuan untuk modal dropdown
        $satuan = SatuanProduk::orderBy('nama_satuan')->get();
        
        return view('admin.konversi.index', compact('data', 'satuan'));
    }

    public function create() {}

    public function store(Request $request)
    {
        // Validasi: Satuan asal & tujuan tidak boleh sama (different)
        $request->validate([
            'satuan_asal_id' => 'required|integer',
            'satuan_tujuan_id' => 'required|integer|different:satuan_asal_id',
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
        $konversi = MasterKonversi::find($id);
        return response()->json($konversi);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'satuan_asal_id' => 'required|integer',
            'satuan_tujuan_id' => 'required|integer|different:satuan_asal_id',
            'nilai_konversi' => 'required|numeric|min:0.01'
        ]);

        $konversi = MasterKonversi::find($id);
        $konversi->update($request->all());
        
        return response()->json(['message' => 'Rumus konversi berhasil diubah!']);
    }

    public function destroy(string $id)
    {
        MasterKonversi::find($id)->delete();
        return response()->json(['message' => 'Rumus konversi berhasil dihapus!']);
    }
}