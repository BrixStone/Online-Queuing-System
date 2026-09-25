<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Online Queuing System</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 40px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #1a4d8f;
            margin-bottom: 15px;
        }

        .intro {
            text-align: center;
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
            background-color: #eef5ff;
            padding: 25px;
            text-align: center;
            border-radius: 8px;
        }

        .contact-box h3 {
            color: #1a4d8f;
            margin-bottom: 10px;
        }

        .contact-box p {
            margin: 5px 0;
            line-height: 1.5;
        }

        .contact-form {
            margin-top: 20px;
        }

        .contact-form label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
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
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .contact-form button:hover {
            background-color: #123967;
        }

        @media (max-width: 700px) {
            .contact-info {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="container">

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
            <input type="text" id="name" name="name"
                   placeholder="Enter your full name" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email"
                   placeholder="Enter your email address" required>

            <label for="message">Message</label>
            <textarea id="message" name="message"
                      placeholder="Write your concern or message here..."
                      required></textarea>

            <button type="submit">Send Message</button>

        </form>

    </div>

</body>
</html>