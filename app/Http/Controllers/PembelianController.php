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
    public function index()
    {
        // Ambil riwayat pembelian beserta supplier dan user pembuatnya
        $pembelian = Pembelian::with(['supplier', 'user'])
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.pembelian.index', compact('pembelian'));
    }

    public function create()
    {
        // Ambil data supplier dan produk untuk form pembelian
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        $produks = Produk::with('satuanDasar')->orderBy('nama_produk')->get();
        
        // Buat nomor faktur otomatis, misal: PB-20261013-0001
        $today = date('Ymd');
        $countToday = Pembelian::whereDate('created_at', today())->count() + 1;
        $noFakturOtomatis = 'PB-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

        

        return view('admin.pembelian.create', compact('suppliers', 'produks', 'noFakturOtomatis'));
    }

        public function store(Request $request)
    {
        $request->validate([
            'no_faktur'    => 'required|string|unique:pembelian,no_faktur',
            'supplier_id'  => 'required|integer',
            'tanggal'      => 'required|date',
            'produk_id'    => 'required|array|min:1',
            'qty'          => 'required|array|min:1',
            'harga_satuan' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Hitung total belanja
            $total = 0;
            foreach ($request->produk_id as $index => $prodId) {
                $subtotal = $request->qty[$index] * $request->harga_satuan[$index];
                $total += $subtotal;
            }

            // 1. Simpan Header Pembelian (Status default: belum_diterima)
            $pembelian = Pembelian::create([
                'no_faktur'          => $request->no_faktur,
                'supplier_id'        => $request->supplier_id,
                'user_id'            => Auth::id() ?? 1,
                'tanggal'            => $request->tanggal,
                'total'              => $total,
                'status'             => 'proses',
                'status_penerimaan'  => 'belum_diterima',
                'status_pembayaran'  => 'belum_dibayar',
            ]);

            // 2. Simpan Detail Produk (Stok TIDAK bertambah di sini)
            foreach ($request->produk_id as $index => $prodId) {
                $qty = $request->qty[$index];
                $harga = $request->harga_satuan[$index];
                $subtotal = $qty * $harga;

                DetailPembelian::create([
                    'pembelian_id' => $pembelian->id,
                    'produk_id'    => $prodId,
                    'qty'          => $qty,
                    'harga_satuan' => $harga,
                    'subtotal'     => $subtotal,
                ]);
                $produk = Produk::find($prodId);
                if ($produk) {
                    $produk->update([
                        'harga_beli_terakhir' => $harga
                    ]);
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

    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}