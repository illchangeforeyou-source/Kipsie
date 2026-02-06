@extends('layout')

@section('content')
<title>Forgot Password</title>
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
                <h1>Reset Password</h1>
            </th>
        </tr>

        <form action="/send-reset-code" method="post" id="forgotForm">
            @csrf

            <tr>
                <td class="texting">
                    Email Address
                    <input type="email" name="email" id="email" required placeholder="your.email@example.com">
                </td>
            </tr>

            @if ($errors->has('email'))
                <tr>
                    <td style="color: #dc3545; font-size: 13px; padding: 10px 15px; background: #f8d7da; border-radius: 4px; margin: 10px 15px; border: 1px solid #f5c6cb;">
                        {{ $errors->first('email') }}
                    </td>
                </tr>
            @endif

            @if (session('success'))
                <tr>
                    <td style="color: #155724; font-size: 13px; padding: 10px 15px; background: #d4edda; border-radius: 4px; margin: 10px 15px; border: 1px solid #c3e6cb;">
                        {{ session('success') }}
                    </td>
                </tr>
            @endif

            <tr>
                <td>
                    <button type="submit" class="btn">Send Reset Code</button>
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
        <h2 class="text-center mb-5">Password Recovery</h2>

        <div class="row mb-5 align-items-center">
            <div class="col-md-6 d-flex justify-content-end">
                <img width="100" src="/foto/kaister.jpg" class="img-fluid rounded">
            </div>

            <div class="col-md-6">
                <p>
                    Forgot your password? No worries! We'll help you regain access to your KIPS account by sending a verification code to your registered email address.
                </p>
            </div>
        </div>

        <div class="row mb-5 align-items-center flex-md-row-reverse">
            <div class="col-md-6 d-flex justify-content-start">
                <img width="100" src="/foto/chipster.jpg" class="img-fluid rounded">
            </div>

            <div class="col-md-6 text-md-end">
                <p>
                    Your account security is our priority. We use secure email verification to ensure only authorized users can reset their passwords.
                </p>
            </div>
        </div>
    </div>
</section>

@endsection
