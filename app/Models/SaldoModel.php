<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoModel extends Model
{
    use HasFactory;
    protected $table = 'saldo';
    protected $fillable = [
        'id', 'user_id', 'nominal', 'tanggal', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'nomor_identitas');
    }
}
