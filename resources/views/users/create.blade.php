@extends('layouts.app')
@section('content')
<section class="card"><h1>Create User</h1>
<form method="POST" action="{{ route('users.store') }}">@csrf 
<label>Username<input type="text" name="username" value="{{ old('username') }}" maxlength="255" required></label>
<label>Full name<input type="text" name="full_name" value="{{ old('full_name') }}" maxlength="255" required></label>
<label>Email<input type="email" name="email" value="{{ old('email') }}" maxlength="255" required></label>
<label>Password<input type="password" name="password" minlength="8" autocomplete="new-password" required></label>
<label>Confirm password<input type="password" name="password_confirmation" autocomplete="new-password"></label>
<button type="submit">Save user</button> <a href="{{ route('users.index') }}">Cancel</a></form></section>
@endsection
