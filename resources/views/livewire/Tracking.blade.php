<?php

use function Livewire\Volt\{state, mount};
use App\Models\QueueTicket;

state([
    'token' => null,
    'queue' => null,
    'position' => null,
]);

mount(function ($token) {
    $this->token = $token;

    $this->queue = QueueTicket::where('access_token', $token)->firstOrFail();

    $this->checkNoShow();
    $this->updatePosition();
});

$updatePosition = function () {
    if (!$this->queue) {
        $this->position = null;
        return;
    }

    $this->position =
        QueueTicket::where('created_at', '<', $this->queue->created_at)
            ->whereIn('status', [QueueTicket::STATUS_HOLDING, QueueTicket::STATUS_ACTIVE, QueueTicket::STATUS_SERVING])
            ->count() + 1;
};

$checkNoShow = function () {
    if (!$this->queue) {
        return;
    }

    $this->queue->refresh();

    if ($this->queue->status === QueueTicket::STATUS_SERVING) {
        $this->queue->checkNoShow();
        $this->queue->refresh();
    }
};

$refreshQueue = function () {
    if (!$this->token) {
        return;
    }

    $this->queue = QueueTicket::where('access_token', $this->token)->first();

    if (!$this->queue) {
        $this->position = null;
        return;
    }

    $this->checkNoShow();
    $this->updatePosition();
};

$imHere = function () {
    if (!$this->queue) {
        return;
    }

    $this->queue->refresh();

    $this->queue->markArrived();

    $this->queue->refresh();
    $this->updatePosition();
};

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Number Tracking</title>


    <!-- Google Fonts -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@200;300;400;500;600;700&display=swap"
        rel="stylesheet">


    <!-- External CSS -->

    <link rel="stylesheet" href="{{ asset('css/number-tracking.css') }}">

</head>


