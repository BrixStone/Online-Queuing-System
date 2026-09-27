@extends('layouts.navbar')

@section('content')
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

        <style>
            /* Position canvas over background image/overlay but behind main body content */
            #bg-canvas {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 1;
                /* Keeps canvas behind main body content */
                pointer-events: none;
                /* Allows mouse interactions to pass through to form inputs */
            }

            .register-container {
                position: relative;
            }

            .main-body {
                position: relative;
                z-index: 2;
                /* Ensures form sits in front of the background grid */
            }
        </style>
    </head>


    <body>

        <!-- =========================================
            REGISTER CONTAINER
        ========================================== -->

        <div class="register-container">


            <!-- =========================================
                BACKGROUND & INTERACTIVE CANVAS GRID
            ========================================== -->

            <img class="background-image" src="{{ asset('images/Regis_Back.svg') }}" alt="ACLC Logo">

            <div class="background-overlay"></div>

            <!-- Canvas inserted directly behind form content -->
            <canvas id="bg-canvas"></canvas>





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

                                <input type="text" id="student_number" name="student_number"
                                    placeholder="C25-01-*****-MAN121" value="{{ old('student_number') }}" required>

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

                                <input type="text" id="purpose" name="purpose" placeholder="Enter your Purpose"
                                    value="{{ old('purpose') }}" required>

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

                                <input type="tel" id="mobile_number" name="mobile_number" placeholder="(+63)9*********"
                                    value="{{ old('mobile_number') }}" maxlength="11" minlength="11" pattern="09[0-9]{9}"
                                    inputmode="numeric" required>
                                <svg class="phone" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                    <path fill="rgb(255, 255, 255)"
                                        d="M224.2 89C216.3 70.1 195.7 60.1 176.1 65.4L170.6 66.9C106 84.5 50.8 147.1 66.9 223.3C104 398.3 241.7 536 416.7 573.1C493 589.3 555.5 534 573.1 469.4L574.6 463.9C580 444.2 569.9 423.6 551.1 415.8L453.8 375.3C437.3 368.4 418.2 373.2 406.8 387.1L368.2 434.3C297.9 399.4 241.3 341 208.8 269.3L253 233.3C266.9 222 271.6 202.9 264.8 186.3L224.2 89z" />
                                </svg>

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
            | INTERACTIVE CANVAS GRID BACKGROUND
            |--------------------------------------------------------------------------
            */
            const canvas = document.getElementById('bg-canvas');
            const ctx = canvas.getContext('2d');

            const squareSize = 50;
            const gap = 2;
            const radius = 50;
            const fadeSpeed = 0.02;

            // Color palette for the squares (add or change hex colors as you like)
            const palette = ['#00164D', '#1A3C8F', '#9A0202', '#DC2626', '#FFFFFF'];

            let cols = 0;
            let rows = 0;
            let grid = [];
            let mouse = {
                x: -1000,
                y: -1000
            };

            // Helper to convert HEX colors to RGBA with dynamic intensity opacity
            function getRgba(hex, alpha) {
                const r = parseInt(hex.slice(1, 3), 16);
                const g = parseInt(hex.slice(3, 5), 16);
                const b = parseInt(hex.slice(5, 7), 16);
                return `rgba(${r}, ${g}, ${b}, ${alpha})`;
            }

            function resizeCanvas() {
                const container = document.querySelector('.register-container');
                canvas.width = container ? container.clientWidth : window.innerWidth;
                canvas.height = container ? container.clientHeight : window.innerHeight;

                cols = Math.ceil(canvas.width / (squareSize + gap));
                rows = Math.ceil(canvas.height / (squareSize + gap));

                grid = [];
                for (let r = 0; r < rows; r++) {
                    for (let c = 0; c < cols; c++) {
                        grid.push({
                            x: c * (squareSize + gap),
                            y: r * (squareSize + gap),
                            intensity: 0,
                            // Assign a random color from the palette to each square
                            color: palette[Math.floor(Math.random() * palette.length)]
                        });
                    }
                }
            }

            window.addEventListener('mousemove', (e) => {
                const rect = canvas.getBoundingClientRect();
                mouse.x = e.clientX - rect.left;
                mouse.y = e.clientY - rect.top;
            });

            window.addEventListener('mouseleave', () => {
                mouse.x = -1000;
                mouse.y = -1000;
            });

            function animateGrid() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                grid.forEach((sq) => {
                    const squareCenterX = sq.x + squareSize / 2;
                    const squareCenterY = sq.y + squareSize / 2;
                    const dist = Math.hypot(mouse.x - squareCenterX, mouse.y - squareCenterY);

                    if (dist < radius) {
                        const targetIntensity = 1 - dist / radius;
                        if (targetIntensity > sq.intensity) {
                            sq.intensity = targetIntensity;
                        }
                    } else {
                        sq.intensity = Math.max(0, sq.intensity - fadeSpeed);
                    }

                    ctx.shadowBlur = 0; // No blur

                    if (sq.intensity > 0) {
                        ctx.fillStyle = getRgba(sq.color, sq.intensity * 0.8);
                    } else {
                        ctx.fillStyle = 'rgba(255, 255, 255, 0.05)';
                    }

                    ctx.fillRect(sq.x, sq.y, squareSize, squareSize);
                });

                requestAnimationFrame(animateGrid);
            }

            window.addEventListener('resize', resizeCanvas);
            resizeCanvas();
            animateGrid();
        </script>

    </body>

    </html>
@endsection
