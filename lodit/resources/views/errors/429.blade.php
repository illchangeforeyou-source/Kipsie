@extends('errors.layout')

@section('title', 'Too Many Requests')

@section('content')
    <div class="error-icon">
        <i class="bi bi-lightning-fill" style="color: #f59e0b;"></i>
    </div>
    <div class="error-code">429</div>
    <h1>Too Many Requests</h1>
    <p>You're sending requests too quickly. Please slow down and try again.</p>
    
    <div class="error-message">
        Rate limit exceeded. Please wait a moment before trying again.
    </div>

    <div class="button-group">
        <a href="/" class="btn btn-primary">
            <i class="bi bi-house-fill" style="margin-right: 8px;"></i> Go Home
        </a>
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="bi bi-arrow-left" style="margin-right: 8px;"></i> Go Back
        </a>
    </div>

    <p class="footer-text">Error Code: 429</p>
@endsection
