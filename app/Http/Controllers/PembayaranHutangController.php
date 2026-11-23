<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\PembayaranHutang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PembayaranHutangController extends Controller
{
    public function index()
    {
        $fakturHutang = Pembelian::with(['supplier', 'pembayaranHutang'])
            ->where('status', '!=', 'batal')
            ->whereIn('status_pembayaran', ['belum_dibayar', 'sebagian'])
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->get();

        $riwayatBayar = PembayaranHutang::with(['pembelian.supplier', 'user'])
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.pembelian.hutang_index', compact('fakturHutang', 'riwayatBayar'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pembelian_id'  => 'required|integer',
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar'  => 'required|numeric|min:1',
            'catatan'       => 'nullable|string|max:255',
        ]);

        $pembelian = Pembelian::with('pembayaranHutang')->findOrFail($request->pembelian_id);

        if ($request->jumlah_bayar > $pembelian->sisa_hutang) {
            return back()->with('error', 'Jumlah bayar tidak boleh melebihi sisa hutang (Rp ' . number_format($pembelian->sisa_hutang, 0, ',', '.') . ')!');
        }

        DB::beginTransaction();
        try {
            PembayaranHutang::create([
                'pembelian_id'  => $pembelian->id,
                'tanggal_bayar' => $request->tanggal_bayar,
                'jumlah_bayar'  => $request->jumlah_bayar,
                'user_id'       => Auth::id() ?? 1,
                'catatan'       => $request->catatan,
            ]);

            $totalTerbayarBaru = $pembelian->total_dibayar + $request->jumlah_bayar;
            if ($totalTerbayarBaru >= $pembelian->total) {
                $pembelian->update(['status_pembayaran' => 'lunas']);
            } else {
                $pembelian->update(['status_pembayaran' => 'sebagian']);
            }

            DB::commit();
            return back()->with('success', 'Pembayaran hutang berhasil dicatat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
