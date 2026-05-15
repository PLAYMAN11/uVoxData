<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>uVoxData — Consulta Electoral</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1a365d">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <header class="bg-blue-900 text-white px-6 py-4 shadow">
        <h1 class="text-xl font-semibold">uVoxData · Consulta Electoral TEE Chihuahua</h1>
    </header>

    <main class="max-w-4xl mx-auto p-6">
        @include('consulta.partials.offline-banner')
        @yield('content')
    </main>

    <footer class="text-center text-xs text-gray-400 py-4">
        TEE Chihuahua &copy; {{ date('Y') }}
    </footer>
</body>
</html>
