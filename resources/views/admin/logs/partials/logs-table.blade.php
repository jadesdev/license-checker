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
                        <a href="{{ route('admin.logs.by-domain', $log->domain) }}"
                            class="text-blue-600 hover:underline hover:text-blue-800">
                            {{ $log->domain }}
                        </a>
                        <p class="text-gray-500">Main: {{ $log->main_domain ?? 'N/A' }}</p>
                    </td>
                    <td class="font-mono text-sm">
                        <a href="{{ route('admin.logs.by-key', $log->access_key) }}"
                            class="text-blue-600 hover:underline">
                            {{ Str::limit($log->access_key, 8) }}
                        </a>
                    </td>
                    <td class="font-mono text-sm">{{ $log->ip_address }}</td>
                    <td>
                        <a href="{{ route('admin.logs.by-status', Str::slug($log->status)) }}"
                            class="hover:opacity-75 transition-opacity">
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
                        <a href="#" class="text-red-500 hover:text-red-700 delete-log"
                            data-url="{{ route('admin.logs.destroy', $log->id) }}">
                            <i class="fas fa-trash"></i>
                        </a>
                        <!-- View Metadata -->
                        <button
                            class="text-blue-500 hover:text-blue-700 view-metadata inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-300"
                            data-log='@json($log->metadata)'>
                            <i class="fas fa-eye text-base"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-gray-500" No logs found matching your criteria </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<!-- Metadata Modal -->
<div id="metadataModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 relative">
        <button id="closeModal"
            class="absolute top-2 right-3 text-gray-500 hover:text-gray-700 text-xl">&times;</button>
        <h2 class="text-xl font-semibold mb-4">Log Metadata</h2>

        <div class="pt-2" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
            <pre id="metadataContent" class="text-sm font-mono text-gray-800 whitespace-pre-wrap"></pre>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modal = document.getElementById('metadataModal');
            const content = document.getElementById('metadataContent');
            const closeModal = document.getElementById('closeModal');

            // Open modal and show metadata
            document.querySelectorAll('.view-metadata').forEach(button => {
                button.addEventListener('click', function() {
                    const metadata = JSON.parse(this.dataset.metadata || '{}');

                    // Pretty print JSON
                    content.textContent = JSON.stringify(metadata, null, 4);

                    modal.classList.remove('hidden');
                });
            });

            // Close modal on button click or backdrop click
            closeModal.addEventListener('click', () => modal.classList.add('hidden'));
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.classList.add('hidden');
            });
            // Delete Log (your existing code remains unchanged)
            document.querySelectorAll('.delete-log').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.dataset.url;
                    const row = this.closest('tr');

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
                            axios.delete(url, {
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                .then(response => {
                                    if (response.data.success) {
                                        row.remove();
                                        toastr.success(response.data.message);
                                    }
                                })
                                .catch(error => {
                                    toastr.error(error.response?.data?.message ||
                                        'Something went wrong');
                                });
                        }
                    });
                });
            });
        });
    </script>
@endpush
