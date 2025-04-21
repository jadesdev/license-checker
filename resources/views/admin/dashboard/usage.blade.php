@extends('layouts.admin')

@section('title', 'Usage Statistics')

@section('content')
    <div class="px-4 py-6 w-full">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Usage Statistics</h1>
                <div class="flex items-center text-sm text-gray-500">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span>Usage Statistics</span>
                </div>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-200 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-700">Date Range Filter</h3>
            </div>
            <div class="p-6">
                <form id="dateRangeForm" method="GET" action="{{ route('admin.stats.usage') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Date Inputs (span 2 columns on medium screens and above) -->
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
                        <a href="{{ route('admin.stats.usage') }}"
                            class="px-4 py-2 bg-white text-gray-700 font-medium rounded-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>

            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow overflow-hidden transition-transform duration-300 hover:shadow-lg hover:translate-y-px">
                <div class="p-4 bg-blue-600 text-white text-center">
                    <h2 class="text-3xl font-bold mb-1">{{ number_format($summary['total']) }}</h2>
                    <div class="text-sm">Total Validations</div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow overflow-hidden transition-transform duration-300 hover:shadow-lg hover:translate-y-px">
                <div class="p-4 bg-green-600 text-white text-center">
                    <h2 class="text-3xl font-bold mb-1">{{ number_format($summary['valid']) }}</h2>
                    <div class="text-sm">Valid</div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow overflow-hidden transition-transform duration-300 hover:shadow-lg hover:translate-y-px">
                <div class="p-4 bg-red-600 text-white text-center">
                    <h2 class="text-3xl font-bold mb-1">{{ number_format($summary['invalid']) }}</h2>
                    <div class="text-sm">Invalid</div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow overflow-hidden transition-transform duration-300 hover:shadow-lg hover:translate-y-px">
                <div class="p-4 bg-cyan-600 text-white text-center">
                    <h2 class="text-3xl font-bold mb-1">{{ number_format($summary['registrations']) }}</h2>
                    <div class="text-sm">Auto Registrations</div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow overflow-hidden transition-transform duration-300 hover:shadow-lg hover:translate-y-px">
                <div class="p-4 bg-gray-600 text-white text-center">
                    <h2 class="text-3xl font-bold mb-1">{{ number_format($summary['unique_domains']) }}</h2>
                    <div class="text-sm">Unique Domains</div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow overflow-hidden transition-transform duration-300 hover:shadow-lg hover:translate-y-px">
                <div class="p-4 bg-gray-800 text-white text-center">
                    <h2 class="text-3xl font-bold mb-1">{{ number_format($summary['unique_keys']) }}</h2>
                    <div class="text-sm">Unique Keys</div>
                </div>
            </div>
        </div>

        <!-- Daily Validation Chart -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-200 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-700">Daily Validation Trends</h3>
            </div>
            <div class="p-4">
                <div id="dailyChart" class="h-80 w-full"></div>
            </div>
        </div>

        <!-- Status and Hourly Charts -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-700">Status Distribution</h3>
                </div>
                <div class="p-4">
                    <div id="statusChart" class="h-80 w-full"></div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-700">Hourly Distribution</h3>
                </div>
                <div class="p-4">
                    <div id="hourlyChart" class="h-80 w-full"></div>
                </div>
            </div>
        </div>

        <!-- Top IP Addresses -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-200 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                </svg>
                <h3 class="text-lg font-medium text-gray-700">Top IP Addresses</h3>
            </div>
            <div class="p-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($topIPs as $ip)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ip->ip_address }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($ip->count) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format(($ip->count / $summary['total']) * 100, 2) }}%</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.logs.index', ['ip' => $ip->ip_address]) }}"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        View Logs
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Daily Stats Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-200 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                </svg>
                <h3 class="text-lg font-medium text-gray-700">Daily Validation Data</h3>
            </div>
            <div class="p-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valid</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invalid</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registrations</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($dailyStats as $stat)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stat->date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($stat->total) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($stat->valid) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($stat->invalid) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($stat->registrations) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.36.3/apexcharts.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Daily Chart
            var dailyOptions = {
                series: [{
                        name: 'Total',
                        data: {!! json_encode($chartData['daily']['total']) !!}
                    },
                    {
                        name: 'Valid',
                        data: {!! json_encode($chartData['daily']['valid']) !!}
                    },
                    {
                        name: 'Invalid',
                        data: {!! json_encode($chartData['daily']['invalid']) !!}
                    },
                    {
                        name: 'Registrations',
                        data: {!! json_encode($chartData['daily']['registrations']) !!}
                    }
                ],
                chart: {
                    height: 320,
                    type: 'area',
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'inherit'
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                colors: ['#3b82f6', '#10b981', '#ef4444', '#06b6d4'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        opacityFrom: 0.6,
                        opacityTo: 0.1
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                    padding: {
                        left: 10,
                        right: 10
                    }
                },
                xaxis: {
                    categories: {!! json_encode($chartData['daily']['labels']) !!},
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '12px',
                            fontFamily: 'inherit'
                        }
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '12px',
                            fontFamily: 'inherit'
                        }
                    }
                },
                tooltip: {
                    x: {
                        format: 'dd MMM yyyy'
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '13px',
                    fontFamily: 'inherit',
                    markers: {
                        width: 12,
                        height: 12,
                        radius: 12
                    },
                    itemMargin: {
                        horizontal: 8
                    }
                }
            };

            var dailyChart = new ApexCharts(document.querySelector("#dailyChart"), dailyOptions);
            dailyChart.render();

            // Status Chart
            var statusOptions = {
                series: {!! json_encode($chartData['status']['data']) !!},
                chart: {
                    height: 320,
                    type: 'pie',
                    fontFamily: 'inherit'
                },
                labels: {!! json_encode($chartData['status']['labels']) !!},
                colors: ['#10b981', '#06b6d4', '#ef4444', '#f59e0b', '#3b82f6', '#8b5cf6', '#6b7280'],
                legend: {
                    position: 'right',
                    fontSize: '13px',
                    fontFamily: 'inherit',
                    markers: {
                        width: 12,
                        height: 12,
                        radius: 12
                    },
                    itemMargin: {
                        horizontal: 5,
                        vertical: 5
                    }
                },
                responsive: [{
                    breakpoint: 1024,
                    options: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return value + " validations";
                        }
                    }
                }
            };

            var statusChart = new ApexCharts(document.querySelector("#statusChart"), statusOptions);
            statusChart.render();

            // Hourly Chart
            var hourlyOptions = {
                series: [{
                    name: 'Validations',
                    data: {!! json_encode($chartData['hourly']['data']) !!}
                }],
                chart: {
                    height: 320,
                    type: 'bar',
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'inherit'
                },
                colors: ['#3b82f6'],
                plotOptions: {
                    bar: {
                        borderRadius: 3,
                        columnWidth: '70%',
                        dataLabels: {
                            position: 'top',
                        },
                    }
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    borderColor: '#f1f1f1',
                    padding: {
                        left: 10,
                        right: 10
                    }
                },
                xaxis: {
                    categories: {!! json_encode($chartData['hourly']['labels']) !!},
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '12px',
                            fontFamily: 'inherit'
                        }
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    title: {
                        text: 'Hour of Day (24h)',
                        style: {
                            fontSize: '12px',
                            fontFamily: 'inherit',
                            fontWeight: 600,
                            color: '#64748b'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '12px',
                            fontFamily: 'inherit'
                        }
                    },
                    title: {
                        text: 'Number of Validations',
                        style: {
                            fontSize: '12px',
                            fontFamily: 'inherit',
                            fontWeight: 600,
                            color: '#64748b'
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return value + " validations";
                        }
                    },
                    x: {
                        formatter: function(value) {
                            return "Hour: " + value + ":00";
                        }
                    }
                }
            };

            var hourlyChart = new ApexCharts(document.querySelector("#hourlyChart"), hourlyOptions);
            hourlyChart.render();
        });
    </script>
@endpush
