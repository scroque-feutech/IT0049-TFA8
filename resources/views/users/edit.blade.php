@extends('layouts.app')
@section('content')
<section class="card"><h1>Edit User</h1>
<form method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data">@csrf @method('PUT')
<label>Username<input type="text" name="username" value="{{ old('username', $user->username) }}" maxlength="255" required></label>
<label>Full name<input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" maxlength="255" required></label>
<label>Email<input type="email" name="email" value="{{ old('email', $user->email) }}" maxlength="255" required></label>
<label>Password (leave blank to keep current password)<input type="password" name="password" minlength="8" autocomplete="new-password" ></label>
<label>Confirm password<input type="password" name="password_confirmation" autocomplete="new-password"></label>
<label>Avatar<input type="file" name="avatar" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG or WebP; up to 2 MB. Saved as a 400 × 400 JPG.</small></label>
<button type="submit">Save user</button> <a href="{{ route('users.index') }}">Cancel</a></form></section>
@endsection
