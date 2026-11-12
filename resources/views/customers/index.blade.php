@extends('layouts.app')
@section('content')
<div class="heading"><h1>Customer accounts</h1><a class="button" href="{{ route('customers.create') }}">Create customer</a></div>
<div class="table-wrap"><table><thead><tr><th>Full name</th><th>Email</th><th>Phone</th><th>Actions</th></tr></thead><tbody>
@forelse($customers as $customer)<tr>
<td>{{ $customer->full_name }}</td><td>{{ $customer->email }}</td><td>{{ $customer->phone }}</td><td class="actions"><a href="{{ route('customers.show', $customer) }}">View</a><a href="{{ route('customers.edit', $customer) }}">Edit</a>
<form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('Delete this record?')">@csrf @method('DELETE')<button class="danger" type="submit">Delete</button></form></td></tr>
@empty<tr><td colspan="6">No records found.</td></tr>@endforelse</tbody></table></div>
{{ $customers->links('pagination::simple-default') }}
@endsection
