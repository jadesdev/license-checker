@extends('layouts.admin')

@section('title', "Status Logs - " . ucfirst($status))

@section('content')
<div class="space-y-6">
    <!-- Status Header -->
    <div class="bg-white p-6 rounded shadow">
        <div class="flex flex-col sm:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex-1">
                <h1 class="text-2xl font-bold">{{ ucfirst($status) }} Validations</h1>
                <div class="mt-2 text-sm text-gray-600 space-y-1">
                    <p><span class="font-medium">Total Occurrences:</span> {{ number_format($statusStats['total']) }}</p>
                    <p><span class="font-medium">Today's Occurrences:</span> {{ $statusStats['today'] }}</p>
                </div>
            </div>
            <div class="text-left sm:text-right space-y-2">
                <div>
                    <p class="text-lg font-semibold">{{ number_format($statusStats['keys_affected']) }} Affected Keys</p>
                    <p class="text-sm text-gray-600">{{ $statusStats['domains_affected'] }} Affected Domains</p>
                </div>
                <div class="text-sm text-gray-500">
                    <p>First Occurrence: {{ $statusStats['first_seen'] ? $statusStats['first_seen']->format('M j, Y') : 'N/A' }}</p>
                    <p>Last Occurrence: {{ $statusStats['last_seen'] ? $statusStats['last_seen']->format('M j, Y H:i') : 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Metrics -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Total Occurrences</p>
            <p class="text-2xl font-bold">{{ number_format($statusStats['total']) }}</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Today's Count</p>
            <p class="text-2xl font-bold">{{ $statusStats['today'] }}</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Affected Keys</p>
            <p class="text-2xl font-bold">{{ $statusStats['keys_affected'] }}</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Affected Domains</p>
            <p class="text-2xl font-bold">{{ $statusStats['domains_affected'] }}</p>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded shadow overflow-hidden">
        <div class="p-4 border-b flex items-center justify-between">
            <h2 class="text-lg font-medium">Validation History</h2>
            <span class="badge {{ $status === 'valid' ? 'bg-green-500' : 'bg-red-500' }}">
                {{ ucfirst($status) }}
            </span>
        </div>
        @include('admin.logs.partials.logs-table', ['logs' => $logs])
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $logs->appends(request()->query())->links() }}
    </div>
</div>
@endsection
