<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueTicket extends Model
{
    protected $guarded = [];

    public const STATUS_HOLDING = 'holding';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SERVING = 'serving';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_HELD = 'held';

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