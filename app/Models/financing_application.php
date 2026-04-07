<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class financing_application extends Model
{
    use Notifiable, HasUuids;
    protected $fillable = [
        'user_id',
        'business_verification_id',
        'jumlah_pembiayaan',
        'tenor_bulan',
        'tujuan_pembiayaan',
        'skor_kelayakan',
        'rekomendasi_limit',
        'catatan_analisis',
        'status',
        'submitted_at',
        'approved_at',
        'rejected_reason'
    ];
}
