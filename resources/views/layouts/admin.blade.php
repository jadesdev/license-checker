<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{get_setting('description')}}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="Jadesdev" name="author" />
    <!-- Title -->
    <title>@yield('title') | {{get_setting('title')}}</title>
    <!-- Favicon -->
    <link rel="icon shortcut" href="{{my_asset(get_setting('favicon'))}}">

    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Tailwind Apply with Custom Utilities -->
    <style type="text/tailwindcss">
        @layer components {
            .btn {
                @apply bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-200 ease-in-out;
            }

            .btn-sm {
                @apply bg-gray-200 text-black px-3 py-1 rounded text-sm hover:bg-gray-300 transition duration-150;
            }

            .btn-danger {
                @apply bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition duration-200 ease-in-out;
            }

            .btn-success {
                @apply bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition duration-200 ease-in-out;
            }

            .input {
                @apply w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white;
            }

            .card {
                @apply p-4 bg-white rounded shadow-md;
            }

            .table {
                @apply w-full text-sm text-left border border-gray-200;
            }

            .table th {
                @apply bg-gray-100 text-gray-700 font-semibold;
            }

            .table th,
            .table td {
                @apply px-4 py-2 border-b border-gray-200;
            }

            .badge {
                @apply px-2 py-1 rounded text-white text-xs font-medium;
            }

            .badge-green {
                @apply bg-green-500;
            }

            .badge-yellow {
                @apply bg-yellow-500;
            }

            .badge-red {
                @apply bg-red-500;
            }

            .badge-gray {
                @apply bg-gray-500;
            }

            /* Sidebar utilities */
            .sidebar {
                @apply bg-gray-800 text-white w-64 fixed h-full transition-all duration-300 ease-in-out z-30 left-0 top-0;
            }

            /* Different behavior for mobile and desktop */
            @media (min-width: 1024px) {
                /* Desktop: Use margin to push content */
                .sidebar-collapsed {
                    @apply -ml-64;
                }

                .content-container {
                    @apply ml-64 transition-all duration-300 ease-in-out;
                }

                .content-expanded {
                    @apply ml-0;
                }
            }

            @media (max-width: 1023px) {
                /* Mobile: Use transform for overlay effect */
                .sidebar {
                    @apply transform;
                }

                .sidebar-collapsed {
                    @apply -translate-x-full;
                }

                .content-container {
                    @apply ml-0 transition-all duration-300 ease-in-out;
                }
            }

            /* Overlay for mobile */
            .sidebar-overlay {
                @apply fixed inset-0 bg-black bg-opacity-50 z-20 transition-opacity duration-300 ease-in-out opacity-0 pointer-events-none;
            }

            .sidebar-overlay.active {
                @apply opacity-100 pointer-events-auto;
            }

            .nav-item {
                @apply px-4 py-2 hover:bg-gray-700 flex items-center cursor-pointer transition-colors;
            }

            .nav-item.active {
                @apply bg-blue-600;
            }

            .nav-dropdown {
                @apply pl-8 overflow-hidden transition-all duration-300 ease-in-out max-h-0;
            }

            .nav-dropdown.open {
                @apply max-h-96;
            }
        }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-100 text-gray-800">

    <div class="min-h-screen flex flex-col">
        <!-- Sidebar Overlay (for mobile) -->
        <div id="sidebar-overlay" class="sidebar-overlay lg:hidden"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar">
            <div class="p-4 flex justify-between items-center border-b border-gray-700">
                <h2 class="font-bold text-xl">{{get_setting('name')}}</h2>
                <button id="collapse-sidebar-btn" class="text-white hover:text-gray-300">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>

            <nav class="py-4">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                </a>

                <!-- Access Keys Section -->
                <div class="nav-section">
                    <div class="nav-item nav-toggle" data-target="access-keys-menu">
                        <i class="fas fa-key mr-3"></i> Access Keys
                        <i class="fas fa-chevron-down ml-auto transition-transform duration-200"></i>
                    </div>
                    <div id="access-keys-menu" class="nav-dropdown">
                        <a href="{{ route('admin.access-keys.index') }}" class="nav-item {{ request()->routeIs('admin.access-keys.index') ? 'active' : '' }}">
                            <i class="fas fa-list mr-3"></i> All Keys
                        </a>
                        <a href="{{ route('admin.access-keys.create') }}" class="nav-item {{ request()->routeIs('admin.access-keys.create') ? 'active' : '' }}">
                            <i class="fas fa-plus mr-3"></i> Create New
                        </a>
                    </div>
                </div>

                <!-- Validation Logs Section -->
                <div class="nav-section">
                    <div class="nav-item nav-toggle" data-target="logs-menu">
                        <i class="fas fa-history mr-3"></i> Validation Logs
                        <i class="fas fa-chevron-down ml-auto transition-transform duration-200"></i>
                    </div>
                    <div id="logs-menu" class="nav-dropdown">
                        <a href="{{ route('admin.logs.index') }}" class="nav-item {{ request()->routeIs('admin.logs.index') ? 'active' : '' }}">
                            <i class="fas fa-list mr-3"></i> All Logs
                        </a>
                    </div>
                </div>

                <!-- Statistics Section -->
                <div class="nav-section">
                    <div class="nav-item nav-toggle" data-target="stats-menu">
                        <i class="fas fa-chart-bar mr-3"></i> Statistics
                        <i class="fas fa-chevron-down ml-auto transition-transform duration-200"></i>
                    </div>
                    <div id="stats-menu" class="nav-dropdown">
                        <a href="{{ route('admin.stats.usage') }}" class="nav-item {{ request()->routeIs('admin.stats.usage') ? 'active' : '' }}">
                            <i class="fas fa-chart-line mr-3"></i> Usage Stats
                        </a>
                        <a href="{{ route('admin.stats.keys') }}" class="nav-item {{ request()->routeIs('admin.stats.keys') ? 'active' : '' }}">
                            <i class="fas fa-key mr-3"></i> Key Stats
                        </a>
                        <a href="{{ route('admin.stats.domains') }}" class="nav-item {{ request()->routeIs('admin.stats.domains') ? 'active' : '' }}">
                            <i class="fas fa-globe mr-3"></i> Domain Stats
                        </a>
                    </div>
                </div>

                <!-- Settings Section -->
                <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <i class="fas fa-cog mr-3"></i> Settings
                </a>

                <a href="{{ route('admin.tiers') }}" class="nav-item {{ request()->routeIs('admin.tiers') ? 'active' : '' }}">
                    <i class="fas fa-layer-group mr-3"></i> License Tiers
                </a>
            </nav>
        </aside>

        <!-- Main Content Container -->
        <div id="content-container" class="content-container min-h-screen flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow p-4 flex justify-between items-center sticky top-0 z-10">
                <div class="flex items-center">
                    <button id="expand-sidebar-btn" class="mr-4 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="text-xl font-bold">{{get_setting('name')}}</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <a href="{{route('admin.profile')}}" class="flex items-center space-x-2 focus:outline-none">
                            <span class="hidden md:inline-block">{{Auth::user()->name}}</span>
                            <i class="fas fa-user-circle text-2xl"></i>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white p-4 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} {{get_setting('title')}}
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // DOM Elements
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const contentContainer = document.getElementById('content-container');
        const collapseSidebarBtn = document.getElementById('collapse-sidebar-btn');
        const expandSidebarBtn = document.getElementById('expand-sidebar-btn');

        // Mobile detection
        const isMobile = () => window.innerWidth < 1024;

        // Function to collapse sidebar
        function collapseSidebar() {
            sidebar.classList.add('sidebar-collapsed');

            if (isMobile()) {
                // For mobile: hide overlay
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = ''; // Re-enable scrolling
            } else {
                // For desktop: expand content
                contentContainer.classList.add('content-expanded');
            }

            // Show expand button
            expandSidebarBtn.style.display = 'block';
        }

        // Function to expand sidebar
        function expandSidebar() {
            sidebar.classList.remove('sidebar-collapsed');

            if (isMobile()) {
                // For mobile: show overlay
                sidebarOverlay.classList.add('active');
                document.body.style.overflow = 'hidden'; // Prevent scrolling behind overlay
            } else {
                // For desktop: collapse content
                contentContainer.classList.remove('content-expanded');
            }

            // On desktop, hide expand button (it's still visible on mobile)
            if (!isMobile()) {
                expandSidebarBtn.style.display = 'block';
            }
        }

        // Event listeners
        collapseSidebarBtn.addEventListener('click', collapseSidebar);
        expandSidebarBtn.addEventListener('click', expandSidebar);
        sidebarOverlay.addEventListener('click', collapseSidebar);

        // Close sidebar when clicking a menu item on mobile
        const navItems = document.querySelectorAll('.nav-item:not(.nav-toggle)');
        navItems.forEach(item => {
            item.addEventListener('click', function() {
                if (isMobile()) {
                    setTimeout(collapseSidebar, 150); // Small delay for better UX
                }
            });
        });

        // Toggle dropdown menus
        const navToggles = document.querySelectorAll('.nav-toggle');
        navToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent sidebar from closing when opening dropdown
                const targetId = this.getAttribute('data-target');
                const targetDropdown = document.getElementById(targetId);
                const chevron = this.querySelector('.fa-chevron-down');

                if (targetDropdown.classList.contains('open')) {
                    targetDropdown.classList.remove('open');
                    chevron.style.transform = 'rotate(0deg)';
                } else {
                    targetDropdown.classList.add('open');
                    chevron.style.transform = 'rotate(180deg)';
                }
            });
        });

        // Check current route to open corresponding dropdown
        document.addEventListener('DOMContentLoaded', function() {
            const activeItems = document.querySelectorAll('.nav-item.active');
            activeItems.forEach(item => {
                const parent = item.closest('.nav-dropdown');
                if (parent) {
                    parent.classList.add('open');
                    const toggle = parent.previousElementSibling;
                    if (toggle) {
                        const chevron = toggle.querySelector('.fa-chevron-down');
                        if (chevron) {
                            chevron.style.transform = 'rotate(180deg)';
                        }
                    }
                }
            });
        });

        // Responsive behavior
        function checkScreenSize() {
            if (isMobile()) {
                // Mobile: start with sidebar collapsed
                collapseSidebar();
                // Always show the expand button on mobile
                expandSidebarBtn.style.display = 'block';
                // Update icon on collapse button for mobile
                collapseSidebarBtn.innerHTML = '<i class="fas fa-times"></i>';
            } else {
                // Desktop: start with sidebar expanded
                expandSidebar();
                // Update icon on collapse button for desktop
                collapseSidebarBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
            }
        }

        // Initial check and event listener for window resize
        window.addEventListener('resize', checkScreenSize);
        checkScreenSize();

        // Escape key closes sidebar
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !sidebar.classList.contains('sidebar-collapsed')) {
                collapseSidebar();
            }
        });

        // Toast notifications
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif

        // Toastr config
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "timeOut": "5000"
        };
    </script>

    @stack('scripts')
</body>

</html>
