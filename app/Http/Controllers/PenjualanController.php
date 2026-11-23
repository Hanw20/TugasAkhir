<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Customer;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $query = Penjualan::with(['customer', 'user'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('id', 'desc');

        $penjualan = $query->get();
        $totalAkumulasi = $penjualan->where('status', '!=', 'batal')->sum('total');

        return view('admin.penjualan.index', compact('penjualan', 'bulan', 'tahun', 'totalAkumulasi'));
    }

    public function create()
    {
        $customers = Customer::orderBy('nama_customer')->get();
        $produks = Produk::with('satuanDasar')
            ->where(function ($q) {
                $q->where('stok', '>', 0)
                    ->orWhere('stok_onorder_supplier', '>', 0);
            })
            ->orderBy('nama_produk')
            ->get();

        $today = date('Ymd');
        $countToday = Penjualan::whereDate('created_at', today())->count() + 1;
        $noNotaOtomatis = 'PJ-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

        return view('admin.penjualan.create', compact('customers', 'produks', 'noNotaOtomatis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_nota' => 'required|string|unique:penjualan,no_nota',
            'customer_id' => 'required|integer',
            'tanggal' => 'required|date',
            'metode_pengiriman' => 'required|in:langsung,nanti',
            'metode_pembayaran' => 'required|in:lunas,tempo',
            'produk_id' => 'required|array|min:1',
            'qty' => 'required|array|min:1',
            'harga_satuan' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $total = 0;
            foreach ($request->produk_id as $index => $prodId) {
                $subtotal = $request->qty[$index] * $request->harga_satuan[$index];
                $total += $subtotal;
            }

            $statusPengiriman = ($request->metode_pengiriman === 'langsung') ? 'seluruh' : 'belum_dikirim';
            $statusPembayaran = ($request->metode_pembayaran === 'lunas') ? 'lunas' : 'belum_dibayar';

            $penjualan = Penjualan::create([
                'no_nota' => $request->no_nota,
                'customer_id' => $request->customer_id,
                'user_id' => Auth::id() ?? 1,
                'tanggal' => $request->tanggal,
                'total' => $total,
                'ongkos_kirim' => 0,
                'diskon' => 0,
                'status' => ($statusPengiriman === 'seluruh') ? 'selesai' : 'proses',
                'status_pengiriman' => $statusPengiriman,
                'status_pembayaran' => $statusPembayaran,
            ]);

            foreach ($request->produk_id as $index => $prodId) {
                $qty = (float) $request->qty[$index];
                $harga = (float) $request->harga_satuan[$index];
                $subtotal = $qty * $harga;

                $produk = Produk::findOrFail($prodId);

                if ($request->metode_pengiriman === 'langsung') {
                    if ($produk->stok < $qty) {
                        throw new \Exception("Stok fisik '{$produk->nama_produk}' di gudang tidak mencukupi untuk dibawa langsung! (Tersedia: {$produk->stok})");
                    }
                } else {
                    $kapasitas = ((float)$produk->stok + (float)$produk->stok_onorder_supplier) - (float)$produk->stok_onorder_customer;
                    if ($kapasitas < $qty) {
                        $kapasitasTampil = max(0, $kapasitas);
                        throw new \Exception("Total stok '{$produk->nama_produk}' tidak mencukupi untuk pesanan pengiriman ini! (Maksimal tersedia dari fisik + pesanan supplier: {$kapasitasTampil})");
                    }
                }

                $detailPenjualan = DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'produk_id' => $prodId,
                    'qty' => $qty,
                    'harga_satuan' => $harga,
                    'diskon' => 0,
                    'nominal_bayar' => $subtotal,
                    'subtotal' => $subtotal,
                ]);

                if ($request->metode_pengiriman === 'langsung') {
                    $produk->decrement('stok', $qty);
                } else {
                    $produk->increment('stok_onorder_customer', $qty);
                }
            }

            if ($request->metode_pengiriman === 'langsung') {
                $pengiriman = \App\Models\PengirimanPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'tanggal_kirim' => $request->tanggal,
                    'user_id' => Auth::id() ?? 1,
                    'catatan' => 'Barang langsung diserahkan di toko (Bawa Langsung)',
                ]);

                foreach ($penjualan->detailPenjualan as $dp) {
                    \App\Models\DetailPengirimanPenjualan::create([
                        'pengiriman_penjualan_id' => $pengiriman->id,
                        'detail_penjualan_id' => $dp->id,
                        'qty_dikirim' => $dp->qty,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.penjualan.index')
                ->with('success', 'Transaksi penjualan berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
    public function show(string $id)
    {
        $penjualan = Penjualan::with(['customer', 'user', 'detailPenjualan.produk.satuanDasar'])->findOrFail($id);
        return view('admin.penjualan.show', compact('penjualan'));
    }

    public function edit(string $id)
    {
    }
    public function update(Request $request, string $id)
    {
    }
    public function destroy(string $id)
    {
        $penjualan = Penjualan::with(['detailPenjualan.produk', 'pengirimanPenjualan', 'pembayaranPiutang'])->findOrFail($id);

        if ($penjualan->status === 'batal') {
            return back()->with('error', 'Nota penjualan ini sudah dibatalkan sebelumnya!');
        }

        if ($penjualan->pembayaranPiutang()->exists()) {
            return back()->with('error', 'Nota penjualan tidak dapat dibatalkan karena sudah memiliki riwayat pembayaran cicilan piutang!');
        }

        $isBawaLangsung = ($penjualan->pengirimanPenjualan->count() === 1 && $penjualan->pengirimanPenjualan->first()->catatan === 'Barang langsung diserahkan di toko (Bawa Langsung)');
        $isBelumKirim = ($penjualan->status_pengiriman === 'belum_dikirim' && $penjualan->pengirimanPenjualan->count() === 0);

        if (!$isBawaLangsung && !$isBelumKirim) {
            return back()->with('error', 'Nota penjualan tidak dapat dibatalkan karena sebagian atau seluruh barang sudah dalam proses pengiriman ekspedisi!');
        }

        DB::beginTransaction();
        try {
            foreach ($penjualan->detailPenjualan as $dp) {
                if ($dp->produk) {
                    if ($isBawaLangsung) {
                        $dp->produk->increment('stok', $dp->qty);
                    } else {
                        $sisaOnOrder = max(0, (float)$dp->produk->stok_onorder_customer - (float)$dp->qty);
                        $dp->produk->update(['stok_onorder_customer' => $sisaOnOrder]);
                    }
                }
            }

            $penjualan->update([
                'status' => 'batal',
            ]);

            DB::commit();
            return redirect()->route('admin.penjualan.show', $penjualan->id)
                ->with('success', 'Nota penjualan #' . $penjualan->no_nota . ' berhasil dibatalkan dan stok produk telah disesuaikan kembali!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan nota: ' . $e->getMessage());
        }
    }
}
