<?php

namespace App\Http\Controllers\API;

use App\Helper\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\DetailLokasiModel;
use App\Models\SaldoModel;
use App\Models\TransaksiModel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ResponseFormatter::success(TransaksiModel::join('kendaraan', 'transaksi.kendaraan_id', '=', 'kendaraan.id')
            ->where('kendaraan.user_id', Auth::user()->nomor_identitas)->orderBy('created_at', 'desc')->get(), 'Successfully got data History');
    }

    public function getParkirNotPay()
    {
        return ResponseFormatter::success(TransaksiModel::join('kendaraan', 'transaksi.kendaraan_id', '=', 'kendaraan.id')
            ->where('kendaraan.user_id', Auth::user()->nomor_identitas)->where('kendaraan.status_keluar_masuk', 0)
            ->orderBy('created_at', 'desc')->get(), 'Successfully got data History');
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
    public function store(Request $request, $id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $validator = Validator::make($request->all(), [
            'kendaraan_id' => ['required', 'string', 'max:15'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        try {
            $data = TransaksiModel::create([
                'harga_akhir' => null,
                'tanggal' => date('Y-m-d'),
                'status' => 0,
                'status_keluar_masuk' => 0,
                'detail_lokasi_id' => $id,
                'kendaraan_id' => $request->kendaraan_id,
                'jam_masuk' => date('H:i:s'),
                'jam_keluar' => null,
            ]);

            return ResponseFormatter::success([
                'message' => 'Success',
                'data' => $data
            ], 'Parkir Successfully');
        } catch (\Throwable $th) {
            return ResponseFormatter::error([
                'message' => 'something went wrong',
                'error' => $th
            ], 'Parkir Unsuccessfully', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $transaksi = TransaksiModel::find($id);
        $validator = Validator::make($request->all(), [
            'transaksi_id' => ['required', 'string', 'max:15'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        DB::beginTransaction();
        try {
            $kendaraan = TransaksiModel::where('id', $id)->update([
                'harga_akhir' =>   $transaksi->detail_lokasi->harga_tiket,
                'status' => 1,
                'status_keluar_masuk' => 1,
                'jam_keluar' => date('H:i:s'),
            ]);

            $saldo = SaldoModel::create([
                'user_id' =>  Auth::user()->nomor_identitas,
                'nominal' =>  $transaksi->detail_lokasi->harga_tiket,
                'tanggal' => date('Y-m-d'),
                'status' => 0
            ]);

            DB::commit();
            return ResponseFormatter::success([
                'message' => 'Success',
                'data' => $kendaraan
            ], 'Transaction Successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseFormatter::error([
                'message' => 'something went wrong',
                'error' => $th
            ], 'Transaction Unsuccessfully', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
