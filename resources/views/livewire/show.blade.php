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

    $this->queue = QueueTicket::where(
        'access_token',
        $token
    )->firstOrFail();

    $this->checkNoShow();
    $this->updatePosition();
});

$updatePosition = function () {
    if (!$this->queue) {
        $this->position = null;
        return;
    }

    $this->position = QueueTicket::where(
        'created_at',
        '<',
        $this->queue->created_at
    )
    ->whereIn('status', [
        QueueTicket::STATUS_HOLDING,
        QueueTicket::STATUS_ACTIVE,
        QueueTicket::STATUS_SERVING,
    ])
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

    $this->queue = QueueTicket::where(
        'access_token',
        $this->token
    )->first();

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

@if (session('warning'))
    <script>
        alert(@js(session('warning')));
    </script>
@endif

<div wire:poll.5s="refreshQueue">

    <h2>Queue Status</h2>

    <div>
        <strong>
            {{ $queue->tracking_number ?? '---' }}
        </strong>

        <br>

        {{ $queue->name ?? '---' }}
    </div>

    @if(
        $queue &&
        in_array($queue->status, [
            QueueTicket::STATUS_HOLDING,
            QueueTicket::STATUS_ACTIVE,
            QueueTicket::STATUS_SERVING,
        ])
    )
        <div style="margin-top: 20px;">

            <strong>Your Position</strong>

            <div
                style="
                    font-size: 40px;
                    font-weight: bold;
                "
            >
                #{{ $position ?? '---' }}
            </div>

        </div>
    @endif

    <div style="margin-top: 20px;">

        @if(($queue->status ?? '') === QueueTicket::STATUS_HOLDING)

            <div>
                🕒 <strong>Waiting in Line</strong>
            </div>

            <p>
                Please wait. You will be notified when
                you are close to being served.
            </p>

        @elseif(($queue->status ?? '') === QueueTicket::STATUS_ACTIVE)

            <div>
                📢 <strong>Please Stand By</strong>
            </div>

            <p>
                You are close to being served.
            </p>

            <p>
                Please stay nearby and be ready when
                your number is called.
            </p>

        @elseif(($queue->status ?? '') === QueueTicket::STATUS_SERVING)

            @if($queue->arrived_at)

                <div>
                    ✅ <strong>You are checked in!</strong>
                </div>

                <p>
                    Please proceed to
                    <strong>
                        {{ $queue->assigned_teller ?? 'the counter' }}
                    </strong>
                </p>

            @else

                <div>
                    📢 <strong>It is Your Turn</strong>
                </div>

                <p>
                    Please proceed to
                    <strong>
                        {{ $queue->assigned_teller ?? 'the counter' }}
                    </strong>
                </p>

                @if($queue->serving_started_at)

                    <div
                        style="
                            margin-top: 20px;
                            padding: 20px;
                            border: 2px solid #f59e0b;
                            border-radius: 10px;
                        "
                    >

                        <strong>
                            ⏱️ Please confirm that you are here
                        </strong>

                        <p>
                            You have 3 minutes to arrive.
                        </p>

                        <div
                            id="arrival-countdown"
                            data-expires-at="{{ $queue->serving_started_at->copy()->addMinutes(3)->toIso8601String() }}"
                            style="
                                font-size: 42px;
                                font-weight: bold;
                                margin: 15px 0;
                            "
                        >
                            03:00
                        </div>

                        <button
                            type="button"
                            wire:click="imHere"
                            id="im-here-button"
                        >
                            🙋 I'm Here
                        </button>

                        <p id="countdown-message"></p>

                    </div>

                @endif

            @endif

        @elseif(($queue->status ?? '') === QueueTicket::STATUS_NO_SHOW)

            <div>
                ❌ <strong>No Show</strong>
            </div>

            <p>
                Your 3-minute arrival time has expired.
            </p>

            <p>
                Please contact the staff if you believe
                this was a mistake.
            </p>

        @elseif(($queue->status ?? '') === QueueTicket::STATUS_COMPLETED)

            <div>
                ✅ <strong>Completed</strong>
            </div>

        @elseif(($queue->status ?? '') === QueueTicket::STATUS_HELD)

            <div>
                ⏸️ <strong>On Hold</strong>
            </div>

        @else

            <div>
                Status:
                <strong>
                    {{ ucfirst($queue->status ?? 'Unknown') }}
                </strong>
            </div>

        @endif

    </div>

</div>

<script>
    function initializeArrivalCountdown() {

        const countdown =
            document.getElementById('arrival-countdown');

        if (!countdown) {
            return;
        }

        const button =
            document.getElementById('im-here-button');

        const message =
            document.getElementById('countdown-message');

        const expiresAt =
            new Date(
                countdown.dataset.expiresAt
            ).getTime();

        function updateCountdown() {

            const remaining =
                expiresAt - Date.now();

            if (remaining <= 0) {

                countdown.textContent = '00:00';

                if (button) {
                    button.disabled = true;
                    button.textContent = 'Time Expired';
                }

                if (message) {
                    message.textContent =
                        'Your 3-minute arrival time has expired.';
                }

                return;
            }

            const totalSeconds =
                Math.floor(remaining / 1000);

            const minutes =
                Math.floor(totalSeconds / 60);

            const seconds =
                totalSeconds % 60;

            countdown.textContent =
                String(minutes).padStart(2, '0') +
                ':' +
                String(seconds).padStart(2, '0');
        }

        updateCountdown();

        const interval =
            setInterval(function () {

                if (!document.body.contains(countdown)) {
                    clearInterval(interval);
                    return;
                }

                updateCountdown();

            }, 1000);
    }

    document.addEventListener(
        'DOMContentLoaded',
        function () {
            initializeArrivalCountdown();
        }
    );

    document.addEventListener(
        'livewire:navigated',
        function () {
            initializeArrivalCountdown();
        }
    );
</script>