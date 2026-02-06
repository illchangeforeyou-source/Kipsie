@extends('errors.layout')

@section('title', 'Page Not Found')

@section('content')
    <div class="error-icon">
        <i class="bi bi-search" style="color: #667eea;"></i>
    </div>
    <div class="error-code">404</div>
    <h1>Page Not Found</h1>
    <p>The page you're looking for doesn't exist or has been moved.</p>
    
    <div class="error-message">
        Sorry! We couldn't find the resource you requested.
    </div>

    <div class="button-group">
        <a href="/" class="btn btn-primary">
            <i class="bi bi-house-fill" style="margin-right: 8px;"></i> Go Home
        </a>
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="bi bi-arrow-left" style="margin-right: 8px;"></i> Go Back
        </a>
    </div>

    <p class="footer-text">Error Code: 404</p>
@endsection
