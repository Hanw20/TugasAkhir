<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\DetailPembelian;
use App\Models\Supplier;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $query = Pembelian::with(['supplier', 'user'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('id', 'desc');

        $pembelian = $query->get();
        $totalAkumulasi = $pembelian->where('status', '!=', 'batal')->sum('total');

        return view('admin.pembelian.index', compact('pembelian', 'bulan', 'tahun', 'totalAkumulasi'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        $produks = Produk::with('satuanDasar')->orderBy('nama_produk')->get();

        $prefix = 'PO-' . date('Ym') . '-';
        $latest = Pembelian::where('no_faktur', 'like', $prefix . '%')
            ->orderBy('no_faktur', 'desc')
            ->first();
        if ($latest) {
            $lastNum = (int) substr($latest->no_faktur, -3);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }
        $noFakturOtomatis = $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        return view('admin.pembelian.create', compact('suppliers', 'produks', 'noFakturOtomatis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_faktur' => 'required|string|max:50|unique:pembelian,no_faktur',
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal' => 'required|date',
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

            $pembelian = Pembelian::create([
                'no_faktur' => $request->no_faktur,
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id() ?? 1,
                'tanggal' => $request->tanggal,
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'total' => $total,
                'status' => 'proses',
                'status_penerimaan' => 'belum_diterima',
                'status_pembayaran' => 'belum_dibayar',
            ]);

            foreach ($request->produk_id as $index => $prodId) {
                $qty = $request->qty[$index];
                $harga = $request->harga_satuan[$index];
                $subtotal = $qty * $harga;

                DetailPembelian::create([
                    'pembelian_id' => $pembelian->id,
                    'produk_id' => $prodId,
                    'qty' => $qty,
                    'harga_satuan' => $harga,
                    'subtotal' => $subtotal,
                ]);
                $produk = Produk::find($prodId);
                if ($produk) {
                    $produk->update([
                        'harga_beli_terakhir' => $harga
                    ]);
                    $produk->increment('stok_onorder_supplier', $qty);
                }
            }

           

            DB::commit();
            return redirect()->route('admin.pembelian.index')
                ->with('success', 'Faktur Pembelian berhasil dibuat dengan status Belum Diterima!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        $pembelian = Pembelian::with(['supplier', 'user', 'detailPembelian.produk.satuanDasar'])->findOrFail($id);
        return view('admin.pembelian.show', compact('pembelian'));
    }

    public function edit(string $id)
    {
    }
    public function update(Request $request, string $id)
    {
    }
    public function destroy(string $id)
    {
        $pembelian = Pembelian::with(['detailPembelian.produk', 'penerimaanPembelian', 'pembayaranHutang'])->findOrFail($id);

        if ($pembelian->status === 'batal') {
            return back()->with('error', 'Faktur pembelian ini sudah dibatalkan sebelumnya!');
        }

        if ($pembelian->status_penerimaan !== 'belum_diterima' || $pembelian->penerimaanPembelian()->exists()) {
            return back()->with('error', 'Faktur tidak dapat dibatalkan karena sudah ada barang yang diterima dari supplier!');
        }

        if ($pembelian->pembayaranHutang()->exists()) {
            return back()->with('error', 'Faktur tidak dapat dibatalkan karena sudah ada riwayat pembayaran hutang!');
        }

        DB::beginTransaction();
        try {
            foreach ($pembelian->detailPembelian as $dp) {
                if ($dp->produk) {
                    $sisaOnOrder = max(0, (float)$dp->produk->stok_onorder_supplier - (float)$dp->qty);
                    $dp->produk->update(['stok_onorder_supplier' => $sisaOnOrder]);
                }
            }

            $pembelian->update([
                'status' => 'batal',
            ]);

            DB::commit();
            return redirect()->route('admin.pembelian.show', $pembelian->id)
                ->with('success', 'Faktur pembelian #' . $pembelian->no_faktur . ' berhasil dibatalkan dan kuota pesanan supplier telah dikembalikan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan faktur: ' . $e->getMessage());
        }
    }
}
