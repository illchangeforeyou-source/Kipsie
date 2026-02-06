@php
    $themeClass = 'dark-mode';
    try {
        if (session()->has('id')) {
            $pref = \App\Models\UserPreference::where('user_id', session('id'))->first();
            if ($pref && $pref->theme === 'light') {
                $themeClass = 'light-mode';
            }
        } else {
            $default = \App\Models\AppSetting::where('setting_key', 'default_theme')->first();
            if ($default && $default->setting_value === 'light') {
                $themeClass = 'light-mode';
            }
        }
    } catch (\Exception $e) {
        // fallback to dark-mode on error
    }
@endphp
<!DOCTYPE html>
<html lang="en" class="{{ $themeClass }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LODIT - Pharmacy Management System')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    
    <link rel="icon" href="{{ asset('foto/baobei.jpg') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="{{ asset('js/script.js') }}"></script>

    <style>
        /* Light Mode Styles */
        html.light-mode {
            --navbar-bg: #ffffff;
            --navbar-border: #e0e0e0;
            --profile-bg: #f5f5f5;
            --text-primary: #1a1a1a;
            --text-secondary: #666666;
            --page-bg: #fafafa;
            --card-bg: #ffffff;
            --card-text: #1f2937;
            --muted: #6b7280;
            --table-head-bg: #f3f4f6;
            --table-border: #e5e7eb;
            --card-hover: #f9fafb;
            --accent: #1e3a8a;
        }

        /* Dark Mode Styles (Default) */
        html, html.dark-mode {
            --navbar-bg: #1f1f1f;
            --navbar-border: #333;
            --profile-bg: #2a2a2a;
            --text-primary: #f5f5f5;
            --text-secondary: #b0b0b0;
            --page-bg: #1e1e1e;
            --card-bg: #374151;
            --card-text: #f9fafb;
            --muted: #9ca3af;
            --table-head-bg: #4b5563;
            --table-border: #4b5563;
            --card-hover: #1f2937;
            --accent: #1e3a8a;
        }

        /* Layout adjustments for sidebar + top navbar */
        body {
            margin: 0;
            padding: 0;
            background-color: var(--page-bg);
            color: var(--text-primary);
        }

        main {
            margin-left: var(--sidebar-width, 260px);
            margin-top: 70px;
            padding: 20px;
            transition: margin-left 0.3s ease;
            background-color: var(--page-bg);
        }

        /* Ensure page header text sits above cards/boxes */
        .reports-header,
        .permissions-header,
        .notifications-header,
        .notifications-container .notifications-header {
            position: relative;
            z-index: 2;
        }

        .report-card,
        .permissions-card,
        .notification-item,
        .notifications-container .report-card {
            position: relative;
            z-index: 1;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            main {
                margin-left: 70px;
            }
        }

        /* Custom Cropper Styling */
        .cropper-modal {
            background-color: rgba(0, 0, 0, 0.9) !important;
        }

        .cropper-bg {
            background-image: linear-gradient(45deg, #333 25%, transparent 25%, transparent 75%, #333 75%, #333),
                             linear-gradient(45deg, #333 25%, transparent 25%, transparent 75%, #333 75%, #333) !important;
            background-size: 20px 20px !important;
            background-position: 0 0, 10px 10px !important;
            background-color: #1a1a1a !important;
        }

        .cropper-face {
            background-color: rgba(30, 58, 138, 0.7) !important;
        }

        .cropper-line {
            background-color: #1e3a8a !important;
        }

        .cropper-point {
            background-color: #1e3a8a !important;
        }

        .cropper-grid {
            background-image: 
                linear-gradient(0deg, transparent 24%, rgba(30, 58, 138, 0.5) 25%, rgba(30, 58, 138, 0.5) 26%, transparent 27%, transparent 74%, rgba(30, 58, 138, 0.5) 75%, rgba(30, 58, 138, 0.5) 76%, transparent 77%, transparent),
                linear-gradient(90deg, transparent 24%, rgba(30, 58, 138, 0.5) 25%, rgba(30, 58, 138, 0.5) 26%, transparent 27%, transparent 74%, rgba(30, 58, 138, 0.5) 75%, rgba(30, 58, 138, 0.5) 76%, transparent 77%, transparent) !important;
            background-size: 30px 30px !important;
        }

        .cropper-dashed {
            border-color: #1e3a8a !important;
        }

        .cropper-center {
            background-color: rgba(30, 58, 138, 0.8) !important;
        }
    </style>

    @yield('head')
</head>
<body>
    <!-- Set user level globally -->
    <script>
        window.userLevel = {{ session('level') ? session('level') : 2 }};
        window.userId = '{{ session('id') ?? '' }}';
        console.log('🔑 User level set to:', window.userLevel);
        console.log('👤 User ID:', window.userId);
        console.log('📋 Permission system initializing...');
    </script>

    <!-- Sidebar Navigation -->
    @include('components.sidebar')

    <!-- Top Profile Navbar -->
    @include('components.top-navbar')

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('footer-new')

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Permission Enforcer System -->
    <script src="{{ asset('js/permission-enforcer.js') }}"></script>

    @yield('scripts')
</body>
</html>
