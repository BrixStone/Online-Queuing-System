@extends('layouts.navbar')

@section('content')
    <style>
        .background-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;

            /* Add your image URL here */
            background-image: url('path/to/your-image.jpg');

            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;

            filter: blur(20px);
            transform: scale(1.30);
        }

        .background-overlay {
            position: fixed;

            top: 0;
            left: 0;
            width: 100%;
            height: 100%;

            z-index: -1;

            background: #282727;
            opacity: 0.4;

            pointer-events: none;
        }

        .contact-page {
            min-height: 100vh;
            padding: 120px 20px 50px;
            color: #333;
        }

        .contact-container {
            max-width: 900px;
            margin: 0 auto;
            margin-top: -80px auto;
            padding: 40px;
            background: rgba(255, 255, 255, 0.121569);

            border: 1.5px solid rgba(255, 255, 255, 0.231373);

            box-shadow: 0px 16px 32px rgba(0, 0, 0, 0.247059);

            backdrop-filter: blur(12px);

            -webkit-backdrop-filter: blur(12px);

            border-radius: 24px;

            transition: transform 0.2s ease;
        }

        .contact-container:hover {
            transform: scale(1.01);
        }

        .contact-container h1 {
            text-align: center;
            color: #ffffff;
            margin-bottom: 15px;
        }

        .contact-container h2 {
            color: #ffffff;
        }

        .intro {
            text-align: center;
            color: #dddfe2;
            font-size: 17px;
            line-height: 1.6;
            margin-bottom: 35px;
        }

        .contact-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 35px;
        }

        .contact-box {
            background: rgba(255, 255, 255, 0.121569);

            border: 1.5px solid rgba(255, 255, 255, 0.231373);

            box-shadow: 0px 16px 32px rgba(0, 0, 0, 0.247059);

            backdrop-filter: blur(12px);

            -webkit-backdrop-filter: blur(12px);

            border-radius: 24px;
            padding: 25px;
            text-align: center;
            border-radius: 8px;

            transition: transform 0.2s ease;
        }

        .contact-box:hover {
            transform: scale(1.1);
        }

        .contact-box h3 {
            color: #ffffff;
            margin-bottom: 10px;
        }

        .contact-box p {
            margin: 5px 0;
            color: #dddfe2;
            line-height: 1.5;
        }

        .contact-form {
            margin-top: 20px;
        }

        .contact-form label {
            display: block;
            color: #ffffff;
            margin-bottom: 7px;
            font-weight: bold;
        }

        .contact-form input,
        .contact-form textarea {
            background: rgba(255, 255, 255, 0.121569);

            border: 1.5px solid rgba(255, 255, 255, 0.231373);

            box-shadow: 0px 16px 32px rgba(0, 0, 0, 0.247059);

            backdrop-filter: blur(12px);

            -webkit-backdrop-filter: blur(12px);

            color: #ffffff;

            border-radius: 24px;
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            font-size: 15px;
        }

        .contact-form textarea {
            height: 150px;
            resize: vertical;
        }

        .contact-form button {
            width: 100%;
            padding: 13px;
            background-color: #1a4d8f;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            cursor: pointer;

            transition:
                background 0.2s ease,
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .contact-form button:hover {
            background: #9a0202;

            box-shadow: 0px 8px 16px rgba(26, 60, 143, 0.35);

            transform: translateY(-1px);
        }

        .contact-form button:active {
            transform: translateY(1px);

            box-shadow: 0px 4px 8px rgba(26, 60, 143, 0.25);
        }

        .contact-form button:focus {
            outline: none;

            box-shadow:
                0 0 0 3px rgba(55, 55, 255, 0.15),
                0px 6px 12px rgba(0, 72, 255, 0.251);
        }


        input::placeholder {
            color: #b2b3b5;
        }

        textarea::placeholder {
            color: #b2b3b5;
        }


        @media (max-width: 700px) {
            .contact-info {
                grid-template-columns: 1fr;
            }

            .nav-links {
                top: 20px;
                right: 20px;
                gap: 12px;
            }

            .nav-links a {
                font-size: 14px;
            }
        }
    </style>


    <div class="contact-page">
        <img class="background-image" src="{{ asset('images/Regis_Back.svg') }}" alt="ACLC Logo">
        <div class="background-overlay"></div>

        <div class="contact-container">

            <h1>Contact Us</h1>

            <p class="intro">
                If you have any questions, concerns, or need assistance with our
                Online Queuing System, feel free to contact us. We are here to
                help you with your concerns regarding the queue and cashier
                services.
            </p>

            <div class="contact-info">

                <div class="contact-box">
                    <h3>📞 Phone</h3>
                    <p>0912-345-6789</p>
                    <p>Available during office hours</p>
                </div>

                <div class="contact-box">
                    <h3>📧 Email</h3>
                    <p>ACLCMANDAUECOLLEGE@gmail.com</p>
                    <p>We will respond to your concerns.</p>
                </div>

                <div class="contact-box">
                    <h3>📍 Location</h3>
                    <p>Cashier Office</p>
                    <p>ACLC MANDAUE COLLEGE</p>
                </div>

            </div>

            <h2>Send Us a Message</h2>

            <form class="contact-form">

                <label for="name">Full Name</label>

                <input type="text" id="name" name="name" placeholder="Enter your full name" required>

                <label for="email">Email Address</label>

                <input type="email" id="email" name="email" placeholder="Enter your email address" required>

                <label for="message">Message</label>

                <textarea id="message" name="message" placeholder="Write your concern or message here..." required></textarea>

                <button type="submit">
                    Send Message
                </button>

            </form>

        </div>

    </div>
@endsection
