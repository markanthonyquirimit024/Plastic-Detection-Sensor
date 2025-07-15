<!-- resources/views/auth/verify-email.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="alert alert-success">
            <h4>{{ session('message', 'Please check your email to verify your account.') }}</h4>
            <p>You've successfully verified your email address. You can now log in.</p>
            <a href="{{ route('login') }}" class="btn btn-primary">Go to Login</a>
        </div>
    </div>
@endsection
