@extends('errors.layout')

@section('title', 'Service Unavailable')

@section('content')
    <div class="error-icon">
        <i class="bi bi-clock-history" style="color: #764ba2;"></i>
    </div>
    <div class="error-code">503</div>
    <h1>Service Unavailable</h1>
    <p>The service is temporarily unavailable. Please try again later.</p>
    
    <div class="error-message">
        Our system is undergoing maintenance. We'll be back online shortly.
    </div>

    <div class="button-group">
        <a href="/" class="btn btn-primary">
            <i class="bi bi-house-fill" style="margin-right: 8px;"></i> Go Home
        </a>
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="bi bi-arrow-left" style="margin-right: 8px;"></i> Go Back
        </a>
    </div>

    <p class="footer-text">Error Code: 503</p>
@endsection
