@extends('layouts.app')
@section('content')
<div class="heading"><h1>User accounts</h1><a class="button" href="{{ route('users.create') }}">Create user</a></div>
<div class="table-wrap"><table><thead><tr><th>Avatar</th><th>Username</th><th>Full name</th><th>Email</th><th>Actions</th></tr></thead><tbody>
@forelse($users as $user)<tr>
<td><img class="avatar" src="{{ $user->avatar ? route('avatars.show', basename($user->avatar)) : asset('avatar-placeholder.svg') }}" alt="Avatar"></td>
<td>{{ $user->username }}</td><td>{{ $user->full_name }}</td><td>{{ $user->email }}</td><td class="actions"><a href="{{ route('users.show', $user) }}">View</a><a href="{{ route('users.edit', $user) }}">Edit</a>
@if(!auth()->user()->is($user))<form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Delete this record?')">@csrf @method('DELETE')<button class="danger" type="submit">Delete</button></form>@endif</td></tr>
@empty<tr><td colspan="6">No records found.</td></tr>@endforelse</tbody></table></div>
{{ $users->links('pagination::simple-default') }}
@endsection
