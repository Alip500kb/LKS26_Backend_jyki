<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class keuangan extends Controller
{
    public function pengajuan_pembiyaan(Request $request) {
        $valid = Validator::make($request->all(), [
            'pembiayaan' => 'required|numeric',
            'tenor' => 'required|numeric',
            'tujuan_pembiayaan' => 'required'
        ]);

        
    }
}
