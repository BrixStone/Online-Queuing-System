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

        .about-page {
            min-height: 100vh;
            padding: 120px 20px 50px;
            color: #333;
        }

        .about-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
            background: rgba(255, 255, 255, 0.121569);

            border: 1.5px solid rgba(255, 255, 255, 0.231373);

            box-shadow: 0px 16px 32px rgba(0, 0, 0, 0.247059);

            backdrop-filter: blur(12px);

            -webkit-backdrop-filter: blur(12px);

            border-radius: 24px;

            transition: transform 0.2s ease;
        }

        .about-container:hover {
            transform: scale(1.01);
        }

        .about-container h1 {
            text-align: center;
            color: #ffffff;
            margin-bottom: 30px;
        }

        .about-container h2 {
            color: #ffffff;
            margin-top: 30px;
        }

        .about-container p {
            font-size: 17px;
            line-height: 1.7;
            color: #dddfe2;
            text-align: justify;
        }

        strong {
            color: rgb(253, 253, 253);
        }

        .highlight {
            background-color: #444546;
            padding: 20px;
            border-left: 20px solid #4795fa;
            margin-top: 25px;
            border-radius: 5px;
        }


    </style>


    <div class="about-page">
        <img class="background-image" src="{{ asset('images/Regis_Back.svg') }}" alt="ACLC Logo">
        <div class="background-overlay"></div>

        <div class="about-container">

            <h1>About Our Online Queuing System</h1>

            <p>
                Our <strong>Online Queuing System</strong> is designed to make the
                cashier process easier and more convenient for students, parents,
                and other users. Instead of waiting in a long line at the cashier
                area, users can get a <strong>tracking number online</strong>
                through our website.
            </p>

            <p>
                To get a tracking number, the user only needs to provide their
                <strong>Student USN ID</strong> and the
                <strong>purpose of their transaction</strong>. After submitting
                the information, they will receive a tracking number and can wait
                for their turn without staying in the cashier area.
            </p>

            <p>
                When their turn is near, the system will send a
                <strong>text message</strong> to their phone informing them that
                they are next and can proceed to the cashier to complete their
                transaction.
            </p>

            <h2>Why We Built This System</h2>

            <p>
                We built this system to help
                <strong>reduce long lines and waiting times</strong> in the
                cashier area. It allows students and parents to wait somewhere
                more comfortable instead of standing in line for a long time.
            </p>

            <p>
                The system also helps make the cashier area more organized because
                users are served based on their tracking numbers and queue order.
            </p>

            <div class="highlight">
                <p>
                    Our goal is to provide a
                    <strong>simple, convenient, and organized way of managing
                        queues</strong> while making the cashier experience better
                    for both users and staff.
                </p>
            </div>

        </div>

    </div>
@endsection
