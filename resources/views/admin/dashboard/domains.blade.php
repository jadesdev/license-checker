@extends('layouts.admin')

@section('title', 'Domain Statistics')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-semibold text-gray-800 mt-4">Domain Analytics</h1>
        <nav class="flex py-3 mb-4">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                </li>
                <li class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                    </svg>
                    <span class="text-gray-500 ml-1 md:ml-2">Domain Analytics</span>
                </li>
            </ol>
        </nav>

        <!-- Date Range Filter -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="bg-gray-50 px-4 py-3 border-b rounded-t-lg">
                <div class="flex items-center">
                    <i class="fa-solid fa-filter mr-2 text-gray-600"></i>
                    <h3 class="text-gray-700 font-medium">Date Range Filter</h3>
                </div>
            </div>
            <div class="p-4">
                <form id="dateRangeForm" method="GET" action="{{ route('admin.stats.domains') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:col-span-2">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" id="start_date" name="start_date" value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" id="end_date" name="end_date" value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-end justify-start space-x-2">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            Apply Filter
                        </button>
                        <a href="{{ route('admin.stats.domains') }}"
                            class="px-4 py-2 bg-white text-gray-700 font-medium rounded-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>

            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-600 text-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 text-center">
                    <h2 class="text-3xl font-bold mb-1">{{ number_format($summary['total_domains']) }}</h2>
                    <div class="text-blue-100">Total Domains</div>
                </div>
            </div>
            <div class="bg-green-600 text-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 text-center">
                    <h2 class="text-3xl font-bold mb-1">{{ number_format($summary['total_validations']) }}</h2>
                    <div class="text-green-100">Total Validations</div>
                </div>
            </div>
            <div class="bg-cyan-600 text-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 text-center">
                    <h2 class="text-3xl font-bold mb-1">{{ number_format($summary['valid_validations']) }}</h2>
                    <div class="text-cyan-100">Valid Validations</div>
                </div>
            </div>
            <div class="bg-amber-400 text-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="p-4 text-center">
                    <h2 class="text-3xl font-bold mb-1">{{ number_format($summary['single_use_domains']) }}</h2>
                    <div class="text-amber-900">Single Use Domains</div>
                </div>
            </div>
        </div>

        <!-- Domain List Table -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="bg-gray-50 px-4 py-3 border-b rounded-t-lg">
                <div class="flex items-center">
                    <i class="fa-solid fa-globe mr-2 text-gray-600"></i>
                    <h3 class="text-gray-700 font-medium">Domain Statistics</h3>
                </div>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domain</th>
                                <th class="px-4 py-2 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Validations</th>
                                <th class="px-4 py-2 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registrations</th>
                                <th class="px-4 py-2 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unique Keys</th>
                                <th class="px-4 py-2 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider">First Seen</th>
                                <th class="px-4 py-2 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Seen</th>
                                <th class="px-4 py-2 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($paginator as $domain)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 border text-sm text-gray-900">{{ $domain['domain'] }}</td>
                                    <td class="px-4 py-3 border text-sm text-gray-900">
                                        <div>Invalid: {{ number_format($domain['invalid_count']) }}</div>
                                        <div>Valid: {{ number_format($domain['valid_count']) }}</div>
                                        <div>Total: {{ number_format($domain['validation_count']) }}</div>
                                    </td>
                                    <td class="px-4 py-3 border text-sm text-gray-900">{{ number_format($domain['registration_count']) }}</td>
                                    <td class="px-4 py-3 border text-sm text-gray-900">{{ number_format($domain['unique_keys']) }}</td>
                                    <td class="px-4 py-3 border text-sm">
                                        @if ($domain['first_seen'])
                                            <div class="text-gray-900">{{ $domain['first_seen']->format('Y-m-d') }}</div>
                                            <div class="text-xs text-gray-500">{{ $domain['first_seen']->diffForHumans() }}</div>
                                        @else
                                            <span class="text-gray-500">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 border text-sm">
                                        @if ($domain['last_seen'])
                                            <div class="text-gray-900">{{ $domain['last_seen']->format('Y-m-d') }}</div>
                                            <div class="text-xs text-gray-500">{{ $domain['last_seen']->diffForHumans() }}</div>
                                        @else
                                            <span class="text-gray-500">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 border text-sm whitespace-nowrap">
                                        <div class="flex space-x-1">
                                            <a href="{{ route('admin.logs.by-domain', $domain['domain']) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 text-white bg-blue-600 rounded-md hover:bg-blue-700" title="View Logs">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.logs.index', ['domain' => $domain['domain']]) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 text-white bg-cyan-600 rounded-md hover:bg-cyan-700"
                                                title="View Details">
                                                <i class="fa-solid fa-magnifying-glass"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
                <div class="mt-4">
                    {{ $paginator->withQueryString()->links() }}
                </div>
            </div>
        </div>

        <!-- Domain Analytics Chart (using ApexCharts) -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="bg-gray-50 px-4 py-3 border-b rounded-t-lg">
                <div class="flex items-center">
                    <i class="fa-solid fa-chart-line mr-2 text-gray-600"></i>
                    <h3 class="text-gray-700 font-medium">Validation Trends</h3>
                </div>
            </div>
            <div class="p-4">
                <div id="domain-validation-chart" class="h-64"></div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var options = {
                series: [{
                    name: 'Valid',
                    data: [
                        @foreach ($paginator->take(10) as $domain)
                            {{ $domain['valid_count'] }},
                        @endforeach
                    ]
                }, {
                    name: 'Invalid',
                    data: [
                        @foreach ($paginator->take(10) as $domain)
                            {{ $domain['invalid_count'] }},
                        @endforeach
                    ]
                }],
                chart: {
                    type: 'bar',
                    height: 256,
                    stacked: true,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '60%',
                    }
                },
                dataLabels: {
                    enabled: false
                },
                colors: ['#10B981', '#EF4444'],
                xaxis: {
                    categories: [
                        @foreach ($paginator->take(10) as $domain)
                            '{{ $domain['domain'] }}',
                        @endforeach
                    ],
                    labels: {
                        rotate: -45,
                        trim: true,
                        maxHeight: 120
                    }
                },
                legend: {
                    position: 'top'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#domain-validation-chart"), options);
            chart.render();
        });
    </script>
@endpush
