<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\PengirimanPenjualan;
use App\Models\DetailPengirimanPenjualan;
use App\Models\DetailPenjualan;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PengirimanPenjualanController extends Controller
{
    // Daftar Log Semua Pengiriman Barang (Surat Jalan Gudang)
    public function index()
    {
        $pengiriman = PengirimanPenjualan::with([
            'penjualan.customer',
            'user',
            'detailPengiriman.detailPenjualan.produk.satuanDasar'
        ])
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.penjualan.pengiriman_index', compact('pengiriman'));
    }

    // Form Checklist Barang Keluar / Truk Berangkat
    public function create(string $id)
    {
        $penjualan = Penjualan::with(['customer', 'detailPenjualan.produk.satuanDasar'])->findOrFail($id);

        if ($penjualan->status_pengiriman === 'terkirim') {
            return redirect()->route('admin.penjualan.show', $id)
                ->with('error', 'Semua barang pada nota ini sudah dikirimkan!');
        }

        // Sekarang mengarah ke pengiriman.blade.php (sama persis dengan penerimaan)
        return view('admin.penjualan.pengiriman', compact('penjualan'));
    }

    // Simpan Checklist Pengiriman & Kurangi Stok Fisik Gudang
    public function store(Request $request, string $id)
    {
        $request->validate([
            'tanggal_kirim' => 'required|date',
            'detail_id' => 'required|array',
            'qty_dikirim' => 'required|array',
            'catatan' => 'nullable|string|max:255',
        ]);

        $penjualan = Penjualan::with('detailPenjualan')->findOrFail($id);

        DB::beginTransaction();
        try {
            // 1. Simpan Header Surat Jalan Pengiriman
            $pengiriman = PengirimanPenjualan::create([
                'penjualan_id' => $penjualan->id,
                'tanggal_kirim' => $request->tanggal_kirim,
                'user_id' => Auth::id() ?? 1,
                'catatan' => $request->catatan,
            ]);

            // 2. Simpan Detail Pengiriman & Potong Stok Produk
            foreach ($request->detail_id as $index => $detailId) {
                $qtyDikirim = (float) $request->qty_dikirim[$index];

                if ($qtyDikirim > 0) {
                    $detailPenjualan = DetailPenjualan::find($detailId);

                    if ($detailPenjualan && $detailPenjualan->produk) {
                        $produk = $detailPenjualan->produk;

                        // Cek apakah stok cukup saat barang fisik dinaikkan ke truk
                        if ($produk->stok < $qtyDikirim) {
                            throw new \Exception("Stok fisik '{$produk->nama_produk}' tidak mencukupi di gudang (Sisa: {$produk->stok})!");
                        }

                        DetailPengirimanPenjualan::create([
                            'pengiriman_penjualan_id' => $pengiriman->id,
                            'detail_penjualan_id' => $detailId,
                            'qty_dikirim' => $qtyDikirim,
                        ]);

                        // Potong stok produk nyata
                        $produk->decrement('stok', $qtyDikirim);
                    }
                }
            }

            // 3. Cek Status Akumulasi Pengiriman (Apakah sudah terkirim semua atau baru sebagian?)
            $penjualan->refresh();
            $semuaLengkap = true;
            $adaYangDikirim = false;

            foreach ($penjualan->detailPenjualan as $dp) {
                if ($dp->sisa_qty > 0) {
                    $semuaLengkap = false;
                }
                if ($dp->qty_dikirim > 0) {
                    $adaYangDikirim = true;
                }
            }

            if ($semuaLengkap) {
                $penjualan->update([
                    'status_pengiriman' => 'terkirim',
                    'status' => 'selesai'
                ]);
            } elseif ($adaYangDikirim) {
                $penjualan->update([
                    'status_pengiriman' => 'sebagian',
                    'status' => 'proses'
                ]);
            }

            DB::commit();
            return redirect()->route('admin.penjualan.pengiriman.show', $pengiriman->id)
                ->with('success', 'Barang berhasil diberangkatkan dan stok gudang telah terpotong!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // Lihat Surat Jalan Bukti Fisik Pengiriman
    public function show(string $id)
    {
        $pengiriman = PengirimanPenjualan::with([
            'penjualan.customer',
            'user',
            'detailPengiriman.detailPenjualan.produk.satuanDasar'
        ])->findOrFail($id);

        return view('admin.penjualan.pengiriman_show', compact('pengiriman'));
    }
}