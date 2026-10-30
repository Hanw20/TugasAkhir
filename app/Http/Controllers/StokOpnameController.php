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
            // Ubah format tanggal dari form browser ke format standar MySQL
            $tanggal = \Carbon\Carbon::parse($request->tanggal)->format('Y-m-d H:i:s');

            $opname = StokOpname::create([
                'tanggal' => $tanggal,
                'user_id' => Auth::id() ?? 1,
                'catatan' => $request->catatan,
            ]);

            foreach ($request->items as $item) {
                $prod = Produk::findOrFail($item['produk_id']);
                $stokSistem = (float) $prod->stok;
                $stokFisik = (float) $item['stok_fisik'];
                $selisih = $stokFisik - $stokSistem;

                // Catat detail opname
                DetailStokOpname::create([
                    'stok_opname_id' => $opname->id,
                    'produk_id' => $prod->id,
                    'stok_sistem' => $stokSistem,
                    'stok_fisik' => $stokFisik,
                    'selisih' => $selisih,
                ]);

                // Update stok produk di master produk agar sinkron dengan gudang
                $prod->update(['stok' => $stokFisik]);
            }

            DB::commit();
            return redirect()->route('admin.stok-opname.index')->with('success', 'Stok Opname berhasil dicatat dan stok gudang telah disinkronkan!');
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
}