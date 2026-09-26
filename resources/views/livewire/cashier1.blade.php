<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cashier - Window 1</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@100;400;700&display=swap"
        rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/cashier.css') }}">
</head>

<body>

    <div class="cashier-page">

        <!-- Background -->
        <div class="background"></div>
        <div class="background-overlay"></div>


        <main class="main-body">

            <!-- =========================
                 HEADER
            ========================== -->

            <div class="window-title">
                Cashier: Window 1
            </div>

            <button class="top-button close-window">
                Close Window
            </button>

            <button class="top-button logout">
                Log out
            </button>


            <!-- =========================
                 CURRENTLY SERVING
            ========================== -->

            <section class="glass-card cashier-window">

                <h1 class="cashier-heading">
                    Currently Serving
                </h1>

                <div class="cashier-divider"></div>

                <p class="empty-message">
                    No one is currently in your window.
                </p>

                <button class="cashier-action call-next">
                    Call Next Student
                </button>

                <button class="cashier-action skip-student">
                    Skip Student
                </button>

            </section>


            <!-- =========================
                 ACTIVE LINE
            ========================== -->

            <section class="glass-card active-line">

                <h2 class="active-heading">
                    Active Line
                </h2>

                <div class="active-divider"></div>

                <div class="active-student">

                    <div class="student-queue-text">
                        A010

                        <span class="student-queue-name">
                            Kaiser - Zyed G. Uy
                        </span>
                    </div>

                </div>

            </section>


            <!-- =========================
                 HOLDING LINE
            ========================== -->

            <section class="glass-card holding-line">

                <h2 class="holding-heading">
                    Holding Line
                </h2>

                <div class="holding-divider"></div>

            </section>


            <!-- =========================
                 STUDENT INFORMATION
            ========================== -->

            <section class="glass-card student-info">

                <h2 class="student-info-heading">
                    Student Information
                </h2>

                <span class="star">★</span>

                <div class="student-info-divider"></div>


                <div class="student-fields">

                    <!-- USN -->
                    <div class="field usn-field">

                        <span class="field-label">
                            USN Number:
                        </span>

                        <span class="field-value">
                            C25-01-*****-MAN121
                        </span>

                    </div>


                    <!-- Full Name -->
                    <div class="field name-field">

                        <span class="field-label">
                            Full Name:
                        </span>

                        <span class="field-value">
                            Kaiser-Zyed G. Uy
                        </span>

                    </div>


                    <!-- Academic Level -->
                    <div class="field academic-field">

                        <span class="field-label">
                            Academic Level:
                        </span>

                        <span class="field-value">
                            College or Senior
                        </span>

                    </div>


                    <!-- Year Level -->
                    <div class="field year-field">

                        <span class="field-label">
                            Year Level:
                        </span>

                        <span class="field-value">
                            2nd Year
                        </span>

                    </div>



                    <!-- Course -->
                    <div class="field course-field">

                        <span class="field-label">
                            Course:
                        </span>

                        <span class="field-value">
                            BSCS
                        </span>

                    </div>

                </div>

            </section>

        </main>

    </div>

</body>

</html>
