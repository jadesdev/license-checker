@extends('layouts.admin')

@section('title', "Domain Logs - $domain")

@section('content')
<div class="space-y-6">
    <!-- Domain Header -->
    <div class="bg-white p-6 rounded shadow">
        <div class="flex flex-col sm:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex-1">
                <h1 class="text-2xl font-bold break-all">{{ $domain }}</h1>
                <div class="mt-2 text-sm text-gray-600 space-y-1">
                    <p><span class="font-medium">Total Validations:</span> {{ number_format($domainStats['total_validations']) }}</p>
                    <p><span class="font-medium">Unique Keys Used:</span> {{ $domainStats['keys_used'] }}</p>
                </div>
            </div>
            <div class="text-left sm:text-right space-y-2">
                <div>
                    <p class="text-lg font-semibold">{{ number_format($domainStats['total_validations']) }} Validations</p>
                    <p class="text-sm text-gray-600">{{ $domainStats['keys_used'] }} Unique Keys</p>
                </div>
                <div class="text-sm text-gray-500">
                    <p>First Seen: {{ $domainStats['first_seen']->format('M j, Y') ?? 'N/A' }}</p>
                    <p>Last Seen: {{ $domainStats['last_seen']->format('M j, Y H:i') ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Domain Details Card -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Total Validations</p>
            <p class="text-2xl font-bold">{{ number_format($domainStats['total_validations']) }}</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Unique Keys</p>
            <p class="text-2xl font-bold">{{ $domainStats['keys_used'] }}</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">First Seen</p>
            <p class="text-lg font-medium">
                {{ $domainStats['first_seen'] ? $domainStats['first_seen']->format('M j, Y') : 'N/A' }}
            </p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Last Seen</p>
            <p class="text-lg font-medium">
                {{ $domainStats['last_seen'] ? $domainStats['last_seen']->format('M j, Y H:i') : 'N/A' }}
            </p>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded shadow overflow-hidden">
        <div class="p-4 border-b flex items-center justify-between">
            <h2 class="text-lg font-medium">Validation History</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.logs.by-domain', $domain) }}" class="btn-sm">
                    <i class="fas fa-sync mr-2"></i>Refresh
                </a>
            </div>
        </div>
        @include('admin.logs.partials.logs-table', ['logs' => $logs])
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $logs->appends(request()->query())->links() }}
    </div>
</div>
@endsection
