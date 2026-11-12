@extends('layouts.app')
@section('content')
<section class="card"><h1>Create Customer</h1>
<form method="POST" action="{{ route('customers.store') }}">@csrf 
<label>Full name<input type="text" name="full_name" value="{{ old('full_name') }}" maxlength="255" required></label>
<label>Email<input type="email" name="email" value="{{ old('email') }}" maxlength="255" required></label>
<label>Phone<input type="text" name="phone" value="{{ old('phone') }}" maxlength="20" ></label>
<button type="submit">Save customer</button> <a href="{{ route('customers.index') }}">Cancel</a></form></section>
@endsection
