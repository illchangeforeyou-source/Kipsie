<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LODIT - Pharmacy Management System</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('foto/baobei.jpg') }}" type="image/png">
    
    <style>
        body {
            background-image: url('{{ asset('foto/hala.png') }}');
            background-size: cover;
            color: #000;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .welcome-container {
            text-align: center;
        }
        
        .welcome-container h1 {
            margin: 0;
        }
        
        .welcome-container button {
            padding: 10px 30px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <h1>
            <a href="/login" style="text-decoration: none;">
                <button class="btn btn-outline-light">Go to Login Page</button>
            </a>
        </h1>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
