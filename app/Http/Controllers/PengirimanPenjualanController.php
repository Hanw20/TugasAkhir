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
    public function index()
    {
        $pengiriman = PengirimanPenjualan::with([
            'penjualan.customer',
            'user',
            'detailPengirimanPenjualan.detailPenjualan.produk.satuanDasar'
        ])
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.penjualan.pengiriman_index', compact('pengiriman'));
    }

    public function create(string $id)
    {
        $penjualan = Penjualan::with(['customer', 'detailPenjualan.produk.satuanDasar'])->findOrFail($id);

        if ($penjualan->status_pengiriman === 'seluruh') {
            return redirect()->route('admin.penjualan.show', $id)
                ->with('error', 'Semua barang pada nota ini sudah dikirimkan!');
        }

        return view('admin.penjualan.pengiriman', compact('penjualan'));
    }

    public function store(Request $request, string $id)
    {
        $request->validate([
            'tanggal_kirim' => 'required|date',
            'detail_id' => 'required|array',
            'qty_dikirim' => 'required|array',
            'catatan' => 'nullable|string|max:255',
        ]);

        $totalQtyDikirim = 0;
        foreach ($request->detail_id as $index => $detailId) {
            $qty = (float) ($request->qty_dikirim[$index] ?? 0);
            if ($qty < 0) {
                return back()->withInput()->with('error', 'Jumlah kirim tidak boleh bernilai negatif!');
            }
            $totalQtyDikirim += $qty;
        }

        if ($totalQtyDikirim <= 0) {
            return back()->withInput()->with('error', 'Harap masukkan jumlah kirim minimal 1 barang yang bernilai lebih dari 0!');
        }

        $penjualan = Penjualan::with('detailPenjualan')->findOrFail($id);

        DB::beginTransaction();
        try {
            $pengiriman = PengirimanPenjualan::create([
                'penjualan_id' => $penjualan->id,
                'tanggal_kirim' => $request->tanggal_kirim,
                'user_id' => Auth::id() ?? 1,
                'catatan' => $request->catatan,
            ]);

            foreach ($request->detail_id as $index => $detailId) {
                $qtyDikirim = (float) $request->qty_dikirim[$index];

                if ($qtyDikirim > 0) {
                    $detailPenjualan = DetailPenjualan::find($detailId);

                    if ($detailPenjualan && $detailPenjualan->produk) {
                        $produk = $detailPenjualan->produk;

                        if ($qtyDikirim > $detailPenjualan->sisa_qty) {
                            throw new \Exception("Jumlah kirim untuk '{$produk->nama_produk}' ({$qtyDikirim}) melebihi sisa pesanan yang belum dikirim ({$detailPenjualan->sisa_qty})!");
                        }

                        if ($produk->stok < $qtyDikirim) {
                            throw new \Exception("Stok fisik '{$produk->nama_produk}' tidak mencukupi di gudang (Sisa: {$produk->stok})!");
                        }

                        DetailPengirimanPenjualan::create([
                            'pengiriman_penjualan_id' => $pengiriman->id,
                            'detail_penjualan_id' => $detailId,
                            'qty_dikirim' => $qtyDikirim,
                        ]);

                        $produk->decrement('stok', $qtyDikirim);
                        $sisaOnOrderCust = max(0, (float)$produk->stok_onorder_customer - $qtyDikirim);
                        $produk->update(['stok_onorder_customer' => $sisaOnOrderCust]);
                    }
                }
            }

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
                    'status_pengiriman' => 'seluruh',
                    'status' => 'selesai'
                ]);
            } elseif ($adaYangDikirim) {
                $penjualan->update([
                    'status_pengiriman' => 'dikirim_sebagian',
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

    public function show(string $id)
    {
        $pengiriman = PengirimanPenjualan::with([
            'penjualan.customer',
            'user',
            'detailPengirimanPenjualan.detailPenjualan.produk.satuanDasar'
        ])->findOrFail($id);

        return view('admin.penjualan.pengiriman_show', compact('pengiriman'));
    }
}
