<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class application_log extends Model
{
    use Notifiable, HasUuids;
    protected $fillable = [
        'financing_application_id',
        'status_from',
        'status_to',
        'role',
        'user_id',
        'notes'
    ];
}
