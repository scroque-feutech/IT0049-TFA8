@extends('layouts.app')
@section('content')
<section class="card"><h1>Edit Customer</h1>
<form method="POST" action="{{ route('customers.update', $customer) }}">@csrf @method('PUT')
<label>Full name<input type="text" name="full_name" value="{{ old('full_name', $customer->full_name) }}" maxlength="255" required></label>
<label>Email<input type="email" name="email" value="{{ old('email', $customer->email) }}" maxlength="255" required></label>
<label>Phone<input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" maxlength="20" ></label>
<button type="submit">Save customer</button> <a href="{{ route('customers.index') }}">Cancel</a></form></section>
@endsection
