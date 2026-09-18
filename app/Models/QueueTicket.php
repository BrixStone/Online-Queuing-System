<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueTicket extends Model
{
    protected $guarded = [];

    // Queue Statuses
    public const STATUS_HOLDING = 'holding';       // Waiting in virtual line
    public const STATUS_ACTIVE = 'active';         // In physical line, waiting for cashier
    public const STATUS_SERVING = 'serving';       // Currently at the counter
    public const STATUS_COMPLETED = 'completed';   // Finished
    public const STATUS_HELD = 'held';             // No show / Put on hold by cashier

    // Scopes to easily grab tickets by status
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