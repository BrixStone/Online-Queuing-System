<?php

namespace App\Services;

use App\Models\QueueTicket;

class QueueNotificationService
{
    public function __construct(
        protected TextBeeService $textBee
    ) {}

    public function sendPositionSms(
        QueueTicket $ticket,
        int $position
    ): void {
        $message =
            "ACLC Mandaue Queue\n\n" .
            "Hello {$ticket->name},\n\n" .
            "Your queue ticket is {$ticket->tracking_number}.\n" .
            "Your current queue position is #{$position}.\n\n" .
            "Please stay available. You will be notified when it is your turn.";

        $this->textBee->sendSms(
            $ticket->mobile_number,
            $message
        );
    }
    public function sendServingSms(QueueTicket $ticket): void
    {
        $message =
            "ACLC Mandaue Queue\n\n" .
            "Hello {$ticket->name},\n\n" .
            "It is now your turn.\n" .
            "Queue ticket: {$ticket->tracking_number}\n\n" .
            "Please proceed to the assigned teller.";

        $this->textBee->sendSms(
            $ticket->mobile_number,
            $message
        );
    }
}
