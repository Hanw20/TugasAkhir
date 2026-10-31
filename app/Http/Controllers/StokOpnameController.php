<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokOpname;
use App\Models\DetailStokOpname;
use App\Models\Produk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StokOpnameController extends Controller
{
    /**
     * Tampilkan riwayat transaksi stok opname.
     */
    public function index()
    {
        $data = StokOpname::with(['user', 'detailStokOpname.produk.satuanDasar'])->orderBy('tanggal', 'desc')->get();
        return view('admin.stok_opname.index', compact('data'));
    }

    /**
     * Form input penyesuaian stok opname baru.
     */
    public function create()
    {
        $produk = Produk::with('satuanDasar')->orderBy('nama_produk', 'asc')->get();
        return view('admin.stok_opname.create', compact('produk'));
    }

    /**
     * Simpan hasil opname dan update stok produk di gudang.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required',
            'catatan' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produk,id',
            'items.*.stok_fisik' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $tanggal = \Carbon\Carbon::parse($request->tanggal)->format('Y-m-d H:i:s');

            // Buat sesi opname dengan status default: menunggu_persetujuan
            $opname = StokOpname::create([
                'tanggal' => $tanggal,
                'user_id' => Auth::id() ?? 1,
                'catatan' => $request->catatan,
                'status' => 'menunggu_persetujuan',
            ]);

            foreach ($request->items as $item) {
                $prod = Produk::findOrFail($item['produk_id']);
                $stokSistem = (float) $prod->stok;
                $stokFisik = (float) $item['stok_fisik'];
                $selisih = $stokFisik - $stokSistem;

                // Catat detail opname (stok master produk BELUM diubah di sini)
                DetailStokOpname::create([
                    'stok_opname_id' => $opname->id,
                    'produk_id' => $prod->id,
                    'stok_sistem' => $stokSistem,
                    'stok_fisik' => $stokFisik,
                    'selisih' => $selisih,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.stok-opname.show', $opname->id)
                ->with('success', 'Hasil hitung fisik stok opname berhasil dicatat! Menunggu persetujuan pemilik untuk disesuaikan ke stok sistem.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses stok opname: ' . $e->getMessage());
        }
    }

    /**
     * Lihat detail bukti stok opname.
     */
    public function show(string $id)
    {
        $opname = StokOpname::with(['user', 'detailStokOpname.produk.satuanDasar'])->findOrFail($id);
        return view('admin.stok_opname.show', compact('opname'));
    }

    /**
     * Eksekusi persetujuan penyesuaian (Reconcile) stok sistem ke fisik nyata.
     */
    public function reconcile(string $id)
    {
        $opname = StokOpname::with('detailStokOpname')->findOrFail($id);

        if ($opname->status === 'disesuaikan') {
            return back()->with('info', 'Sesi stok opname ini sudah pernah disetujui dan disesuaikan sebelumnya.');
        }

        DB::beginTransaction();
        try {
            // Update stok fisik ke master produk
            foreach ($opname->detailStokOpname as $detail) {
                $prod = Produk::findOrFail($detail->produk_id);
                $prod->update([
                    'stok' => $detail->stok_fisik
                ]);
            }

            // Ubah status sesi opname menjadi 'disesuaikan'
            $opname->update([
                'status' => 'disesuaikan'
            ]);

            DB::commit();
            return back()->with('success', 'Stok master produk berhasil disesuaikan dengan kondisi fisik gudang!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyesuaikan stok: ' . $e->getMessage());
        }
    }
}