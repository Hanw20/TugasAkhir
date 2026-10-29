<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPenilaianSupplier extends Model
{
    protected $table = 'riwayat_penilaian_supplier';

    protected $fillable = [
        'supplier_id', 'detail_penerimaan_pembelian_id',
        'kriteria_ahp_id', 'nilai', 'tanggal_penilaian', 'keterangan',
    ];

    protected $casts = ['tanggal_penilaian' => 'date'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function detailPenerimaanPembelian()
    {
        return $this->belongsTo(DetailPenerimaanPembelian::class, 'detail_penerimaan_pembelian_id');
    }

    public function kriteria()
    {
        return $this->belongsTo(KriteriaAhp::class, 'kriteria_ahp_id');
    }
}
