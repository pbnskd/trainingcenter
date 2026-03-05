<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Nexus RBAC') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light text-dark font-inter">
    <div id="app" class="d-flex min-vh-100">
        
        {{-- 1. Sidebar (Only for Authenticated Users) --}}
        @auth
            @include('layouts.partials.sidebar')
        @endauth

        {{-- 2. Main Page Wrapper --}}
        {{-- If Guest: ms-0 ensures no margin. If Auth: Sidebar handles spacing via Flexbox --}}
        <div id="page-wrapper" class="d-flex flex-column flex-grow-1 {{ !Auth::check() ? 'ms-0' : '' }}" style="min-width: 0;">
            
            {{-- 3. Topbar --}}
            @include('layouts.partials.topbar')

            {{-- 4. Main Content --}}
            <main class="flex-grow-1 p-4">
                
                {{-- Flash Messages Area --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Page Content Injection --}}
                @yield('content')
            </main>

            {{-- 5. Footer --}}
            @include('layouts.partials.footer')
        </div>
    </div>
    
    {{-- Sidebar Toggle Script for Mobile --}}
    <script>
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-open');
        }


        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    </script>
</body>
</html>