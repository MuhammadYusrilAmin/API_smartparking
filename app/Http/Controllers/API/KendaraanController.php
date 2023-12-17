<?php

namespace App\Http\Controllers\API;

use App\Helper\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\KendaraanModel;
use Illuminate\Http\Request;
use chillerlan\QRCode\{QRCode, QROptions};
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class KendaraanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ResponseFormatter::success(KendaraanModel::where('user_id', Auth::user()->nomor_identitas)->orderBy('created_at', 'desc')->get(), 'Data Kendaraan berhasil diambil');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kendaraan' => ['required', 'string', 'max:255'],
            'nomor_plat' => ['required', 'string', 'max:255', 'min:3'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        DB::beginTransaction();
        try {
            $foto_stnk = null;
            $foto_kendaraan_tampak_depan = null;
            $foto_kendaraan_tampak_belakang = null;
            $foto_kendaraan_dengan_pemilik = null;

            if ($request->foto_stnk) {
                $foto_stnk = uploadBase64image($request->foto_stnk);
            }

            if ($request->foto_kendaraan_tampak_depan) {
                $foto_kendaraan_tampak_depan = uploadBase64image($request->foto_kendaraan_tampak_depan);
            }

            if ($request->foto_kendaraan_tampak_belakang) {
                $foto_kendaraan_tampak_belakang = uploadBase64image($request->foto_kendaraan_tampak_belakang);
            }

            if ($request->foto_kendaraan_dengan_pemilik) {
                $foto_kendaraan_dengan_pemilik = uploadBase64image($request->foto_kendaraan_dengan_pemilik);
            }

            $is_active = (KendaraanModel::where('user_id', Auth::user()->nomor_identitas)->count() == 0) ? 1 : 0;

            $kendaraan = KendaraanModel::create([
                'nama_kendaraan' => $request->nama_kendaraan,
                'user_id' => Auth::user()->nomor_identitas,
                'nomor_plat' => $request->nomor_plat,
                'foto_stnk' => $foto_stnk,
                'foto_kendaraan_tampak_depan' => $foto_kendaraan_tampak_depan,
                'foto_kendaraan_tampak_belakang' => $foto_kendaraan_tampak_belakang,
                'foto_kendaraan_dengan_pemilik' => $foto_kendaraan_dengan_pemilik,
                'is_active' => $is_active
            ]);

            // generate qr code 
            $data =  $kendaraan->id; // Ganti dengan data yang sesuai\
            $path = public_path('kendaraan/'); // Tentukan lokasi untuk menyimpan kode QR

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $fileName = 'kendaraaan_' . uniqid() . '.png'; // Nama file kode QR
            $filePath = $path . $fileName; // Path lengkap ke file kode QR

            $options = new QROptions([
                'version'        => 5,
                'outputType'     => QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel'       => QRCode::ECC_L,
                'imageBase64'    => false,
                'imageTransparency' => false, // Set latar belakang menjadi solid
                'bgColor'        => [255, 255, 255], // Warna latar belakang (putih)
            ]);

            KendaraanModel::where('id', $data)->update(['image_qr' => $fileName]);

            $get_data = KendaraanModel::where('id', $data)->first();

            $qrcode = new QRCode($options);
            $qrcode->render($data, $filePath);

            DB::commit();
            return ResponseFormatter::success(
                $get_data,
                'Create Vehicle Successfully'
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseFormatter::error(
                $th,
                'Create Vehicle Unsuccessfully',
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function is_active(string $id)
    {
        DB::beginTransaction();
        try {
            KendaraanModel::where('user_id', Auth::user()->nomor_identitas)->update(['is_active' => 0]);

            $kendaraan = KendaraanModel::find($id);
            $kendaraan->is_active = 1;
            $kendaraan->update();

            DB::commit();
            return ResponseFormatter::success(
                $kendaraan,
                'Vehicle Active Succesfully'
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseFormatter::error(
                $th->getMessage(),
                'Update Active Failed',
                500
            );
        }
    }

    public function is_nonactive(string $id)
    {
        DB::beginTransaction();
        try {
            KendaraanModel::where('user_id', Auth::user()->nomor_identitas)->update(['is_active' => 0]);

            $kendaraan = KendaraanModel::find($id);
            $kendaraan->is_active = 0;
            $kendaraan->update();

            DB::commit();
            return ResponseFormatter::success(
                $kendaraan,
                'Vehicle Non Active Succesfully'
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseFormatter::error(
                $th->getMessage(),
                'Update Non Active Failed',
                500
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $kendaraan = KendaraanModel::find($id);
            $data = $request->only('nama_kendaraan', 'nomor_plat', 'foto_stnk', 'foto_kendaraan_tampak_depan', 'foto_kendaraan_tampak_belakang', 'foto_kendaraan_dengan_pemilik');

            if ($request->nama_kendaraan != $kendaraan->nama_kendaraan) {
                $isExistNamaKendaraan = KendaraanModel::where('nama_kendaraan', $request->nama_kendaraan)->exists();
                if ($isExistNamaKendaraan) {
                    return response()->json(['message' => 'Nama Kendaraan already taken'], 409);
                }
            }

            if ($request->nomor_plat != $kendaraan->nomor_plat) {
                $isExistnomor_plat = KendaraanModel::where('nomor_plat', $request->nomor_plat)->exists();
                if ($isExistnomor_plat) {
                    return response()->json(['message' => 'Nomor Plat already taken'], 409);
                }
            }

            if ($request->foto_stnk) {
                $foto_stnk = uploadBase64image($request->foto_stnk);
                $data['foto_stnk'] = $foto_stnk;
                if ($kendaraan->foto_stnk) {
                    File::delete('dokumen/' . $kendaraan->foto_stnk);
                }
            }

            if ($request->foto_kendaraan_tampak_depan) {
                $foto_kendaraan_tampak_depan = uploadBase64image($request->foto_kendaraan_tampak_depan);
                $data['foto_kendaraan_tampak_depan'] = $foto_kendaraan_tampak_depan;
                if ($kendaraan->foto_kendaraan_tampak_depan) {
                    File::delete('dokumen/' . $kendaraan->foto_kendaraan_tampak_depan);
                }
            }

            if ($request->foto_kendaraan_tampak_belakang) {
                $foto_kendaraan_tampak_belakang = uploadBase64image($request->foto_kendaraan_tampak_belakang);
                $data['foto_kendaraan_tampak_belakang'] = $foto_kendaraan_tampak_belakang;
                if ($kendaraan->foto_kendaraan_tampak_belakang) {
                    File::delete('dokumen/' . $kendaraan->foto_kendaraan_tampak_belakang);
                }
            }

            if ($request->foto_kendaraan_dengan_pemilik) {
                $foto_kendaraan_dengan_pemilik = uploadBase64image($request->foto_kendaraan_dengan_pemilik);
                $data['foto_kendaraan_dengan_pemilik'] = $foto_kendaraan_dengan_pemilik;
                if ($kendaraan->foto_kendaraan_dengan_pemilik) {
                    File::delete('dokumen/' . $kendaraan->foto_kendaraan_dengan_pemilik);
                }
            }

            $kendaraan->update($data);
            return ResponseFormatter::success(
                $kendaraan
            , 'Update Vehicle Succesfully');
        } catch (\Throwable $th) {
            return ResponseFormatter::error(
              $th->getMessage()
            , 'Update Vehicle Failed', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $kendaraan = KendaraanModel::find($id);
            if ($kendaraan->foto_stnk) {
                File::delete('dokumen/' . $kendaraan->foto_stnk);
            }

            if ($kendaraan->foto_kendaraan_tampak_depan) {
                File::delete('dokumen/' . $kendaraan->foto_kendaraan_tampak_depan);
            }


            if ($kendaraan->foto_kendaraan_tampak_belakang) {
                File::delete('dokumen/' . $kendaraan->foto_kendaraan_tampak_belakang);
            }

            if ($kendaraan->foto_kendaraan_dengan_pemilik) {
                File::delete('dokumen/' . $kendaraan->foto_kendaraan_dengan_pemilik);
            }

            if ($kendaraan->image_qr) {
                File::delete('kendaraan/' . $kendaraan->image_qr);
            }

            $kendaraan->delete($id);
            return ResponseFormatter::success([
                'message' => 'Success'
            ], 'Delete Vehicle Succesfully');
        } catch (\Throwable $th) {
            return ResponseFormatter::error(
            $th->getMessage(),
             'Delete Vehicle Failed', 500);
        }
    }
}
