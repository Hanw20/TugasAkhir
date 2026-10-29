<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\PembayaranPiutang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PembayaranPiutangController extends Controller
{
    public function index()
    {
        // Ambil semua nota penjualan yang status pembayarannya belum lunas / tempo
        $notaPiutang = Penjualan::with(['customer', 'pembayaranPiutang'])
            ->whereIn('status_pembayaran', ['belum_dibayar', 'sebagian'])
            ->orderBy('id', 'desc')
            ->get();

        // Riwayat cicilan pembayaran piutang dari customer
        $riwayatBayar = PembayaranPiutang::with(['penjualan.customer', 'user'])
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.penjualan.piutang_index', compact('notaPiutang', 'riwayatBayar'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'penjualan_id'  => 'required|integer',
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar'  => 'required|numeric|min:1',
            'catatan'       => 'nullable|string|max:255',
        ]);

        $penjualan = Penjualan::with('pembayaranPiutang')->findOrFail($request->penjualan_id);

        if ($request->jumlah_bayar > $penjualan->sisa_piutang) {
            return back()->with('error', 'Jumlah bayar tidak boleh melebihi sisa piutang (Rp ' . number_format($penjualan->sisa_piutang, 0, ',', '.') . ')!');
        }

        DB::beginTransaction();
        try {
            // 1. Simpan Transaksi Pembayaran Piutang
            PembayaranPiutang::create([
                'penjualan_id'  => $penjualan->id,
                'tanggal_bayar' => $request->tanggal_bayar,
                'jumlah_bayar'  => $request->jumlah_bayar,
                'user_id'       => Auth::id() ?? 1,
                'catatan'       => $request->catatan,
            ]);

            // 2. Cek apakah piutang customer sudah lunas atau masih ada sisa
            $totalTerbayarBaru = $penjualan->total_dibayar + $request->jumlah_bayar;
            if ($totalTerbayarBaru >= $penjualan->total) {
                $penjualan->update(['status_pembayaran' => 'lunas']);
            } else {
                $penjualan->update(['status_pembayaran' => 'sebagian']);
            }

            DB::commit();
            return back()->with('success', 'Pembayaran cicilan piutang customer berhasil dicatat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}