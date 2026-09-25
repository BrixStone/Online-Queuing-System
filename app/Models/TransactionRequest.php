<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransactionRequest extends Model
{
    use HasUuids;

    protected $table = 'transaction_requests';

    protected $fillable = [
        'student_id',
        'description',
        'status',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(
            Student::class,
            'student_id'
        );
    }

    public function queueTicket(): HasOne
    {
        return $this->hasOne(
            QueueTicket::class,
            'transaction_request_id'
        );
    }
}
