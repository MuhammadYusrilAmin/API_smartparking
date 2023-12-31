<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KendaraanModel extends Model
{
    use HasFactory;
    protected $table = 'kendaraan';
    protected $fillable = [
        'id', 'user_id', 'nama_kendaraan', 'nomor_plat', 'foto_stnk', 'foto_kendaraan_tampak_depan', 'foto_kendaraan_tampak_belakang', 'foto_kendaraan_dengan_pemilik', 'image_qr', 'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaksi()
    {
        return $this->hasMany(TransaksiModel::class);
    }
}
