@extends('layouts.app')

@section('content')
<div class="flex-center">
    <div class="glass-card" style="padding: 3rem; width: 100%; max-width: 400px; text-align: center;">
        <h1 style="margin-top: 0; font-size: 2rem; margin-bottom: 0.5rem;">Welcome Back</h1>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Sign in to manage billboards</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-6" style="text-align: left;">
                <label for="login_email" style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem;">Email Address</label>
                <input id="login_email" type="email" name="login_email" class="form-input" required autofocus placeholder="admin@example.com">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Sign In
            </button>
        </form>
        
        <div class="mt-4" style="font-size: 0.8rem; color: var(--text-muted);">
            Note: Use existing email to login (e.g. admin@example.com)
        </div>
    </div>
</div>
@endsection
