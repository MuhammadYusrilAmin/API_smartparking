<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use App\Helper\ResponseFormatter;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use chillerlan\QRCode\{QRCode, QROptions};
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // return DB::table('users')->count();
        $data = $request->all();
        $validator = Validator::make($data, [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'max:255', 'min:6'],
            'no_telp' => ['required', 'string', 'max:15'],
            'pin' => ['required', 'string', 'max:255', 'min:6'],
        ]);

        DB::beginTransaction();
        try {

            $no_identitas = $request->nomor_identitas;
            // generate qr code 
            $data =  $no_identitas; // Ganti dengan data yang sesuai\
            $path = public_path('qrcodes/'); // Tentukan lokasi untuk menyimpan kode QR

            // Pastikan folder penyimpanan kode QR ada. Jika belum ada, buat folder tersebut.
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $fileName = 'user_' . uniqid() . '.png'; // Nama file kode QR
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

            $foto_identitas = null;

            if ($request->foto_identitas) {
                $foto_identitas = uploadBase64image($request->foto_identitas);
            }

            User::create([
                'nama_lengkap' => $request->nama_lengkap,
                'no_telp' => $request->no_telp,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'nomor_identitas' => $no_identitas,
                'foto_identitas' => $foto_identitas,
                'saldo' => 0,
                'pin' => $request->pin,
                'qr_code' => $fileName
            ]);

            $token = JWTAuth::attempt(['no_telp' => $request->no_telp, 'password' => $request->password]);
            $userResponse = getUser($request->no_telp);
            $userResponse->token = $token;
            $userResponse->token_expires_in = (auth()->factory()->getTTL() * 60) * 25;
            $userResponse->token_type = 'bearer';

            DB::commit();
            return ResponseFormatter::success(
                $userResponse,
                'User Registered'
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseFormatter::error(
                $th->getMessage(),
                'Aunthentication Failed',
                500
            );
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
            $userResponse->token_expires_in = (auth()->factory()->getTTL() * 60) * 25;
            $userResponse->token_type = 'bearer';

            return ResponseFormatter::success(
                $userResponse,
                'Authenticated'
            );

            // return $token;
        } catch (JWTException $th) {
            return ResponseFormatter::error(
                $th->getMessage(),
                'Authentication Failed',
                500
            );
        }
    }

    public function show()
    {
        $user = User::LeftJoin('kendaraan', 'users.nomor_identitas', '=', 'kendaraan.user_id')
            ->where('users.nomor_identitas', Auth::user()->nomor_identitas)
            ->OrWhere('kendaraan.is_active', 1)
            ->first();
        return ResponseFormatter::success(
            $user,
            'User Found Succesfully'
        );
    }

    public function UpdateUser(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::user()->nomor_identitas);
            $data = $request->only('nama_lengkap', 'no_telp', 'email', 'foto_identitas');

            if ($request->email != $user->email) {
                $isExistEmail = User::where('email', $request->email)->exists();
                if ($isExistEmail) {
                    return response()->json(['message' => 'Email already taken'], 409);
                }
            }

            if ($request->no_telp != $user->no_telp) {
                $isExistno_telp = User::where('no_telp', $request->no_telp)->exists();
                if ($isExistno_telp) {
                    return response()->json(['message' => 'Nomor Telepon already taken'], 409);
                }
            }

            if ($request->foto_identitas) {
                $foto_identitas = uploadBase64image($request->foto_identitas);
                $data['foto_identitas'] = $foto_identitas;
                if ($user->foto_identitas) {
                    File::delete('dokumen/' . $user->foto_identitas);
                }
            }

            $user->update($data);
            DB::commit();
            return ResponseFormatter::success(
                $user,
                'Update User Succesfully'
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseFormatter::error(
                $th->getMessage(),
                'Update User Failed',
                500
            );
        }
    }

    public function isEmailExist(Request $request)
    {
        $validator = Validator::make($request->only('email'), [
            'email' => 'required|email'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        $isExists = User::where('email', $request->email)->exists();

        return  response()->json(['is_email_exist' => $isExists]);
    }

    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Logout User Succesfully']);
    }
}
