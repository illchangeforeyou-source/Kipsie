<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.05)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,144C960,149,1056,139,1152,128C1248,117,1344,107,1392,101.3L1440,96L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path></svg>');
            background-repeat: repeat-x;
            background-size: auto 100%;
            pointer-events: none;
            z-index: 1;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(20px); }
        }

        body.dark-mode {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        }

        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            padding: 60px 40px;
            max-width: 600px;
            text-align: center;
            animation: slideUp 0.5s ease-out;
            position: relative;
            z-index: 10;
            backdrop-filter: blur(10px);
        }

        body.dark-mode .error-container {
            background: #2a2a3e;
            color: #e8e8e8;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
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

        .error-code {
            font-size: 120px;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            line-height: 1;
        }

        body.dark-mode .error-code {
            background: linear-gradient(135deg, #9d9dff 0%, #b8b8ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .error-icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.8;
        }

        h1 {
            font-size: 36px;
            color: #333;
            margin-bottom: 15px;
            font-weight: 700;
        }

        body.dark-mode h1 {
            color: #e8e8e8;
        }

        p {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
            line-height: 1.6;
        }

        body.dark-mode p {
            color: #b0b0b0;
        }

        .error-message {
            background: #f5f5f5;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 30px 0;
            border-radius: 8px;
            text-align: left;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
        }

        body.dark-mode .error-message {
            background: #1a1a2e;
            border-left-color: #9d9dff;
            color: #e8e8e8;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 40px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 8px;
            border: none;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #e8e8e8;
            color: #333;
            border: 2px solid #667eea;
        }

        body.dark-mode .btn-secondary {
            background: #3a3a4e;
            color: #e8e8e8;
            border-color: #9d9dff;
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .footer-text {
            margin-top: 40px;
            font-size: 12px;
            color: #999;
        }

        body.dark-mode .footer-text {
            color: #777;
        }

        .error-details {
            margin-top: 20px;
            padding: 15px;
            background: #f0f0f0;
            border-radius: 8px;
            font-size: 12px;
            color: #666;
            display: none;
        }

        body.dark-mode .error-details {
            background: #1a1a2e;
            color: #b0b0b0;
        }

        .error-details.show {
            display: block;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .toggle-details {
            background: none;
            border: none;
            color: #667eea;
            cursor: pointer;
            font-size: 12px;
            text-decoration: underline;
            padding: 0;
            margin-top: 15px;
        }

        body.dark-mode .toggle-details {
            color: #9d9dff;
        }

        .toggle-details:hover {
            opacity: 0.8;
        }

        @media (max-width: 600px) {
            .error-container {
                padding: 40px 25px;
            }

            .error-code {
                font-size: 80px;
            }

            h1 {
                font-size: 28px;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        @yield('content')
    </div>

    <script>
        // Dark mode detection
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.body.classList.add('dark-mode');
        }

        // Listen for dark mode changes
        window.matchMedia('(prefers-color-scheme: dark)').addListener(function(e) {
            e.matches ? document.body.classList.add('dark-mode') : document.body.classList.remove('dark-mode');
        });

        // Toggle error details
        function toggleErrorDetails() {
            const details = document.querySelector('.error-details');
            if (details) {
                details.classList.toggle('show');
            }
        }
    </script>
</body>
</html>
