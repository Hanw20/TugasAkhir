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
        $penerimaan = PenerimaanPembelian::with([
            'pembelian.supplier',
            'user',
            'detailPenerimaanPembelian.detailPembelian.produk.satuanDasar'
        ])
            ->orderBy('tanggal_terima', 'desc')
            ->get();

        return view('admin.pembelian.penerimaan_index', compact('penerimaan'));
    }

    public function create(string $id)
    {
        $pembelian = Pembelian::with(['supplier', 'detailPembelian.produk.satuanDasar'])->findOrFail($id);

        if ($pembelian->status_penerimaan === 'terima_penuh') {
            return redirect()->route('admin.pembelian.show', $id)
                ->with('error', 'Semua barang pada faktur ini sudah diterima penuh!');
        }

        return view('admin.pembelian.penerimaan', compact('pembelian'));
    }

    public function store(Request $request, string $id)
    {
        $request->validate([
            'tanggal_terima' => 'required|date',
            'detail_id' => 'required|array',
            'qty_diterima' => 'required|array',
            'nilai_kualitas' => 'required|array',
            'catatan' => 'nullable|string|max:255',
        ]);

        $totalQtyDiterima = 0;
        foreach ($request->detail_id as $index => $detailId) {
            $qty = (float) ($request->qty_diterima[$index] ?? 0);
            if ($qty < 0) {
                return back()->withInput()->with('error', 'Jumlah penerimaan tidak boleh bernilai negatif!');
            }
            $totalQtyDiterima += $qty;
        }

        if ($totalQtyDiterima <= 0) {
            return back()->withInput()->with('error', 'Harap masukkan jumlah penerimaan minimal 1 barang yang bernilai lebih dari 0!');
        }

        $pembelian = Pembelian::with('detailPembelian')->findOrFail($id);

        $kriteriaKualitas = \App\Models\KriteriaSupplier::where('kode_kriteria', 'K2')
            ->orWhere('nama_kriteria', 'like', '%Kualitas%')
            ->first();

        DB::beginTransaction();
        try {
            $penerimaan = PenerimaanPembelian::create([
                'pembelian_id' => $pembelian->id,
                'tanggal_terima' => $request->tanggal_terima,
                'user_id' => Auth::id() ?? 1,
                'catatan' => $request->catatan,
            ]);

            foreach ($request->detail_id as $index => $detailId) {
                $qtyDiterima = (float) $request->qty_diterima[$index];

                if ($qtyDiterima > 0) {
                    $detailPembelian = DetailPembelian::find($detailId);
                    if ($detailPembelian && $detailPembelian->produk) {
                        $produk = $detailPembelian->produk;

                        if ($qtyDiterima > $detailPembelian->sisa_qty) {
                            throw new \Exception("Jumlah terima untuk '{$produk->nama_produk}' ({$qtyDiterima}) melebihi sisa pesanan faktur ({$detailPembelian->sisa_qty})!");
                        }

                        $detailPenerimaan = DetailPenerimaanPembelian::create([
                            'penerimaan_pembelian_id' => $penerimaan->id,
                            'detail_pembelian_id' => $detailId,
                            'qty_diterima' => $qtyDiterima,
                        ]);

                        if ($kriteriaKualitas) {
                            \App\Models\RiwayatPenilaianSupplier::create([
                                'supplier_id' => $pembelian->supplier_id,
                                'detail_penerimaan_pembelian_id' => $detailPenerimaan->id,
                                'kriteria_supplier_id' => $kriteriaKualitas->id,
                                'nilai' => $request->nilai_kualitas[$index] ?? 5,
                                'tanggal_penilaian' => $request->tanggal_terima,
                                'keterangan' => 'Penilaian fisik ' . $produk->nama_produk . ' pada faktur #' . $pembelian->no_faktur,
                            ]);
                        }

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

                $kriteriaPengiriman = \App\Models\KriteriaSupplier::where('kode_kriteria', 'K5')->first();
                if ($kriteriaPengiriman) {
                    $tglPo = \Carbon\Carbon::parse($pembelian->tanggal);
                    $tglTerima = \Carbon\Carbon::parse($request->tanggal_terima);
                    $diff = $tglPo->diffInDays($tglTerima);
                    $hariKirim = $diff > 0 ? $diff : 1;
                    \App\Models\RiwayatPenilaianSupplier::create([
                        'supplier_id' => $pembelian->supplier_id,
                        'kriteria_supplier_id' => $kriteriaPengiriman->id,
                        'nilai' => $hariKirim,
                        'tanggal_penilaian' => $request->tanggal_terima,
                        'keterangan' => 'Waktu pengiriman pesanan faktur #' . $pembelian->no_faktur . ' (' . $hariKirim . ' hari)',
                    ]);
                }
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
        $penerimaan = PenerimaanPembelian::with([
            'pembelian.supplier',
            'user',
            'detailPenerimaanPembelian.detailPembelian.produk.satuanDasar'
        ])->findOrFail($id);

        return view('admin.pembelian.penerimaan_show', compact('penerimaan'));
    }
}
