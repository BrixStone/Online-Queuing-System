<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\QueueTicket;
use App\Models\Student;
use App\Models\TransactionRequest;

class UserControllers extends Controller
{
    public function store(Request $request)
{
    $validated = $request->validate([
        'student_number' => ['required', 'string'],
        'purpose' => ['required', 'string'],
        'device_id' => ['required', 'uuid'],
        'mobile_number' => ['nullable', 'string', 'max:20'],
        'platform' => ['required'],
    ]);

    $today = now()->toDateString();

    /*
    |--------------------------------------------------------------------------
    | 1. Find student
    |--------------------------------------------------------------------------
    */

    $student = Student::where(
        'student_number',
        $validated['student_number']
    )->first();

    if (!$student) {
        return back()
            ->withInput()
            ->with(
                'error',
                'Student not found. Please check your student number.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Check existing active ticket today
    |--------------------------------------------------------------------------
    */

    $existingTicket = QueueTicket::where('queue_date', $today)
        ->whereIn('status', [
            QueueTicket::STATUS_HOLDING,
            QueueTicket::STATUS_ACTIVE,
            QueueTicket::STATUS_SERVING,
        ])
        ->whereHas('transactionRequest', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
        ->first();

    /*
    |--------------------------------------------------------------------------
    | 3. If ticket already exists, return to that ticket
    |--------------------------------------------------------------------------
    */

    if ($existingTicket) {
    return redirect()
        ->route('queue.status', [
            'token' => $existingTicket->access_token
        ])
        ->with(
            'warning',
            'You already have an active queue ticket today. You have been redirected to your existing ticket ' .
            $existingTicket->tracking_number . '.'
        );
}


    /*
    |--------------------------------------------------------------------------
    | 4. Generate next tracking number
    |--------------------------------------------------------------------------
    */

    $lastTicket = QueueTicket::where('queue_date', $today)
        ->orderByDesc('id')
        ->first();

    if ($lastTicket) {
        $lastNumber = (int) substr(
            $lastTicket->tracking_number,
            1
        );

        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }

    $trackingNumber = 'A' . str_pad(
        $nextNumber,
        3,
        '0',
        STR_PAD_LEFT
    );

    /*
    |--------------------------------------------------------------------------
    | 5. Create transaction request
    |--------------------------------------------------------------------------
    */

    $transactionRequest = TransactionRequest::create([
        'student_id' => $student->id,
        'description' => $validated['purpose'],
        'status' => 'pending',
    ]);

    /*
    |--------------------------------------------------------------------------
    | 6. Create queue ticket
    |--------------------------------------------------------------------------
    */

    $queue = QueueTicket::create([
        'name' => $student->first_name . ' ' . $student->last_name,
        'mobile_number' => $validated['mobile_number'] ?? null,
        'device_id' => $validated['device_id'],
        'platform' => $validated['platform'],
        'tracking_number' => $trackingNumber,
        'status' => QueueTicket::STATUS_HOLDING,
        'access_token' => (string) Str::uuid(),
        'queue_date' => $today,
        'transaction_request_id' => $transactionRequest->id,
    ]);

    /*
    |--------------------------------------------------------------------------
    | 7. Redirect to ticket
    |--------------------------------------------------------------------------
    */

    return redirect()->route('queue.status', [
        'token' => $queue->access_token
    ]);
}


    public function checkDevice(Request $request)
    {
        $deviceId = $request->query('device_id');
        $today = now()->toDateString();

        $ticket = QueueTicket::where('device_id', $deviceId)
            ->where('queue_date', $today)
            ->whereNotNull('access_token')
            ->whereNotIn('status', [
                QueueTicket::STATUS_COMPLETED
            ])
            ->first();

        return response()->json([
            'exists' => (bool) $ticket,
            'ticket' => $ticket
        ]);
    }

    public function status($token)
    {
        $queue = QueueTicket::where(
            'access_token',
            $token
        )->firstOrFail();

        return view('show', compact('queue'));
    }
}
