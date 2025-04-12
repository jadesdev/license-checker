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
                    <td class="font-mono text-sm">
                        <a href="{{ route('admin.logs.by-domain', $log->domain) }}" class="text-blue-600 hover:underline hover:text-blue-800">
                            {{ $log->domain }}
                        </a>
                    </td>
                    <td class="font-mono text-sm">
                        <a href="{{ route('admin.logs.by-key', $log->access_key) }}" class="text-blue-600 hover:underline">
                            {{ Str::limit($log->access_key, 8) }}
                        </a>
                    </td>
                    <td class="font-mono text-sm">{{ $log->ip_address }}</td>
                    <td>
                        <a href="{{ route('admin.logs.by-status', Str::slug($log->status)) }}" class="hover:opacity-75 transition-opacity">
                            <span class="badge {{ $log->status === 'valid' ? 'bg-green-500' : 'bg-red-500' }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </a>
                    </td>
                    <td>
                        @if ($log->auto_registered)
                            <i class="fas fa-check text-green-500"></i>
                        @else
                            <i class="fas fa-times text-red-500"></i>
                        @endif
                    </td>
                    <td>
                        <a href="#" class="text-red-500 hover:text-red-700 delete-log" data-url="{{ route('admin.logs.destroy', $log->id) }}">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete log handler
        document.querySelectorAll('.delete-log').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.dataset.url;
                const row = this.closest('tr');

                // SweetAlert2 for confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // AJAX request
                        axios.delete(url, {
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (response.data.success) {
                                // Remove row from table
                                row.remove();
                                // Toastr success notification
                                toastr.success(response.data.message);
                            }
                        })
                        .catch(error => {
                            // Toastr error notification
                            toastr.error(error.response?.data?.message || 'Something went wrong');
                        });
                    }
                });
            });
        });
    });
    </script>
@endpush
