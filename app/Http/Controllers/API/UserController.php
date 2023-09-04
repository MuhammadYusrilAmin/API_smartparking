<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use App\Helper\ResponseFormatter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Melihovv\Base64ImageDecoder\Base64ImageDecoder;
use chillerlan\QRCode\{QRCode, QROptions};
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'max:255', 'min:6'],
            'no_telp' => ['required', 'string', 'max:15'],
            'no_plat' => ['required', 'string', 'max:255', 'min:3'],
            'pin' => ['required', 'string', 'max:255', 'min:6'],
        ]);


        $no_identitas = $request->nomor_identitas;
        // generate qr code 
        $data =  $no_identitas; // Ganti dengan data yang sesuai\
        $path = public_path('qrcodes/'); // Tentukan lokasi untuk menyimpan kode QR

        // Pastikan folder penyimpanan kode QR ada. Jika belum ada, buat folder tersebut.
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $fileName = uniqid() . '.png'; // Nama file kode QR
        $filePath = $path . $fileName; // Path lengkap ke file kode QR

        $options = new QROptions([
            'version'        => 5,
            'outputType'     => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'       => QRCode::ECC_L,
            'imageBase64'    => false,
            'imageTransparency' => false, // Set latar belakang menjadi solid
            'bgColor'        => [255, 255, 255], // Warna latar belakang (putih)
        ]);

        $qrcode = new QRCode($options);
        $qrcode->render($data, $filePath);
        // end generate

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        $user = User::where('nomor_identitas', $no_identitas)->exists();

        if ($user) {
            return response()->json(['message' => 'Nomor identitas already taken'], 409);
        }


        try {
            $foto_identitas = null;
            $foto_stnk = null;
            $foto_kendaraan_depan = null;
            $foto_kendaraan_belakang = null;

            if ($request->foto_identitas) {
                $foto_identitas = $this->uploadBase64image($request->foto_identitas);
            }

            if ($request->foto_stnk) {
                $foto_stnk = $this->uploadBase64image($request->foto_stnk);
            }

            if ($request->foto_kendaraan_depan) {
                $foto_kendaraan_depan = $this->uploadBase64image($request->foto_kendaraan_depan);
            }

            if ($request->foto_kendaraan_belakang) {
                $foto_kendaraan_belakang = $this->uploadBase64image($request->foto_kendaraan_belakang);
            }

            User::create([
                'nama_lengkap' => $request->nama_lengkap,
                'no_telp' => $request->no_telp,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'nomor_identitas' => $no_identitas,
                'no_plat' => $request->no_plat,
                'foto_identitas' => $foto_identitas,
                'foto_stnk' => $foto_stnk,
                'foto_kendaraan_depan' => $foto_kendaraan_depan,
                'foto_kendaraan_belakang' => $foto_kendaraan_belakang,
                'saldo' => 0,
                'pin' => $request->pin,
                'qr_code' => $fileName
            ]);


            // $user = User::where('nomor_identitas', $no_identitas)->first();

            $token = JWTAuth::attempt(['no_telp' => $request->no_telp, 'password' => $request->password]);
            $userResponse = getUser($request->no_telp);
            $userResponse->token = $token;
            $userResponse->token_expires_in = auth()->factory()->getTTL() * 60;
            $userResponse->token_type = 'bearer';

            return ResponseFormatter::success([
                $userResponse
            ], 'User Registered');
        } catch (\Throwable $th) {
            return ResponseFormatter::error([
                'message' => 'something went wrong',
                'error' => $th->getMessage()
            ], 'Aunthentication Failed', 500);
        }
    }

    public function login(Request $request)
    {
        $credential = $request->only('no_telp', 'password');

        $validator = Validator::make($credential, [
            'no_telp' => ['required', 'string', 'max:15'],
            'password' => ['required', 'string', 'max:255', 'min:6'],
        ]);

        // return $credential;

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        try {
            $token = JWTAuth::attempt($credential);

            if (!$token) {
                return response()->json(['message' => 'Login Credential are invalid']);
            }

            $userResponse = getUser($request->no_telp);
            $userResponse->token = $token;
            $userResponse->token_expires_in = auth()->factory()->getTTL() * 60;
            $userResponse->token_type = 'bearer';

            return ResponseFormatter::success([
                $userResponse
            ], 'Authenticated');

            // return $token;
        } catch (JWTException $th) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $th->getMessage(),
            ], 'Authentication Failed', 500);
        }
    }

    // https://www.base64-image.de/
    private function uploadBase64image($base64image)
    {
        $decoder = new Base64ImageDecoder($base64image, $allowedFormats = ['jpeg', 'png', 'jpg']);

        $decodedContent = $decoder->getDecodedContent();
        $format = $decoder->getFormat(); // 'png', or 'jpeg', or 'gif', or etc.
        $image = Str::random(10) . '.' . $format;
        File::put('dokumen/' . $image, $decodedContent);
        // Storage::disk('public')->put('dokumen/' . $image, $decodedContent);

        return $image;
    }
}
