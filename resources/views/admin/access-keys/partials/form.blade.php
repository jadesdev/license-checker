<div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-6 rounded shadow">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Owner Name</label>
        <input name="owner_name" value="{{ old('owner_name', $key->owner_name ?? '') }}" class="input focus:outline-none focus:ring-2 focus:ring-blue-500" required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input name="owner_email" value="{{ old('owner_email', $key->owner_email ?? '') }}" class="input focus:outline-none focus:ring-2 focus:ring-blue-500" required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tier</label>
        <select name="tier" class="input focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <option value="standard" @selected(old('tier', $key->tier ?? '') === 'standard')>Standard</option>
            <option value="premium" @selected(old('tier', $key->tier ?? '') === 'premium')>Premium</option>
            <option value="enterprise" @selected(old('tier', $key->tier ?? '') === 'enterprise')>Enterprise</option>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Max Domains
            <span class="text-xs text-gray-500">(Current: {{ count($allowedDomains ?? []) }})</span>
        </label>
        <input name="max_domains" type="number" min="{{ max(1, count($allowedDomains ?? [])) }}" value="{{ old('max_domains', $key->max_domains ?? 1) }}"
            class="input focus:outline-none focus:ring-2 focus:ring-blue-500" required>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Allowed Domains
            <span class="text-xs text-gray-500">(One per line or comma-separated)</span>
        </label>
        <textarea name="allowed_domains" rows="5" class="input focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="example.com&#10;subdomain.example.org">{{ old('allowed_domains', $domainsText ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Expires At</label>
        <input name="expires_at" type="date" value="{{ old('expires_at', optional($key->expires_at ?? null)->format('Y-m-d')) }}"
            class="input focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Grace Period (hours)</label>
        <input name="grace_period_hours" type="number" value="{{ old('grace_period_hours', $key->grace_period_hours ?? 72) }}"
            class="input focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="space-y-4">
        <!-- Auto Registration -->
        <div class="flex items-start gap-3">
            <div class="flex h-5 items-center">
                <input type="hidden" name="allow_auto_registration" value="0">
                <input type="checkbox"
                       id="allow_auto_registration"
                       name="allow_auto_registration"
                       value="1"
                       @checked(old('allow_auto_registration', $key->allow_auto_registration ?? false))
                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            </div>
            <div class="flex flex-col">
                <label for="allow_auto_registration" class="text-sm font-medium text-gray-700">
                    Allow Auto Registration
                </label>
                <p class="text-sm text-gray-500 mt-1">
                    When enabled, new domains will be automatically registered on first validation
                </p>
            </div>
        </div>

        <!-- Localhost Access -->
        <div class="flex items-start gap-3">
            <div class="flex h-5 items-center">
                <input type="hidden" name="allow_localhost" value="0">
                <input type="checkbox"
                       id="allow_localhost"
                       name="allow_localhost"
                       value="1"
                       @checked(old('allow_localhost', $key->allow_localhost ?? false))
                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            </div>
            <div class="flex flex-col">
                <label for="allow_localhost" class="text-sm font-medium text-gray-700">
                    Allow Localhost
                </label>
                <p class="text-sm text-gray-500 mt-1">
                    Enable to permit validations from localhost and 127.0.0.1
                </p>
            </div>
        </div>
    </div>
</div>
