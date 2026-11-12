@extends('layouts.app')
@section('content')<section class="card auth"><h1>Confirm password</h1><form method="POST" action="{{ route('password.confirm') }}">@csrf<label>Password<input type="password" name="password" required></label><button type="submit">Continue</button></form></section>@endsection
