<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherModel extends Model
{
    use HasFactory;
    protected $table = 'voucher';
    protected $fillable = [
        'id', 'tittle', 'subtittle', 'metode_pembayaran', 'tanggal_berakhir', 'status'
    ];

    public function voucher_detail()
    {
        return $this->hasOne(VoucherDetailModel::class, 'id', 'voucher_id');
    }
}
