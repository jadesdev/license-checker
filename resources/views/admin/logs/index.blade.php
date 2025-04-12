
@extends('layouts.admin')

@section('title', 'Validation Logs')

@section('content')
<div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Logs</p>
                    <p class="text-2xl font-bold">{{ number_format($stats['total']) }}</p>
                </div>
                <i class="fas fa-database text-gray-400 text-2xl"></i>
            </div>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Today's Logs</p>
                    <p class="text-2xl font-bold">{{ number_format($stats['today']) }}</p>
                </div>
                <i class="fas fa-calendar-day text-blue-400 text-2xl"></i>
            </div>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Valid</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['valid']) }}</p>
                </div>
                <i class="fas fa-check-circle text-green-400 text-2xl"></i>
            </div>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Invalid</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($stats['invalid']) }}</p>
                </div>
                <i class="fas fa-times-circle text-red-400 text-2xl"></i>
            </div>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Registrations</p>
                    <p class="text-2xl font-bold">{{ number_format($stats['registrations']) }}</p>
                </div>
                <i class="fas fa-user-plus text-purple-400 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="mt-6 bg-white p-4 rounded shadow">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium">Export Logs</h3>
                <p class="text-sm text-gray-500">Export filtered results as CSV</p>
            </div>
            <a href="{{ route('admin.logs.export', request()->query()) }}"
               class="btn-sm bg-green-600 text-white">
                <i class="fas fa-file-export mr-2"></i>
                Export CSV
            </a>
        </div>
    </div>
    <!-- Filters Card -->
    <div class="bg-white rounded shadow">
        <div class="p-4 border-b">
            <button @click="showFilters = !showFilters" class="flex items-center text-sm text-gray-600 hover:text-gray-800">
                <i class="fas fa-filter mr-2"></i>
                Advanced Filters
                <i :class="showFilters ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas ml-2"></i>
            </button>
        </div>

        <div x-show="showFilters" class="p-4 border-b">
            <form method="GET" action="{{ route('admin.logs.index') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Access Key</label>
                        <select name="access_key" class="input mt-1">
                            <option value="">All Keys</option>
                            @foreach($keys as $key)
                            <option value="{{ $key['key'] }}" {{ request('access_key') == $key['key'] ? 'selected' : '' }}>
                                {{ $key['label'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Domain</label>
                        <input type="text" name="domain" value="{{ request('domain') }}" class="input mt-1">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="input mt-1">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Date </label>
                        <div class="flex flex-col sm:flex-row gap-2 mt-1">
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="input">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                        <div class="flex flex-col sm:flex-row gap-2 mt-1">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="input">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">IP Address</label>
                        <input type="text" name="ip" value="{{ request('ip') }}" class="input mt-1">
                    </div>
                </div>


                <div class="mt-4 flex justify-end gap-2">
                    <a href="{{ route('admin.logs.index') }}" class="btn-sm bg-gray-100 text-gray-700">
                        Clear Filters
                    </a>
                    <button type="submit" class="btn-sm bg-blue-600 text-white">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th class="cursor-pointer" onclick="sortBy('created_at')">
                        Date/Time
                        @include('partials.sort-arrow', ['field' => 'created_at'])
                    </th>
                    <th>Domain</th>
                    <th>Access Key</th>
                    <th>IP Address</th>
                    <th class="cursor-pointer" onclick="sortBy('status')">
                        Status
                        @include('partials.sort-arrow', ['field' => 'status'])
                    </th>
                    <th>Auto Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('M j, Y H:i') }}</td>
                    <td class="font-mono text-sm">{{ $log->domain }}</td>
                    <td class="font-mono text-sm">
                        <a href="{{ route('admin.logs.by-key', $log->access_key) }}" class="text-blue-600 hover:underline">
                            {{ Str::limit($log->access_key, 8) }}
                        </a>
                    </td>
                    <td class="font-mono text-sm">{{ $log->ip_address }}</td>
                    <td>
                        <span class="badge {{ $log->status === 'valid' ? 'bg-green-500' : 'bg-red-500' }}">
                            {{ ucfirst($log->status) }}
                        </span>
                    </td>
                    <td>
                        @if($log->auto_registered)
                        <i class="fas fa-check text-green-500"></i>
                        @else
                        <i class="fas fa-times text-red-500"></i>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.logs.destroy', $log->id) }}"
                           class="text-red-500 hover:text-red-700"
                           onclick="return confirm('Delete this log entry?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-gray-500">
                        No logs found matching your criteria
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $logs->appends(request()->query())->links() }}
    </div>

</div>

<script>
function sortBy(field) {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);

    if(params.get('sort') === field && params.get('direction') === 'asc') {
        params.set('direction', 'desc');
    } else {
        params.set('direction', 'asc');
    }

    params.set('sort', field);
    window.location.href = url.pathname + '?' + params.toString();
}
</script>
@endsection
