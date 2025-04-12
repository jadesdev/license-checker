<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Optional: Add custom Tailwind config or styles here -->
    <style>
        .btn {
            @apply bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700;
        }

        .btn-sm {
            @apply bg-gray-200 text-black px-2 py-1 rounded text-sm hover:bg-gray-300;
        }

        .btn-danger {
            @apply bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700;
        }

        .input {
            @apply w-full px-3 py-2 border border-gray-300 rounded;
        }

        .card {
            @apply p-4 bg-white rounded shadow;
        }

        .table {
            @apply w-full border-collapse;
        }

        .table th,
        .table td {
            @apply border px-4 py-2 text-left;
        }

        .badge {
            @apply px-2 py-1 rounded text-white text-xs;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800">

    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white shadow p-4 flex justify-between items-center">
            <h1 class="text-xl font-bold">Admin Panel</h1>
            <a href="{{ route('admin.access-keys.index') }}" class="btn-sm">Access Keys</a>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white p-4 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Admin Panel
        </footer>
    </div>

</body>

</html>
