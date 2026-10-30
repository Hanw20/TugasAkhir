<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Konversi;
use App\Models\MasterKonversi;
use App\Models\Produk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KonversiProdukController extends Controller
{
    /**
     * Tampilkan riwayat pemecahan/konversi kemasan produk di toko.
     */
    public function index()
    {
        $data = Konversi::with(['user', 'produkAsal.satuanDasar', 'produkTujuan.satuanDasar', 'masterKonversi'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('admin.konversi_produk.index', compact('data'));
    }

    /**
     * Tampilkan form transaksi konversi produk baru.
     */
    public function create()
    {
        $produk = Produk::with('satuanDasar')->orderBy('nama_produk', 'asc')->get();
        $rumus = MasterKonversi::with(['satuanAsal', 'satuanTujuan'])->get();

        return view('admin.konversi_produk.create', compact('produk', 'rumus'));
    }

    /**
     * Simpan transaksi konversi fisik & sesuaikan stok kedua produk.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'master_konversi_id' => 'required|exists:master_konversi,id',
            'produk_asal_id' => 'required|exists:produk,id',
            'qty_asal_dipakai' => 'required|numeric|min:0.01',
            'produk_tujuan_id' => 'required|exists:produk,id|different:produk_asal_id',
            'qty_hasil_aktual' => 'required|numeric|min:0.01',
            'catatan' => 'nullable|string|max:255',
        ], [
            'produk_tujuan_id.different' => 'Produk tujuan hasil konversi harus berbeda dengan produk asal!'
        ]);

        $prodAsal = Produk::findOrFail($request->produk_asal_id);
        $prodTujuan = Produk::findOrFail($request->produk_tujuan_id);

        // Cek kecukupan stok produk asal
        if ($prodAsal->stok < $request->qty_asal_dipakai) {
            return back()->with('error', "Stok {$prodAsal->nama_produk} tidak mencukupi! Stok saat ini: {$prodAsal->stok}, dibutuhkan: {$request->qty_asal_dipakai}");
        }

        DB::beginTransaction();
        try {
            // 1. Simpan riwayat transaksi konversi ke tabel 'konversi'
            Konversi::create([
                'master_konversi_id' => $request->master_konversi_id,
                'produk_asal_id' => $prodAsal->id,
                'qty_asal_dipakai' => $request->qty_asal_dipakai,
                'produk_tujuan_id' => $prodTujuan->id,
                'qty_hasil_aktual' => $request->qty_hasil_aktual,
                'tanggal' => $request->tanggal,
                'user_id' => Auth::id() ?? 1,
                'catatan' => $request->catatan,
            ]);

            // 2. Potong stok produk asal (misal sak semen berkurang)
            $prodAsal->decrement('stok', $request->qty_asal_dipakai);

            // 3. Tambah stok produk tujuan (misal kiloan semen bertambah)
            $prodTujuan->increment('stok', $request->qty_hasil_aktual);

            DB::commit();
            return redirect()->route('admin.konversi-produk.index')->with('success', 'Konversi produk berhasil! Stok produk asal telah dipotong dan stok produk tujuan telah ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses konversi: ' . $e->getMessage());
        }
    }
}