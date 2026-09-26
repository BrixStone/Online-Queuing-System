<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\TransactionRequest;
use App\Models\Student;


class QueueTicket extends Model
{
    protected $table = 'queue_tickets';


    protected $fillable = [
        'student_id',
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
        'serving_started_at',
        'arrived_at',
    ];



    public const STATUS_HOLDING = 'holding';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_SERVING = 'serving';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_HELD = 'held';

    public const STATUS_NO_SHOW = 'no_show';



    // Maximum number of customers in active status.
    public const MAX_ACTIVE = 5;

    // Customer has 3 minutes to press "I'm Here".
    public const ARRIVAL_MINUTES = 3;


    protected $casts = [
        'queue_date' => 'date',

        'serving_started_at' => 'datetime',

        'arrived_at' => 'datetime',
    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function student(): BelongsTo
    {
        return $this->belongsTo(
            Student::class,
            'student_id'
        );
    }


    public function transactionRequest(): BelongsTo
    {
        return $this->belongsTo(
            TransactionRequest::class,
            'transaction_request_id'
        );
    }
    public function scopeHolding($query)
    {
        return $query
            ->where(
                'status',
                self::STATUS_HOLDING
            )
            ->orderBy(
                'created_at',
                'asc'
            );
    }


    public function scopeActive($query)
    {
        return $query
            ->where(
                'status',
                self::STATUS_ACTIVE
            )
            ->orderBy(
                'created_at',
                'asc'
            );
    }


    public function scopeServing($query)
    {
        return $query->where(
            'status',
            self::STATUS_SERVING
        );
    }




    public function startServing(?string $teller = null): void
    {
        $this->update([
            'status' => self::STATUS_SERVING,

            'assigned_teller' => $teller,

            // Start the 3-minute countdown.
            'serving_started_at' => now(),

            // Reset previous arrival confirmation.
            'arrived_at' => null,
        ]);
    }




    public function markArrived(): bool
    {


        if ($this->status !== self::STATUS_SERVING) {
            return false;
        }




        if (!$this->serving_started_at) {
            return false;
        }




        $expiresAt = $this->serving_started_at
            ->copy()
            ->addMinutes(self::ARRIVAL_MINUTES);




        if (now()->greaterThanOrEqualTo($expiresAt)) {

            $this->markNoShow();

            return false;
        }




        $this->update([
            'arrived_at' => now(),
        ]);


        return true;
    }




    public function markNoShow(): void
    {
        $this->update([
            'status' => self::STATUS_NO_SHOW,
        ]);




        self::maintainActiveQueue();
    }




    public function completeServing(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
        ]);




        self::maintainActiveQueue();
    }


    public static function maintainActiveQueue(): void
    {


        $activeCount = self::where(
            'status',
            self::STATUS_ACTIVE
        )->count();




        $slotsAvailable =
            self::MAX_ACTIVE - $activeCount;


        if ($slotsAvailable <= 0) {
            return;
        }




        $tickets = self::where(
            'status',
            self::STATUS_HOLDING
        )
            ->orderBy(
                'created_at',
                'asc'
            )
            ->limit(
                $slotsAvailable
            )
            ->get();



        foreach ($tickets as $ticket) {

            $ticket->update([
                'status' => self::STATUS_ACTIVE,
            ]);
        }
    }




    public function checkNoShow(): bool
    {

        if ($this->status !== self::STATUS_SERVING) {
            return false;
        }




        if (!$this->serving_started_at) {
            return false;
        }




        if ($this->arrived_at) {
            return false;
        }



        $expiresAt = $this->serving_started_at
            ->copy()
            ->addMinutes(self::ARRIVAL_MINUTES);




        if (now()->greaterThanOrEqualTo($expiresAt)) {

            $this->markNoShow();

            return true;
        }


        return false;
    }
}
