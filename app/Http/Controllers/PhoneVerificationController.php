<?php

namespace App\Http\Controllers;

use App\Models\PhoneVerification;
use App\Models\QueueTicket;
use App\Models\TransactionRequest;
use App\Models\Student;
use App\Services\TextBeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PhoneVerificationController extends Controller
{
    public function show()
    {
        $registration = session('queue_registration');

        if (!$registration) {
            return redirect('/')
                ->with('error', 'Your registration session has expired.');
        }

        $verification = PhoneVerification::find(
            $registration['verification_id']
        );

        if (!$verification || $verification->verified) {
            return redirect('/')
                ->with('error', 'Invalid verification request.');
        }

        return view('verify-phone', [
            'mobile_number' => $registration['mobile_number'],
            'expires_at' => $verification->expires_at,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'otp' => [
                'required',
                'digits:6',
            ],
        ]);

        $registration = session('queue_registration');

        if (!$registration) {
            return redirect('/')
                ->with('error', 'Your registration session has expired.');
        }

        $verification = PhoneVerification::find(
            $registration['verification_id']
        );

        if (!$verification || $verification->verified) {
            return back()
                ->withInput()
                ->with('error', 'Invalid verification request.');
        }

        if ($verification->expires_at->isPast()) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Your verification code has expired. Please request a new code.'
                );
        }

        if ($verification->attempts >= 5) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Too many incorrect attempts. Please request a new code.'
                );
        }

        if (!Hash::check(
            $validated['otp'],
            $verification->otp_hash
        )) {
            $verification->increment('attempts');

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Incorrect verification code.'
                );
        }

        $verification->update([
            'verified' => true,
            'verified_at' => now(),
        ]);

        $queue = DB::transaction(function () use ($registration) {
            $today = now()->toDateString();

            $existingTicket = QueueTicket::where(
                'queue_date',
                $today
            )
                ->where(
                    'student_id',
                    $registration['student_id']
                )
                ->whereIn('status', [
                    QueueTicket::STATUS_HOLDING,
                    QueueTicket::STATUS_ACTIVE,
                    QueueTicket::STATUS_SERVING,
                ])
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

            $student = Student::find(
                $registration['student_id']
            );

            $transactionRequest = TransactionRequest::create([
                'student_id' => $registration['student_id'],
                'description' => $registration['purpose'],
                'status' => 'pending',
            ]);

            return QueueTicket::create([
                'student_id' => $registration['student_id'],
                'name' => $student->first_name . ' ' . $student->last_name,
                'mobile_number' => $registration['mobile_number'],
                'device_id' => $registration['device_id'],
                'platform' => $registration['platform'],
                'tracking_number' => $trackingNumber,
                'status' => QueueTicket::STATUS_HOLDING,
                'access_token' => (string) Str::uuid(),
                'queue_date' => $today,
                'transaction_request_id' => $transactionRequest->id,
            ]);
        });

        session()->forget('queue_registration');

        return redirect()->route(
            'queue.status',
            [
                'token' => $queue->access_token,
            ]
        );
    }

    public function resendOtp(
        Request $request,
        TextBeeService $textBee
    ) {
        $registration = session('queue_registration');

        if (!$registration) {
            return redirect('/')
                ->with('error', 'Your registration session has expired.');
        }

        $oldVerification = PhoneVerification::find(
            $registration['verification_id']
        );

        if (!$oldVerification) {
            return back()
                ->with(
                    'error',
                    'Verification request not found.'
                );
        }

        if ($oldVerification->verified) {
            return redirect('/')
                ->with(
                    'error',
                    'Your phone number has already been verified.'
                );
        }

        if ($oldVerification->expires_at->isFuture()) {
            return back()
                ->with(
                    'error',
                    'Your current verification code has not expired yet.'
                );
        }

        $otp = (string) random_int(100000, 999999);

        $verification = PhoneVerification::create([
            'mobile_number' => $registration['mobile_number'],
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
            'verified' => false,
            'verified_at' => null,
        ]);

        $message =
            "ACLC Mandaue Queue\n\n" .
            "Your verification code is: {$otp}\n\n" .
            "This code expires in 5 minutes.\n" .
            "Do not share this code with anyone.";

        try {
            $textBee->sendSms(
                $registration['mobile_number'],
                $message
            );
        } catch (\Throwable $e) {
            $verification->delete();

            return back()
                ->with(
                    'error',
                    'We could not send the verification code. Please try again.'
                );
        }

        session([
            'queue_registration.verification_id' =>
            $verification->id,
        ]);

        return back()->with(
            'success',
            'A new verification code has been sent.'
        );
    }
}
