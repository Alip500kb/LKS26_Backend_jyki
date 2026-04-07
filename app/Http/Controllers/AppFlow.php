<?php

namespace App\Http\Controllers;

use App\Models\business_verification;
use App\Models\financing_application;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AppFlow extends Controller
{
    public function verifikasi_bisnis(Request $request) {

        $valid = Validator::make($request->all(), [
            'nama_usaha' => 'required',
            'nib' => 'required',
            'npwp' => 'required',
            'omzet_bulanan' => 'required|numeric',
            'jumlah_karyawan' => 'required|numeric',
            'lama_usaha' => 'required|numeric'
        ]);

        if ($valid->fails()) {
            return response()->json($valid->errors(),422);
        } elseif (business_verification::where('user_id', $request->user()->id)->exists()) {
            return response()->json([
                'status' => 'gagal',
                'message' => 'kamu memiliki pengajuan yang aktif'
            ],403);
        } elseif (!$request->user()->role == 'applicant') {
            return response()->json([
                'status' => 'ditolak',
                'message' => 'kamu bukan applicant'
            ],403);
        }


        $formulir = business_verification::create([
            'user_id' => $request->user()->id,
            'nama_usaha' => $request->nama_usaha,
            'nib' => $request->nib,
            'npwp' => $request->npwp,
            'omzet_bulanan' => $request->omzet_bulanan,
            'jumlah_karyawan' => $request->jumlah_karyawan,
            'lama_usaha_tahun' => $request->lama_usaha,
            'status' => 'submitted',
        ]);

        return response()->json([
            'status' => 'berhasil',
            'id' => $formulir->id
        ],201);
    }

    public function verifikasi_oleh_verifier(Request $request,$id) {
        $valid = Validator::make($request->all(), [
            'status' => 'required',
            'rejected_reason' => 'sometimes'
        ]);

        $verifikasi = business_verification::where('id', $id)->first();

        if ($valid->fails()) {
            return response()->json($valid->errors(),422);
        } elseif ($request->user()->role != 'verifier') {
            return response()->json([
                'status' => 'ditolak',
                'message' => 'anda bukan verifier'
            ],403);
        } elseif (!$verifikasi) {
            return response()->json(['status' => 'tidak ditemukan'],404);
        }

        $data = $request->only(['status','rejected_reason']);

        if ($request->status == 'rejected' && !$request->filled('rejected_reason')) {
            return response()->json([
                'message' => 'data reason belum ter isi'
            ],422);
        } elseif ($request->status == 'verified') {
            $verifikasi->update([
                'status' => $request->status
            ]);
        }
        else {
            $verifikasi->update($data);
            $verifikasi->update([
                'verified_by' => $request->user()->id,
                'verified_at' => Carbon::now()
            ]);
        }
        return response()->json([
                'status' => 'berhasil',
                'id' => $verifikasi->id
            ],200);
    }

    public function pengajuan_pembiayaan(Request $request) {
        $aplikasi_bisnis = business_verification::where('user_id', $request->user()->id)->first();

        $valid = Validator::make($request->all(), [
            'jumlah_pembiayaan' => 'required|numeric',
            'tenor' => 'required|numeric',
            'tujuan_pembiayaan' => 'required'
        ]);

        if ($valid->fails()) {
            return response()->json($valid->errors(),422);
        } elseif ((!$aplikasi_bisnis) || ($aplikasi_bisnis->status != 'verified')) {
            return response()->json([
                'status' => 'ditolak',
                'message' => 'anda tidak memiliki verifikasi bisnis yang sah/ ditolak'
            ],403);
        } elseif ($request->jumlah_pembiayaan > ($aplikasi_bisnis->omzet_bulanan * 3)) {
            return response()->json([
                'message' => 'anda melebihi batas pembiayaan yaitu ' . $aplikasi_bisnis->omzet_bulanan * 3
            ],403);
        } elseif ($aplikasi_bisnis->lama_usaha_tahun <= 1 ) {
            return response()->json([
                'message' => 'usaha anda masih terlalu baru, minimal berjalan selama lebih dari 1 tahun.Tetap semangat!!'
            ],403);
        } elseif (financing_application::where('user_id', $request->user()->id)->exists()) {
            return response()->json([
                'message' => 'anda hanya dapat mengajukan sekali'
            ],403);
        }

        financing_application::create([
            'user_id' => $request->user()->id,
            'business_verification_id' => $aplikasi_bisnis->id,
            'jumlah_pembiayaan' => $request->jumlah_pembiayaan,
            'tenor_bulan' => $request->tenor,
            'tujuan_pembiayaan' => $request->tujuan_pembiayaan,
            'status' => 'submitted',
            'submitted_at' => Carbon::now()
        ]);

        return response()->json([
            'status' => 'berhasil',
            'messagge' => 'terimakasih,aplikasi anda sedang kami riview'
        ],201);
    }

    public function analisis_peminjaman(Request $request,$id) {
        $ajuan_pembiayaan = financing_application::where('id', $id)->first();
        $valid = Validator::make($request->all(), [
            'skor_kelayakan' => 'numeric|required',
            'rekomendasi_limit' => 'required|numeric',
            'catatan_analisis' => 'required',
            'status' => 'required'
        ]);

        $status = $request->status;
        if ($request->user()->role == 'applicant') {
            return response()->json([
                'message' => 'anda tidak memiliki otorisasi untuk melakukan ini'
            ],403);
        } elseif ($valid->fails()) {
            return response()->json($valid->errors(),422);
        } elseif (!$ajuan_pembiayaan) {
            return response()->json(['status' => 'tidak ditemukan'],404);
        } elseif ($request->status == 'rejected') {
            $status = 'rejected_by_' . $request->user()->role;
        }
        // dd($status);
        $ajuan_pembiayaan->update([
            'skor_kelayakan' => $request->skor_kelayakan,
            'rekomendasi_limit' => $request->rekomendasi_limit,
            'catatan_analisis' => $request->catatan_analisis,
            'status' => $status
        ]);

        if (($request->user()->role == 'manager') && ($request->status == 'approved')) {
            $ajuan_pembiayaan->update([
                'approved_at' => Carbon::now()
            ]);
        }

        return response()->json([
            'message' => 'berhasil',
            'id' => $id,
        ]);
    }
}
