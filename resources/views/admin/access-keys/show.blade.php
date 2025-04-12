@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Access Key Details</h1>
        <a href="{{ route('admin.access-keys.edit', $key->id) }}" class="btn-sm">
            Edit Key
        </a>
    </div>

    <div class="bg-white p-6 rounded shadow space-y-4 mb-6">
        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-b pb-4">
            <div>
                <p class="font-medium">Key ID:</p>
                <p class="text-gray-600 text-sm">{{ $key->id }}</p>
            </div>
            <div>
                <p class="font-medium">Access Key:</p>
                <p class="text-gray-600 text-sm font-mono break-all">{{ $key->key }}</p>
            </div>
        </div>

        <!-- Status Information -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-b pb-4">
            <div>
                <p class="font-medium">Status:</p>
                @if($key->revoked)
                    <span class="badge bg-red-500">Revoked</span>
                    <p class="text-sm text-gray-600 mt-1">Reason: {{ $key->revocation_reason ?? 'Not specified' }}</p>
                @elseif($key->expires_at && $key->expires_at->isPast())
                    <span class="badge bg-yellow-500">Expired</span>
                    <p class="text-sm text-gray-600 mt-1">Expired {{ $key->expires_at->diffForHumans() }}</p>
                @else
                    <span class="badge bg-green-500">Active</span>
                    @if($key->expires_at)
                        <p class="text-sm text-gray-600 mt-1">Expires {{ $key->expires_at->diffForHumans() }}</p>
                    @endif
                @endif
            </div>

            <div>
                <p class="font-medium">Last Used:</p>
                <p class="text-gray-600 text-sm">
                    {{ $key->last_used_at ? $key->last_used_at->format('M j, Y H:i') : 'Never' }}
                </p>
            </div>

            <div>
                <p class="font-medium">Grace Period:</p>
                <p class="text-gray-600 text-sm">
                    {{ floor($key->grace_period_hours / 24) }} days remaining after expiration
                </p>
            </div>
        </div>

        <!-- Owner & Tier Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-b pb-4">
            <div>
                <p class="font-medium">Owner Information:</p>
                <p class="text-gray-600 text-sm">
                    {{ $key->owner_name ?? 'No owner specified' }}<br>
                    {{ $key->owner_email ?? 'No email specified' }}
                </p>
            </div>

            <div>
                <p class="font-medium">Subscription Tier:</p>
                <p class="text-gray-600 text-sm capitalize">
                    {{ $key->tier }}
                    @if($key->tier === 'enterprise')
                        <span class="text-xs text-purple-500 ml-2">✨ Premium Features</span>
                    @endif
                </p>

                <p class="font-medium mt-2">Enabled Features:</p>
                <div class="flex flex-wrap gap-2 mt-1">
                    @forelse(json_decode($key->features ?? '[]') as $feature)
                        <span class="badge bg-blue-100 text-blue-800 text-xs">{{ $feature }}</span>
                    @empty
                        <span class="text-gray-400 text-sm italic">No special features enabled</span>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Domain Management -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-b pb-4">
            <div>
                <p class="font-medium">Allowed Domains ({{ count($key->allowed_domains ?? []) }}/{{ $key->max_domains }}):</p>
                <ul class="list-disc ml-6 text-gray-700 text-sm mt-1">
                    @forelse ($key->allowed_domains ?? [] as $domain)
                        <li>{{ $domain }}</li>
                    @empty
                        <li class="text-gray-400 italic">No domains whitelisted</li>
                    @endforelse
                </ul>
            </div>

            <div>
                <p class="font-medium">Security Settings:</p>
                <div class="space-y-1 mt-1">
                    <div class="flex items-center gap-2">
                        <span class="text-sm {{ $key->allow_auto_registration ? 'text-green-500' : 'text-red-500' }}">
                            @if($key->allow_auto_registration)
                                ✓ Auto-registration enabled
                            @else
                                ✗ Auto-registration disabled
                            @endif
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-sm {{ $key->allow_localhost ? 'text-green-500' : 'text-red-500' }}">
                            @if($key->allow_localhost)
                                ✓ Localhost allowed
                            @else
                                ✗ Localhost blocked
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Metadata -->
        @if($key->metadata)
        <div>
            <p class="font-medium">Additional Metadata:</p>
            <pre class="text-sm bg-gray-50 p-3 rounded mt-1 overflow-x-auto">{{ json_encode(json_decode($key->metadata), JSON_PRETTY_PRINT) }}</pre>
        </div>
        @endif
    </div>

    <div class="bg-white p-6 rounded shadow mt-6">
        <h2 class="text-xl font-semibold mb-4">Recent Validations</h2>
        <div class="overflow-x-auto">
            <table class="table w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Domain</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Auto Registered</th>
                        <th class="px-4 py-2 text-left">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentLogs as $log)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $log->domain }}</td>
                            <td class="px-4 py-2">{{ ucfirst($log->status) }}</td>
                            <td class="px-4 py-2">{{ $log->auto_registered ? 'Yes' : 'No' }}</td>
                            <td class="px-4 py-2">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-500 italic">No recent validations</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Domain Management Section -->
    <div class="bg-white p-6 rounded shadow mb-6">
        <h2 class="text-xl font-semibold mb-4">Domain Management</h2>

        <!-- Add Domain Form -->
        <form method="POST" action="{{ route('admin.access-keys.domains', $key->id) }}" class="mb-4">
            @csrf
            <div class="flex gap-3 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium mb-1">Add New Domain</label>
                    <input type="text" name="new_domain" class="input w-full"
                           placeholder="example.com" required>
                </div>
                <input type="hidden" name="action" value="add">
                <button type="submit" class="btn btn-primary">Add Domain</button>
            </div>
        </form>

        <!-- Remove Domain Form -->
        @if(count($key->allowed_domains ?? []))
        <form method="POST" action="{{ route('admin.access-keys.domains', $key->id) }}">
            @csrf
            <div class="flex gap-3 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium mb-1">Remove Existing Domain</label>
                    <select name="domain" class="input w-full" required>
                        @foreach($key->allowed_domains as $domain)
                            <option value="{{ $domain }}">{{ $domain }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="action" value="remove">
                <button type="submit" class="btn btn-danger">Remove Domain</button>
            </div>
        </form>
        @endif
    </div>

    <!-- Validity Extension Section -->
    <div class="bg-white p-6 rounded shadow mb-6">
        <h2 class="text-xl font-semibold mb-4">Validity Management</h2>
        <form method="POST" action="{{ route('admin.access-keys.extend', $key->id) }}">
            @csrf
            <div class="flex gap-3 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium mb-1">Extend Validity</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="extend_months"
                               class="input"
                               min="1"
                               value="1"
                               required>
                        <span class="whitespace-nowrap">months</span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Extend</button>
            </div>
        </form>
    </div>

    <div class="mt-8 space-y-4">
        <form method="POST" action="{{ route('admin.access-keys.revoke', $key->id) }}" class="flex items-center gap-3">
            @csrf
            <input type="text" name="revocation_reason" class="input w-full" placeholder="Revocation reason..." required>
            <button type="submit" class="btn btn-danger">Revoke</button>
        </form>

        <div class="flex justify-between items-center">

            <form method="POST" action="{{ route('admin.access-keys.restore', $key->id) }}">
                @csrf
                <button type="submit" class="btn btn-success">Restore Key</button>
            </form>

            <form method="POST" action="{{ route('admin.access-keys.reset-domains', $key->id) }}">
                @csrf
                <button type="submit" class="btn btn-warning"
                        onclick="return confirm('This will reset all domain tracking for this key. Continue?')">
                    Reset Domain Tracking
                </button>
            </form>

            <form method="POST" action="{{ route('admin.access-keys.destroy', $key->id) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger">Delete Key</button>
            </form>
        </div>


    </div>
@endsection
