<!DOCTYPE html>
<html>
<head>
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

 <link rel="icon" href="{{ asset('foto/baobei.jpg') }}" type="image/png">

 <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
 <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">

<!-- Cropper.js Library -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script src="{{ asset('js/script.js') }}"></script>

<style>
    /* Layout adjustments for sidebar + top navbar */
    body {
        margin: 0;
        padding: 0;
    }

    main {
        margin-left: var(--sidebar-width, 260px);
        margin-top: 70px;
        padding: 20px;
        transition: margin-left 0.3s ease;
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
</head>
<body>
    <!-- Sidebar Navigation -->
    @include('components.sidebar')

    <!-- Top Profile Navbar -->
    @include('components.top-navbar')

    <!-- Main Content Wrapper -->
    <main>
        @yield('content')
