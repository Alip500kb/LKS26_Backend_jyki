<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class installment extends Model
{
    use HasUuids,Notifiable;
    protected $fillable = [
        'financing_application_id',
        'installment_number',
        'jatuh_tempo',
        'nominal_pokok',
        'nominal_bunga',
        'total_cicilan',
        'status',
        'paid_at'
    ];
}
