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
    
    public function index()
    {
        $data = Konversi::with(['user', 'produkAsal.satuanDasar', 'produkTujuan.satuanDasar', 'masterKonversi'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('admin.konversi_produk.index', compact('data'));
    }

    
    public function create()
    {
        $produk = Produk::with('satuanDasar')->orderBy('nama_produk', 'asc')->get();
        $rumus = MasterKonversi::with(['satuanAsal', 'satuanTujuan'])->get();

        return view('admin.konversi_produk.create', compact('produk', 'rumus'));
    }

    
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

        if ($prodAsal->stok < $request->qty_asal_dipakai) {
            return back()->with('error', "Stok {$prodAsal->nama_produk} tidak mencukupi! Stok saat ini: {$prodAsal->stok}, dibutuhkan: {$request->qty_asal_dipakai}");
        }

        DB::beginTransaction();
        try {
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

            $prodAsal->decrement('stok', $request->qty_asal_dipakai);

            $hargaAsal = (float) $prodAsal->harga_beli_terakhir > 0 
                ? (float) $prodAsal->harga_beli_terakhir 
                : (float) $prodAsal->harga_beli;

            $qtyAsal = (float) $request->qty_asal_dipakai;
            $qtyHasil = (float) $request->qty_hasil_aktual;

            if ($hargaAsal > 0 && $qtyHasil > 0) {
                $modalPerUnitTujuan = round(($qtyAsal * $hargaAsal) / $qtyHasil, 2);
                $stokLamaTujuan = (float) $prodTujuan->stok;
                $hargaLamaTujuan = (float) $prodTujuan->harga_beli;

                if ($stokLamaTujuan > 0 && $hargaLamaTujuan > 0) {
                    $totalStokTujuan = $stokLamaTujuan + $qtyHasil;
                    $hargaRataBaru = (($stokLamaTujuan * $hargaLamaTujuan) + ($qtyHasil * $modalPerUnitTujuan)) / $totalStokTujuan;
                } else {
                    $hargaRataBaru = $modalPerUnitTujuan;
                }

                $prodTujuan->update([
                    'harga_beli' => round($hargaRataBaru, 2),
                    'harga_beli_terakhir' => $modalPerUnitTujuan,
                ]);
            }

            $prodTujuan->increment('stok', $request->qty_hasil_aktual);

            DB::commit();
            return redirect()->route('admin.konversi-produk.index')->with('success', 'Konversi produk berhasil! Stok produk asal telah dipotong dan stok produk tujuan telah ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses konversi: ' . $e->getMessage());
        }
    }
}
