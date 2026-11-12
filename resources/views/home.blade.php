@extends('layouts.app')
@section('content')
<section class="hero"><p class="eyebrow">LOS SANTOS / ACCOUNT MANAGEMENT</p><h1>GTA V Laravel POS</h1><p>Manage customer records and user accounts with authenticated access.</p>
@auth <a class="button" href="{{ route('customers.index') }}">Manage customers</a> <a class="button secondary" href="{{ route('users.index') }}">Manage users</a>
@else <a class="button" href="{{ route('login') }}">Log in</a> <a class="button secondary" href="{{ route('register') }}">Create an account</a> @endauth</section>
@endsection
