<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    
    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-[#f5f7f8] dark:bg-[#101722] text-gray-800 dark:text-gray-200">
    <div class="relative flex min-h-screen w-full flex-col overflow-x-hidden">
        
        <!-- Navbar -->
        @include('frontend.partials.navbar')
        
        <!-- Main Content -->
        <main class="flex-grow">
            @yield('content')
        </main>
        
        <!-- Footer -->
        @include('frontend.partials.footer')
    </div>
    
    @stack('scripts')
</body>
</html>
