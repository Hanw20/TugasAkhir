@extends('layouts.adminlte')

@section('title', 'Sesi Perhitungan AHP')
@section('page_title', 'Perhitungan Bobot Kriteria (AHP)')

@section('content')
    <div class="container-fluid">

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center text-white"
                style="background-color: #264653;">
                <h5 class="card-title fw-bold m-0"><i class="bi bi-folder2-open me-2"></i>Daftar Sesi Pembobotan AHP</h5>
                <span class="badge" style="background-color: #2A9D8F; color: #ffffff;">{{ $daftarSesi->count() }} Sesi
                    AHP</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle m-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th class="text-start">Nama Sesi</th>
                                <th>Tanggal Hitung</th>
                                <th>Dibuat Oleh</th>
                                <th>Status Tampilan</th>
                                <th style="width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($daftarSesi as $idx => $s)
                                <tr class="{{ ($sesiAktif && $sesiAktif->id == $s->id) ? 'table-light fw-bold' : '' }}">
                                    <td>{{ $idx + 1 }}</td>
                                    <td class="text-start">
                                        <i class="bi bi-bookmark-fill me-1" style="color: #2A9D8F;"></i> {{ $s->nama_sesi }}
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($s->tanggal_hitung)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $s->user->name ?? '-' }}</td>
                                    <td>
                                        @if($sesiAktif && $sesiAktif->id == $s->id)
                                            <span class="badge" style="background-color: #D1FAE5; color: #065F46;"><i
                                                    class="bi bi-check-circle me-1"></i> Sedang
                                                Dibuka</span>
                                        @else
                                            <span class="badge" style="background-color: #F1F5F9; color: #475569;">Tersimpan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('admin.sesi-perhitungan-ahp.index', ['sesi_id' => $s->id]) }}"
                                                class="btn btn-sm text-decoration-none"
                                                style="background-color: #E0F2FE; color: #0369A1; font-weight: 500;"
                                                title="Buka Sesi Ini">
                                                <i class="bi bi-eye"></i> Buka
                                            </a>
                                            <form action="{{ route('admin.sesi-perhitungan-ahp.destroy', $s->id) }}"
                                                method="POST" onsubmit="return confirm('Hapus sesi perhitungan AHP ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm"
                                                    style="background-color: #FEE2E2; color: #B91C1C; font-weight: 500;"
                                                    title="Hapus Sesi">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                        Belum ada sesi AHP yang tersimpan. Silakan isi form di bawah untuk membuat sesi baru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if(isset($hasilAhp) && $hasilAhp)

            <div class="card card-outline {{ $hasilAhp['isConsistent'] ? 'card-success' : 'card-danger' }} mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold m-0">
                        <i
                            class="bi {{ $hasilAhp['isConsistent'] ? 'bi-check2-circle text-success' : 'bi-x-circle text-danger' }} me-2"></i>
                        Bobot Prioritas: {{ $sesiAktif->nama_sesi ?? 'Hasil Perhitungan' }}
                    </h5>
                    @if($sesiAktif)
                        <span class="badge" style="background-color: #E0F2FE; color: #0369A1;">
                            <i class="bi bi-clock-history me-1"></i>
                            {{ \Carbon\Carbon::parse($sesiAktif->tanggal_hitung)->format('d/m/Y H:i') }}
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-4">
                            <div
                                class="p-3 border rounded text-center {{ $hasilAhp['isConsistent'] ? 'bg-success-subtle border-success' : 'bg-danger-subtle border-danger' }}">
                                <div class="small text-muted text-uppercase fw-bold">Consistency Ratio (CR)</div>
                                <h2 class="fw-bold my-1 {{ $hasilAhp['isConsistent'] ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($hasilAhp['cr'], 4) }}
                                </h2>
                                <span class="badge {{ $hasilAhp['isConsistent'] ? 'bg-success' : 'bg-danger' }}">
                                    {{ $hasilAhp['isConsistent'] ? 'KONSISTEN (CR <= 0.1)' : 'TIDAK KONSISTEN (CR > 0.1)' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <table class="table table-sm table-bordered m-0">
                                <tr>
                                    <th width="40%">Lambda Max (&lambda;<sub>max</sub>)</th>
                                    <td>{{ number_format($hasilAhp['lambdaMax'], 4) }}</td>
                                </tr>
                                <tr>
                                    <th>Consistency Index (CI)</th>
                                    <td>{{ number_format($hasilAhp['ci'], 4) }}</td>
                                </tr>
                                <tr>
                                    <th>Random Index (RI, n={{ count($hasilAhp['kriteria']) }})</th>
                                    <td>{{ count($hasilAhp['kriteria']) == 6 ? '1.24' : (count($hasilAhp['kriteria']) == 5 ? '1.12' : '0.90') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-4 mb-2"><i class="bi bi-grid-3x3 me-1 text-primary"></i> Matriks Perbandingan Berpasangan (A) Sesi Ini</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm text-center align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start" style="width: 20%;">Kriteria</th>
                                    @foreach($hasilAhp['kriteria'] as $k)
                                        <th>{{ $k->kode_kriteria }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hasilAhp['kriteria'] as $ri => $rk)
                                    <tr>
                                        <td class="text-start fw-bold table-light">{{ $rk->kode_kriteria }} - {{ $rk->nama_kriteria }}</td>
                                        @foreach($hasilAhp['kriteria'] as $ci => $ck)
                                            <td class="font-monospace {{ $ri == $ci ? 'fw-bold bg-light text-muted' : '' }}">
                                                {{ number_format($hasilAhp['matrix'][$ri][$ci], 4) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h6 class="fw-bold mt-3 mb-2"><i class="bi bi-pie-chart-fill me-1 text-primary"></i> Vektor Prioritas /
                        Bobot Kriteria (w<sub>j</sub>)</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Kode</th>
                                    <th class="text-start">Nama Kriteria</th>
                                    <th>Tipe Kriteria</th>
                                    <th class="text-end" style="width: 150px;">Bobot Prioritas (w)</th>
                                    <th class="text-end" style="width: 120px;">Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hasilAhp['kriteria'] as $idx => $k)
                                    <tr>
                                        <td class="text-center">{{ $idx + 1 }}</td>
                                        <td class="text-center"><span
                                                class="badge bg-secondary font-monospace">{{ $k->kode_kriteria }}</span></td>
                                        <td class="fw-bold text-start">{{ $k->nama_kriteria }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $k->jenis_kriteria == 'benefit' ? 'bg-success' : 'bg-danger' }}">
                                                {{ strtoupper($k->jenis_kriteria) }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold font-monospace">
                                            {{ number_format($hasilAhp['eigenVector'][$idx], 4) }}
                                        </td>
                                        <td class="text-end fw-bold text-primary">
                                            {{ number_format($hasilAhp['eigenVector'][$idx] * 100, 2) }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header text-white" style="background-color: #264653;">
                <h5 class="card-title fw-bold m-0">
                    <i class="bi bi-grid-3x3-gap-fill me-2"></i> Form Matriks Perbandingan Berpasangan Skala Saaty (1 - 9)
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Tentukan tingkat kepentingan relatif antar kriteria. Nilai <b>1</b> = Sama penting, <b>3</b> = Sedikit
                    lebih penting, <b>5</b> = Lebih penting, <b>7</b> = Sangat lebih penting, <b>9</b> = Mutlak lebih
                    penting.
                </p>

                <form action="{{ route('admin.sesi-perhitungan-ahp.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Beri Nama Sesi Penilaian Ini</label>
                        <input type="text" name="nama_sesi" class="form-control"
                            placeholder="Contoh: Sesi Fokus Harga Murah / Sesi Fokus Kualitas Tinggi" required>
                        <small class="text-muted">Beri nama spesifik agar mudah dipilih saat analisis rekomendasi supplier
                            nanti.</small>
                    </div>

                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="bi bi-info-circle me-1"></i> <strong>Panduan Pengisian:</strong> Cukup isi sel pada <strong>Segitiga Atas (kotak pilihan dropdown)</strong>. Sel diagonal bernilai <strong>1</strong>, dan sel segitiga bawah otomatis terisi nilai kebalikannya (1/x) secara real-time.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center mb-0" id="tabel-matriks-input">
                            <thead>
                                <tr class="text-white" style="background-color: #264653;">
                                    <th style="width: 18%;" class="text-start">Kriteria</th>
                                    @foreach($kriteria as $k)
                                        <th>
                                            <span class="badge bg-light text-dark font-monospace mb-1">{{ $k->kode_kriteria }}</span><br>
                                            <small style="font-size: 0.75rem;">{{ $k->nama_kriteria }}</small>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php $n = count($kriteria); @endphp
                                @for($i = 0; $i < $n; $i++)
                                    @php $kA = $kriteria[$i]; @endphp
                                    <tr>
                                        <td class="text-start fw-bold" style="background-color: #f8fafc;">
                                            <span class="badge me-1" style="background-color: #E0F2FE; color: #0369A1; font-family: monospace;">{{ $kA->kode_kriteria }}</span>
                                            <span style="font-size: 0.85rem;">{{ $kA->nama_kriteria }}</span>
                                        </td>
                                        @for($j = 0; $j < $n; $j++)
                                            @php
                                                $kB = $kriteria[$j];
                                                $val = $matrixValues[$kA->id][$kB->id] ?? 1.0;
                                            @endphp
                                            @if($i == $j)
                                                <td class="fw-bold text-muted" style="background-color: #E2E8F0; font-size: 0.95rem;">
                                                    1
                                                </td>
                                            @elseif($i < $j)
                                                <td style="background-color: #ffffff; padding: 4px;">
                                                    <select name="pair[{{ $kA->id }}][{{ $kB->id }}]" 
                                                            class="form-select form-select-sm text-center fw-semibold cell-input" 
                                                            id="input-{{ $i }}-{{ $j }}"
                                                            data-row="{{ $i }}" 
                                                            data-col="{{ $j }}"
                                                            style="font-size: 0.78rem; border-color: #2A9D8F;"
                                                            onchange="updateReciprocal({{ $i }}, {{ $j }})">
                                                        <option value="9" {{ abs($val - 9) < 0.01 ? 'selected' : '' }}>{{ $kA->kode_kriteria }} mutlak lebih penting (9)</option>
                                                        <option value="7" {{ abs($val - 7) < 0.01 ? 'selected' : '' }}>{{ $kA->kode_kriteria }} sangat lebih penting (7)</option>
                                                        <option value="5" {{ abs($val - 5) < 0.01 ? 'selected' : '' }}>{{ $kA->kode_kriteria }} lebih penting (5)</option>
                                                        <option value="3" {{ abs($val - 3) < 0.01 ? 'selected' : '' }}>{{ $kA->kode_kriteria }} sedikit lebih penting (3)</option>
                                                        <option value="1" {{ abs($val - 1) < 0.01 ? 'selected' : '' }}>Sama penting (1)</option>
                                                        <option value="0.3333" {{ abs($val - 0.3333) < 0.01 ? 'selected' : '' }}>{{ $kB->kode_kriteria }} sedikit lebih penting (1/3)</option>
                                                        <option value="0.2" {{ abs($val - 0.2) < 0.01 ? 'selected' : '' }}>{{ $kB->kode_kriteria }} lebih penting (1/5)</option>
                                                        <option value="0.1429" {{ abs($val - 0.1429) < 0.01 ? 'selected' : '' }}>{{ $kB->kode_kriteria }} sangat lebih penting (1/7)</option>
                                                        <option value="0.1111" {{ abs($val - 0.1111) < 0.01 ? 'selected' : '' }}>{{ $kB->kode_kriteria }} mutlak lebih penting (1/9)</option>
                                                    </select>
                                                </td>
                                            @else
                                                @php
                                                    $reciprocalVal = ($matrixValues[$kB->id][$kA->id] ?? 0) > 0 ? (1.0 / $matrixValues[$kB->id][$kA->id]) : 1.0;
                                                @endphp
                                                <td class="font-monospace text-muted cell-reciprocal" 
                                                    id="cell-{{ $i }}-{{ $j }}" 
                                                    style="background-color: #F8FAFC; font-size: 0.88rem;">
                                                    {{ number_format($reciprocalVal, 4) }}
                                                </td>
                                            @endif
                                        @endfor
                                    </tr>
                                @endfor
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td class="text-start" style="background-color: #f1f5f9;">Jumlah Kolom (&Sigma;)</td>
                                    @for($j = 0; $j < $n; $j++)
                                        <td class="font-monospace text-primary text-center" id="col-sum-{{ $j }}">0.0000</td>
                                    @endfor
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="small text-muted">
                            <i class="bi bi-shield-check text-success me-1"></i> Nilai inkonsistensi (CR &le; 0.1) akan otomatis dihitung dan divalidasi oleh sistem saat disimpan.
                        </span>
                        <button type="submit" class="btn text-white fw-bold px-4" style="background-color: #2A9D8F;">
                            <i class="bi bi-save me-1"></i> Hitung & Simpan Sesi AHP Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateReciprocal(row, col) {
            const selectEl = document.getElementById(`input-${row}-${col}`);
            const val = parseFloat(selectEl.value) || 1.0;
            const recipVal = 1.0 / val;

            const recipCell = document.getElementById(`cell-${col}-${row}`);
            if (recipCell) {
                recipCell.innerText = recipVal.toFixed(4);
            }
            recalculateColSums();
        }

        function recalculateColSums() {
            const n = {{ count($kriteria) }};
            for (let j = 0; j < n; j++) {
                let sum = 0.0;
                for (let i = 0; i < n; i++) {
                    if (i === j) {
                        sum += 1.0;
                    } else if (i < j) {
                        const el = document.getElementById(`input-${i}-${j}`);
                        if (el) {
                            sum += parseFloat(el.value) || 1.0;
                        }
                    } else {
                        const cell = document.getElementById(`cell-${i}-${j}`);
                        if (cell) {
                            sum += parseFloat(cell.innerText) || 1.0;
                        }
                    }
                }
                const colSumEl = document.getElementById(`col-sum-${j}`);
                if (colSumEl) {
                    colSumEl.innerText = sum.toFixed(4);
                }
            }
        }

        $(document).ready(function () {
            recalculateColSums();
        });
    </script>
@endpush