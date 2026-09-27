<?php

namespace App\Http\Controllers;

use App\Models\QueueTicket;
use App\Models\Student; // change
use App\Models\TransactionRequest; // change
use Illuminate\Support\Facades\DB; // change
use Illuminate\Support\Str; // change
use App\Models\Ticket; //change

class QueueController extends Controller
{
    const MAX_ACTIVE_CAPACITY = 5;
    #change here
    public function requestQueue(string $studentName,
        string $studentNumber,
        string $purpose,
        string $mobileNumber,
        ?string $deviceId = null,
        ?string $platform = null)
    {
        #change here
        $student = Student::where(
        'student_number',
        $studentNumber
    )->first();


    //change here if the student not found
    if (!$student) {

        throw new \Exception(
            'Student number was not found.'
        );
    }


    //change here
    return DB::transaction(function () use (
        $student,
        $purpose,
        $mobileNumber,
        $deviceId,
        $platform,
    ) {


        $transactionRequest = TransactionRequest::create([

            // Student UUID
            'student_id' => $student->id,

            // PURPOSE IS STORED HERE
            'description' => $purpose,

            // Initial transaction status
            'status' => 'pending',
        ]);

        #___________________

        $count = QueueTicket::count();

        $trackingNumber =
            'TKT-' .
            str_pad(
                $count + 1,
                3,
                '0',
                STR_PAD_LEFT
            );

        $ticket = QueueTicket::create([

            // Student UUID
            'student_id' => $student->id,

            // CHANGED:
            // Connect queue ticket to transaction request
            'transaction_request_id' => $transactionRequest->id,

            // School USN
            'name' => $studentNumber,

            // Generated tracking number
            'tracking_number' => $trackingNumber,

            // Device information
            'device_id' => $deviceId,

            // Platform
            'platform' => $platform,

            // Mobile number
            'mobile_number' => $mobileNumber,

            // Initial queue status
            'status' => QueueTicket::STATUS_HOLDING,

            // Queue date
            'queue_date' => now()->toDateString(),

            // Tracking page token
            'access_token' => Str::uuid(),
        ]);

        $this->fillActiveQueue();

        return $ticket;
        });
    }

    public function startQueue(string $tellerName)
    {
        $this->fillActiveQueue();
    }

    public function stopQueue(string $tellerName)
    {
        $serving = QueueTicket::serving()
            ->where(
                'assigned_teller',
                $tellerName
            )
            ->first();

        if ($serving) {
            $serving->update([
                'status' => QueueTicket::STATUS_HELD,
            ]);

            $this->fillActiveQueue();
        }

        return $serving;
    }

    public function callNext(string $tellerName)
    {
        $nextInLine = QueueTicket::active()->first();

        if (!$nextInLine) {
            return null;
        }

        $nextInLine->startServing($tellerName);

        $this->fillActiveQueue();

        return $nextInLine->fresh();
    }

    public function holdCurrent(string $tellerName)
    {
        $serving = QueueTicket::serving()
            ->where(
                'assigned_teller',
                $tellerName
            )
            ->first();

        if ($serving) {
            $serving->update([
                'status' => QueueTicket::STATUS_HELD,
            ]);

            $this->fillActiveQueue();
        }

        return $serving;
    }

    public function completeCurrent(string $tellerName)
    {
        $serving = QueueTicket::serving()
            ->where(
                'assigned_teller',
                $tellerName
            )
            ->first();

        if ($serving) {
            $serving->completeServing();
        }

        return $serving;
    }

    public function fillActiveQueue(string $tellerName = null)
    {
        $activeCount = QueueTicket::active()->count();

        while ($activeCount < self::MAX_ACTIVE_CAPACITY) {
            $nextHolding = QueueTicket::holding()->first();

            if (!$nextHolding) {
                break;
            }

            $nextHolding->update([
                'status' => QueueTicket::STATUS_ACTIVE,
                'assigned_teller' => null,
            ]);

            $activeCount++;
        }
    }
}
