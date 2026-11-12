@extends('layouts.app')
@section('content')<section class="card auth"><h1>Reset password</h1><form method="POST" action="{{ route('password.email') }}">@csrf<label>Email<input type="email" name="email" value="{{ old('email') }}" required></label><button type="submit">Continue</button></form></section>@endsection
