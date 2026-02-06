@extends('layout')

@section('content')

<title>Register</title>
<section class="wide-header">
    <div class="wide-header-inner centered">
        <img src="/foto/logo.jpg" alt="Logo" class="wide-logo">
        <span class="wide-title">KIPS</span>
    </div>
</section>

@if($is_online)
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

<div class="login-section">
    <table class="table1">
        <tr>
            <th><h1>Register</h1></th>
        </tr>

        <form action="/registersave" method="post" id="registerForm">
            @csrf

            <tr>
                <td class="texting">
                    Username
                    <input type="text" name="u" required>
                </td>
            </tr>

            <tr>
                <td class="texting">
                    Email
                    <input type="email" name="email" id="email" required>
                </td>
            </tr>

            <tr>
                <td class="texting">
                    Password
                    <input type="password" name="p" required>
                </td>
            </tr>

            <tr>
                <td class="texting">
                    Age
                    <input type="number" name="age" min="13" max="120" required>
                </td>
            </tr>

            @if($is_online)
                <!-- Google reCAPTCHA v2 for online mode -->
                <tr>
                    <td class="texting captcha-row-online">
                        <div class="g-recaptcha" data-sitekey="{{ $recaptcha_key }}" style="transform: scale(0.9); transform-origin: 0 0;"></div>
                    </td>
                </tr>
            @else
                <!-- Math Captcha for offline mode -->
                <tr>
                    <td class="texting captcha-row">
                        <label for="captcha" class="captcha-label">Verify: <span class="captcha-question">{{ $captcha_question ?? '?' }}</span></label>
                        <input type="hidden" name="captcha_answer_hidden" value="{{ $captcha_answer ?? '' }}">
                        <input type="number" name="captcha_answer" id="captcha" class="captcha-input" placeholder="Enter the answer" required>
                    </td>
                </tr>
            @endif

            <tr>
                <td>
                    <button type="submit" class="btn">Register</button>
                </td>
            </tr>

            <tr>
                <td>
                    <a href="/login" class="btn">Already have an account?</a>
                </td>
            </tr>
            <tr><td>
@if ($errors->any())
    <div style="color:red">
        {{ $errors->first() }}
    </div>
@endif
</td></tr>
        </form>
    </table>
    
</div>

<section class="info-section">
    <div class="container py-5 text-light">
        <h2 class="text-center mb-5">About KIPS</h2>

        <div class="row mb-5 align-items-center">
            <div class="col-md-6 d-flex justify-content-end">
                <img width="100" src="/foto/kaister.jpg" class="img-fluid rounded">
            </div>

            <div class="col-md-6">
                <p>
                 KIPS will bring you the guarantee to health, medicine, treatments, and everything to make you and your family be happy and healthy together.

                </p>
            </div>
        </div>

        <div class="row mb-5 align-items-center flex-md-row-reverse">
            <div class="col-md-6 d-flex justify-content-start">
                <img width="100" src="/foto/chipster.jpg" class="img-fluid rounded">
            </div>

            <div class="col-md-6 text-md-end">
                <p>
                    With our trained and experienced staff, we can guarantee you will be satisfied with your time.
                </p>
            </div>
        </div>
    </div>
</section>

@endsection
