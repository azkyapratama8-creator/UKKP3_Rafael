<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketReservation extends Model
{
    protected $fillable = [
        'nomor_tiket',
        'user_id',
        'status',
    ];
}
