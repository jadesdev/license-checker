<div class=" overflow-hidden">
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
        <h2 class="text-xl font-bold text-white">Subscription Tiers</h2>
        <p class="text-blue-100 text-sm mt-1">Manage your subscription plans and pricing</p>
    </div>

    <form method="POST" action="{{ route('admin.tiers.update') }}" id="tiers-form" class="p-6 px-2">
        @csrf
        <div class="space-y-6">
            <div id="tiers-container" class="space-y-6">
                @foreach ($tiers as $tier)
                    <div class="tier-item bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="font-medium text-gray-700 flex items-center">
                                <span class="h-2 w-2 rounded-full mr-2 @if ($tier->status) bg-green-500 @else bg-red-500 @endif"></span>
                                <span>Tier #{{ $loop->iteration }}</span>
                            </h3>
                            <button type="button" class="text-gray-400 hover:text-red-600 transition-colors duration-200 remove-tier group flex items-center rounded-md p-1 border border-red-300">
                                <i class="fa-solid fa-trash-can mr-1"></i>
                                <span class="text-sm group-hover:text-red-600 ">Remove</span>
                            </button>
                        </div>

                        <div class="p-2">
                            <input type="hidden" name="tiers[{{ $loop->index }}][id]" value="{{ $tier->id }}">

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Tier Name</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-tag text-gray-400"></i>
                                        </div>
                                        <input type="text" name="tiers[{{ $loop->index }}][name]" value="{{ old("tiers.{$loop->index}.name", $tier->name) }}"
                                            class="pl-8 py-2 block border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Price</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-dollar-sign text-gray-400"></i>
                                        </div>
                                        <input type="number" step="0.01" name="tiers[{{ $loop->index }}][price]"
                                            value="{{ old("tiers.{$loop->index}.price", $tier->price) }}"
                                            class="pl-8 py-2 block border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Duration (days)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-calendar-days text-gray-400"></i>
                                        </div>
                                        <input type="number" name="tiers[{{ $loop->index }}][duration]"
                                            value="{{ old("tiers.{$loop->index}.duration", $tier->duration) }}"
                                            class="pl-8 py-2 block border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 flex items-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="tiers[{{ $loop->index }}][status]" id="status-{{ $loop->index }}" value="1"
                                        @checked($tier->status) class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Active</span>
                                </label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class=" pt-6 flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                <button type="button" id="add-tier"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-3 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <i class="fa-solid fa-circle-plus mr-2"></i>
                    Add New Tier
                </button>

                <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center py-3 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <i class="fa-solid fa-floppy-disk mr-2"></i>
                    Save Changes
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add new tier
            document.getElementById('add-tier').addEventListener('click', function() {
                const container = document.getElementById('tiers-container');
                const index = document.querySelectorAll('.tier-item').length;
                const newTierNumber = index + 1;

                const template = `
            <div class="tier-item bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md animate-fadeIn">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-medium text-gray-700 flex items-center">
                        <span class="h-2 w-2 rounded-full mr-2 bg-blue-500"></span>
                        <span>Tier #${newTierNumber} (New)</span>
                    </h3>
                    <button type="button" class="text-gray-400 hover:text-red-600 transition-colors duration-200 remove-tier group flex items-center rounded-md p-1 border border-red-300">
                        <i class="fa-solid fa-trash-can mr-1"></i>
                        <span class="text-sm group-hover:text-red-600">Remove</span>
                    </button>
                </div>

                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Tier Name</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-tag text-gray-400"></i>
                                </div>
                                <input type="text" name="tiers[${index}][name]"
                                    placeholder="e.g. Basic, Premium, Pro"
                                    class="pl-8 py-2 block border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Price</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-dollar-sign text-gray-400"></i>
                                </div>
                                <input type="number" step="0.01" name="tiers[${index}][price]"
                                    placeholder="9.99"
                                    class="pl-8 py-2 block border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Duration (days)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-calendar-days text-gray-400"></i>
                                </div>
                                <input type="number" name="tiers[${index}][duration]"
                                    placeholder="30"
                                    class="pl-8 py-2 block border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="tiers[${index}][status]" id="status-${index}"
                                value="1" checked
                                class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ml-3 text-sm font-medium text-gray-700">Active</span>
                        </label>
                    </div>
                </div>
            </div>
        `;

                container.insertAdjacentHTML('beforeend', template);

                // Scroll to the newly added tier
                setTimeout(() => {
                    const newTier = container.lastElementChild;
                    newTier.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }, 100);
            });

            // Remove tier
            document.getElementById('tiers-container').addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-tier') || e.target.closest('.remove-tier')) {
                    const tierItem = e.target.closest('.tier-item');

                    if (confirm('Are you sure you want to remove this tier?')) {
                        if (tierItem.querySelector('input[name$="[id]"]')) {
                            const removedTiers = document.createElement('input');
                            removedTiers.type = 'hidden';
                            removedTiers.name = 'removed_tiers[]';
                            removedTiers.value = tierItem.querySelector('input[name$="[id]"]').value;
                            document.getElementById('tiers-form').appendChild(removedTiers);
                        }

                        // Fade out animation before removal
                        tierItem.classList.add('opacity-0');
                        tierItem.style.maxHeight = tierItem.offsetHeight + 'px';
                        tierItem.style.maxHeight = '0px';

                        setTimeout(() => {
                            tierItem.remove();
                        }, 300);
                    }
                }
            });
        });
    </script>

    <style>
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush
