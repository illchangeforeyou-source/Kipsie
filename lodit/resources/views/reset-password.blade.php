@extends('layout')

@section('content')
<title>Reset Password</title>
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
                <h1>Create New Password</h1>
            </th>
        </tr>

        <form action="/update-password" method="post" id="resetForm">
            @csrf

            <input type="hidden" name="email" value="{{ $email ?? '' }}">
            <input type="hidden" name="code" value="{{ $code ?? '' }}">

            <tr>
                <td class="texting">
                    Email Address
                    <input type="email" name="display_email" id="display_email" value="{{ $email ?? '' }}" required readonly style="background: #f5f5f5; cursor: not-allowed;">
                </td>
            </tr>

            <tr>
                <td class="texting">
                    New Password
                    <input type="password" name="password" id="password" required placeholder="Enter new password">
                </td>
            </tr>

            <tr>
                <td class="texting">
                    Confirm Password
                    <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Confirm new password">
                </td>
            </tr>

            @if ($errors->has('password'))
                <tr>
                    <td style="color: #dc3545; font-size: 13px; padding: 10px 15px; background: #f8d7da; border-radius: 4px; margin: 10px 15px; border: 1px solid #f5c6cb;">
                        {{ $errors->first('password') }}
                    </td>
                </tr>
            @endif

            @if ($errors->has('email') || $errors->has('code'))
                <tr>
                    <td style="color: #dc3545; font-size: 13px; padding: 10px 15px; background: #f8d7da; border-radius: 4px; margin: 10px 15px; border: 1px solid #f5c6cb;">
                        {{ $errors->first('email') ?? $errors->first('code') }}
                    </td>
                </tr>
            @endif

            <tr>
                <td>
                    <button type="submit" class="btn">Update Password</button>
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
        <h2 class="text-center mb-5">Secure Password Reset</h2>

        <div class="row mb-5 align-items-center">
            <div class="col-md-6 d-flex justify-content-end">
                <img width="100" src="/foto/kaister.jpg" class="img-fluid rounded">
            </div>

            <div class="col-md-6">
                <p>
                    Create a strong, unique password to protect your account. Avoid using common words or patterns that are easy to guess.
                </p>
            </div>
        </div>

        <div class="row mb-5 align-items-center flex-md-row-reverse">
            <div class="col-md-6 d-flex justify-content-start">
                <img width="100" src="/foto/chipster.jpg" class="img-fluid rounded">
            </div>

            <div class="col-md-6 text-md-end">
                <p>
                    Your new password will be securely encrypted and stored. Make sure to keep your password confidential and never share it with anyone.
                </p>
            </div>
        </div>
    </div>
</section>

<script>
    // Password match checker
    document.getElementById('resetForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmation = document.getElementById('password_confirmation').value;
        
        if (password !== confirmation) {
            e.preventDefault();
            alert('Passwords do not match!');
            return false;
        }
    });
</script>

@endsection
