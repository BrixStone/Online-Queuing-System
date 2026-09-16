<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueTicket extends Model
{
    protected $table = 'queue_tickets';

    protected $fillable = [
        'name',
        'tracking_number',
        'device_id',
        'platform',
        'mobile_number',
        'status',
        'assigned_teller',
    ];
}
