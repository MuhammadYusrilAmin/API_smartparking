<?php

namespace App\Http\Controllers\API;

use App\Helper\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\DetailLokasiModel;
use App\Models\SaldoModel;
use App\Models\TransaksiModel;
use chillerlan\QRCode\{QRCode, QROptions};
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
        return ResponseFormatter::success(TransaksiModel::where('user_id', Auth::user()->nomor_identitas)
            ->orderBy('created_at', 'desc')->get(), 'Successfully got data History');
    }

    public function getParkirSaatIni()
    {
        return ResponseFormatter::success(TransaksiModel::where('user_id', Auth::user()->nomor_identitas)
            ->where('status_keluar_masuk', 0)
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
            $transaksi = TransaksiModel::create([
                'harga_akhir' => null,
                'user_id' => Auth::user()->nomor_identitas,
                'tanggal' => date('Y-m-d'),
                'status' => 0,
                'status_keluar_masuk' => 0,
                'detail_lokasi_id' => $id,
                'kendaraan_id' => $request->kendaraan_id,
                'jam_masuk' => date('H:i:s'),
                'jam_keluar' => null,
            ]);

            // generate qr code 
            $data =  $transaksi->id; // Ganti dengan data yang sesuai\
            $path = public_path('tiket/'); // Tentukan lokasi untuk menyimpan kode QR

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $fileName = 'tiket_' . uniqid() . '.png'; // Nama file kode QR
            $filePath = $path . $fileName; // Path lengkap ke file kode QR

            $options = new QROptions([
                'version'        => 5,
                'outputType'     => QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel'       => QRCode::ECC_L,
                'imageBase64'    => false,
                'imageTransparency' => false, // Set latar belakang menjadi solid
                'bgColor'        => [255, 255, 255], // Warna latar belakang (putih)
            ]);

            TransaksiModel::where('id', $data)->update(['image_qr' => $fileName]);

            DetailLokasiModel::where('id', $id)->update(['status' => 1]);

            $get_data = TransaksiModel::where('id', $data)->first();

            $qrcode = new QRCode($options);
            $qrcode->render($data, $filePath);

            return ResponseFormatter::success([
                $get_data
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
    }

    public function pay(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $validator = Validator::make($request->all(), [
            'transaksi_id' => ['required', 'string', 'max:15'],
        ]);
        $transaksi = TransaksiModel::find($request->transaksi_id);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        DB::beginTransaction();
        try {
            TransaksiModel::where('id', $request->transaksi_id)->update([
                'harga_akhir' =>   $transaksi->detail_lokasi->harga_tiket,
                'status' => 1,
                'status_keluar_masuk' => 0,
                'jam_keluar' => date('H:i:s'),
            ]);

            $saldo = SaldoModel::create([
                'user_id' =>  Auth::user()->nomor_identitas,
                'nominal' =>  $transaksi->detail_lokasi->harga_tiket,
                'tanggal' => date('Y-m-d'),
                'status' => 0
            ]);


            $data = TransaksiModel::find($request->transaksi_id);

            DB::commit();
            return ResponseFormatter::success([
                 $data
            ], 'Transaction Successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseFormatter::error([
                'message' => 'something went wrong',
                'error' => $th
            ], 'Transaction Unsuccessfully', 500);
        }
    }


    public function out(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $validator = Validator::make($request->all(), [
            'transaksi_id' => ['required', 'string', 'max:15'],
        ]);
        $transaksi = TransaksiModel::find($request->transaksi_id);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        DB::beginTransaction();
        try {
            TransaksiModel::where('id', $request->transaksi_id)->update([
                'status_keluar_masuk' => 1,
            ]);

            DetailLokasiModel::where('id', $transaksi->detail_lokasi_id)->update(['status' => 0]);

            $data = TransaksiModel::find($request->transaksi_id);
            DB::commit();
            return ResponseFormatter::success([
                $data
            ], 'get out Successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseFormatter::error([
                'message' => 'something went wrong',
                'error' => $th
            ], 'get out Unsuccessfully', 500);
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
