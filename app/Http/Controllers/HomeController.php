<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\Pembelian;
use Carbon\Carbon;

class HomeController extends Controller
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

            // 1. Total Penjualan Bulan Ini
            $totalPenjualanBulanIni = Penjualan::whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->sum('total');
            $jumlahPenjualanBulanIni = Penjualan::whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->count();

            // 2. Total Pembelian Bulan Ini
            $totalPembelianBulanIni = Pembelian::whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->sum('total');
            $jumlahPembelianBulanIni = Pembelian::whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->count();

            // 3. Produk Stok Menipis (stok <= stok_minimum)
            $produkStokMenipis = Produk::with('satuanDasar')
                ->whereColumn('stok', '<=', 'stok_minimum')
                ->orderBy('stok', 'asc')
                ->get();

            // 4. Hutang Pembelian Mendekati Jatuh Tempo (belum lunas)
            $hutangJatuhTempo = Pembelian::with('supplier')
                ->where('status_pembayaran', '!=', 'lunas')
                ->orderBy('tanggal_jatuh_tempo', 'asc')
                ->take(5)
                ->get();

            return view('admin.dashboard', compact(
                'totalPenjualanBulanIni',
                'jumlahPenjualanBulanIni',
                'totalPembelianBulanIni',
                'jumlahPembelianBulanIni',
                'produkStokMenipis',
                'hutangJatuhTempo'
            ));
        } 
        
        return view('kasir.dashboard');
    }
}