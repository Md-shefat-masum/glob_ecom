<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
    <title>Email Verification - TPMART</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
            color: #333;
        }

        .container {
            background-color: #ffffff;
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .header {
            font-size: 24px;
            font-weight: bold;
            color: #00788a;
            margin-bottom: 20px;
        }

        .content {
            font-size: 16px;
            line-height: 1.6;
        }

        .verification-code {
            font-size: 28px;
            font-weight: bold;
            background-color: #f0f4f8;
            color: #00788a;
            padding: 12px 20px;
            margin: 20px 0;
            display: block;
            letter-spacing: 4px;
            border-radius: 6px;
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #999;
        }
    </style>
</head>

<body>
    
    <div class="container">
        <div class="header">Email Verification</div>
        <div class="content">
            Hi {{ $sendLinkInfo['name'] ?? 'User' }}, <br><br>

            Thank you for registering with <strong>TPMART</strong>!<br>
            Please use the following verification code to complete your signup process:

            <div class="verification-code">
                {{ $sendLinkInfo['verification_code'] }}
            </div>

            If you didn’t request this, you can safely ignore this email.<br><br>

            Thanks & regards,<br>
            <strong>TPMART Team</strong>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} TPMART. All rights reserved.
        </div>
    </div>
</body>

</html>
