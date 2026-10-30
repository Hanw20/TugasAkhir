<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\KriteriaAhp;
use App\Models\SesiPerhitunganAhp;
use App\Models\DetailPembelian;
use App\Models\RiwayatPenilaianSupplier;
use App\Models\SawNormalisasi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RekomendasiSupplierController extends Controller
{
    /**
     * Menampilkan rekomendasi supplier, menghitung SAW, dan menyimpan ke tabel saw_normalisasi.
     */
    public function index(Request $request)
    {
        // 1. Ambil daftar produk untuk dropdown
        $daftarProduk = Produk::with('satuanDasar')->orderBy('nama_produk', 'asc')->get();
        $selectedProdukId = $request->input('produk_id');
        $selectedProduk = null;

        // 2. Ambil semua sesi AHP untuk pilihan dropdown
        $daftarSesiAhp = SesiPerhitunganAhp::orderBy('tanggal_hitung', 'desc')->get();
        $selectedSesiId = $request->input('sesi_id');

        if ($selectedSesiId) {
            $sesiDipilih = SesiPerhitunganAhp::with('perbandingan')->find($selectedSesiId);
        } else {
            $sesiDipilih = SesiPerhitunganAhp::with('perbandingan')->latest('tanggal_hitung')->first();
        }

        $kriteriaList = KriteriaAhp::orderBy('id', 'asc')->get();

        // Hitung bobot prioritas AHP dari sesi yang dipilih user
        $bobotAHP = $this->hitungBobotDariSesi($sesiDipilih, $kriteriaList);

        $alternatif = [];
        $matriksKeputusan = [];
        $matriksNormalisasi = [];
        $ranking = [];

        if ($selectedProdukId) {
            $selectedProduk = Produk::with(['kategori', 'merek', 'satuanDasar'])->findOrFail($selectedProdukId);

            // 3. Ambil supplier yang terhubung dengan produk ini di produk_supplier
            $supplierList = Supplier::whereHas('produk', function ($q) use ($selectedProdukId) {
                $q->where('produk.id', $selectedProdukId);
            })->get();

            if ($supplierList->count() > 0) {
                $pivotMap = DB::table('produk_supplier')
                    ->where('produk_id', $selectedProdukId)
                    ->pluck('id', 'supplier_id')
                    ->toArray();

                // 4. Bentuk Matriks Keputusan Awal (X)
                foreach ($supplierList as $sup) {
                    $k1_harga = $this->getHargaTerakhir($sup->id, $selectedProduk);
                    $k2_kualitas = $this->getNilaiKualitas($sup->id);
                    $k3_pelayanan = $this->getNilaiPelayanan($sup->id);
                    $k4_pengiriman = $this->getWaktuPengiriman($sup->id);
                    $k5_tempo = $this->getTempoPembayaran($sup->id);

                    $alternatif[$sup->id] = $sup;
                    $matriksKeputusan[$sup->id] = [
                        'K1' => (float) $k1_harga,
                        'K2' => (float) $k2_kualitas,
                        'K3' => (float) $k3_pelayanan,
                        'K4' => (float) $k4_pengiriman,
                        'K5' => (float) $k5_tempo,
                    ];
                }

                // 5. Cari Nilai Max dan Min per Kriteria
                $minMax = [];
                foreach ($kriteriaList as $k) {
                    $kode = $k->kode_kriteria;
                    $columnValues = array_column($matriksKeputusan, $kode);
                    $minMax[$kode] = [
                        'max' => count($columnValues) > 0 ? max($columnValues) : 1,
                        'min' => count($columnValues) > 0 ? min($columnValues) : 1,
                    ];
                }

                // 6. Normalisasi Matriks SAW (R), Simpan ke Tabel saw_normalisasi, & Hitung Skor Preferensi (V)
                foreach ($matriksKeputusan as $supId => $nilaiKriteria) {
                    $totalSkor = 0.0;
                    $matriksNormalisasi[$supId] = [];
                    $produkSupplierId = $pivotMap[$supId] ?? null;

                    foreach ($kriteriaList as $k) {
                        $kode = $k->kode_kriteria;
                        $tipe = strtolower($k->jenis_kriteria);
                        $x = $nilaiKriteria[$kode];
                        $r = 0.0;

                        if ($tipe === 'benefit') {
                            $maxVal = $minMax[$kode]['max'];
                            $r = $maxVal > 0 ? ($x / $maxVal) : 0;
                        } else {
                            $minVal = $minMax[$kode]['min'];
                            $r = $x > 0 ? ($minVal / $x) : 0;
                        }

                        $rBulat = round($r, 4);
                        $matriksNormalisasi[$supId][$kode] = $rBulat;

                        // SIMPAN / UPDATE KE TABEL saw_normalisasi
                        if ($produkSupplierId) {
                            SawNormalisasi::updateOrCreate(
                                [
                                    'produk_supplier_id' => $produkSupplierId,
                                    'kriteria_ahp_id' => $k->id,
                                ],
                                [
                                    'nilai_asli' => $x,
                                    'nilai_normalisasi' => $rBulat,
                                ]
                            );
                        }

                        // Preferensi V = SUM( w_j * R_ij )
                        $w = $bobotAHP[$kode] ?? 0.2;
                        $totalSkor += ($r * $w);
                    }

                    $ranking[] = [
                        'supplier_id' => $supId,
                        'supplier' => $alternatif[$supId],
                        'nilai_keputusan' => $nilaiKriteria,
                        'nilai_normalisasi' => $matriksNormalisasi[$supId],
                        'skor_preferensi' => round($totalSkor, 4),
                    ];
                }

                // 7. Urutkan Ranking dari Skor Tertinggi ke Terendah
                usort($ranking, function ($a, $b) {
                    return $b['skor_preferensi'] <=> $a['skor_preferensi'];
                });
            }
        }

        return view('admin.rekomendasi_supplier.index', compact(
            'daftarProduk',
            'selectedProduk',
            'daftarSesiAhp',
            'sesiDipilih',
            'kriteriaList',
            'bobotAHP',
            'matriksKeputusan',
            'matriksNormalisasi',
            'ranking'
        ));
    }

    /**
     * Hitung bobot prioritas eigenvector dari sesi perbandingan AHP terakhir.
     */
    private function hitungBobotDariSesi($sesi, $kriteriaList)
    {
        $n = count($kriteriaList);
        if (!$sesi || $n == 0) {
            $defaultWeight = $n > 0 ? (1 / $n) : 0.2;
            $bobot = [];
            foreach ($kriteriaList as $k) {
                $bobot[$k->kode_kriteria] = $defaultWeight;
            }
            return $bobot;
        }

        $matrixValues = [];
        foreach ($sesi->perbandingan as $p) {
            $matrixValues[$p->kriteria_a_id][$p->kriteria_b_id] = (float) $p->nilai_perbandingan;
        }

        $kriteriaIds = $kriteriaList->pluck('id')->toArray();
        $matrix = [];
        for ($i = 0; $i < $n; $i++) {
            $idA = $kriteriaIds[$i];
            for ($j = 0; $j < $n; $j++) {
                $idB = $kriteriaIds[$j];
                $matrix[$i][$j] = $matrixValues[$idA][$idB] ?? ($i == $j ? 1.0 : 1.0);
            }
        }

        // Total Kolom
        $colSums = array_fill(0, $n, 0.0);
        for ($j = 0; $j < $n; $j++) {
            for ($i = 0; $i < $n; $i++) {
                $colSums[$j] += $matrix[$i][$j];
            }
        }

        // Eigen Vector (Bobot)
        $bobot = [];
        for ($i = 0; $i < $n; $i++) {
            $rowSum = 0.0;
            for ($j = 0; $j < $n; $j++) {
                $rowSum += ($colSums[$j] > 0) ? ($matrix[$i][$j] / $colSums[$j]) : 0;
            }
            $bobot[$kriteriaList[$i]->kode_kriteria] = round($rowSum / $n, 4);
        }

        return $bobot;
    }

    // --- Helper Pengambilan Nilai Kriteria Riil ---

    private function getHargaTerakhir($supplierId, $produk)
    {
        $lastPurchase = DetailPembelian::whereHas('pembelian', function ($q) use ($supplierId) {
            $q->where('supplier_id', $supplierId);
        })->where('produk_id', $produk->id)->latest()->first();

        if ($lastPurchase) {
            return (float) $lastPurchase->harga_satuan;
        }

        return (float) $produk->harga_beli;
    }

    private function getNilaiKualitas($supplierId)
    {
        $avg = RiwayatPenilaianSupplier::where('supplier_id', $supplierId)
            ->whereHas('kriteria', function ($q) {
                $q->where('kode_kriteria', 'K2')->orWhere('nama_kriteria', 'like', '%Kualitas%');
            })->avg('nilai');

        return $avg ? round($avg, 2) : 3.5;
    }

    private function getNilaiPelayanan($supplierId)
    {
        $avg = RiwayatPenilaianSupplier::where('supplier_id', $supplierId)
            ->whereHas('kriteria', function ($q) {
                $q->where('kode_kriteria', 'K3')->orWhere('nama_kriteria', 'like', '%Pelayanan%');
            })->avg('nilai');

        return $avg ? round($avg, 2) : 3.5;
    }

    private function getWaktuPengiriman($supplierId)
    {
        $pembelian = \App\Models\Pembelian::where('supplier_id', $supplierId)
            ->whereHas('penerimaanPembelian')
            ->with('penerimaanPembelian')
            ->get();

        if ($pembelian->count() > 0) {
            $totalHari = 0;
            $count = 0;
            foreach ($pembelian as $p) {
                foreach ($p->penerimaanPembelian as $terima) {
                    $tglPo = Carbon::parse($p->tanggal);
                    $tglTerima = Carbon::parse($terima->tanggal_terima);
                    $diff = $tglPo->diffInDays($tglTerima);
                    $totalHari += $diff > 0 ? $diff : 1;
                    $count++;
                }
            }
            if ($count > 0) {
                return round($totalHari / $count, 1);
            }
        }

        return 3.0;
    }

    private function getTempoPembayaran($supplierId)
    {
        $pembelian = \App\Models\Pembelian::where('supplier_id', $supplierId)
            ->whereNotNull('tanggal_jatuh_tempo')
            ->get();

        if ($pembelian->count() > 0) {
            $totalHari = 0;
            foreach ($pembelian as $p) {
                $tglPo = Carbon::parse($p->tanggal);
                $tglTempo = Carbon::parse($p->tanggal_jatuh_tempo);
                $diff = $tglPo->diffInDays($tglTempo);
                $totalHari += $diff;
            }
            return round($totalHari / $pembelian->count(), 1);
        }

        return 14.0;
    }
}