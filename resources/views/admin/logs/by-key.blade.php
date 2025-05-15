@extends('layouts.admin')

@section('title', "Key Logs - {$key->key}")

@section('content')
<div class="space-y-6">
    <!-- Key Header -->
    <div class="bg-white p-6 rounded shadow">
        <div class="flex flex-col sm:flex-row items-start md:items-center justify-between gap-4">
            <!-- Left Side: Key Details -->
            <div class="flex-1 mb-4 md:mb-0">
                <h1 class="text-2xl font-bold break-all">{{ $key->key }}</h1>
                <div class="mt-2 text-sm text-gray-600 space-y-1">
                    <p><span class="font-medium">Owner:</span> {{ $key->owner_name }} ({{ $key->owner_email }})</p>
                    <p><span class="font-medium">Tier:</span> {{ ucfirst($key->tier) }}</p>
                    <p><span class="font-medium">Status:</span>
                        <span class="badge {{ $key->status === 'revoked' ? 'bg-red-500' : 'bg-green-500' }}">
                            {{ $key->status === 'revoked' ? 'Revoked' : 'Active' }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <div class="space-y-4 md:space-y-2">
                    <div>
                        <p class="text-lg font-semibold">{{ number_format($keyStats['total_validations']) }} Validations</p>
                        <p class="text-sm text-gray-600">{{ $keyStats['domains_used'] }} Unique Domains</p>
                    </div>
                    <div class="text-sm text-gray-500">
                        <p>First Seen: {{ $keyStats['first_seen'] ? $keyStats['first_seen']->format('M j, Y') : 'N/A' }}</p>
                        <p>Last Seen: {{ $keyStats['last_seen'] ? $keyStats['last_seen']->format('M j, Y H:i') : 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Key Details Card -->
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Max Domains</p>
            <p class="text-2xl font-bold">{{ $key->max_domains }}</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Allowed Domains</p>
            <p class="text-2xl font-bold">{{ count($key->allowed_domains ?? []) }}</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Expires At</p>
            <p class="text-lg font-medium">
                {{ $key->expires_at ? $key->expires_at->format('M j, Y') : 'Never' }}
            </p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Grace Period</p>
            <p class="text-lg font-medium">{{ $key->grace_period_hours }} hours</p>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded shadow overflow-hidden">
        <div class="p-4 border-b flex items-center justify-between">
            <h2 class="text-lg font-medium">Validation History</h2>
            <a href="{{ route('admin.access-keys.edit', $key->id) }}" class="btn-sm">
                <i class="fas fa-edit mr-2"></i>Edit Key
            </a>
        </div>
        @include('admin.logs.partials.logs-table', ['logs' => $logs])
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $logs->appends(request()->query())->links() }}
    </div>
</div>
@endsection
