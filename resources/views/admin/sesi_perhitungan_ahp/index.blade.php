@extends('layouts.adminlte')

@section('title', 'Sesi Perhitungan AHP')
@section('page_title', 'Perhitungan Bobot Kriteria (AHP)')

@section('content')
<div class="container-fluid">

    <!-- CARD 1: DAFTAR RIWAYAT SESI AHP YANG TERSIMPAN -->
    <div class="card card-outline card-secondary shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold m-0"><i class="bi bi-folder2-open me-2"></i>Daftar Sesi Pembobotan AHP Tersimpan</h5>
            <span class="badge bg-primary">{{ $daftarSesi->count() }} Sesi Tersimpan</span>
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
                            <tr class="{{ ($sesiAktif && $sesiAktif->id == $s->id) ? 'table-primary fw-bold' : '' }}">
                                <td>{{ $idx + 1 }}</td>
                                <td class="text-start">
                                    <i class="bi bi-bookmark-fill me-1 text-primary"></i> {{ $s->nama_sesi }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($s->tanggal_hitung)->format('d/m/Y H:i') }}</td>
                                <td>{{ $s->user->name ?? '-' }}</td>
                                <td>
                                    @if($sesiAktif && $sesiAktif->id == $s->id)
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Sedang Dibuka</span>
                                    @else
                                        <span class="badge bg-light text-muted">Tersimpan</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('admin.sesi-perhitungan-ahp.index', ['sesi_id' => $s->id]) }}" class="btn btn-sm btn-outline-primary" title="Buka Sesi Ini">
                                            <i class="bi bi-eye"></i> Buka
                                        </a>
                                        <form action="{{ route('admin.sesi-perhitungan-ahp.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus sesi perhitungan AHP ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Sesi">
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
    <!-- HASIL PERHITUNGAN BOBOT SESI TERPILIH -->
    <div class="card card-outline {{ $hasilAhp['isConsistent'] ? 'card-success' : 'card-danger' }} mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold m-0">
                <i class="bi {{ $hasilAhp['isConsistent'] ? 'bi-check2-circle text-success' : 'bi-x-circle text-danger' }} me-2"></i>
                Bobot Prioritas: {{ $sesiAktif->nama_sesi ?? 'Hasil Perhitungan' }}
            </h5>
            @if($sesiAktif)
                <span class="badge bg-info text-dark">
                    <i class="bi bi-clock-history me-1"></i> {{ \Carbon\Carbon::parse($sesiAktif->tanggal_hitung)->format('d/m/Y H:i') }}
                </span>
            @endif
        </div>
        <div class="card-body">
            <div class="row align-items-center mb-3">
                <div class="col-md-4">
                    <div class="p-3 border rounded text-center {{ $hasilAhp['isConsistent'] ? 'bg-success-subtle border-success' : 'bg-danger-subtle border-danger' }}">
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
                            <th>Random Index (RI, n=5)</th>
                            <td>1.12</td>
                        </tr>
                    </table>
                </div>
            </div>

            <h6 class="fw-bold mt-3 mb-2"><i class="bi bi-pie-chart-fill me-1 text-primary"></i> Vektor Prioritas / Bobot Kriteria (w<sub>j</sub>)</h6>
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
                                <td class="text-center"><span class="badge bg-secondary font-monospace">{{ $k->kode_kriteria }}</span></td>
                                <td class="fw-bold text-start">{{ $k->nama_kriteria }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $k->jenis_kriteria == 'benefit' ? 'bg-success' : 'bg-danger' }}">
                                        {{ strtoupper($k->jenis_kriteria) }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold font-monospace">{{ number_format($hasilAhp['eigenVector'][$idx], 4) }}</td>
                                <td class="text-end fw-bold text-primary">{{ number_format($hasilAhp['eigenVector'][$idx] * 100, 2) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- FORM MATRIKS PERBANDINGAN BERPASANGAN -->
    <div class="card card-outline card-primary shadow-sm mb-4">
        <div class="card-header">
            <h5 class="card-title fw-bold m-0">
                <i class="bi bi-grid-3x3-gap-fill me-2"></i> Form Matriks Perbandingan Berpasangan Skala Saaty (1 - 9)
            </h5>
        </div>
        <div class="card-body">
            <p class="text-muted small">
                Tentukan tingkat kepentingan relatif antar kriteria. Nilai <b>1</b> = Sama penting, <b>3</b> = Sedikit lebih penting, <b>5</b> = Lebih penting, <b>7</b> = Sangat lebih penting, <b>9</b> = Mutlak lebih penting.
            </p>

            <form action="{{ route('admin.sesi-perhitungan-ahp.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">Beri Nama Sesi Penilaian Ini</label>
                    <input type="text" name="nama_sesi" class="form-control" placeholder="Contoh: Sesi Fokus Harga Murah / Sesi Fokus Kualitas Tinggi" required>
                    <small class="text-muted">Beri nama spesifik agar mudah dipilih saat analisis rekomendasi supplier nanti.</small>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 25%;">Kriteria Pertama</th>
                                <th style="width: 50%;">Skala Kepentingan Relatif (1 - 9)</th>
                                <th style="width: 25%;">Kriteria Kedua</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $count = count($kriteria); @endphp
                            @for($i = 0; $i < $count; $i++)
                                @for($j = $i + 1; $j < $count; $j++)
                                    @php
                                        $kA = $kriteria[$i];
                                        $kB = $kriteria[$j];
                                        $existingVal = $matrixValues[$kA->id][$kB->id] ?? 1.0;
                                    @endphp
                                    <tr>
                                        <td class="text-start fw-bold">
                                            <span class="badge bg-primary font-monospace me-1">{{ $kA->kode_kriteria }}</span>
                                            {{ $kA->nama_kriteria }}
                                        </td>
                                        <td>
                                            <select name="pair[{{ $kA->id }}][{{ $kB->id }}]" class="form-select text-center fw-bold">
                                                <option value="9" {{ $existingVal == 9 ? 'selected' : '' }}>9 - Mutlak lebih penting dari</option>
                                                <option value="7" {{ $existingVal == 7 ? 'selected' : '' }}>7 - Sangat lebih penting dari</option>
                                                <option value="5" {{ $existingVal == 5 ? 'selected' : '' }}>5 - Lebih penting dari</option>
                                                <option value="3" {{ $existingVal == 3 ? 'selected' : '' }}>3 - Sedikit lebih penting dari</option>
                                                <option value="1" {{ $existingVal == 1 ? 'selected' : '' }}>1 - Sama penting dengan</option>
                                                <option value="0.3333" {{ abs($existingVal - 0.3333) < 0.01 ? 'selected' : '' }}>1/3 - Sedikit kurang penting dari</option>
                                                <option value="0.2" {{ abs($existingVal - 0.2) < 0.01 ? 'selected' : '' }}>1/5 - Kurang penting dari</option>
                                                <option value="0.1429" {{ abs($existingVal - 0.1429) < 0.01 ? 'selected' : '' }}>1/7 - Sangat kurang penting dari</option>
                                                <option value="0.1111" {{ abs($existingVal - 0.1111) < 0.01 ? 'selected' : '' }}>1/9 - Mutlak kurang penting dari</option>
                                            </select>
                                        </td>
                                        <td class="text-end fw-bold">
                                            {{ $kB->nama_kriteria }}
                                            <span class="badge bg-secondary font-monospace ms-1">{{ $kB->kode_kriteria }}</span>
                                        </td>
                                    </tr>
                                @endfor
                            @endfor
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        <i class="bi bi-save me-1"></i> Hitung & Simpan Sesi AHP Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection