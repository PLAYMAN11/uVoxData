<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Rutas absolutas correctas para sprites (subcarpetas / artisan) --}}
    <script>
        window.OrientaVox = Object.assign(window.OrientaVox || {}, {
            lupinDefaultSrc: @json(asset('assets/lupin-default.png')),
            lupinProcessingSrc: @json(asset('assets/lupin-processing.png')),
        });
    </script>

    <title>OrientaVox</title>

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0f172a">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap (opcional pero válido en tu caso) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- VITE -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="app-body">

    <!-- FRAME TIPO IPHONE -->
    <div class="phone-frame">

        <div class="app-shell @yield('shellClass')">

            <header class="app-header">


                @yield('header', 'OrientaVox')
            </header>

            <main class="app-main">
                @include('consulta.partials.offline-banner')
                @yield('content')
            </main>

            <nav class="app-bottom"></nav>

        </div>

    </div>

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

</body>
</html>
