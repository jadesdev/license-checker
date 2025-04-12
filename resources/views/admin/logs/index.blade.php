@extends('layouts.admin')

@section('title', 'Validation Logs')

@section('content')
    <div class="space-y-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
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
            <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0 sm:space-x-4">
                <div class="text-center sm:text-left">
                    <h3 class="text-lg font-medium">Export Logs</h3>
                    <p class="text-sm text-gray-500">Export filtered results as CSV</p>
                </div>
                <a href="{{ route('admin.logs.export', request()->query()) }}" class="btn-sm bg-green-600 text-white flex items-center justify-center space-x-2">
                    <i class="fas fa-file-export"></i>
                    <span>Export CSV</span>
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
                                @foreach ($keys as $key)
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
                                @foreach ($statuses as $status)
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

        @include('admin.logs.partials.logs-table', ['logs' => $logs])

        <!-- Pagination -->
        <div class="mt-4">
            {{ $logs->appends(request()->query())->links() }}
        </div>

    </div>

@endsection
@push('styles')
    <style>
        .table {
            min-width: 1000px;
        }

        @media (min-width: 1024px) {
            .table {
                min-width: auto;
            }
        }

        .font-mono {
            font-family: Monaco, Consolas, "Liberation Mono", Courier, monospace;
        }
    </style>
@endpush
@push('scripts')
    <script>
        function sortBy(field) {
            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);

            if (params.get('sort') === field && params.get('direction') === 'asc') {
                params.set('direction', 'desc');
            } else {
                params.set('direction', 'asc');
            }

            params.set('sort', field);
            window.location.href = url.pathname + '?' + params.toString();
        }
    </script>
@endpush
