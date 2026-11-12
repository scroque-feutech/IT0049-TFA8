@extends('layouts.app')
@section('content')<section class="card auth"><h1>Create your account</h1><form method="POST" action="{{ route('register') }}">@csrf
<label>Full name<input name="full_name" value="{{ old('full_name') }}" maxlength="255" autocomplete="name" required></label>
<label>Username<input name="username" value="{{ old('username') }}" maxlength="255" required></label>
<label>Email<input type="email" name="email" value="{{ old('email') }}" maxlength="255" autocomplete="username" required></label>
<label>Password<input type="password" name="password" minlength="8" autocomplete="new-password" required></label>
<label>Confirm password<input type="password" name="password_confirmation" autocomplete="new-password" required></label>
<button type="submit">Register</button></form><p>Already registered? <a href="{{ route('login') }}">Log in</a></p></section>@endsection
