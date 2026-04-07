<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class business_verification extends Model
{
    use Notifiable, HasUuids;
    protected $fillable = [
        'user_id',
        'nama_usaha',
        'nib',
        'npwp',
        'omzet_bulanan',
        'jumlah_karyawan',
        'jumlah_karyawan',
        'status',
        'rejected_reason',
        'verified_by',
        'verified_at',
    ];

}
