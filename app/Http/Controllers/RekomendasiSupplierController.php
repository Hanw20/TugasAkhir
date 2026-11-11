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
use Bardiz12\AHPDss\AHP;

class RekomendasiSupplierController extends Controller
{
    /**
     * Menampilkan rekomendasi supplier, menghitung AHP-SAW Hybrid, SAW Murni, dan AHP Murni (Library).
     */
    public function index(Request $request)
    {
        // 1. Ambil daftar produk untuk dropdown
        $daftarProduk = Produk::with('satuanDasar')->orderBy('nama_produk', 'asc')->get();
        $selectedProdukId = $request->input('produk_id');
        $selectedProduk = null;

        // 2. Ambil sesi AHP untuk bobot kriteria
        $daftarSesiAhp = SesiPerhitunganAhp::orderBy('tanggal_hitung', 'desc')->get();
        $selectedSesiId = $request->input('sesi_id');

        if ($selectedSesiId) {
            $sesiDipilih = SesiPerhitunganAhp::with('perbandingan')->find($selectedSesiId);
        } else {
            $sesiDipilih = SesiPerhitunganAhp::with('perbandingan')->latest('tanggal_hitung')->first();
        }

        $kriteriaList = KriteriaAhp::orderBy('id', 'asc')->get();

        // Hitung bobot AHP (Eigen Vector)
        $bobotAHP = $this->hitungBobotDariSesi($sesiDipilih, $kriteriaList);

        $alternatif = [];
        $matriksKeputusan = [];
        $matriksNormalisasi = [];
        $minMax = [];
        $ranking = [];
        $rankingSawMurni = [];
        $rankingAhpMurni = [];

        if ($selectedProdukId) {
            $selectedProduk = Produk::with(['kategori', 'merek', 'satuanDasar'])->findOrFail($selectedProdukId);

            // 3. Ambil supplier yang terhubung dengan produk ini
            $supplierList = Supplier::whereHas('produk', function ($q) use ($selectedProdukId) {
                $q->where('produk.id', $selectedProdukId);
            })->get();

            if ($supplierList->count() > 0) {
                $pivotMap = DB::table('produk_supplier')
                    ->where('produk_id', $selectedProdukId)
                    ->pluck('id', 'supplier_id')
                    ->toArray();

                // 4. Bentuk Matriks Keputusan Awal (X) dengan 6 Kriteria
                foreach ($supplierList as $sup) {
                    $k1_harga       = $this->getHargaTerakhir($sup->id, $selectedProduk);
                    $k2_kualitas    = $this->getNilaiKualitas($sup->id);
                    $k3_respon      = $this->getNilaiRespon($sup->id);
                    $k4_garansi     = $this->getNilaiGaransi($sup->id);
                    $k5_pengiriman  = $this->getWaktuPengiriman($sup->id);
                    $k6_tempo       = $this->getTempoPembayaran($sup->id);

                    $alternatif[$sup->id] = $sup;
                    $matriksKeputusan[$sup->id] = [
                        'K1' => (float) $k1_harga,
                        'K2' => (float) $k2_kualitas,
                        'K3' => (float) $k3_respon,
                        'K4' => (float) $k4_garansi,
                        'K5' => (float) $k5_pengiriman,
                        'K6' => (float) $k6_tempo,
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

                // Normalisasi SAW
                foreach ($matriksKeputusan as $supId => $nilaiKriteria) {
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

                        if ($produkSupplierId && $k->id) {
                            SawNormalisasi::updateOrCreate(
                                [
                                    'produk_supplier_id' => $produkSupplierId,
                                    'kriteria_ahp_id'    => $k->id,
                                ],
                                [
                                    'nilai_asli'         => $x,
                                    'nilai_normalisasi'  => $rBulat,
                                ]
                            );
                        }
                    }
                }

                // ========================================================
                // 6. PERHITUNGAN 3 METODE PEMBANDING SEKALIGUS
                // ========================================================
                $bobotRata = 1.0 / count($kriteriaList);

                // A. Metode 1: Hybrid AHP-SAW (Utama) & Metode 2: SAW Murni (Equal Weights)
                foreach ($matriksKeputusan as $supId => $nilaiKriteria) {
                    $skorHybrid   = 0.0;
                    $skorSawMurni = 0.0;

                    foreach ($kriteriaList as $k) {
                        $kode = $k->kode_kriteria;
                        $r = $matriksNormalisasi[$supId][$kode];
                        $wAhp = $bobotAHP[$kode] ?? $bobotRata;

                        $skorHybrid   += ($r * $wAhp);
                        $skorSawMurni += ($r * $bobotRata);
                    }

                    $dataItem = [
                        'supplier_id'       => $supId,
                        'supplier'          => $alternatif[$supId],
                        'nilai_keputusan'   => $nilaiKriteria,
                        'nilai_normalisasi' => $matriksNormalisasi[$supId],
                    ];

                    $ranking[]           = array_merge($dataItem, ['skor_preferensi' => round($skorHybrid, 4)]);
                    $rankingSawMurni[]   = array_merge($dataItem, ['skor_preferensi' => round($skorSawMurni, 4)]);
                }

                usort($ranking, fn($a, $b) => $b['skor_preferensi'] <=> $a['skor_preferensi']);
                usort($rankingSawMurni, fn($a, $b) => $b['skor_preferensi'] <=> $a['skor_preferensi']);

                // B. Metode 3: AHP Murni (Menggunakan Library Bardiz12\AHPDss\AHP)
                try {
                    $ahpEngine = new AHP();
                    foreach ($kriteriaList as $k) {
                        $ahpEngine->addQuantitativeCriteria($k->kode_kriteria);
                    }

                    // Ambil matriks perbandingan kriteria dari sesi AHP
                    $matrixKriteriaValues = [];
                    $kriteriaIds = $kriteriaList->pluck('id')->toArray();
                    $n = count($kriteriaList);
                    $sessionPairs = [];
                    if ($sesiDipilih) {
                        foreach ($sesiDipilih->perbandingan as $p) {
                            $sessionPairs[$p->kriteria_a_id][$p->kriteria_b_id] = (float) $p->nilai_perbandingan;
                        }
                    }

                    for ($i = 0; $i < $n; $i++) {
                        $idA = $kriteriaIds[$i];
                        $matrixKriteriaValues[$i] = [];
                        for ($j = 0; $j < $n; $j++) {
                            $idB = $kriteriaIds[$j];
                            $matrixKriteriaValues[$i][$j] = $sessionPairs[$idA][$idB] ?? ($i == $j ? 1.0 : 1.0);
                        }
                    }
                    $ahpEngine->setRelativeInterestMatrix($matrixKriteriaValues);

                    // Set kandidat supplier
                    $candidatesNames = [];
                    $supOrderMap = [];
                    $idx = 0;
                    foreach ($supplierList as $sup) {
                        $candidatesNames[] = $sup->nama_supplier;
                        $supOrderMap[$sup->nama_supplier] = $sup;
                        $idx++;
                    }
                    $ahpEngine->setCandidates($candidatesNames);

                    // Masukkan nilai riil kuantitatif tiap kriteria
                    foreach ($kriteriaList as $k) {
                        $kode = $k->kode_kriteria;
                        $colVals = [];
                        foreach ($supplierList as $sup) {
                            $rawVal = (float) $matriksKeputusan[$sup->id][$kode];
                            // Untuk kriteria cost (Harga, Pengiriman), semakin kecil semakin baik: gunakan 1/x
                            $colVals[] = ($k->jenis_kriteria === 'cost') ? (1.0 / ($rawVal > 0 ? $rawVal : 1.0)) : $rawVal;
                        }
                        $ahpEngine->setCriteriaPairWise($kode, $colVals);
                    }

                    $ahpEngine->finalize();
                    $ahpResults = $ahpEngine->getResult();

                    foreach ($ahpResults as $res) {
                        $supObj = $supOrderMap[$res['name']] ?? null;
                        if ($supObj) {
                            $rankingAhpMurni[] = [
                                'supplier'          => $supObj,
                                'skor_preferensi'   => round($res['value'], 4),
                            ];
                        }
                    }
                    usort($rankingAhpMurni, fn($a, $b) => $b['skor_preferensi'] <=> $a['skor_preferensi']);
                } catch (\Exception $e) {
                    // Fallback jika library AHP pairwise membutuhkan min kandidat tertentu
                    $rankingAhpMurni = $ranking;
                }
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
            'ranking',
            'rankingSawMurni',
            'rankingAhpMurni',
            'minMax'
        ));
    }

    /**
     * Hitung bobot prioritas eigenvector dari sesi perbandingan AHP
     */
    private function hitungBobotDariSesi($sesi, $kriteriaList)
    {
        $n = count($kriteriaList);
        if (!$sesi || $n == 0) {
            $defaultWeight = $n > 0 ? (1 / $n) : 0.1667;
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

        $colSums = array_fill(0, $n, 0.0);
        for ($j = 0; $j < $n; $j++) {
            for ($i = 0; $i < $n; $i++) {
                $colSums[$j] += $matrix[$i][$j];
            }
        }

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

    // Helper K1: Harga Terakhir
    private function getHargaTerakhir($supplierId, $produk)
    {
        $lastPurchase = DetailPembelian::whereHas('pembelian', function ($q) use ($supplierId) {
            $q->where('supplier_id', $supplierId);
        })->where('produk_id', $produk->id)->latest()->first();

        return $lastPurchase ? (float) $lastPurchase->harga_satuan : (float) $produk->harga_beli;
    }

    // Helper K2: Kualitas Produk
    private function getNilaiKualitas($supplierId)
    {
        $avg = RiwayatPenilaianSupplier::where('supplier_id', $supplierId)
            ->whereHas('kriteria', function ($q) {
                $q->where('kode_kriteria', 'K2');
            })->avg('nilai');

        return $avg ? round($avg, 2) : 3.5;
    }

    // Helper K3: Kecepatan Respon
    private function getNilaiRespon($supplierId)
    {
        $avg = RiwayatPenilaianSupplier::where('supplier_id', $supplierId)
            ->whereHas('kriteria', function ($q) {
                $q->where('kode_kriteria', 'K3');
            })->avg('nilai');

        return $avg ? round($avg, 2) : 3.5;
    }

    // Helper K4: Kebijakan Garansi
    private function getNilaiGaransi($supplierId)
    {
        $avg = RiwayatPenilaianSupplier::where('supplier_id', $supplierId)
            ->whereHas('kriteria', function ($q) {
                $q->where('kode_kriteria', 'K4');
            })->avg('nilai');

        return $avg ? round($avg, 2) : 3.5;
    }

    // Helper K5: Waktu Pengiriman
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

    // Helper K6: Tempo Pembayaran
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

        /**
     * Halaman Rincian & Logika Perhitungan Matematis (Transparansi AHP & SAW)
     */
        /**
     * Halaman Rincian & Logika Perhitungan Matematis (Transparansi AHP & SAW)
     */
    public function show($id, Request $request)
    {
        $selectedProdukId = $id;
        $selectedProduk = Produk::with(['kategori', 'merek', 'satuanDasar'])->findOrFail($selectedProdukId);

        $daftarSesiAhp = SesiPerhitunganAhp::orderBy('tanggal_hitung', 'desc')->get();
        $selectedSesiId = $request->input('sesi_id');

        if ($selectedSesiId) {
            $sesiDipilih = SesiPerhitunganAhp::with('perbandingan')->find($selectedSesiId);
        } else {
            $sesiDipilih = SesiPerhitunganAhp::with('perbandingan')->latest('tanggal_hitung')->first();
        }

        $kriteriaList = KriteriaAhp::orderBy('id', 'asc')->get();
        $bobotAHP = $this->hitungBobotDariSesi($sesiDipilih, $kriteriaList);

        $alternatif = [];
        $matriksKeputusan = [];
        $matriksNormalisasi = [];
        $minMax = [];

        $supplierList = Supplier::whereHas('produk', function ($q) use ($selectedProdukId) {
            $q->where('produk.id', $selectedProdukId);
        })->get();

        if ($supplierList->count() > 0) {
            foreach ($supplierList as $sup) {
                $alternatif[$sup->id] = $sup;
                $matriksKeputusan[$sup->id] = [
                    'K1' => (float) $this->getHargaTerakhir($sup->id, $selectedProduk),
                    'K2' => (float) $this->getNilaiKualitas($sup->id),
                    'K3' => (float) $this->getNilaiRespon($sup->id),
                    'K4' => (float) $this->getNilaiGaransi($sup->id),
                    'K5' => (float) $this->getWaktuPengiriman($sup->id),
                    'K6' => (float) $this->getTempoPembayaran($sup->id),
                ];
            }

            foreach ($kriteriaList as $k) {
                $kode = $k->kode_kriteria;
                $columnValues = array_column($matriksKeputusan, $kode);
                $minMax[$kode] = [
                    'max' => count($columnValues) > 0 ? max($columnValues) : 1,
                    'min' => count($columnValues) > 0 ? min($columnValues) : 1,
                ];
            }

            foreach ($supplierList as $sup) {
                $supId = $sup->id;
                $matriksNormalisasi[$supId] = [];
                foreach ($kriteriaList as $k) {
                    $kode = $k->kode_kriteria;
                    $val = $matriksKeputusan[$supId][$kode];
                    if ($k->jenis_kriteria === 'benefit') {
                        $max = $minMax[$kode]['max'];
                        $matriksNormalisasi[$supId][$kode] = ($max > 0) ? ($val / $max) : 0;
                    } else {
                        $min = $minMax[$kode]['min'];
                        $matriksNormalisasi[$supId][$kode] = ($val > 0) ? ($min / $val) : 0;
                    }
                }
            }
        }

        return view('admin.rekomendasi_supplier.show', compact(
            'selectedProduk',
            'daftarSesiAhp',
            'sesiDipilih',
            'kriteriaList',
            'bobotAHP',
            'matriksKeputusan',
            'matriksNormalisasi',
            'alternatif',
            'minMax'
        ));
    }
}

