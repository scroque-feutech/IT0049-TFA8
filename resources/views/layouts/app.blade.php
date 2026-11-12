<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ config('app.name') }}</title><link rel="stylesheet" href="{{ asset('style.css') }}"></head><body>
<header><strong>GTA V · POS</strong><span>TFA8 / Laravel</span></header>
<nav><a href="{{ route('home') }}">Home</a><a href="{{ route('about') }}">About</a>
@auth <a href="{{ route('customers.index') }}">Customers</a><a href="{{ route('users.index') }}">Users</a><span>{{ auth()->user()->full_name }}</span><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Log out</button></form>
@else <a href="{{ route('login') }}">Log in</a><a href="{{ route('register') }}">Register</a> @endauth</nav>
<main>@if(session('success'))<div class="success">{{ session('success') }}</div>@endif
@if(session('status'))<div class="success">{{ session('status') }}</div>@endif
@if($errors->any())<div class="warning" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')</main><footer>IT0049 · Resource Controllers &amp; Authentication</footer></body></html>
