@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="px-4 py-6 w-full">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                <div class="flex items-center text-sm text-gray-500">
                    <span>Dashboard</span>
                </div>
            </div>
        </div>

        <!-- Summary Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6 mb-6">
            <!-- Access Keys Card -->
            <div class="bg-white rounded-lg shadow overflow-hidden transition-transform duration-300 hover:shadow-lg hover:translate-y-px">
                <div class="p-4 bg-blue-600 text-white">
                    <div class="flex justify-between items-center">
                        <h5 class="font-medium text-lg">Access Keys</h5>
                        <span class="rounded-full bg-white bg-opacity-30 p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </span>
                    </div>
                    <h2 class="text-3xl font-bold mt-2">{{ $keyStats['total'] }}</h2>
                </div>
                <div class="p-4 bg-blue-700 text-white">
                    <div class="grid grid-cols-3 gap-2 mb-2 text-sm">
                        <div class="text-center">
                            <div class="font-semibold text-lg">{{ $keyStats['active'] }}</div>
                            <div class="text-blue-100">Active</div>
                        </div>
                        <div class="text-center">
                            <div class="font-semibold text-lg">{{ $keyStats['revoked'] }}</div>
                            <div class="text-blue-100">Revoked</div>
                        </div>
                        <div class="text-center">
                            <div class="font-semibold text-lg">{{ $keyStats['expiring_soon'] }}</div>
                            <div class="text-blue-100">Expiring</div>
                        </div>
                    </div>
                    <a href="{{ route('admin.access-keys.index') }}" class="flex justify-between items-center text-white hover:text-blue-100 mt-3 text-sm font-medium">
                        <span>View Details</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Validations Card -->
            <div class="bg-white rounded-lg shadow overflow-hidden transition-transform duration-300 hover:shadow-lg hover:translate-y-px">
                <div class="p-4 bg-green-600 text-white">
                    <div class="flex justify-between items-center">
                        <h5 class="font-medium text-lg">Validations</h5>
                        <span class="rounded-full bg-white bg-opacity-30 p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    </div>
                    <h2 class="text-3xl font-bold mt-2">{{ $validationStats['total'] }}</h2>
                </div>
                <div class="p-4 bg-green-700 text-white">
                    <div class="grid grid-cols-3 gap-2 mb-2 text-sm">
                        <div class="text-center">
                            <div class="font-semibold text-lg">{{ $validationStats['today'] }}</div>
                            <div class="text-green-100">Today</div>
                        </div>
                        <div class="text-center">
                            <div class="font-semibold text-lg">{{ $validationStats['this_week'] }}</div>
                            <div class="text-green-100">This Week</div>
                        </div>
                        <div class="text-center">
                            <div class="font-semibold text-lg">{{ $validationStats['this_month'] }}</div>
                            <div class="text-green-100 text-xs">This Month</div>
                        </div>
                    </div>
                    <a href="{{ route('admin.logs.index') }}" class="flex justify-between items-center text-white hover:text-green-100 mt-3 text-sm font-medium">
                        <span>View Details</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Valid vs Invalid Card -->
            <div class="bg-white rounded-lg shadow overflow-hidden transition-transform duration-300 hover:shadow-lg hover:translate-y-px">
                <div class="p-4 bg-cyan-600 text-white">
                    <div class="flex justify-between items-center">
                        <h5 class="font-medium text-lg">Valid vs Invalid</h5>
                        <span class="rounded-full bg-white bg-opacity-30 p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-3">
                        <div class="text-center">
                            <h3 class="text-3xl font-bold mb-1">{{ $validationStats['valid'] }}</h3>
                            <span class="text-cyan-100">Valid</span>
                        </div>
                        <div class="text-center">
                            <h3 class="text-3xl font-bold mb-1">{{ $validationStats['invalid'] }}</h3>
                            <span class="text-cyan-100">Invalid</span>
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-cyan-700 text-white">
                    <div class="mb-2">
                        <span class="text-sm">Auto Registrations: {{ $validationStats['registrations'] }}</span>
                    </div>
                    <a href="{{ route('admin.stats.usage') }}" class="flex justify-between items-center text-white hover:text-cyan-100 mt-3 text-sm font-medium">
                        <span>View Details</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Quick Links Card -->
            <div class="bg-white rounded-lg shadow overflow-hidden transition-transform duration-300 hover:shadow-lg hover:translate-y-px">
                <div class="p-4 bg-amber-500 text-white">
                    <div class="flex justify-between items-center">
                        <h5 class="font-medium text-lg">Quick Links</h5>
                        <span class="rounded-full bg-white bg-opacity-30 p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="divide-y divide-gray-100">
                    <a href="{{ route('admin.stats.usage') }}" class="p-3 flex items-center text-gray-700 hover:bg-amber-50 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Usage Statistics</span>
                    </a>
                    <a href="{{ route('admin.stats.keys') }}" class="p-3 flex items-center text-gray-700 hover:bg-amber-50 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        <span>Key Statistics</span>
                    </a>
                    <a href="{{ route('admin.stats.domains') }}" class="p-3 flex items-center text-gray-700 hover:bg-amber-50 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                        <span>Domain Statistics</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Validation Chart -->
        <div class="mb-6">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-700">Validations (Last 14 Days)</h3>
                </div>
                <div class="p-4">
                    <div id="validationChart" class="h-80 w-full"></div>
                </div>
            </div>
        </div>

        <!-- Top Domains, Keys, and Activity -->
        <div class="grid grid-cols-1 gap-6">
            <!-- Active domains -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200 flex items-center">

                    <i class="mx-1 fa fa-globe"></i>
                    <h3 class="text-lg font-medium text-gray-700">Most Active Domains (Last 30 Days)</h3>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domain</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Validations</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($activeDomains as $domain)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900">{{ $domain->domain }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900">{{ $domain->validation_count }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-right">
                                            <a href="{{ route('admin.logs.by-domain', $domain->domain) }}"
                                                class="inline-flex items-center p-1.5 border border-gray-300 rounded text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition-colors duration-200">
                                                <i class="mx-1 fa fa-search"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-3 py-4 text-sm text-gray-500 text-center">No domain activity found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="p-3 bg-gray-50 border-t border-gray-200">
                    <a href="{{ route('admin.stats.domains') }}" class="flex items-center justify-end text-sm font-medium text-blue-600 hover:text-blue-800">
                        <span>View all domain statistics</span>

                        <i class="mx-1 fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Active keys -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200 flex items-center">

                    <i class="mx-1 fa fa-key"></i>
                    <h3 class="text-lg font-medium text-gray-700">Most Used Access Keys (Last 30 Days)</h3>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Validations</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($activeKeys as $keyData)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-3 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $keyData['owner_name'] }}</div>
                                            <div class="text-xs text-gray-500">{{ substr($keyData['key'], 0, 18) }}...</div>
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900">{{ $keyData['validation_count'] }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900">{{ $keyData['owner_name'] }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-right">
                                            <a href="{{ route('admin.logs.by-key', $keyData['key']) }}"
                                                class="inline-flex items-center p-1.5 border border-gray-300 rounded text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition-colors duration-200">
                                                <i class="mx-1 fa fa-search"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-3 py-4 text-sm text-gray-500 text-center">No key activity found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="p-3 bg-gray-50 border-t border-gray-200">
                    <a href="{{ route('admin.stats.keys') }}" class="flex items-center justify-end text-sm font-medium text-blue-600 hover:text-blue-800">
                        <span>View all key statistics</span>

                        <i class="mx-1 fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Recent activity -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200 flex items-center">

                    <i class="mx-2 far fa-clock"></i>
                    <h3 class="text-lg font-medium text-gray-700">Recent Activity</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        @forelse($recentActivity as $log)
                            <div class="p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                                <div class="flex justify-between items-start mb-1">
                                    <div class="flex items-center">
                                        @if ($log->status == 'valid')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 font-medium">Valid</span>
                                        @elseif($log->status == 'domain_registered')
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 font-medium">Registered</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 font-medium">Invalid</span>
                                        @endif
                                        <span class="ml-2 text-sm font-medium text-gray-900">{{ $log->domain }}</span>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-xs text-gray-500">Key: {{ substr($log->access_key, 0, 18) }}... | IP: {{ $log->ip_address }}</div>
                            </div>
                        @empty
                            <div class="p-4 text-sm text-gray-500 text-center">No recent activity found</div>
                        @endforelse
                    </div>
                </div>
                <div class="p-3 bg-gray-50 border-t border-gray-200">
                    <a href="{{ route('admin.logs.index') }}" class="flex items-center justify-end text-sm font-medium text-blue-600 hover:text-blue-800">
                        <span>View all logs</span>
                        <i class="mx-2 fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.36.3/apexcharts.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var options = {
                series: [{
                        name: 'Total Validations',
                        data: {!! json_encode($chartData['validations']) !!}
                    },
                    {
                        name: 'Valid',
                        data: {!! json_encode($chartData['valid']) !!}
                    },
                    {
                        name: 'Invalid',
                        data: {!! json_encode($chartData['invalid']) !!}
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 320,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'inherit'
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                colors: ['#3b82f6', '#10b981', '#ef4444'],
                xaxis: {
                    categories: {!! json_encode($chartData['labels']) !!},
                    labels: {
                        style: {
                            colors: '#475569',
                            fontSize: '13px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#475569',
                            fontSize: '13px'
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " validations";
                        }
                    },
                    x: {
                        format: 'yyyy-MM-dd'
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '14px'
                }
            };

            var chart = new ApexCharts(document.querySelector("#validationChart"), options);
            chart.render();
        });
    </script>
@endpush
