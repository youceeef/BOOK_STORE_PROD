@extends('emails.layout', ['title' => 'Password Recovery Instructions · BookStore'])

@section('content')
    <h1 class="title">Reset your password</h1>
    <p class="lead">
        Hello <strong>{{ $user->name ?? 'Reader' }}</strong>,<br>
        We received a request to reset the password for your BookStore account. Click the button below to choose a new password:
    </p>

    <div class="btn-wrapper">
        <a href="{{ $url }}" class="btn">Reset Password</a>
    </div>

    <p class="lead" style="font-size: 13px; color: #71717a;">
        This password reset link will expire in <strong>60 minutes</strong>.<br>
        If you did not request a password reset, no further action is required. Your account remains secure.
    </p>

    <div class="subtext">
        If you are experiencing difficulty clicking the "Reset Password" button, copy and paste the following URL into your browser:<br>
        <a href="{{ $url }}">{{ $url }}</a>
    </div>
@endsection
