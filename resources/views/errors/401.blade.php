{{-- @extends('errors::minimal')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __('Unauthorized')) --}}


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>401 - Unauthorized</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated background shapes */
        .bg-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
        }

        .shape:nth-child(1) {
            width: 300px;
            height: 300px;
            top: -150px;
            left: -150px;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 200px;
            height: 200px;
            bottom: -100px;
            right: 10%;
            animation-delay: 4s;
        }

        .shape:nth-child(3) {
            width: 150px;
            height: 150px;
            top: 20%;
            right: -75px;
            animation-delay: 2s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            33% {
                transform: translate(30px, -30px) rotate(120deg);
            }

            66% {
                transform: translate(-20px, 20px) rotate(240deg);
            }
        }

        .error-container {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 60px 40px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 90%;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .lock-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .lock-icon svg {
            width: 60px;
            height: 60px;
            fill: white;
        }

        .error-code {
            font-size: 72px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            line-height: 1;
        }

        .error-title {
            font-size: 32px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
        }

        .error-message {
            font-size: 16px;
            color: #718096;
            line-height: 1.6;
            margin-bottom: 40px;
            max-width: 450px;
            margin-left: auto;
            margin-right: auto;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 32px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .divider {
            margin: 30px 0;
            height: 1px;
            background: linear-gradient(to right, transparent, #e2e8f0, transparent);
        }

        .help-text {
            font-size: 14px;
            color: #a0aec0;
            margin-top: 20px;
        }

        .help-text a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .help-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .error-container {
                padding: 40px 25px;
            }

            .error-code {
                font-size: 56px;
            }

            .error-title {
                font-size: 24px;
            }

            .lock-icon {
                width: 100px;
                height: 100px;
            }

            .lock-icon svg {
                width: 50px;
                height: 50px;
            }

            .button-group {
                flex-direction: column;
                width: 100%;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="bg-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="error-container">
        <div class="lock-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path
                    d="M12 1C8.676 1 6 3.676 6 7v3H5c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V12c0-1.1-.9-2-2-2h-1V7c0-3.324-2.676-6-6-6zm0 2c2.276 0 4 1.724 4 4v3H8V7c0-2.276 1.724-4 4-4zm0 10c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2z" />
            </svg>
        </div>

        <div class="error-code">401</div>
        <h1 class="error-title">Unauthorized Access</h1>
        <p class="error-message">
            You don't have permission to view the content of this page. Please contact the administrator for access or
            return to the homepage.
        </p>

        <div class="button-group">
            <a href="{{ url('/home') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                Go to Homepage
            </a>
            <a href="{{ route('logout') }}" class="btn btn-secondary"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                Logout
            </a>
        </div>

        <div class="divider"></div>

        <p class="help-text">
            Need help? <a href="mailto:support@example.com">Contact Support</a>
        </p>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</body>

</html>
