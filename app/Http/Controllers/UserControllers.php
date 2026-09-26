<?php

namespace App\Http\Controllers;

use App\Models\PhoneVerification;
use App\Models\QueueTicket;
use App\Models\Student;
use App\Services\TextBeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserControllers extends Controller
{
    /**
     * Handle queue registration.
     *
     * Flow:
     * 1. Validate form
     * 2. Find student
     * 3. Check for existing active ticket
     * 4. Generate OTP
     * 5. Send OTP through TextBee
     * 6. Store registration data in session
     * 7. Redirect to OTP verification page
     */
    public function store(
        Request $request,
        TextBeeService $textBee
    ) {
        /*
         * ---------------------------------------------------------
         * 1. VALIDATE FORM
         * ---------------------------------------------------------
         */

        $validated = $request->validate([
            'student_number' => [
                'required',
                'string',
            ],

            'purpose' => [
                'required',
                'string',
            ],

            'device_id' => [
                'required',
                'uuid',
            ],

            'mobile_number' => [
                'required',
                'regex:/^09\d{9}$/',
            ],

            'platform' => [
                'required',
                'string',
            ],
        ]);


        /*
         * ---------------------------------------------------------
         * 2. FIND STUDENT
         * ---------------------------------------------------------
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
         * ---------------------------------------------------------
         * 3. CHECK IF STUDENT ALREADY HAS AN ACTIVE TICKET TODAY
         * ---------------------------------------------------------
         *
         * We check directly using queue_tickets.student_id.
         *
         * Active statuses:
         * - holding
         * - active
         * - serving
         */

        $today = now()->toDateString();

        $existingTicket = QueueTicket::where(
            'queue_date',
            $today
        )
            ->where(
                'student_id',
                $student->id
            )
            ->whereIn('status', [
                QueueTicket::STATUS_HOLDING,
                QueueTicket::STATUS_ACTIVE,
                QueueTicket::STATUS_SERVING,
            ])
            ->first();


        /*
         * If an active ticket already exists,
         * don't send another OTP and don't create another ticket.
         */

        if ($existingTicket) {

            return redirect()->route(
                'queue.status',
                [
                    'token' => $existingTicket->access_token,
                ]
            )->with(
                'error',
                'You already have an active queue ticket today: ' .
                    $existingTicket->tracking_number
            );
        }


        /*
         * ---------------------------------------------------------
         * 4. NORMALIZE MOBILE NUMBER
         * ---------------------------------------------------------
         */

        $mobileNumber = $this->normalizePhilippineNumber(
            $validated['mobile_number']
        );


        /*
         * ---------------------------------------------------------
         * 5. GENERATE OTP
         * ---------------------------------------------------------
         */

        $otp = (string) random_int(
            100000,
            999999
        );


        /*
         * ---------------------------------------------------------
         * 6. STORE HASHED OTP
         * ---------------------------------------------------------
         *
         * We don't store the actual OTP in Supabase.
         */

        $verification = PhoneVerification::create([
            'mobile_number' => $mobileNumber,
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
            'verified' => false,
            'verified_at' => null,
        ]);


        /*
         * ---------------------------------------------------------
         * 7. SEND OTP THROUGH TEXTBEE
         * ---------------------------------------------------------
         */

        $message =
            "ACLC Mandaue Queue\n\n" .
            "Your verification code is: {$otp}\n\n" .
            "This code expires in 5 minutes.\n" .
            "Do not share this code with anyone.";


        try {

            $textBee->sendSms(
                $mobileNumber,
                $message
            );
        } catch (\Throwable $e) {

            Log::error(
                'TextBee OTP sending failed',
                [
                    'mobile_number' => $mobileNumber,
                    'student_number' => $student->student_number,
                    'error' => $e->getMessage(),
                ]
            );


            return back()
                ->withInput()
                ->with(
                    'error',
                    'We could not send the verification code. Please try again.'
                );
        }


        /*
         * ---------------------------------------------------------
         * 8. STORE REGISTRATION DATA IN SESSION
         * ---------------------------------------------------------
         *
         * We need this information after OTP verification.
         *
         * The queue ticket is NOT created yet.
         */

        session([
            'queue_registration' => [
                'student_id' => $student->id,
                'student_number' => $student->student_number,
                'purpose' => $validated['purpose'],
                'device_id' => $validated['device_id'],
                'mobile_number' => $mobileNumber,
                'platform' => $validated['platform'],
                'verification_id' => $verification->id,
            ],
        ]);


        /*
         * ---------------------------------------------------------
         * 9. REDIRECT TO OTP VERIFICATION PAGE
         * ---------------------------------------------------------
         */

        return redirect()
            ->route('phone.verify')
            ->with(
                'success',
                'We have sent a verification code to your mobile number.'
            );
    }


    /**
     * Normalize Philippine mobile numbers.
     *
     * Examples:
     *
     * 09171234567
     *      ↓
     * +639171234567
     *
     * 639171234567
     *      ↓
     * +639171234567
     *
     * 9171234567
     *      ↓
     * +639171234567
     */
    private function normalizePhilippineNumber(
        string $number
    ): string {

        /*
         * Remove spaces, dashes, parentheses, etc.
         */
        $number = preg_replace(
            '/\D/',
            '',
            $number
        );


        /*
         * 09XXXXXXXXX
         */
        if (str_starts_with($number, '09')) {

            return '+63' .
                substr($number, 1);
        }


        /*
         * 63XXXXXXXXXX
         */
        if (str_starts_with($number, '63')) {

            return '+' . $number;
        }


        /*
         * 9XXXXXXXXX
         */
        if (str_starts_with($number, '9')) {

            return '+63' . $number;
        }


        return $number;
    }
}
