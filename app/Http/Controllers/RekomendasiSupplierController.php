<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\KriteriaSupplier;
use App\Models\SesiPerhitunganAhp;
use App\Models\DetailPembelian;
use App\Models\RiwayatPenilaianSupplier;
use App\Models\SawNormalisasi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RekomendasiSupplierController extends Controller
{
    
    public function index(Request $request)
    {
        $daftarProduk = Produk::with('satuanDasar')->orderBy('nama_produk', 'asc')->get();
        $selectedProdukId = $request->input('produk_id');
        $selectedProduk = null;

        $daftarSesiAhp = SesiPerhitunganAhp::orderBy('tanggal_hitung', 'desc')->get();
        $selectedSesiId = $request->input('sesi_id');

        if ($selectedSesiId) {
            $sesiDipilih = SesiPerhitunganAhp::with('perbandingan')->find($selectedSesiId);
        } else {
            $sesiDipilih = SesiPerhitunganAhp::with('perbandingan')->latest('tanggal_hitung')->first();
        }

        $kriteriaList = KriteriaSupplier::orderBy('id', 'asc')->get();

        $bobotAHP = $this->hitungBobotDariSesi($sesiDipilih, $kriteriaList);

        $alternatif = [];
        $matriksKeputusan = [];
        $matriksNormalisasi = [];
        $minMax = [];
        $ranking = [];
        $rankingSawMurni = [];
        $rankingAhpMurni = [];

        if ($request->has('analisis') && $selectedProdukId) {
            $selectedProduk = Produk::with(['kategori', 'merek', 'satuanDasar'])->find($selectedProdukId);

            if ($selectedProduk) {
                $supplierList = Supplier::whereHas('produk', function ($q) use ($selectedProdukId) {
                    $q->where('produk.id', $selectedProdukId);
                })->get();

            if ($supplierList->count() > 0) {
                $pivotMap = DB::table('produk_supplier')
                    ->where('produk_id', $selectedProdukId)
                    ->pluck('id', 'supplier_id')
                    ->toArray();

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

                $minMax = [];
                foreach ($kriteriaList as $k) {
                    $kode = $k->kode_kriteria;
                    $columnValues = array_column($matriksKeputusan, $kode);
                    $minMax[$kode] = [
                        'max' => count($columnValues) > 0 ? max($columnValues) : 1,
                        'min' => count($columnValues) > 0 ? min($columnValues) : 1,
                    ];
                }

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

                        $matriksNormalisasi[$supId][$kode] = (float) $r;

                        if ($produkSupplierId && $k->id) {
                            SawNormalisasi::updateOrCreate(
                                [
                                    'produk_supplier_id' => $produkSupplierId,
                                    'kriteria_supplier_id' => $k->id,
                                ],
                                [
                                    'nilai_asli'         => (float) $x,
                                    'nilai_normalisasi'  => (float) $r,
                                ]
                            );
                        }
                    }
                }

                $bobotRata = 1.0 / count($kriteriaList);

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

                $ahpLocalScores = [];
                foreach ($kriteriaList as $k) {
                    $kode = $k->kode_kriteria;
                    $colSum = 0.0;
                    foreach ($supplierList as $sup) {
                        $rawVal = (float) $matriksKeputusan[$sup->id][$kode];
                        $val = ($k->jenis_kriteria === 'cost') ? (1.0 / ($rawVal > 0 ? $rawVal : 1.0)) : $rawVal;
                        $colSum += $val;
                    }
                    foreach ($supplierList as $sup) {
                        $rawVal = (float) $matriksKeputusan[$sup->id][$kode];
                        $val = ($k->jenis_kriteria === 'cost') ? (1.0 / ($rawVal > 0 ? $rawVal : 1.0)) : $rawVal;
                        $ahpLocalScores[$sup->id][$kode] = $colSum > 0 ? ($val / $colSum) : 0;
                    }
                }

                foreach ($supplierList as $sup) {
                    $skorAhp = 0.0;
                    foreach ($kriteriaList as $k) {
                        $kode = $k->kode_kriteria;
                        $wAhp = $bobotAHP[$kode] ?? (1.0 / count($kriteriaList));
                        $skorAhp += ($ahpLocalScores[$sup->id][$kode] * $wAhp);
                    }
                    $rankingAhpMurni[] = [
                        'supplier'        => $sup,
                        'skor_preferensi' => round($skorAhp, 4),
                    ];
                }
                usort($rankingAhpMurni, fn($a, $b) => $b['skor_preferensi'] <=> $a['skor_preferensi']);
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
            'minMax',
            'alternatif'
        ));
    }

    
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

    private function getHargaTerakhir($supplierId, $produk)
    {
        $lastPurchase = DetailPembelian::whereHas('pembelian', function ($q) use ($supplierId) {
            $q->where('supplier_id', $supplierId);
        })->where('produk_id', $produk->id)->latest()->first();

        return $lastPurchase ? (float) $lastPurchase->harga_satuan : (float) $produk->harga_beli;
    }

    private function getNilaiKualitas($supplierId)
    {
        $avg = RiwayatPenilaianSupplier::where('supplier_id', $supplierId)
            ->whereHas('kriteria', function ($q) {
                $q->where('kode_kriteria', 'K2');
            })->avg('nilai');

        return $avg ? (float) $avg : 3.5;
    }

    private function getNilaiRespon($supplierId)
    {
        $avg = RiwayatPenilaianSupplier::where('supplier_id', $supplierId)
            ->whereHas('kriteria', function ($q) {
                $q->where('kode_kriteria', 'K3');
            })->avg('nilai');

        return $avg ? (float) $avg : 3.0;
    }

    private function getNilaiGaransi($supplierId)
    {
        $avg = RiwayatPenilaianSupplier::where('supplier_id', $supplierId)
            ->whereHas('kriteria', function ($q) {
                $q->where('kode_kriteria', 'K4');
            })->avg('nilai');

        return $avg ? (float) $avg : 3.0;
    }

    private function getWaktuPengiriman($supplierId)
    {
        $avg = RiwayatPenilaianSupplier::where('supplier_id', $supplierId)
            ->whereHas('kriteria', function ($q) {
                $q->where('kode_kriteria', 'K5');
            })->avg('nilai');

        if ($avg) {
            return (float) $avg;
        }

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
                return (float) ($totalHari / $count);
            }
        }

        return 3.0;
    }

    private function getTempoPembayaran($supplierId)
    {
        $avg = RiwayatPenilaianSupplier::where('supplier_id', $supplierId)
            ->whereHas('kriteria', function ($q) {
                $q->where('kode_kriteria', 'K6');
            })->avg('nilai');

        if ($avg) {
            return (float) $avg;
        }

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
            return (float) ($totalHari / $pembelian->count());
        }

        return 14.0;
    }

        
        
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

        $kriteriaList = KriteriaSupplier::orderBy('id', 'asc')->get();
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

            $bobotRata = 1.0 / count($kriteriaList);
            foreach ($matriksKeputusan as $supId => $nilaiKriteria) {
                $skorHybrid = 0.0;
                $skorSawMurni = 0.0;
                $perkalianDetail = [];

                foreach ($kriteriaList as $k) {
                    $kode = $k->kode_kriteria;
                    $r = $matriksNormalisasi[$supId][$kode];
                    $wAhp = $bobotAHP[$kode] ?? $bobotRata;

                    $kali = $r * $wAhp;
                    $skorHybrid += $kali;
                    $skorSawMurni += ($r * $bobotRata);
                    $perkalianDetail[$kode] = [
                        'r' => $r,
                        'w' => $wAhp,
                        'kali' => $kali
                    ];
                }

                $dataItem = [
                    'supplier_id'       => $supId,
                    'supplier'          => $alternatif[$supId],
                    'nilai_keputusan'   => $nilaiKriteria,
                    'nilai_normalisasi' => $matriksNormalisasi[$supId],
                    'perkalian_detail'  => $perkalianDetail,
                ];

                $ranking[]         = array_merge($dataItem, ['skor_preferensi' => round($skorHybrid, 4)]);
                $rankingSawMurni[] = array_merge($dataItem, ['skor_preferensi' => round($skorSawMurni, 4)]);
            }

            usort($ranking, fn($a, $b) => $b['skor_preferensi'] <=> $a['skor_preferensi']);
            usort($rankingSawMurni, fn($a, $b) => $b['skor_preferensi'] <=> $a['skor_preferensi']);

            $ahpLocalScores = [];
            foreach ($kriteriaList as $k) {
                $kode = $k->kode_kriteria;
                $colSum = 0.0;
                foreach ($supplierList as $sup) {
                    $rawVal = (float) $matriksKeputusan[$sup->id][$kode];
                    $val = ($k->jenis_kriteria === 'cost') ? (1.0 / ($rawVal > 0 ? $rawVal : 1.0)) : $rawVal;
                    $colSum += $val;
                }
                foreach ($supplierList as $sup) {
                    $rawVal = (float) $matriksKeputusan[$sup->id][$kode];
                    $val = ($k->jenis_kriteria === 'cost') ? (1.0 / ($rawVal > 0 ? $rawVal : 1.0)) : $rawVal;
                    $ahpLocalScores[$sup->id][$kode] = $colSum > 0 ? ($val / $colSum) : 0;
                }
            }

            foreach ($supplierList as $sup) {
                $skorAhp = 0.0;
                foreach ($kriteriaList as $k) {
                    $kode = $k->kode_kriteria;
                    $wAhp = $bobotAHP[$kode] ?? (1.0 / count($kriteriaList));
                    $skorAhp += ($ahpLocalScores[$sup->id][$kode] * $wAhp);
                }
                $rankingAhpMurni[] = [
                    'supplier'        => $sup,
                    'skor_preferensi' => round($skorAhp, 4),
                ];
            }
            usort($rankingAhpMurni, fn($a, $b) => $b['skor_preferensi'] <=> $a['skor_preferensi']);
        }

        $ahpDetail = null;
        if ($sesiDipilih) {
            $n = count($kriteriaList);
            $kriteriaIds = $kriteriaList->pluck('id')->toArray();
            $matrixValues = [];
            foreach ($sesiDipilih->perbandingan as $p) {
                $matrixValues[$p->kriteria_a_id][$p->kriteria_b_id] = (float) $p->nilai_perbandingan;
            }

            $ahpMatrix = [];
            for ($i = 0; $i < $n; $i++) {
                $idA = $kriteriaIds[$i];
                for ($j = 0; $j < $n; $j++) {
                    $idB = $kriteriaIds[$j];
                    $ahpMatrix[$i][$j] = $matrixValues[$idA][$idB] ?? ($i == $j ? 1.0 : 1.0);
                }
            }

            $ahpColSums = array_fill(0, $n, 0.0);
            for ($j = 0; $j < $n; $j++) {
                for ($i = 0; $i < $n; $i++) {
                    $ahpColSums[$j] += $ahpMatrix[$i][$j];
                }
            }

            $ahpNormalized = [];
            $ahpEigenVector = array_fill(0, $n, 0.0);
            for ($i = 0; $i < $n; $i++) {
                $ahpNormalized[$i] = [];
                for ($j = 0; $j < $n; $j++) {
                    $norm = ($ahpColSums[$j] > 0) ? ($ahpMatrix[$i][$j] / $ahpColSums[$j]) : 0;
                    $ahpNormalized[$i][$j] = $norm;
                    $ahpEigenVector[$i] += $norm;
                }
                $ahpEigenVector[$i] /= $n;
            }

            $ahpLambdaMax = 0.0;
            for ($i = 0; $i < $n; $i++) {
                $ahpLambdaMax += $ahpColSums[$i] * $ahpEigenVector[$i];
            }

            $ahpCI = ($ahpLambdaMax - $n) / ($n - 1);
            $riTable = [1 => 0.0, 2 => 0.0, 3 => 0.58, 4 => 0.90, 5 => 1.12, 6 => 1.24, 7 => 1.32, 8 => 1.41, 9 => 1.45, 10 => 1.49];
            $ahpRI = $riTable[$n] ?? 1.24;
            $ahpCR = $ahpRI > 0 ? ($ahpCI / $ahpRI) : 0.0;

            $ahpDetail = [
                'matrix' => $ahpMatrix,
                'colSums' => $ahpColSums,
                'normalized' => $ahpNormalized,
                'eigenVector' => $ahpEigenVector,
                'lambdaMax' => $ahpLambdaMax,
                'ci' => $ahpCI,
                'ri' => $ahpRI,
                'cr' => $ahpCR,
                'isConsistent' => ($ahpCR <= 0.1)
            ];
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
            'minMax',
            'ahpDetail',
            'ranking',
            'rankingSawMurni',
            'rankingAhpMurni'
        ));
    }
}
