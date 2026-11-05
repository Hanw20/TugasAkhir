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
            'nilai_kualitas' => 'required|array', // Sekarang array per baris produk!
            'catatan' => 'nullable|string|max:255',
        ]);

        $pembelian = Pembelian::with('detailPembelian')->findOrFail($id);

        // Ambil Kriteria Kualitas (K2) untuk SAW
        $kriteriaKualitas = \App\Models\KriteriaAhp::where('kode_kriteria', 'K2')
            ->orWhere('nama_kriteria', 'like', '%Kualitas%')
            ->first();

        DB::beginTransaction();
        try {
            // 1. Simpan Header Penerimaan
            $penerimaan = PenerimaanPembelian::create([
                'pembelian_id' => $pembelian->id,
                'tanggal_terima' => $request->tanggal_terima,
                'user_id' => Auth::id() ?? 1,
                'catatan' => $request->catatan,
            ]);

            // 2. Simpan Detail Penerimaan, Penilaian Kualitas Per Produk, & Tambah Stok
            foreach ($request->detail_id as $index => $detailId) {
                $qtyDiterima = (float) $request->qty_diterima[$index];

                if ($qtyDiterima > 0) {
                    $detailPenerimaan = DetailPenerimaanPembelian::create([
                        'penerimaan_pembelian_id' => $penerimaan->id,
                        'detail_pembelian_id' => $detailId,
                        'qty_diterima' => $qtyDiterima,
                    ]);

                    // Simpan Penilaian Kualitas Fisik Produk ke Tabel riwayat_penilaian_supplier
                    if ($kriteriaKualitas) {
                        \App\Models\RiwayatPenilaianSupplier::create([
                            'supplier_id' => $pembelian->supplier_id,
                            'detail_penerimaan_pembelian_id' => $detailPenerimaan->id, // Tepat per produk!
                            'kriteria_ahp_id' => $kriteriaKualitas->id,
                            'nilai' => $request->nilai_kualitas[$index] ?? 5,
                            'tanggal_penilaian' => $request->tanggal_terima,
                            'keterangan' => 'Penilaian fisik produk pada faktur #' . $pembelian->no_faktur,
                        ]);
                    }

                    // Tambah Stok Fisik Produk & Kurangi stok_onorder_supplier
                    $detailPembelian = DetailPembelian::find($detailId);
                    if ($detailPembelian && $detailPembelian->produk) {
                        $produk = $detailPembelian->produk;

                        $stokLama = (float) $produk->stok;
                        $hargaRataLama = (float) $produk->harga_beli;

                        $qtyBaru = (float) $qtyDiterima;
                        $hargaBeliFaktur = (float) $detailPembelian->harga_satuan;

                        $totalStokBaru = $stokLama + $qtyBaru;
                        $hargaRataBaru = ($totalStokBaru > 0) 
                            ? (($stokLama * $hargaRataLama) + ($qtyBaru * $hargaBeliFaktur)) / $totalStokBaru 
                            : $hargaBeliFaktur;

                        $produk->update([
                            'stok' => $totalStokBaru,
                            'stok_onorder_supplier' => max(0, (float)$produk->stok_onorder_supplier - $qtyBaru),
                            'harga_beli' => round($hargaRataBaru, 2),
                            'harga_beli_terakhir' => $hargaBeliFaktur,
                        ]);
                    }
                }
            }

                   // 3. Update Status Akumulasi Penerimaan Faktur Pembelian
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
                ->with('success', 'Penerimaan barang dan penilaian kualitas produk berhasil disimpan!');

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