<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\TransactionRequest;

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
        'queue_date',
        'access_token',
        'transaction_request_id',
    ];

    public const STATUS_HOLDING = 'holding';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SERVING = 'serving';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_HELD = 'held';

    protected $casts = [
        'queue_date' => 'date',
    ];

    public function transactionRequest(): BelongsTo {
        return $this->belongsTo(TransactionRequest::class, 'transaction_request_id');
    }
    public function scopeHolding($query)
    {
        return $query->where('status', self::STATUS_HOLDING)->orderBy('created_at', 'asc');
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)->orderBy('created_at', 'asc');
    }

    public function scopeServing($query)
    {
        return $query->where('status', self::STATUS_SERVING);
    }
}
