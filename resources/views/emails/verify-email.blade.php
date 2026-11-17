@extends('emails.layout', ['title' => 'Verify Your Email Address · BookStore'])

@section('content')
    <h1 class="title">Verify your email address</h1>
    <p class="lead">
        Welcome to BookStore, <strong>{{ $user->name }}</strong>. Before exploring catalog checkouts and order archives, please confirm your email address by clicking the verification link below.
    </p>

    <div class="btn-wrapper">
        <a href="{{ $url }}" class="btn">Verify Email Address</a>
    </div>

    <p class="lead" style="font-size: 13px; color: #71717a;">
        This verification link will remain active for 60 minutes. If you did not create a BookStore account, no further action is required and you may safely disregard this message.
    </p>

    <div class="subtext">
        If you are experiencing difficulty clicking the "Verify Email Address" button, copy and paste the following URL into your browser:<br>
        <a href="{{ $url }}">{{ $url }}</a>
    </div>
@endsection
