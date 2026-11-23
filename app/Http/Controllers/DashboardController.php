<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\Pembelian;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (auth()->user()->role == 'admin') {
            $bulanIni = Carbon::now()->month;
            $tahunIni = Carbon::now()->year;

            $totalPenjualanBulanIni = Penjualan::where('status', '!=', 'batal')
                ->whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->sum('total');
            $jumlahPenjualanBulanIni = Penjualan::where('status', '!=', 'batal')
                ->whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->count();

            $totalPembelianBulanIni = Pembelian::where('status', '!=', 'batal')
                ->whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->sum('total');
            $jumlahPembelianBulanIni = Pembelian::where('status', '!=', 'batal')
                ->whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->count();

            $produkStokMenipis = Produk::with('satuanDasar')
                ->whereColumn('stok', '<=', 'stok_minimum')
                ->orderBy('stok', 'asc')
                ->get();

            $daftarHutang = Pembelian::with(['supplier', 'pembayaranHutang'])
                ->where('status', '!=', 'batal')
                ->where('status_pembayaran', '!=', 'lunas')
                ->orderBy('tanggal_jatuh_tempo', 'asc')
                ->get();

            $totalHutangBelumLunas = $daftarHutang->sum(function ($p) {
                return $p->sisa_hutang;
            });
            $hutangJatuhTempo = $daftarHutang->take(5);

            $daftarPiutang = Penjualan::with(['customer', 'pembayaranPiutang'])
                ->where('status', '!=', 'batal')
                ->where('status_pembayaran', '!=', 'lunas')
                ->orderBy('tanggal', 'desc')
                ->get();

            $totalPiutangBelumLunas = $daftarPiutang->sum(function ($p) {
                return $p->sisa_piutang;
            });
            $piutangBelumLunas = $daftarPiutang->take(5);

            return view('admin.dashboard', compact(
                'totalPenjualanBulanIni',
                'jumlahPenjualanBulanIni',
                'totalPembelianBulanIni',
                'jumlahPembelianBulanIni',
                'produkStokMenipis',
                'totalHutangBelumLunas',
                'hutangJatuhTempo',
                'totalPiutangBelumLunas',
                'piutangBelumLunas'
            ));
        } 
        
        return view('kasir.dashboard');
    }
}
