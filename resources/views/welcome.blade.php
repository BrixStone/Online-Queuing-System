<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Welcome - Fill Up Form</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800&display=swap"
        rel="stylesheet">

    <!-- Your CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>


<body>

    <!-- =========================================
         REGISTER CONTAINER
    ========================================== -->

    <div class="register-container">


        <!-- =========================================
             BACKGROUND
        ========================================== -->

        <div class="background-image"></div>

        <div class="background-overlay"></div>


        <!-- =========================================
             NAVIGATION
        ========================================== -->

        <nav class="register-nav">

            <!-- ACLC LOGO -->
            <div class="aclc-logo-group">

                <div class="logo-circle">

                    <img src="{{ asset('images/ACLC_nav.svg') }}" alt="ACLC Logo">

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


            <!-- NAVIGATION LINKS -->
            <div class="nav-links">

                <a href="#" class="active">
                    Home
                </a>

                <a href="service">
                    Service
                </a>

                <a href="Contact">
                    Contact
                </a>

                <a href="about">
                    About
                </a>

            </div>

        </nav>


        <!-- =========================================
             MAIN BODY
        ========================================== -->

        <main class="main-body">


            <!-- =========================================
                 GLASS FORM CARD
            ========================================== -->

            <div class="glass-registration-card">


                <!-- ACLC LOGO -->
                <img class="log" src="{{ asset('images/ACLC_logo.svg') }}" alt="ACLC Logo">





                <!-- =========================================
                     CARD HEADER
                ========================================== -->

                <div class="card-header">

                    <div class="badge-crest">
                        ACLC online queuing system
                    </div>

                    <h1>
                        Queue Registration Form
                    </h1>

                </div>


                <!-- =========================================
                     SUCCESS MESSAGE
                ========================================== -->

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



                <!-- =========================================
                     FORM
                ========================================== -->

                <form action="{{ route('submit.form') }}" method="POST" class="registration-form">

                    @csrf


                    <!-- SCHOOL USN -->

                    <div class="form-field">

                        <label for="student_number">
                            School USN
                        </label>

                        <div class="input-underline">

                            <input
                                type="text"
                                id="student_number"
                                name="student_number"
                                placeholder="C25-01-*****-MAN121"
                                value="{{ old('student_number') }}"
                                required>

                            <svg class="user-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">

                                <circle cx="12" cy="8" r="4" stroke="white" stroke-width="2" />

                                <path d="M5 20C5.8 16.7 8.2 15 12 15C15.8 15 18.2 16.7 19 20" stroke="white"
                                    stroke-width="2" stroke-linecap="round" />

                            </svg>

                        </div>

                    </div>

                    <!-- PURPOSE -->

                    <div class="form-field">

                        <label for="purpose">
                            Purpose
                        </label>

                        <div class="input-underline">

                            <input
                                type="text"
                                id="purpose"
                                name="purpose"
                                placeholder="Enter your Purpose"
                                value="{{ old('purpose') }}"
                                required>

                            <svg class="purpose-icon" width="10" height="13" viewBox="0 0 10 13" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.72135 7.39121C8.35938 6.73359 8.75 5.85 8.75 4.875C8.75 2.85645 7.07031 1.21875 5 1.21875C2.92969 1.21875 1.25 2.85645 1.25 4.875C1.25 5.85 1.64062 6.73359 2.27865 7.39121C2.83333 7.95996 3.44792 8.7623 3.66667 9.75H6.33333C6.55208 8.75977 7.16667 7.95996 7.72135 7.39121ZM8.6276 8.2291C8.01302 8.86133 7.5 9.6332 7.5 10.5041V10.9688C7.5 12.091 6.56771 13 5.41667 13H4.58333C3.43229 13 2.5 12.091 2.5 10.9688V10.5041C2.5 9.6332 1.98698 8.86133 1.3724 8.2291C0.520833 7.35566 0 6.175 0 4.875C0 2.18359 2.23958 0 5 0C7.76042 0 10 2.18359 10 4.875C10 6.175 9.47917 7.35566 8.6276 8.2291ZM3.75 4.67187C3.75 5.00957 3.47135 5.28125 3.125 5.28125C2.77865 5.28125 2.5 5.00957 2.5 4.67187C2.5 3.43789 3.52604 2.4375 4.79167 2.4375C5.13802 2.4375 5.41667 2.70918 5.41667 3.04687C5.41667 3.38457 5.13802 3.65625 4.79167 3.65625C4.21615 3.65625 3.75 4.11074 3.75 4.67187Z"
                                    fill="white" />
                            </svg>

                        </div>

                    </div>


                    <!-- MOBILE NUMBER -->

                    <div class="form-field" id="mobileGroup">

                        <label for="mobile_number">
                            Mobile Number
                        </label>

                        <div class="input-underline">

                            <input
                                type="tel"
                                id="mobile_number"
                                name="mobile_number"
                                placeholder="09XXXXXXXXX"
                                value="{{ old('mobile_number') }}"
                                maxlength="11"
                                minlength="11"
                                pattern="09[0-9]{9}"
                                inputmode="numeric"
                                required>


                        </div>

                    </div>


                    <!-- HIDDEN DEVICE ID -->

                    <input type="hidden" name="device_id" id="device_id">


                    <!-- HIDDEN PLATFORM -->

                    <input type="hidden" name="platform" id="platform">


                    <!-- SUBMIT -->

                    <div class="form-actions">

                        <button type="submit" class="register-button">
                            Get Tracking Number
                        </button>

                    </div>


                </form>

            </div>

        </main>

    </div>


    <!-- =========================================
         JAVASCRIPT
    ========================================== -->

    <script>
        /*
            |--------------------------------------------------------------------------
            | CLOSE CARD
            |--------------------------------------------------------------------------
            */

        function closeCard() {

            const card =
                document.querySelector(
                    '.glass-registration-card'
                );

            if (card) {

                card.classList.add('hidden');

            }

        }


        /*
        |--------------------------------------------------------------------------
        | DEVICE ID
        |--------------------------------------------------------------------------
        */

        let deviceId =
            localStorage.getItem('device_id');


        if (!deviceId) {

            deviceId =
                crypto.randomUUID();

            localStorage.setItem(
                'device_id',
                deviceId
            );

        }


        /*
        |--------------------------------------------------------------------------
        | PLATFORM DETECTION
        |--------------------------------------------------------------------------
        */

        let platform;


        if (
            /Android/i.test(
                navigator.userAgent
            )
        ) {

            platform = 'android';

        } else if (
            /iPhone|iPad|ipod/i.test(
                navigator.userAgent
            )
        ) {

            platform = 'ios';

        } else if (
            /Windows|Macintosh|Linux/i.test(
                navigator.userAgent
            )
        ) {

            platform = 'desktop';

        } else {

            platform = 'other';

        }


        /*
        |--------------------------------------------------------------------------
        | SHOW MOBILE FIELD ON IOS
        |--------------------------------------------------------------------------
        */

        // if (platform === 'ios') {

        //     document.getElementById(
        //         'mobileGroup'
        //     ).style.display = 'flex';

        // }


        /*
        |--------------------------------------------------------------------------
        | SET HIDDEN VALUES
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'device_id'
        ).value = deviceId;


        document.getElementById(
            'platform'
        ).value = platform;


        /*
        |--------------------------------------------------------------------------
        | DEBUG
        |--------------------------------------------------------------------------
        */

        console.log(
            'Device ID:',
            deviceId
        );

        console.log(
            'Platform:',
            platform
        );


        /*
        |--------------------------------------------------------------------------
        | CHECK EXISTING DEVICE
        |--------------------------------------------------------------------------
        */

        // fetch(
        //         `/queue/check-device?device_id=${deviceId}`
        //     )

        //     .then(response => response.json())

        //     .then(data => {

        //         if (data.exists) {

        //             window.location.href =
        //                 `/queue/status/${data.ticket.access_token}`;

        //         }

        //     })

        //     .catch(error => {

        //         console.error(
        //             'Error checking device:',
        //             error
        //         );

        //     }); 
    </script>

</body>

</html>