@extends('layout')

@section('content')
<title>Verify Reset Code</title>
<section class="wide-header">
    <div class="wide-header-inner centered">
        <img src="/foto/logo.jpg" alt="Logo" class="wide-logo">
        <span class="wide-title">KIPS</span>
    </div>
</section>

<div class="login-section">
    <table class="table1">
        <tr>
            <th class="login-header">
                <h1>Verify Code</h1>
            </th>
        </tr>

        <form action="/verify-reset-code" method="post" id="verifyForm">
            @csrf

            <tr>
                <td class="texting">
                    Email Address
                    <input type="email" name="email" id="email" value="{{ old('email', $email ?? '') }}" required readonly style="background: #f5f5f5; cursor: not-allowed;">
                </td>
            </tr>

            <tr>
                <td class="texting">
                    Verification Code
                    <input type="text" name="code" id="code" maxlength="6" placeholder="000000" required style="text-align: center; letter-spacing: 5px; font-size: 16px; font-weight: bold;">
                    <small style="display: block; margin-top: 5px; color: #666;">Check your email for the 6-digit code</small>
                </td>
            </tr>

            @if ($errors->has('code'))
                <tr>
                    <td style="color: #dc3545; font-size: 13px; padding: 10px 15px; background: #f8d7da; border-radius: 4px; margin: 10px 15px; border: 1px solid #f5c6cb;">
                        {{ $errors->first('code') }}
                    </td>
                </tr>
            @endif

            @if ($errors->has('email'))
                <tr>
                    <td style="color: #dc3545; font-size: 13px; padding: 10px 15px; background: #f8d7da; border-radius: 4px; margin: 10px 15px; border: 1px solid #f5c6cb;">
                        {{ $errors->first('email') }}
                    </td>
                </tr>
            @endif

            <tr>
                <td>
                    <button type="submit" class="btn">Verify Code</button>
                </td>
            </tr>

            <tr>
                <td style="padding: 15px 25px; text-align: center;">
                    <form action="/resend-code" method="post" style="display: inline;">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email ?? '' }}">
                        <button type="submit" style="background: none; border: none; color: #000000; cursor: pointer; text-decoration: underline; font-size: 14px; font-weight: 500; padding: 0;">
                            Didn't receive a code? Resend
                        </button>
                    </form>
                </td>
            </tr>

            <tr>
                <td>
                    <a href="/login" class="btn">Back to Login</a>
                </td>
            </tr>
        </form>
    </table>
</div>

<section class="info-section">
    <div class="container py-5 text-light">
        <h2 class="text-center mb-5">Code Verification</h2>

        <div class="row mb-5 align-items-center">
            <div class="col-md-6 d-flex justify-content-end">
                <img width="100" src="/foto/kaister.jpg" class="img-fluid rounded">
            </div>

            <div class="col-md-6">
                <p>
                    We've sent a 6-digit verification code to your email address. This code will expire in 15 minutes for your security.
                </p>
            </div>
        </div>

        <div class="row mb-5 align-items-center flex-md-row-reverse">
            <div class="col-md-6 d-flex justify-content-start">
                <img width="100" src="/foto/chipster.jpg" class="img-fluid rounded">
            </div>

            <div class="col-md-6 text-md-end">
                <p>
                    This verification step ensures that only you can reset your account password. Check your spam folder if you don't see the email.
                </p>
            </div>
        </div>
    </div>
</section>

<script>
    // Auto-format the code input to only accept numbers
    document.getElementById('code').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>

@endsection
