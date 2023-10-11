<?php

namespace App\Http\Controllers\API;

use App\Helper\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\KendaraanModel;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class KendaraanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ResponseFormatter::success(KendaraanModel::where('user_id', Auth::user()->nomor_identitas)->orderBy('created_at', 'desc')->get(), 'Data profile user berhasil diambil');
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
            'no_plat' => ['required', 'string', 'max:255', 'min:3'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

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

            KendaraanModel::create([
                'nama_kendaraan' => $request->nama_kendaraan,
                'user_id' => Auth::user()->nomor_identitas,
                'no_plat' => $request->no_plat,
                'foto_stnk' => $foto_stnk,
                'foto_kendaraan_tampak_depan' => $foto_kendaraan_tampak_depan,
                'foto_kendaraan_tampak_belakang' => $foto_kendaraan_tampak_belakang,
                'foto_kendaraan_dengan_pemilik' => $foto_kendaraan_dengan_pemilik
            ]);

            return ResponseFormatter::success([
                'message' => 'Success',
            ], 'Create Vehicle Successfully');
        } catch (\Throwable $th) {
            return ResponseFormatter::error([
                'message' => 'something went wrong',
                'error' => $th
            ], 'Create Vehicle Unsuccessfully', 500);
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $kendaraan = KendaraanModel::find($id);
            $data = $request->only('nama_kendaraan', 'no_plat', 'foto_stnk', 'foto_kendaraan_tampak_depan', 'foto_kendaraan_tampak_belakang', 'foto_kendaraan_dengan_pemilik');

            if ($request->nama_kendaraan != $kendaraan->nama_kendaraan) {
                $isExistNamaKendaraan = KendaraanModel::where('nama_kendaraan', $request->nama_kendaraan)->exists();
                if ($isExistNamaKendaraan) {
                    return response()->json(['message' => 'Nama Kendaraan already taken'], 409);
                }
            }

            if ($request->no_plat != $kendaraan->no_plat) {
                $isExistno_plat = KendaraanModel::where('no_plat', $request->no_plat)->exists();
                if ($isExistno_plat) {
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
            return ResponseFormatter::success([
                $kendaraan
            ], 'Update Vehicle Succesfully');
        } catch (\Throwable $th) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $th->getMessage(),
            ], 'Update Vehicle Failed', 500);
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


            $kendaraan->delete($id);
            return ResponseFormatter::success([
                'message' => 'Success'
            ], 'Delete Vehicle Succesfully');
        } catch (\Throwable $th) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $th->getMessage(),
            ], 'Delete Vehicle Failed', 500);
        }
    }
}
