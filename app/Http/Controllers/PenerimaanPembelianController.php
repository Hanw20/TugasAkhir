<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\PenerimaanPembelian;
use App\Models\DetailPenerimaanPembelian;
use App\Models\DetailPembelian;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PenerimaanPembelianController extends Controller
{
    public function index()
    {
        // Ambil riwayat setiap sesi kedatangan barang beserta rincian item fisik yang diterima
        $penerimaan = PenerimaanPembelian::with([
            'pembelian.supplier',
            'user',
            'detailPenerimaan.detailPembelian.produk.satuanDasar'
        ])
            ->orderBy('tanggal_terima', 'desc')
            ->get();

        return view('admin.pembelian.penerimaan_index', compact('penerimaan'));
    }

    // Form Checklist Barang Datang
    public function create(string $id)
    {
        $pembelian = Pembelian::with(['supplier', 'detailPembelian.produk.satuanDasar'])->findOrFail($id);

        if ($pembelian->status_penerimaan === 'terima_penuh') {
            return redirect()->route('admin.pembelian.show', $id)
                ->with('error', 'Semua barang pada faktur ini sudah diterima penuh!');
        }

        return view('admin.pembelian.penerimaan', compact('pembelian'));
    }

    // Simpan Checklist Barang Masuk & Update Stok Produk Nyata
    public function store(Request $request, string $id)
    {
        $request->validate([
            'tanggal_terima' => 'required|date',
            'detail_id' => 'required|array',
            'qty_diterima' => 'required|array',
            'nilai_kualitas' => 'required|numeric|min:1|max:5', // Validasi input kualitas
            'catatan' => 'nullable|string|max:255',
        ]);

        $pembelian = Pembelian::with('detailPembelian')->findOrFail($id);

        DB::beginTransaction();
        try {
            // 1. Simpan Header Penerimaan
            $penerimaan = PenerimaanPembelian::create([
                'pembelian_id' => $pembelian->id,
                'tanggal_terima' => $request->tanggal_terima,
                'user_id' => Auth::id() ?? 1,
                'catatan' => $request->catatan,
            ]);

            $firstDetailPenerimaanId = null;

            // 2. Simpan Detail Penerimaan & Tambah Stok Produk
            foreach ($request->detail_id as $index => $detailId) {
                $qtyDiterima = (float) $request->qty_diterima[$index];

                if ($qtyDiterima > 0) {
                    $detailPenerimaan = DetailPenerimaanPembelian::create([
                        'penerimaan_pembelian_id' => $penerimaan->id,
                        'detail_pembelian_id' => $detailId,
                        'qty_diterima' => $qtyDiterima,
                    ]);

                    if (!$firstDetailPenerimaanId) {
                        $firstDetailPenerimaanId = $detailPenerimaan->id;
                    }

                    // Tambah Stok Fisik Produk & Hitung Moving Average
                    $detailPembelian = DetailPembelian::find($detailId);
                    if ($detailPembelian && $detailPembelian->produk) {
                        $produk = $detailPembelian->produk;

                        $stokLama = (float) $produk->stok;
                        $hargaRataLama = (float) $produk->harga_beli;

                        $qtyBaru = (float) $qtyDiterima;
                        $hargaBeliFaktur = (float) $detailPembelian->harga_satuan;

                        $totalStokBaru = $stokLama + $qtyBaru;
                        if ($totalStokBaru > 0) {
                            $hargaRataBaru = (($stokLama * $hargaRataLama) + ($qtyBaru * $hargaBeliFaktur)) / $totalStokBaru;
                        } else {
                            $hargaRataBaru = $hargaBeliFaktur;
                        }

                        $produk->update([
                            'stok' => $totalStokBaru,
                            'harga_beli' => round($hargaRataBaru, 2),
                            'harga_beli_terakhir' => $hargaBeliFaktur,
                        ]);
                    }
                }
            }

            // 👇 2B. SIMPAN PENILAIAN KRITERIA KUALITAS (K2) KE TABEL riwayat_penilaian_supplier 👇
            // Cari ID Kriteria Kualitas (kode K2)
            $kriteriaKualitas = \App\Models\KriteriaAhp::where('kode_kriteria', 'K2')
                ->orWhere('nama_kriteria', 'like', '%Kualitas%')
                ->first();

            if ($kriteriaKualitas) {
                \App\Models\RiwayatPenilaianSupplier::create([
                    'supplier_id' => $pembelian->supplier_id,
                    'detail_penerimaan_pembelian_id' => $firstDetailPenerimaanId,
                    'kriteria_ahp_id' => $kriteriaKualitas->id,
                    'nilai' => $request->nilai_kualitas,
                    'tanggal_penilaian' => $request->tanggal_terima,
                    'keterangan' => 'Penilaian fisik penerimaan faktur #' . $pembelian->no_faktur . ': ' . ($request->catatan ?? 'Kondisi barang dicek gudang'),
                ]);
            }

            // 3. Cek Status Akumulasi Pembelian
            $pembelian->refresh();
            $semuaLengkap = true;
            $adaYangDiterima = false;

            foreach ($pembelian->detailPembelian as $dp) {
                if ($dp->sisa_qty > 0) {
                    $semuaLengkap = false;
                }
                if ($dp->qty_diterima > 0) {
                    $adaYangDiterima = true;
                }
            }

            if ($semuaLengkap) {
                $pembelian->update([
                    'status_penerimaan' => 'terima_penuh',
                    'status' => 'selesai'
                ]);
            } elseif ($adaYangDiterima) {
                $pembelian->update([
                    'status_penerimaan' => 'sebagian',
                    'status' => 'dikirim'
                ]);
            }

            DB::commit();
            return redirect()->route('admin.pembelian.show', $pembelian->id)
                ->with('success', 'Barang berhasil diceklis dan skor kualitas fisik telah disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memproses penerimaan: ' . $e->getMessage());
        }
    }
    public function show(string $id)
    {
        // Ambil data 1 sesi penerimaan fisik tertentu beserta rincian barang yang diterima
        $penerimaan = PenerimaanPembelian::with([
            'pembelian.supplier',
            'user',
            'detailPenerimaan.detailPembelian.produk.satuanDasar'
        ])->findOrFail($id);

        return view('admin.pembelian.penerimaan_show', compact('penerimaan'));
    }
}