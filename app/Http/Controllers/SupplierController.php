<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Produk;
use App\Models\KriteriaSupplier;
use App\Models\RiwayatPenilaianSupplier;

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

        $avgRespon = $supplier->riwayatPenilaianSupplier()
            ->where('kriteria_supplier_id', $k3->id ?? 0)
            ->avg('nilai');

        $countRespon = $supplier->riwayatPenilaianSupplier()
            ->where('kriteria_supplier_id', $k3->id ?? 0)
            ->count();

        $terakhirGaransi = $supplier->riwayatPenilaianSupplier()
            ->where('kriteria_supplier_id', $k4->id ?? 0)
            ->latest('tanggal_penilaian')
            ->first();

        $avgGaransi = $supplier->riwayatPenilaianSupplier()
            ->where('kriteria_supplier_id', $k4->id ?? 0)
            ->avg('nilai');

        $countGaransi = $supplier->riwayatPenilaianSupplier()
            ->where('kriteria_supplier_id', $k4->id ?? 0)
            ->count();

        return view('admin.supplier.show', compact('supplier', 'semuaProduk', 'terakhirRespon', 'avgRespon', 'countRespon', 'terakhirGaransi', 'avgGaransi', 'countGaransi'));
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
            'nama_supplier' => 'required|string|max:100',
            'alamat' => 'required|string',
            'nomor_telepon' => 'required|string|max:20',
            'email' => 'nullable|email|max:100'
        ]);

        $supplier->update([
            'nama_supplier' => $request->nama_supplier,
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
                'keterangan' => 'Evaluasi Kecepatan Respon' . ($request->keterangan ? ': ' . $request->keterangan : ' berkala'),
            ]);
        }

        if ($k4) {
            \App\Models\RiwayatPenilaianSupplier::create([
                'supplier_id' => $supplier->id,
                'kriteria_supplier_id' => $k4->id,
                'nilai' => $request->nilai_garansi,
                'tanggal_penilaian' => $request->tanggal_penilaian,
                'keterangan' => 'Evaluasi Garansi' . ($request->keterangan ? ': ' . $request->keterangan : ' berkala'),
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

    public function laporanPenilaian(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $supplierId = $request->get('supplier_id');
        $kriteriaId = $request->get('kriteria_supplier_id');
        $produkId = $request->get('produk_id');

        $query = RiwayatPenilaianSupplier::with(['supplier', 'kriteria', 'detailPenerimaanPembelian.detailPembelian.produk'])
            ->whereMonth('tanggal_penilaian', $bulan)
            ->whereYear('tanggal_penilaian', $tahun);

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        if ($kriteriaId) {
            $query->where('kriteria_supplier_id', $kriteriaId);
        }

        if ($produkId) {
            $selectedProduk = Produk::find($produkId);
            $namaSelected = $selectedProduk ? $selectedProduk->nama_produk : '';
            $query->where(function ($q) use ($produkId, $namaSelected) {
                $q->whereHas('detailPenerimaanPembelian.detailPembelian', function ($sq) use ($produkId) {
                    $sq->where('produk_id', $produkId);
                });
                if ($namaSelected) {
                    $q->orWhere('keterangan', 'LIKE', '%' . $namaSelected . '%');
                }
            });
        }

        $riwayat = $query->orderBy('tanggal_penilaian', 'desc')->get();

        $suppliers = Supplier::orderBy('nama_supplier')->get();
        $produks = Produk::orderBy('nama_produk')->get();
        $kriteriaList = KriteriaSupplier::orderBy('kode_kriteria')->get();

        return view('admin.laporan.penilaian', compact(
            'riwayat', 'suppliers', 'produks', 'kriteriaList',
            'bulan', 'tahun', 'supplierId', 'kriteriaId', 'produkId'
        ));
    }
}
