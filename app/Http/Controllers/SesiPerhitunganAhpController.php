<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KriteriaSupplier;
use App\Models\SesiPerhitunganAhp;
use App\Models\PerbandinganKriteriaAhp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SesiPerhitunganAhpController extends Controller
{
    
    public function index(Request $request)
    {
        $kriteria = KriteriaSupplier::orderBy('id', 'asc')->get();
        $daftarSesi = SesiPerhitunganAhp::with('user')->orderBy('tanggal_hitung', 'desc')->get();

        $selectedSesiId = $request->input('sesi_id');
        if ($selectedSesiId) {
            $sesiAktif = SesiPerhitunganAhp::with(['perbandingan.kriteriaA', 'perbandingan.kriteriaB'])->find($selectedSesiId);
        } else {
            $sesiAktif = SesiPerhitunganAhp::with(['perbandingan.kriteriaA', 'perbandingan.kriteriaB'])->latest('tanggal_hitung')->first();
        }

        $matrixValues = [];
        $hasilAhp = session('hasil_ahp');

        if ($sesiAktif) {
            foreach ($sesiAktif->perbandingan as $p) {
                $matrixValues[$p->kriteria_a_id][$p->kriteria_b_id] = (float) $p->nilai_perbandingan;
            }

            if (!$hasilAhp) {
                $hasilAhp = $this->kalkulasiUlangSesi($sesiAktif, $kriteria);
            }
        }

        return view('admin.sesi_perhitungan_ahp.index', compact('kriteria', 'daftarSesi', 'sesiAktif', 'matrixValues', 'hasilAhp'));
    }

    
    public function store(Request $request)
    {
        $kriteria = KriteriaSupplier::orderBy('id', 'asc')->get();
        $n = count($kriteria);

        if ($n < 2) {
            return back()->with('error', 'Jumlah kriteria minimal 2.');
        }

        $inputPair = $request->input('pair', []);
        $kriteriaIds = $kriteria->pluck('id')->toArray();
        $matrix = [];

        for ($i = 0; $i < $n; $i++) {
            $idA = $kriteriaIds[$i];
            $matrix[$i] = [];
            for ($j = 0; $j < $n; $j++) {
                $idB = $kriteriaIds[$j];
                if ($i == $j) {
                    $matrix[$i][$j] = 1.0;
                } elseif ($i < $j) {
                    $val = isset($inputPair[$idA][$idB]) ? (float) $inputPair[$idA][$idB] : 1.0;
                    $matrix[$i][$j] = $val > 0 ? $val : 1.0;
                } else {
                    $matrix[$i][$j] = 1.0 / $matrix[$j][$i];
                }
            }
        }

        $colSums = array_fill(0, $n, 0.0);
        for ($j = 0; $j < $n; $j++) {
            for ($i = 0; $i < $n; $i++) {
                $colSums[$j] += $matrix[$i][$j];
            }
        }

        $normalizedMatrix = [];
        $eigenVector = array_fill(0, $n, 0.0);
        for ($i = 0; $i < $n; $i++) {
            $normalizedMatrix[$i] = [];
            for ($j = 0; $j < $n; $j++) {
                $normalizedMatrix[$i][$j] = $matrix[$i][$j] / $colSums[$j];
                $eigenVector[$i] += $normalizedMatrix[$i][$j];
            }
            $eigenVector[$i] /= $n;
        }

        $lambdaMax = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $lambdaMax += $colSums[$i] * $eigenVector[$i];
        }

        $ci = ($lambdaMax - $n) / ($n - 1);
        $ri = $this->getRandomIndex($n);
        $cr = $ri > 0 ? ($ci / $ri) : 0.0;

        $isConsistent = ($cr <= 0.1);

        if ($isConsistent) {
            DB::beginTransaction();
            try {
                $namaSesi = $request->input('nama_sesi') ?: ('Sesi AHP ' . date('d/m/Y H:i'));
                $sesi = SesiPerhitunganAhp::create([
                    'nama_sesi'      => $namaSesi,
                    'tanggal_hitung' => now(),
                    'user_id'        => Auth::id() ?? 1,
                ]);

                for ($i = 0; $i < $n; $i++) {
                    $idA = $kriteriaIds[$i];
                    for ($j = 0; $j < $n; $j++) {
                        $idB = $kriteriaIds[$j];
                        PerbandinganKriteriaAhp::create([
                            'sesi_perhitungan_ahp_id' => $sesi->id,
                            'kriteria_a_id'           => $idA,
                            'kriteria_b_id'           => $idB,
                            'nilai_perbandingan'      => $matrix[$i][$j],
                        ]);
                    }
                }

                DB::commit();

                return redirect()->route('admin.sesi-perhitungan-ahp.index', ['sesi_id' => $sesi->id])
                    ->with('success', "Sesi baru '{$namaSesi}' berhasil disimpan! (CR = " . round($cr, 4) . " <= 0.1)")
                    ->with('hasil_ahp', [
                        'matrix' => $matrix,
                        'eigenVector' => $eigenVector,
                        'lambdaMax' => $lambdaMax,
                        'ci' => $ci,
                        'cr' => $cr,
                        'kriteria' => $kriteria,
                        'isConsistent' => true
                    ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
            }
        } else {
            return back()->with('error', 'Hasil perbandingan TIDAK KONSISTEN! Nilai CR = ' . round($cr, 4) . ' > 0.1. Silakan atur kembali perbandingan kriteria.')
                ->with('hasil_ahp', [
                    'matrix' => $matrix,
                    'eigenVector' => $eigenVector,
                    'lambdaMax' => $lambdaMax,
                    'ci' => $ci,
                    'cr' => $cr,
                    'kriteria' => $kriteria,
                    'isConsistent' => false
                ]);
        }
    }

    
    public function destroy(string $id)
    {
        $sesi = SesiPerhitunganAhp::findOrFail($id);
        $nama = $sesi->nama_sesi;
        $sesi->delete();

        return redirect()->route('admin.sesi-perhitungan-ahp.index')->with('success', "Sesi '{$nama}' berhasil dihapus!");
    }

    private function kalkulasiUlangSesi($sesi, $kriteria)
    {
        $n = count($kriteria);
        $kriteriaIds = $kriteria->pluck('id')->toArray();
        $matrixValues = [];
        foreach ($sesi->perbandingan as $p) {
            $matrixValues[$p->kriteria_a_id][$p->kriteria_b_id] = (float) $p->nilai_perbandingan;
        }

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

        $eigenVector = array_fill(0, $n, 0.0);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $eigenVector[$i] += ($colSums[$j] > 0) ? ($matrix[$i][$j] / $colSums[$j]) : 0;
            }
            $eigenVector[$i] /= $n;
        }

        $lambdaMax = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $lambdaMax += $colSums[$i] * $eigenVector[$i];
        }

        $ci = ($lambdaMax - $n) / ($n - 1);
        $ri = $this->getRandomIndex($n);
        $cr = $ri > 0 ? ($ci / $ri) : 0.0;

        return [
            'matrix' => $matrix,
            'eigenVector' => $eigenVector,
            'lambdaMax' => $lambdaMax,
            'ci' => $ci,
            'cr' => $cr,
            'kriteria' => $kriteria,
            'isConsistent' => ($cr <= 0.1)
        ];
    }

    
    private function getRandomIndex(int $n): float
    {
        $riTable = [
            1 => 0.0,
            2 => 0.0,
            3 => 0.58,
            4 => 0.90,
            5 => 1.12,
            6 => 1.24,
            7 => 1.32,
            8 => 1.41,
            9 => 1.45,
            10 => 1.49,
        ];

        return $riTable[$n] ?? 1.24;
    }
}
