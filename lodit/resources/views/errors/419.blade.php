@extends('errors.layout')

@section('title', 'Session Expired')

@section('content')
    <div class="error-icon">
        <i class="bi bi-exclamation-circle" style="color: #ef4444;"></i>
    </div>
    <div class="error-code">419</div>
    <h1>Session Expired</h1>
    <p>Your session has expired. Please refresh and try again.</p>
    
    <div class="error-message">
        For security purposes, sessions expire after a period of inactivity. Please log in again.
    </div>

    <div class="button-group">
        <a href="/login" class="btn btn-primary">
            <i class="bi bi-door-open" style="margin-right: 8px;"></i> Log In Again
        </a>
        <a href="/" class="btn btn-secondary">
            <i class="bi bi-house-fill" style="margin-right: 8px;"></i> Go Home
        </a>
    </div>

    <p class="footer-text">Error Code: 419</p>
@endsection
