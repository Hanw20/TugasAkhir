<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KriteriaAhp;
use App\Models\SesiPerhitunganAhp;
use App\Models\PerbandinganKriteriaAhp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Bardiz12\AHPDss\Constants;

class SesiPerhitunganAhpController extends Controller
{
    /**
     * Menampilkan daftar sesi AHP dan form matriks perbandingan kriteria.
     */
    public function index(Request $request)
    {
        $kriteria = KriteriaAhp::orderBy('id', 'asc')->get();
        $daftarSesi = SesiPerhitunganAhp::with('user')->orderBy('tanggal_hitung', 'desc')->get();

        // Jika user memilih sesi tertentu dari list, buka sesi tersebut. Jika tidak, buka yang terbaru.
        $selectedSesiId = $request->input('sesi_id');
        if ($selectedSesiId) {
            $sesiAktif = SesiPerhitunganAhp::with(['perbandingan.kriteriaA', 'perbandingan.kriteriaB'])->find($selectedSesiId);
        } else {
            $sesiAktif = SesiPerhitunganAhp::with(['perbandingan.kriteriaA', 'perbandingan.kriteriaB'])->latest('tanggal_hitung')->first();
        }

        // Pre-fill nilai matriks dari sesi yang aktif
        $matrixValues = [];
        $hasilAhp = session('hasil_ahp');

        if ($sesiAktif) {
            foreach ($sesiAktif->perbandingan as $p) {
                $matrixValues[$p->kriteria_a_id][$p->kriteria_b_id] = (float) $p->nilai_perbandingan;
            }

            // Hitung ulang bobot sesi yang dibuka untuk ditampilkan
            if (!$hasilAhp) {
                $hasilAhp = $this->kalkulasiUlangSesi($sesiAktif, $kriteria);
            }
        }

        return view('admin.sesi_perhitungan_ahp.index', compact('kriteria', 'daftarSesi', 'sesiAktif', 'matrixValues', 'hasilAhp'));
    }

    /**
     * Memproses perhitungan matriks AHP, menguji nilai CR, dan menyimpan sebagai sesi baru.
     */
    public function store(Request $request)
    {
        $kriteria = KriteriaAhp::orderBy('id', 'asc')->get();
        $n = count($kriteria);

        if ($n < 2) {
            return back()->with('error', 'Jumlah kriteria minimal 2.');
        }

        $inputPair = $request->input('pair', []);
        $kriteriaIds = $kriteria->pluck('id')->toArray();
        $matrix = [];

        // 1. Membentuk Matriks Bujursangkar (n x n)
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

        // 2. Normalisasi Matriks & Hitung Eigen Vector
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

        // 3. Uji Konsistensi (Lambda Max, CI, CR)
        $lambdaMax = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $lambdaMax += $colSums[$i] * $eigenVector[$i];
        }

        $ci = ($lambdaMax - $n) / ($n - 1);
        $ri = Constants::getIR($n);
        $cr = $ri > 0 ? ($ci / $ri) : 0.0;

        $isConsistent = ($cr <= 0.1);

        if ($isConsistent) {
            DB::beginTransaction();
            try {
                // Simpan Sesi Baru
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

    /**
     * Hapus salah satu sesi AHP yang tersimpan.
     */
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
        $ri = Constants::getIR($n);
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
}