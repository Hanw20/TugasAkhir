<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index()
    {
        $data = Supplier::withCount(['pembelian', 'riwayatPenilaianSupplier'])->orderBy('id', 'desc')->get();
        return view('admin.supplier.index', compact('data'));
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_supplier' => 'required|string|max:50|unique:suppliers,kode_supplier',
            'nama_supplier' => 'required|string|max:100',
            'alamat' => 'required|string',
            'nomor_telepon' => 'required|string|max:20',
            'email' => 'nullable|email|max:100'
        ]);

        Supplier::create($request->all());

        return response()->json(['message' => 'Supplier berhasil disimpan!']);
    }

    public function show(string $id)
    {
        $supplier = Supplier::with(['riwayatPenilaianSupplier.kriteria', 'produk.satuanDasar'])->findOrFail($id);
        $semuaProduk = \App\Models\Produk::orderBy('nama_produk', 'asc')->get();

        $k3 = \App\Models\KriteriaSupplier::where('kode_kriteria', 'K3')->first();
        $k4 = \App\Models\KriteriaSupplier::where('kode_kriteria', 'K4')->first();

        $terakhirRespon = $supplier->riwayatPenilaianSupplier()
            ->where('kriteria_supplier_id', $k3->id ?? 0)
            ->latest('tanggal_penilaian')
            ->first();

        $terakhirGaransi = $supplier->riwayatPenilaianSupplier()
            ->where('kriteria_supplier_id', $k4->id ?? 0)
            ->latest('tanggal_penilaian')
            ->first();

        return view('admin.supplier.show', compact('supplier', 'semuaProduk', 'terakhirRespon', 'terakhirGaransi'));
    }

    public function edit(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        return response()->json($supplier);
    }

    public function update(Request $request, string $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'alamat' => 'required|string',
            'nomor_telepon' => 'required|string|max:20',
            'email' => 'nullable|email|max:100'
        ]);

        $supplier->update([
            'nomor_telepon' => $request->nomor_telepon,
            'email' => $request->email,
            'alamat' => $request->alamat,
        ]);

        return response()->json(['message' => 'Data supplier berhasil diperbarui!']);
    }

    public function destroy(string $id)
    {
        $supplier = Supplier::findOrFail($id);

        $adaTransaksi = $supplier->pembelian()->exists() 
            || $supplier->riwayatPenilaianSupplier()->exists();

        if ($adaTransaksi) {
            return response()->json([
                'status'  => 'error',
                'message' => "Supplier '{$supplier->nama_supplier}' tidak dapat dihapus karena sudah memiliki riwayat transaksi pembelian atau penilaian!"
            ], 422);
        }

        $supplier->produk()->detach();
        $supplier->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Supplier berhasil dihapus!'
        ]);
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
        $k3 = \App\Models\KriteriaSupplier::where('kode_kriteria', 'K3')->first();
        $k4 = \App\Models\KriteriaSupplier::where('kode_kriteria', 'K4')->first();

        if ($k3) {
            \App\Models\RiwayatPenilaianSupplier::create([
                'supplier_id' => $supplier->id,
                'kriteria_supplier_id' => $k3->id,
                'nilai' => $request->nilai_respon,
                'tanggal_penilaian' => $request->tanggal_penilaian,
                'keterangan' => 'Evaluasi Kecepatan Respon: ' . ($request->keterangan ?? '-'),
            ]);
        }

        if ($k4) {
            \App\Models\RiwayatPenilaianSupplier::create([
                'supplier_id' => $supplier->id,
                'kriteria_supplier_id' => $k4->id,
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
