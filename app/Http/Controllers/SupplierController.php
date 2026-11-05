<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
class SupplierController extends Controller
{
    public function index()
    {
        $data = Supplier::orderBy('id', 'desc')->get();
        return view('admin.supplier.index', compact('data'));
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        // Validasi: unique:suppliers artinya kode tidak boleh kembar di database
        $request->validate([
            'kode_supplier' => 'required|string|unique:suppliers,kode_supplier',
            'nama_supplier' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'nomor_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email'
        ]);

        Supplier::create($request->all());

        return response()->json(['message' => 'Supplier berhasil disimpan!']);
    }

    public function show(string $id)
    {
        $supplier = Supplier::with(['riwayatPenilaian.kriteria', 'produk.satuanDasar'])->findOrFail($id);
        $semuaProduk = \App\Models\Produk::orderBy('nama_produk', 'asc')->get();
        return view('admin.supplier.show', compact('supplier', 'semuaProduk'));
    }

    public function edit(string $id)
    {
        $supplier = Supplier::find($id);
        return response()->json($supplier);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            // Pengecualian unique agar saat di-edit kodenya sendiri tidak terbaca kembar
            'kode_supplier' => 'required|string|unique:suppliers,kode_supplier,' . $id,
            'nama_supplier' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'nomor_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email'
        ]);

        $supplier = Supplier::find($id);
        $supplier->update($request->all());

        return response()->json(['message' => 'Supplier berhasil diubah!']);
    }

    public function destroy(string $id)
    {
        Supplier::find($id)->delete();
        return response()->json(['message' => 'Supplier berhasil dihapus!']);
    }
    public function storePenilaian(Request $request, string $id)
    {
        $request->validate([
            'nilai_respon' => 'required|numeric|min:1|max:5',
            'nilai_garansi' => 'required|numeric|min:1|max:5',
            'tanggal_penilaian' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $supplier = Supplier::findOrFail($id);
        $k3 = \App\Models\KriteriaAhp::where('kode_kriteria', 'K3')->first();
        $k4 = \App\Models\KriteriaAhp::where('kode_kriteria', 'K4')->first();

        // Simpan Nilai Kecepatan Respon (K3)
        if ($k3) {
            \App\Models\RiwayatPenilaianSupplier::create([
                'supplier_id' => $supplier->id,
                'kriteria_ahp_id' => $k3->id,
                'nilai' => $request->nilai_respon,
                'tanggal_penilaian' => $request->tanggal_penilaian,
                'keterangan' => 'Evaluasi Kecepatan Respon: ' . ($request->keterangan ?? '-'),
            ]);
        }

        // Simpan Nilai Garansi (K4)
        if ($k4) {
            \App\Models\RiwayatPenilaianSupplier::create([
                'supplier_id' => $supplier->id,
                'kriteria_ahp_id' => $k4->id,
                'nilai' => $request->nilai_garansi,
                'tanggal_penilaian' => $request->tanggal_penilaian,
                'keterangan' => 'Evaluasi Kebijakan Garansi: ' . ($request->keterangan ?? '-'),
            ]);
        }

        return back()->with('success', 'Evaluasi Kecepatan Respon & Garansi supplier berhasil dicatat!');
    }

    public function attachProduk(Request $request, string $id)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id'
        ]);

        $supplier = Supplier::findOrFail($id);

        // syncWithoutDetaching agar jika sudah ada tidak error / tidak duplikat
        $supplier->produk()->syncWithoutDetaching([$request->produk_id]);

        return back()->with('success', 'Produk berhasil ditambahkan ke daftar pasokan supplier ini!');
    }

    public function detachProduk(string $id, string $produk_id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->produk()->detach($produk_id);

        return back()->with('success', 'Produk berhasil dihapus dari daftar pasokan supplier ini!');
    }
}