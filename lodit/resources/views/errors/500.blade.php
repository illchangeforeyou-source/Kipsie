@extends('errors.layout')

@section('title', 'Server Error')

@section('content')
    <div class="error-icon">
        <i class="bi bi-exclamation-triangle-fill" style="color: #764ba2;"></i>
    </div>
    <div class="error-code">500</div>
    <h1>Server Error</h1>
    <p>Something went wrong on our end. We're working to fix it.</p>
    
    <div class="error-message">
        The server encountered an unexpected condition that prevented it from fulfilling the request.
        @if(isset($message) && $message)
            <div style="margin-top:12px; font-family:monospace; background:#fff; color:#333; padding:10px; border-radius:6px;">{{ $message }}</div>
        @endif
        @if(isset($trace) && $trace)
            <button class="toggle-details" onclick="toggleErrorDetails()">Show details</button>
            <div class="error-details">{{ $trace }}</div>
        @endif
    </div>

    <div class="button-group">
        <a href="/" class="btn btn-primary">
            <i class="bi bi-house-fill" style="margin-right: 8px;"></i> Go Home
        </a>
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="bi bi-arrow-left" style="margin-right: 8px;"></i> Go Back
        </a>
    </div>

    <p class="footer-text">Error Code: 500 | Please try again later</p>
@endsection
