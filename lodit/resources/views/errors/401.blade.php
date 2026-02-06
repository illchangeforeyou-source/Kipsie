@extends('errors.layout')

@section('title', 'Unauthorized')

@section('content')
    <div class="error-icon">
        <i class="bi bi-person-lock" style="color: #f59e0b;"></i>
    </div>
    <div class="error-code">401</div>
    <h1>Unauthorized</h1>
    <p>You need to be logged in to access this page.</p>
    
    <div class="error-message">
        Please log in with your credentials to continue.
    </div>

    <div class="button-group">
        <a href="/login" class="btn btn-primary">
            <i class="bi bi-door-open" style="margin-right: 8px;"></i> Go to Login
        </a>
        <a href="/" class="btn btn-secondary">
            <i class="bi bi-house-fill" style="margin-right: 8px;"></i> Go Home
        </a>
    </div>

    <p class="footer-text">Error Code: 401</p>
@endsection
