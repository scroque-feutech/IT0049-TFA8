@extends('layouts.app')
@section('content')<section class="card auth"><h1>Log in</h1><form method="POST" action="{{ route('login') }}">@csrf
<label>Email<input type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus></label>
<label>Password<input type="password" name="password" autocomplete="current-password" required></label>
<label class="check"><input type="checkbox" name="remember" value="1"> Remember me</label><button type="submit">Log in</button></form>
<p>New here? <a href="{{ route('register') }}">Register</a></p>
@if(config('mail.default') !== 'log')<a href="{{ route('password.request') }}">Forgot password?</a>@endif</section>@endsection
