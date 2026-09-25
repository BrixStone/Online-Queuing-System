<?php

use function Livewire\Volt\{state, mount};
use App\Models\QueueTicket;

state([
    'token' => null,
    'queue' => null,
]);

mount(function ($token) {
    $this->token = $token;

    $this->queue = QueueTicket::where(
        'access_token',
        $token
    )->firstOrFail();
});

$refreshQueue = function () {
    if ($this->token) {
        $this->queue = QueueTicket::where(
            'access_token',
            $this->token
        )->first();
    }
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
        {{ $queue->tracking_number ?? '---' }}
        <br>
        {{ $queue->name ?? '---' }}
    </div>

    <div>
        <div id="status-badge" data-status="{{ $queue->status }}">

            @if(($queue->status ?? '') === 'holding')
                <div>
                    🕒 Waiting in Line
                </div>

            @elseif(($queue->status ?? '') === 'active')
                <div>
                    📢 You are next in line!
                </div>

            @elseif(($queue->status ?? '') === 'serving')
                <div>
                    ✅ Please proceed to
                    {{ $queue->assigned_teller ?? 'the counter' }}
                </div>

            @else
                <div>
                    Status:
                    {{ ucfirst($queue->status ?? 'Unknown') }}
                </div>
            @endif

        </div>

        <div>
            <button
                id="enable-notifications"
                type="button">
                🔔 Allow Notifications
            </button>

            <p id="notification-message"></p>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const button = document.getElementById('enable-notifications');
    const message = document.getElementById('notification-message');

    if (!button) {
        return;
    }

    button.addEventListener('click', async function () {

        try {

            // Check Service Worker support
            if (!('serviceWorker' in navigator)) {
                message.textContent =
                    'Your browser does not support service workers.';
                return;
            }

            // Check Push API support
            if (!('PushManager' in window)) {
                message.textContent =
                    'Your browser does not support push notifications.';
                return;
            }

            // Check Notification API support
            if (!('Notification' in window)) {
                message.textContent =
                    'Your browser does not support notifications.';
                return;
            }

            message.textContent =
                'Requesting notification permission...';

            // Ask browser permission
            const permission =
                await Notification.requestPermission();

            if (permission !== 'granted') {
                message.textContent =
                    'Notification permission was not granted.';
                return;
            }

            message.textContent =
                'Registering notification service...';

            // Register service worker
            const registration =
                await navigator.serviceWorker.register('/sw.js');

            // Wait until service worker is ready
            await navigator.serviceWorker.ready;

            // VAPID public key
            const publicKey =
                @js(config('services.vapid.public_key'));

            if (!publicKey) {
                throw new Error(
                    'VAPID public key is missing.'
                );
            }

            // Create browser push subscription
            const subscription =
                await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey:
                        urlBase64ToUint8Array(publicKey)
                });

            const subscriptionData =
                subscription.toJSON();

            if (!subscriptionData.endpoint) {
                throw new Error(
                    'Push subscription endpoint is missing.'
                );
            }

            message.textContent =
                'Saving notification subscription...';

            // Send subscription to Laravel
            const response = await fetch(
                @js(route(
                    'queue.push-subscription.store',
                    ['token' => $token]
                )),
                {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN':
                            document
                                .querySelector(
                                    'meta[name="csrf-token"]'
                                )
                                ?.getAttribute('content')
                    },

                    body: JSON.stringify({
                        endpoint:
                            subscriptionData.endpoint,

                        public_key:
                            subscriptionData.keys?.p256dh ?? null,

                        auth_token:
                            subscriptionData.keys?.auth ?? null
                    })
                }
            );

            const result =
                await response.json();

            if (!response.ok) {
                throw new Error(
                    result.message ||
                    'Failed to save subscription.'
                );
            }

            message.textContent =
                '✅ Notifications are now enabled.';

            button.textContent =
                '🔔 Notifications Enabled';

            button.disabled = true;

        } catch (error) {

            console.error(
                'Push notification error:',
                error
            );

            message.textContent =
                '❌ Could not enable notifications: ' +
                error.message;
        }
    });


    /*
    |--------------------------------------------------------------------------
    | Convert VAPID Base64URL key to Uint8Array
    |--------------------------------------------------------------------------
    */

    function urlBase64ToUint8Array(base64String) {

        const padding =
            '='.repeat(
                (4 - base64String.length % 4) % 4
            );

        const base64 =
            (base64String + padding)
                .replace(/-/g, '+')
                .replace(/_/g, '/');

        const rawData =
            window.atob(base64);

        return Uint8Array.from(
            [...rawData].map(function (char) {
                return char.charCodeAt(0);
            })
        );
    }

});
</script>
