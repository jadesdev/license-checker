@extends('layouts.admin')
@section('title', 'Access keys')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Access Keys</h1>
        <a href="{{ route('admin.access-keys.create') }}" class="btn">+ Create New</a>
    </div>

    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <input name="search" type="text" placeholder="Search..." value="{{ request('search') }}" class="input">
        <select name="tier" class="input">
            <option value="">All Tiers</option>
            @foreach ($tiers as $tier)
                <option value="{{ $tier }}" {{ request('tier') == $tier ? 'selected' : '' }}>{{ $tier }}</option>
            @endforeach
        </select>
        <select name="status" class="input">
            <option value="">All Statuses</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
            <option value="revoked" {{ request('status') == 'revoked' ? 'selected' : '' }}>Revoked</option>
        </select>
        <button type="submit" class="btn">Filter</button>
    </form>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="card text-center">
            <p class="text-gray-600">Total</p>
            <p class="text-xl font-bold">{{ $stats['total'] }}</p>
        </div>
        <div class="card text-center">
            <p class="text-gray-600">Active</p>
            <p class="text-xl font-bold text-green-600">{{ $stats['active'] }}</p>
        </div>
        <div class="card text-center">
            <p class="text-gray-600">Expired</p>
            <p class="text-xl font-bold text-yellow-600">{{ $stats['expired'] }}</p>
        </div>
        <div class="card text-center">
            <p class="text-gray-600">Revoked</p>
            <p class="text-xl font-bold text-red-600">{{ $stats['revoked'] }}</p>
        </div>
    </div>

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="table min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th>Key</th>
                    <th>Owner</th>
                    <th>Email</th>
                    <th>Tier</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @forelse($keys as $key)
                    <tr>
                        <td class="whitespace-nowrap">{{ $key->key }}</td>
                        <td>{{ $key->owner_name }}</td>
                        <td>{{ $key->owner_email }}</td>
                        <td>{{ $key->tier }}</td>
                        <td>
                            @if ($key->revoked)
                                <span class="badge bg-red-500">Revoked</span>
                            @elseif($key->isExpired())
                                <span class="badge bg-yellow-500">Expired</span>
                            @else
                                <span class="badge bg-green-500">Active</span>
                            @endif
                        </td>
                        <td>{{ $key->created_at->diffForHumans() }}</td>
                        <td>
                            <div class="flex gap-1">

                                <a href="{{ route('admin.access-keys.edit', $key->id) }}" class="btn-sm"><i class="fa fa-edit"></i></a>
                                <a href="{{ route('admin.access-keys.show', $key->id) }}" class="btn-success btn-sm "><i class="fa fa-eye"></i></a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-500 py-4">No access keys found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $keys->withQueryString()->links() }}
    </div>
@endsection
