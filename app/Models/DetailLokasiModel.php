<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailLokasiModel extends Model
{
    use HasFactory;
    protected $table = 'detail_lokasi';
    protected $fillable = [
        'id', 'parkir_id', 'lokasi_detail_parkir', 'status', 'harga_tiket'
    ];

    public function parkir()
    {
        return $this->belongsTo(ParkirModel::class);
    }
}
