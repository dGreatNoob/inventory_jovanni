<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>403 - Access Denied</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="text-center p-10 bg-white rounded-xl shadow-xl">
        <!-- Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-red-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M12 9v2m0 4h.01M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0z" />
        </svg>

        <h1 class="text-6xl font-extrabold text-red-600 mb-2">403</h1>
        <h2 class="text-2xl font-semibold text-gray-800">Access Denied</h2>
        <p class="mt-3 text-gray-600">You don’t have permission to view this page.</p>
        <a href="{{ route('dashboard') }}"
           class="mt-6 inline-flex items-center px-6 py-2 bg-blue-600 text-white font-medium rounded hover:bg-blue-700 transition">
            <!-- Home Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 12l2-2m0 0l7-7 7 7m-9 12v-6h4v6m5-6h3m-3 0a9 9 0 1 0-18 0h3"/>
            </svg>
            Go Back Home
        </a>
    </div>
</body>
</html>
