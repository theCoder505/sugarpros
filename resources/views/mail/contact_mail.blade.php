<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
        }

        .email-header {
            background-color: #1C3B6F;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 20px;
        }

        .email-content {
            padding: 25px 20px;
        }

        .email-content p {
            line-height: 1.6;
            font-size: 15px;
            margin: 12px 0;
        }

        .email-content strong {
            color: #111827;
        }

        .email-footer {
            background-color: #1C3B6F;
            color: #fff;
            text-align: center;
            font-size: 14px;
            padding: 16px;
        }

        @media (max-width: 600px) {
            .email-container {
                margin: 15px;
            }

            .email-header h1 {
                font-size: 18px;
            }

            .email-content {
                padding: 20px 15px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>📬 New Contact Form Submission</h1>
        </div>
        <div class="email-content">
            <p><strong>You've received a message from your website's contact page.</strong></p>
            <p><strong>Form User Name:</strong> {{ $claiming_username }}</p>
            <p><strong>Form User Email:</strong> {{ $claiming_useremail }}</p>
            <p><strong>Form Subject:</strong> {{ $subject }}</p>
            <p><strong>Form Message:</strong></p>
            <p>{{ $user_message }}</p>
        </div>
        <div class="email-footer">
            <p>Please respond to the user as soon as possible. Thank you!</p>
        </div>
    </div>
</body>

</html>
