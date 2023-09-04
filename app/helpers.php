<?php

use App\Models\User;

function getUser($params)
{
    $user = User::where('nomor_identitas', $params)
        ->orWhere('no_telp', $params)
        ->first();
    $user->foto_identitas = $user->foto_identitas ? url('dokumen/' . $user->foto_identitas) : "";
    $user->foto_stnk = $user->foto_stnk ? url('dokumen/' . $user->foto_stnk) : "";
    $user->foto_kendaraan_depan = $user->foto_kendaraan_depan ? url('dokumen/' . $user->foto_kendaraan_depan) : "";
    $user->foto_kendaraan_belakang = $user->foto_kendaraan_belakang ? url('dokumen/' . $user->foto_kendaraan_belakang) : "";

    return $user;
}
