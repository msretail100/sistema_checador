<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Control de asistencia') · {{ config('app.name', 'Reloj Checador') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --ink: #17243b; --muted: #64748b; --brand: #2856a6; --canvas: #f3f6fb; }
        body { min-height: 100vh; background: var(--canvas); color: var(--ink); }
        .navbar { background: #102449; }
        .navbar-brand, .navbar-text { color: #fff !important; }
        .page-wrap { max-width: 1180px; }
        .card { border: 0; border-radius: 1rem; box-shadow: 0 8px 26px rgba(20, 42, 78, .07); }
        .metric { font-size: 2rem; font-weight: 700; }
        .muted { color: var(--muted); }
        .btn-primary { background: var(--brand); border-color: var(--brand); }
        .table > :not(caption) > * > * { padding: .85rem .75rem; vertical-align: middle; }
        .check-button { min-height: 64px; font-size: 1.15rem; }
        @media (max-width: 575.98px) { .table-responsive { font-size: .9rem; } }
    </style>
    @stack('head')
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container page-wrap">
            <a class="navbar-brand fw-semibold" href="{{ route('dashboard') }}">Reloj Checador</a>
            @auth
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <span class="navbar-text d-none d-sm-inline">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-light" type="submit">Cerrar sesión</button>
                    </form>
                </div>
            @endauth
        </div>
    </nav>
    <main class="container page-wrap py-4 py-md-5">
        @include('partials.flash')
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
