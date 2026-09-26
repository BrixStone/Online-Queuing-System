<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Verify Phone - ACLC Mandaue</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

    <div class="register-container">

        <div class="background-image"></div>

        <div class="background-overlay"></div>

        <nav class="register-nav">

            <div class="aclc-logo-group">

                <div class="logo-circle">

                    <img
                        src="{{ asset('images/ACLC_nav.svg') }}"
                        alt="ACLC Logo">

                </div>

                <div class="logo-info">

                    <div class="logo-title">
                        ACLC
                    </div>

                    <div class="logo-location">
                        Mandaue City
                    </div>

                </div>

            </div>

        </nav>


        <main class="main-body">

            <div class="glass-registration-card">

                <img
                    class="log"
                    src="{{ asset('images/ACLC_logo.svg') }}"
                    alt="ACLC Logo">

                <div class="card-header">

                    <div class="badge-crest">
                        ACLC online queuing system
                    </div>

                    <h1>
                        Verify Your Phone
                    </h1>

                </div>


                @if (session('success'))

                <div class="alert-success">
                    {{ session('success') }}
                </div>

                @endif


                @if (session('error'))

                <div class="alert-error">
                    {{ session('error') }}
                </div>

                @endif


                @if ($errors->any())

                <div class="alert-error">
                    {{ $errors->first() }}
                </div>

                @endif


                <div class="otp-message">

                    <p>
                        We have sent a verification code to the
                        mobile number you provided.
                    </p>

                    <p>
                        Enter the 6-digit code below to continue.
                    </p>

                </div>


                <form
                    action="{{ route('phone.verify.submit') }}"
                    method="POST"
                    class="registration-form">

                    @csrf


                    <div class="form-field">

                        <label for="otp">
                            Verification Code
                        </label>

                        <div class="input-underline">

                            <input
                                type="text"
                                id="otp"
                                name="otp"
                                placeholder="Enter 6-digit OTP"
                                maxlength="6"
                                minlength="6"
                                pattern="[0-9]{6}"
                                inputmode="numeric"
                                autocomplete="one-time-code"
                                required
                                autofocus>

                        </div>

                    </div>


                    <div class="form-actions">

                        <button
                            type="submit"
                            class="register-button">
                            Verify OTP
                        </button>

                    </div>

                </form>


                <div class="otp-resend-section">

                    <p id="otpCountdown">
                        You can request a new OTP in
                        <strong>05:00</strong>
                    </p>


                    <form
                        id="resendOtpForm"
                        action="{{ route('verify.phone.resend') }}"
                        method="POST"
                        style="display: none;">

                        @csrf

                        <button
                            type="submit"
                            class="register-button">
                            Resend OTP
                        </button>

                    </form>

                </div>


            </div>

        </main>

    </div>


    <script>
    const countdownElement =
        document.getElementById('otpCountdown');

    const resendForm =
        document.getElementById('resendOtpForm');

    const expiresAt =
        new Date("{{ $expires_at->toIso8601String() }}").getTime();

    function updateCountdown() {

        const now = new Date().getTime();

        const remainingMilliseconds =
            expiresAt - now;

        const remainingSeconds =
            Math.max(
                0,
                Math.floor(remainingMilliseconds / 1000)
            );

        const minutes =
            Math.floor(remainingSeconds / 60);

        const seconds =
            remainingSeconds % 60;

        if (remainingSeconds <= 0) {

            countdownElement.style.display = 'none';

            resendForm.style.display = 'block';

            clearInterval(countdown);

            return;
        }

        countdownElement.innerHTML =
            `You can request a new OTP in <strong>${
                String(minutes).padStart(2, '0')
            }:${
                String(seconds).padStart(2, '0')
            }</strong>`;
    }

    updateCountdown();

    const countdown =
        setInterval(updateCountdown, 1000);
</script>


</body>

</html>
