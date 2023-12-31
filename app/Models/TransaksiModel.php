<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiModel extends Model
{
    use HasFactory;
    protected $table = 'transaksi';
    protected $fillable = [
        'id', 'user_id', 'harga_akhir', 'tanggal', 'status', 'status_keluar_masuk', 'detail_lokasi_id', 'kendaraan_id', 'jam_masuk', 'jam_keluar', 'image_qr'
    ];

    public function detail_lokasi()
    {
        return $this->belongsTo(DetailLokasiModel::class);
    }

    public function kendaraan()
    {
        return $this->belongsTo(KendaraanModel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function voucher()
    {
        return $this->hasOne(VoucherDetailModel::class);
    }
}
