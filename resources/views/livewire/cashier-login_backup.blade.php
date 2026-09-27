<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Cashier Login</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Outfit:wght@100;400;700&display=swap"
    rel="stylesheet"
  >

  <!-- CSS -->
  <link rel="stylesheet" href="{{ asset('css/cashier-login.css') }}">
</head>

<body>

  <div class="cashier-login">

    <!-- Background -->
    <div class="background"></div>
    <div class="background-overlay"></div>

    <!-- Main Content -->
    <main class="main-body">

      <!-- Glass Login Card -->
      <section class="glass-card">

        <!-- Header -->
        <header class="card-header">

          <h1 class="card-title">
            QUEUE STATUS
          </h1>

          <p class="card-subtitle">
            Please enter PIN to access the dashboard
          </p>

        </header>

        <!-- Form -->
        <div class="form-actions">

          <!-- PIN -->
          <div class="pin-box">
            <span class="pin-dot">•</span>
            <span class="pin-dot">•</span>
            <span class="pin-dot">•</span>
            <span class="pin-dot">•</span>
          </div>

          <!-- Enter Button -->
          <button class="enter-button">
            Enter Dashboard
          </button>

        </div>

      </section>

    </main>

    <!-- Watermark -->
    <div class="watermark">
      <img class="icon-line" src="{{ asset('images/ACLC_logo.svg') }}" alt="Line">
    </div>

  </div>

</body>
</html>