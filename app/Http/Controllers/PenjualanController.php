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
    public function index()
    {
        // Ambil riwayat faktur penjualan / laporan penjualan
        $penjualan = Penjualan::with(['customer', 'user'])
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.penjualan.index', compact('penjualan'));
    }

    public function create()
    {
        $customers = Customer::orderBy('nama_customer')->get();
        $produks = Produk::with('satuanDasar')->where('stok', '>', 0)->orderBy('nama_produk')->get();

        // Buat nomor nota otomatis: PJ-20261022-0001
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

            $statusPengiriman = ($request->metode_pengiriman === 'langsung') ? 'terkirim' : 'belum_dikirim';
            $statusPembayaran = ($request->metode_pembayaran === 'lunas') ? 'lunas' : 'belum_dibayar';

            // 1. Simpan Header Penjualan
            $penjualan = Penjualan::create([
                'no_nota' => $request->no_nota,
                'customer_id' => $request->customer_id,
                'user_id' => Auth::id() ?? 1,
                'tanggal' => $request->tanggal,
                'total' => $total,
                'ongkos_kirim' => 0,
                'diskon' => 0,
                'status' => ($statusPengiriman === 'terkirim') ? 'selesai' : 'proses',
                'status_pengiriman' => $statusPengiriman,
                'status_pembayaran' => $statusPembayaran,
            ]);

            // 2. Simpan Detail Item
            foreach ($request->produk_id as $index => $prodId) {
                $qty = (float) $request->qty[$index];
                $harga = (float) $request->harga_satuan[$index];
                $subtotal = $qty * $harga;

                $produk = Produk::findOrFail($prodId);

                // Cek ketersediaan stok
                if ($produk->stok < $qty) {
                    throw new \Exception("Stok untuk produk '{$produk->nama_produk}' tidak mencukupi (Sisa stok: {$produk->stok}).");
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

                // Jika barang langsung dibawa pulang oleh pembeli, kurangi stok dan catat pengiriman otomatis
                if ($request->metode_pengiriman === 'langsung') {
                    $produk->decrement('stok', $qty);
                }
            }

            // Jika langsung dibawa pembeli, catat log pengiriman langsung
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
    }
}