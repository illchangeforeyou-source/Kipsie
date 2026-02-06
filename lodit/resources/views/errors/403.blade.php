@extends('errors.layout')

@section('title', 'Unauthorized')

@section('content')
    <div class="error-icon">
        <i class="bi bi-lock-fill" style="color: #667eea;"></i>
    </div>
    <div class="error-code">403</div>
    <h1>Access Forbidden</h1>
    <p>You don't have permission to access this resource.</p>
    
    <div class="error-message">
        Your access to this page has been denied. Contact support if you believe this is an error.
    </div>

    <div class="button-group">
        <a href="/" class="btn btn-primary">
            <i class="bi bi-house-fill" style="margin-right: 8px;"></i> Go Home
        </a>
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="bi bi-arrow-left" style="margin-right: 8px;"></i> Go Back
        </a>
    </div>

    <p class="footer-text">Error Code: 403</p>
@endsection