<body>

    <div class="number-tracking">


        <!-- ========================================================= -->
        <!-- Background -->
        <!-- ========================================================= -->

        <div class="background">

            <div class="background-image"></div>

            <div class="background-overlay"></div>

            <div class="background-gradient"></div>

        </div>


        <!-- ========================================================= -->
        <!-- Main Body -->
        <!-- ========================================================= -->

        <main class="main-body">


            <!-- ===================================================== -->
            <!-- Tracking Number -->
            <!-- ===================================================== -->

            <section class="tracking-card glass-card">

                <div class="tracking-header">

                    <div class="tracking-label">
                        YOUR TRACKING NUMBER
                    </div>

                    <div class="tracking-number">
                        {{ $queue->tracking_number ?? '---' }}
                    </div>

                    <div class="customer-name">
                        Kaiser-Zyed G. Uy
                    </div>

                </div>

            </section>


            <!-- ===================================================== -->
            <!-- Cancel Ticket -->
            <!-- ===================================================== -->

            <div class="cancel-container">

                <button class="cancel-button" type="button">
                    Cancel Ticket
                </button>

            </div>


            <!-- ===================================================== -->
            <!-- Position -->
            <!-- ===================================================== -->

            <section class="position-card glass-card">

                <div class="position-content">


                    <!-- Position Title -->

                    <div class="position-title">

                        YOU'RE IN THE<br>

                        5TH POSITION

                    </div>


                    <!-- Position Icon -->

                    <div class="position-number-circle">

                        <div class="users-icon">

                            <img class="icon-line" src="{{ asset('images/users-line-solid-full.svg') }}" alt="Line">

                        </div>

                    </div>


                    <!-- Position Progress -->

                    <div class="position-status">

                        <div class="position-status-fill"></div>

                    </div>


                    <!-- ================================================= -->
                    <!-- Reminder -->
                    <!-- ================================================= -->

                    <div class="reminder">

                        <svg class="hour" width="25" height="32" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 640 640" aria-hidden="true">

                            <path fill="#ffffff"
                                d="M192 64C156.7 64 128 92.7 128 128L128 544C128 555.5 134.2 566.2 144.2 571.8C154.2 577.4 166.5 577.3 176.4 571.4L320 485.3L463.5 571.4C473.4 577.3 485.7 577.5 495.7 571.8C505.7 566.1 512 555.5 512 544L512 128C512 92.7 483.3 64 448 64L192 64z" />

                        </svg>


                        <span>
                            KEEP AN EYE ON YOUR NUMBER!
                        </span>

                    </div>

                </div>

            </section>


            <!-- ===================================================== -->
            <!-- Queue Status -->
            <!-- ===================================================== -->

            <section class="queue-card glass-card">

                <div class="queue-content">


                    <!-- Queue Title -->

                    <div class="queue-title">
                        QUEUE STATUS
                    </div>


                    <!-- Queue Icon -->

                    <div class="queue-icon-circle">

                        <div class="bell-icon">

                            <svg class="clocks" width="52" height="52" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 640 640">


                                <path fill="rgb(255, 255, 255)"
                                    d="M320 64C461.4 64 576 178.6 576 320C576 461.4 461.4 576 320 576C178.6 576 64 461.4 64 320C64 178.6 178.6 64 320 64zM296 184L296 320C296 328 300 335.5 306.7 340L402.7 404C413.7 411.4 428.6 408.4 436 397.3C443.4 386.2 440.4 371.4 429.3 364L344 307.2L344 184C344 170.7 333.3 160 320 160C306.7 160 296 170.7 296 184z" />
                            </svg>
                        </div>

                    </div>


                    <!-- Queue Status -->

                    <div class="queue-status">

                        <span class="status-dot">
                            •
                        </span>

                        <span class="queue-status-text">
                            WAITING IN LINE
                        </span>

                    </div>


                    <!-- Queue Description -->

                    <div class="hour-glass">


                        <svg class="glasshour" width="25" height="32" xmlns="http://www.w3.org/2000/svg" 
                        viewBox="0 0 640 640">
                            
                            <path fill="rgb(255, 255, 255)"
                                d="M160 64C142.3 64 128 78.3 128 96C128 113.7 142.3 128 160 128L160 139C160 181.4 176.9 222.1 206.9 252.1L274.8 320L206.9 387.9C176.9 417.9 160 458.6 160 501L160 512C142.3 512 128 526.3 128 544C128 561.7 142.3 576 160 576L480 576C497.7 576 512 561.7 512 544C512 526.3 497.7 512 480 512L480 501C480 458.6 463.1 417.9 433.1 387.9L365.2 320L433.1 252.1C463.1 222.1 480 181.4 480 139L480 128C497.7 128 512 113.7 512 96C512 78.3 497.7 64 480 64L160 64zM416 501L416 512L224 512L224 501C224 475.5 234.1 451.1 252.1 433.1L320 365.2L387.9 433.1C405.9 451.1 416 475.5 416 501z" />
                        
                            </svg>

                        <span>
                            WE'LL NOTIFY YOU WHEN IT'S YOUR TURN
                        </span>

                    </div>

                </div>

            </section>


            <!-- ===================================================== -->
            <!-- Dividers -->
            <!-- ===================================================== -->

            <div class="divider divider-left"></div>

            <div class="divider divider-right"></div>


            <!-- ===================================================== -->
            <!-- How To Know -->
            <!-- ===================================================== -->

            <section class="how-to-card glass-card">


                <!-- How To Header -->

                <div class="how-to-header">

                    <div class="star">
                        ★
                    </div>

                    <div class="how-to-title">
                        HOW TO KNOW IT'S YOUR TURN
                    </div>

                </div>


                <!-- Instructions -->

                <div class="instructions">

                    Please press the button below and allow
                    notifications so we'll be able to notify
                    you when it's your turn.

                    <br><br>

                    Please keep an eye on your tracking number.
                    When your number is called, proceed to the
                    designated counter.

                </div>


                <!-- ================================================= -->
                <!-- Notification Button -->
                <!-- ================================================= -->

                <div class="form-actions">

                    <button class="notification-button" type="button">
                        Allow Notification
                    </button>

                </div>

            </section>


        </main>

    </div>

</body>

</html>
