<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ACLC Registration</title>

    <!-- Load Google Fonts here (faster, doesn't block CSS) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800&display=swap" rel="stylesheet">

    <!-- Your CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

    <div class="register-container">

        <!-- =========================================
             SHARED BACKGROUND (NEVER RELOADS)
        ========================================== -->
        <img class="background-image" src="{{ asset('images/Regis_Back.svg') }}" alt="Background">
        <div class="background-overlay"></div>
        <canvas id="bg-canvas"></canvas>

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
                    <div class="logo-title">ACLC</div>
                    <div class="logo-location">Mandaue City</div>
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

        <!-- =========================================
             DYNAMIC PAGE CONTENT
        ========================================== -->
        <main class="main-body" id="page-content">
            @yield('content')
        </main>

    </div>

    <!-- =========================================
         SEAMLESS NAVIGATION SCRIPT (NO FLASH)
    ========================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navLinks = document.querySelectorAll('.nav-links a');
            const pageContent = document.getElementById('page-content');

            async function loadPage(url, pushState = true) {
                try {
                    // Fetch the new page in the background
                    const response = await fetch(url);
                    const html = await response.text();

                    // Extract only the #page-content from the new page
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('page-content');

                    if (newContent) {
                        pageContent.innerHTML = newContent.innerHTML;
                        document.title = doc.title;

                        if (pushState) {
                            window.history.pushState({}, '', url);
                        }

                        // Update the active gradient class on the navbar
                        navLinks.forEach(link => {
                            link.classList.toggle('active', link.href === url);
                        });
                    }
                } catch (error) {
                    window.location.href = url; // Fallback if anything fails
                }
            }

            // Intercept navbar clicks
            navLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (link.href !== window.location.href) {
                        loadPage(link.href);
                    }
                });
            });

            // Handle browser Back/Forward buttons
            window.addEventListener('popstate', () => {
                loadPage(window.location.href, false);
            });
        });
    </script>

    @stack('scripts')
</body>

</html>