<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkirModel extends Model
{
    use HasFactory;
    protected $table = 'parkir';
    protected $fillable = [
        'id', 'lokasi_parkir', 'alamat'
    ];

    public function detail_lokasi()
    {
        return $this->hasMany(DetailLokasiModel::class, 'parkir_id', 'id');
    }
}
