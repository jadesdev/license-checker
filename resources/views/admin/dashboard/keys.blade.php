@extends('layouts.admin')

@section('title', 'Access Key Statistics')

@section('content')
<div class="px-4 py-6 w-full">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Access Key Statistics</h1>
            <div class="flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span>Access Key Statistics</span>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 flex items-center">
            <i class="fa-solid fa-filter text-gray-500 mr-2"></i>
            <h3 class="text-lg font-medium text-gray-700">Date Range Filter</h3>
        </div>
        <div class="p-6">
            <form id="dateRangeForm" method="GET" action="{{ route('admin.stats.keys') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:col-span-2">
                        <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            id="start_date" name="start_date" value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            id="end_date" name="end_date" value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}">
                    </div>
                </div>
                <div class="flex items-end justify-start space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Apply Filter
                    </button>
                    <a href="{{ route('admin.stats.keys') }}" class="px-4 py-2 bg-white text-gray-700 font-medium rounded-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow overflow-hidden transition-transform duration-300 hover:shadow-lg hover:translate-y-px">
            <div class="p-4 bg-blue-600 text-white text-center">
                <h2 class="text-3xl font-bold mb-1">{{ number_format($summary['total']) }}</h2>
                <div class="text-sm">Total Keys</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden transition-transform duration-300 hover:shadow-lg hover:translate-y-px">
            <div class="p-4 bg-green-600 text-white text-center">
                <h2 class="text-3xl font-bold mb-1">{{ number_format($summary['active']) }}</h2>
                <div class="text-sm">Active</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden transition-transform duration-300 hover:shadow-lg hover:translate-y-px">
            <div class="p-4 bg-red-600 text-white text-center">
                <h2 class="text-3xl font-bold mb-1">{{ number_format($summary['revoked']) }}</h2>
                <div class="text-sm">Revoked</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden transition-transform duration-300 hover:shadow-lg hover:translate-y-px">
            <div class="p-4 bg-amber-500 text-white text-center">
                <h2 class="text-3xl font-bold mb-1">{{ number_format($summary['expired']) }}</h2>
                <div class="text-sm">Expired</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden transition-transform duration-300 hover:shadow-lg hover:translate-y-px">
            <div class="p-4 bg-cyan-600 text-white text-center">
                <h2 class="text-3xl font-bold mb-1">{{ number_format($summary['expiring_soon']) }}</h2>
                <div class="text-sm">Expiring Soon</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden transition-transform duration-300 hover:shadow-lg hover:translate-y-px">
            <div class="p-4 bg-gray-600 text-white text-center">
                <h2 class="text-3xl font-bold mb-1">{{ number_format($summary['unused']) }}</h2>
                <div class="text-sm">Unused</div>
            </div>
        </div>
    </div>

    <!-- Key usage table -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 flex flex-wrap justify-between items-center">
            <div class="flex items-center">
                <i class="fa-solid fa-table text-gray-500 mr-2"></i>
                <h3 class="text-lg font-medium text-gray-700">Access Key Usage Statistics</h3>
            </div>
            <div class="flex mt-2 sm:mt-0">
                <div class="inline-flex rounded-md shadow-sm">
                    <a href="{{ route('admin.stats.keys', array_merge(request()->except(['sort', 'direction']), ['sort' => 'total_validations', 'direction' => request('direction') == 'desc' && request('sort') == 'total_validations' ? 'asc' : 'desc'])) }}"
                       class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-l-md {{ request('sort', 'total_validations') == 'total_validations' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                        Sort by Usage
                        @if(request('sort', 'total_validations') == 'total_validations')
                            <i class="fa-solid {{ request('direction', 'desc') == 'desc' ? 'fa-arrow-down' : 'fa-arrow-up' }} ml-1"></i>
                        @endif
                    </a>
                    <a href="{{ route('admin.stats.keys', array_merge(request()->except(['sort', 'direction']), ['sort' => 'key.created_at', 'direction' => request('direction') == 'desc' && request('sort') == 'key.created_at' ? 'asc' : 'desc'])) }}"
                       class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-r-md {{ request('sort') == 'key.created_at' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                        Sort by Date
                        @if(request('sort') == 'key.created_at')
                            <i class="fa-solid {{ request('direction', 'desc') == 'desc' ? 'fa-arrow-down' : 'fa-arrow-up' }} ml-1"></i>
                        @endif
                    </a>
                </div>
            </div>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Key</th>
                        <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                        <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Validations</th>
                        <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valid</th>
                        <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invalid</th>
                        <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unique Domains</th>
                        <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">First Used</th>
                        <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Used</th>
                        <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($paginator as $keyData)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>{{ substr($keyData['key']->key, 0, 8) }}...</div>
                            @if($keyData['is_expired'])
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                    Expired
                                </span>
                            @endif
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm">
                            <div class="text-gray-900">{{ $keyData['key']->owner_name }}</div>
                            <div class="text-gray-500 text-xs">{{ $keyData['key']->owner_email }}</div>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm">
                            @if(!$keyData['key']->revoked)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                    Revoked
                                </span>
                            @endif
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($keyData['total_validations']) }}</td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($keyData['valid_validations']) }}</td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($keyData['invalid_validations']) }}</td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($keyData['unique_domains']) }}</td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm">
                            @if($keyData['first_validation'])
                                <div class="text-gray-900">{{ $keyData['first_validation']->format('Y-m-d H:i') }}</div>
                                <div class="text-gray-500 text-xs">{{ $keyData['first_validation']->diffForHumans() }}</div>
                            @else
                                <span class="text-gray-500">Never used</span>
                            @endif
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm">
                            @if($keyData['last_validation'])
                                <div class="text-gray-900">{{ $keyData['last_validation']->format('Y-m-d H:i') }}</div>
                                <div class="text-gray-500 text-xs">{{ $keyData['last_validation']->diffForHumans() }}</div>
                            @else
                                <span class="text-gray-500">Never used</span>
                            @endif
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.access-keys.show', $keyData['key']->id) }}"
                                   class="inline-flex items-center p-1.5 border border-transparent rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.logs.by-key', $keyData['key']->key) }}"
                                   class="inline-flex items-center p-1.5 border border-transparent rounded-md shadow-sm text-white bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-3 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-key-skeleton text-gray-400 text-4xl mb-3"></i>
                                <span>No access keys found</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            {{ $paginator->links() }}
        </div>
    </div>
</div>
@endsection
