<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherDetailModel extends Model
{
    use HasFactory;
    protected $table = 'voucher_detail';
    protected $fillable = [
        'id', 'transaksi_id', 'voucher_id', 'status_pakai'
    ];

    public function transaksi()
    {
        return $this->belongsTo(TransaksiModel::class, 'transaksi_id', 'id');
    }

    public function voucher()
    {
        return $this->belongsTo(VoucherModel::class, 'voucher_id', 'id');
    }
}
