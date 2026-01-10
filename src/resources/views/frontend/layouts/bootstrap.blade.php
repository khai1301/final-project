<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    <!-- Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    
    {{-- Vite Assets (includes custom CSS/JS) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary: #3780f6;
            --background-light: #f5f7f8;
            --background-dark: #101722;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background-light);
        }
        
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: #2d6bd9;
            border-color: #2d6bd9;
        }
        
        .text-primary {
            color: var(--primary) !important;
        }
    </style>
    
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    {{-- Navbar --}}
    @include('frontend.partials.navbar')
    
    <main class="flex-grow-1">
        @yield('content')
    </main>
    
    {{-- Footer --}}
    @include('frontend.partials.footer')
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
            'flash' => [
                'swal' => session('swal'),
            ]
        ]) !!};
    </script>
    
    @stack('scripts')
</body>
</html>
