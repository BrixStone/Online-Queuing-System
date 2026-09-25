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

        $queue = DB::transaction(function () use (
            $today,
            $student,
            $validated
        ) {
            $existingTicket = QueueTicket::where('queue_date', $today)
                ->whereIn('status', [
                    QueueTicket::STATUS_HOLDING,
                    QueueTicket::STATUS_ACTIVE,
                    QueueTicket::STATUS_SERVING,
                ])
                ->whereHas('transactionRequest', function ($query) use ($student) {
                    $query->where('student_id', $student->id);
                })
                ->lockForUpdate()
                ->first();

            if ($existingTicket) {
                return $existingTicket;
            }

            $counter = DB::table('queue_counters')
                ->where('queue_date', $today)
                ->lockForUpdate()
                ->first();

            if (!$counter) {
                DB::table('queue_counters')->insert([
                    'queue_date' => $today,
                    'last_number' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $counter = DB::table('queue_counters')
                    ->where('queue_date', $today)
                    ->lockForUpdate()
                    ->first();
            }

            $nextNumber = $counter->last_number + 1;

            DB::table('queue_counters')
                ->where('id', $counter->id)
                ->update([
                    'last_number' => $nextNumber,
                    'updated_at' => now(),
                ]);

            $trackingNumber = 'A' . str_pad(
                $nextNumber,
                3,
                '0',
                STR_PAD_LEFT
            );

            $transactionRequest = TransactionRequest::create([
                'student_id' => $student->id,
                'description' => $validated['purpose'],
                'status' => 'pending',
            ]);

            return QueueTicket::create([
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
        });

        return redirect()->route('queue.status', [
            'token' => $queue->access_token
        ]);
    }
}
