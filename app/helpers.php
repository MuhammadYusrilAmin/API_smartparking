<?php

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Melihovv\Base64ImageDecoder\Base64ImageDecoder;

function getUser($params)
{
    $user = User::where('nomor_identitas', $params)
        ->orWhere('no_telp', $params)
        ->first();

    return $user;
}


// https://www.base64-image.de/
function uploadBase64image($base64image)
{
    $decoder = new Base64ImageDecoder($base64image, $allowedFormats = ['jpeg', 'png', 'jpg']);

    $decodedContent = $decoder->getDecodedContent();
    $format = $decoder->getFormat(); // 'png', or 'jpeg', or 'gif', or etc.
    $image = Str::random(10) . '.' . $format;
    File::put('dokumen/' . $image, $decodedContent);
    // Storage::disk('public')->put('dokumen/' . $image, $decodedContent);

    return $image;
}


