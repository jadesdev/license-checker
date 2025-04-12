<div class=" overflow-hidden space-y-4">
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
        <h2 class="text-xl font-bold text-white">General Settings</h2>
        <p class="text-blue-100 text-sm mt-1">Manage Site Settigns</p>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 ">
            <h2 class="text-xl font-semibold text-gray-700">Website Information</h2>
        </div>
        <div class="px-6 py-4">
            <form action="{{ route('admin.settings.update') }}" method="post" class="space-y-2">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Website Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Website Name</label>
                        <input type="text" name="title" value="{{ $settings->title ?? '' }}"
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <!-- Short Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Short Name</label>
                        <input type="text" name="name" maxlength="15" value="{{ $settings->name ?? '' }}"
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <!-- Website Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Website Email</label>
                        <input type="text" name="email" value="{{ $settings->email ?? '' }}"
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <!-- Support Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Support Email</label>
                        <input type="text" name="support_email" value="{{ $settings->support_email ?? '' }}"
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <!-- Website Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Website Phone</label>
                        <input type="tel" name="phone" value="{{ $settings->phone ?? '' }}"
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <!-- Website About -->
                    <div class="">
                        <label class="block text-sm font-medium text-gray-700">Website About</label>
                        <textarea name="description" rows="3"
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ $settings->description ?? '' }}</textarea>
                    </div>
                </div>
                <button type="submit"
                    class="w-full mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Save Settings
                </button>
            </form>
        </div>
    </div>

    <!-- License Settings -->
    <div class="bg-white rounded-lg shadow">
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 ">
            <h2 class="text-xl font-semibold text-gray-700">License Settings</h2>
        </div>
        <div class="px-6 py-4">
            <form action="{{ route('admin.settings.update') }}" method="post" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Default License Term (days)</label>
                        <input type="number" name="default_license_term" value="{{ old('default_license_term', $settings->default_license_term) }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">

                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Max Domains Per License</label>
                        <input type="number" name="max_domains_per_license" value="{{ old('max_domains_per_license', $settings->max_domains_per_license) }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">

                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">License Expiration Alert (days before)</label>
                        <input type="number" name="license_expiration_alert" value="{{ old('license_expiration_alert', $settings->license_expiration_alert) }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">

                    </div>
                    <div>
                        <label for="registration_active" class="block text-sm font-medium text-gray-700">Registration Active</label>

                        <label class="relative inline-flex items-center cursor-pointer mt-4">
                            <input type="checkbox" name="registration_active" id="registration_active" value="1" @checked(old('registration_active', $settings->registration_active)) class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300
                                       rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white
                                       after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                       after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5
                                       after:transition-all peer-checked:bg-blue-600">
                            </div>
                        </label>
                    </div>
                </div>
                <button type="submit"
                    class="w-full mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Save License Settings
                </button>
            </form>
        </div>
    </div>

    <!-- Logo/Image Settings -->
    <div class="bg-white rounded-lg shadow">
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 ">
            <h2 class="text-xl font-semibold text-gray-700">Logo/Image Settings</h2>
        </div>
        <div class="px-6 py-4">
            <form action="{{ route('admin.settings.update') }}" method="post" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Site Logo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Site Logo</label>
                        <input type="file" name="logo" accept="image/*"
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @if ($settings->logo)
                            <img src="{{ my_asset($settings->logo) }}" alt="Site Logo" class="mt-2 h-16">
                        @endif
                    </div>
                    <!-- Favicon -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Favicon</label>
                        <input type="file" name="favicon" accept="image/*"
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @if ($settings->favicon)
                            <img src="{{ my_asset($settings->favicon) }}" alt="Favicon" class="mt-2 h-16">
                        @endif
                    </div>
                </div>
                <button type="submit"
                    class="w-full mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Update Logo Settings
                </button>
            </form>
        </div>
    </div>

    <!-- Currency Settings -->
    <div class="bg-white rounded-lg shadow">
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 ">
            <h2 class="text-xl font-semibold text-gray-700">Currency Settings</h2>
        </div>
        <div class="px-6 py-4">
            <form action="{{ route('admin.settings.update') }}" method="post" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Currency Symbol</label>
                        <input type="text" name="currency" value="{{ $settings->currency ?? '' }}" required placeholder="Currency Symbol"
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Currency Code</label>
                        <input type="text" name="currency_code" value="{{ $settings->currency_code ?? '' }}" required placeholder="Currency Code"
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <button type="submit"
                    class="w-full mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Update Currency Settings
                </button>
            </form>
        </div>
    </div>
</div>
