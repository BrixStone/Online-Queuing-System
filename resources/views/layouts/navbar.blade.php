<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>

<body>
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

            <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">
                <p class="kil">Home</p>
            </a>

            <a href="{{ url('/contact') }}" class="{{ request()->is('contact') ? 'active' : '' }}">
                <p class="kil">Contact</p>
            </a>

            <a href="{{ url('/about') }}" class="{{ request()->is('about') ? 'active' : '' }}">
                <p class="kil">About</p>
            </a>

        </div>

    </nav>

    <!-- ===== PAGE CONTENT ===== -->
    <main>
        @yield('content')
    </main>

</body>

</html>
