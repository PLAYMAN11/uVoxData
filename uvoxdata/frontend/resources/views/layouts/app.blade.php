<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>OrientaVox</title>

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0f172a">

    <!-- Bootstrap (opcional pero válido en tu caso) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- VITE -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="app-body">

    <!-- FRAME TIPO IPHONE -->
    <div class="phone-frame">

        <div class="app-shell">

            <header class="app-header">
                <h1>Orienta<span>Vox</span></h1>
            </header>

            <main class="app-main">
                @yield('content')
            </main>

            <nav class="app-bottom"></nav>

        </div>

    </div>

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>