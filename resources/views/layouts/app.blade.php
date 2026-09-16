<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Snippet Organizer'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Highlight.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/github-dark.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Styles -->
    <style>
        :root {
            --c-primary: #2563EB;
            --c-primary-dark: #1E40AF;
            --c-primary-light: #DBEAFE;
            --c-accent: #38BDF8;
            --c-success: #10B981;
            --c-danger: #EF4444;
            --c-warning: #F59E0B;
            --c-dark: #0F172A;
            --c-gray: #6B7280;
            --c-bg: #F8FAFC;
            --c-border: #E2E8F0;
        }

        body {
            font-family: 'DM Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--c-bg);
            color: var(--c-dark);
        }

        .navbar-theme {
            background: linear-gradient(135deg, var(--c-primary-dark), var(--c-primary));
        }
        .navbar-theme .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .navbar-theme .nav-link {
            font-weight: 500;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(15, 23, 42, 0.06);
            transition: box-shadow 0.15s ease-in-out, transform 0.15s ease-in-out;
        }
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(15, 23, 42, 0.12);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--c-primary);
            box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.15);
        }
        .btn {
            border-radius: 0.5rem;
        }
        .btn-primary {
            background-color: var(--c-primary);
            border-color: var(--c-primary);
        }
        .btn-primary:hover {
            background-color: var(--c-primary-dark);
            border-color: var(--c-primary-dark);
        }
        .snippet-code {
            background: #0f172a;
            color: #e2e8f0;
            border: 1px solid var(--c-border);
            border-radius: 0.5rem;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
        }
        .language-badge {
            font-size: 0.75rem;
        }
        .chip {
            display: inline-block;
            background: var(--c-primary-light);
            color: var(--c-primary-dark);
            border-radius: 999px;
            padding: 0.15rem 0.7rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin: 0.125rem;
        }
        .chip-tag {
            background: #F0FDF4;
            color: #15803D;
        }
    </style>
</head>
<body>
    <div class="min-vh-100 bg-light">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark navbar-theme">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <i class="fas fa-code me-1"></i>{{ config('app.name', 'Snippet Organizer') }}
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('snippets.index') }}">
                                    <i class="fas fa-code me-1"></i>Snippets
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('categories.index') }}">
                                    <i class="fas fa-layer-group me-1"></i>Colecciones
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('languages.index') }}">
                                    <i class="fas fa-language me-1"></i>Lenguajes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('stats.index') }}">
                                    <i class="fas fa-chart-bar me-1"></i>Estadísticas
                                </a>
                            </li>
                            @if (Auth::user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('auditorias.index') }}">
                                        <i class="fas fa-clipboard-list me-1"></i>Auditoría
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">
                                        <i class="fas fa-sign-in-alt me-1"></i>Login
                                    </a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">
                                        <i class="fas fa-user-plus me-1"></i>Registro
                                    </a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item d-flex align-items-center">
                                <span id="thiscodeworksStatus"
                                      class="badge rounded-pill text-bg-secondary my-1"
                                      role="status"
                                      title="Comprobando estado de thiscodeworks…">
                                    <i class="fas fa-lightbulb me-1"></i>thisCodeWorks…
                                </span>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                                    @if (Auth::user()->isAdmin())
                                        <span class="badge bg-light text-dark ms-1">admin</span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="fas fa-user-edit me-1"></i>Editar Perfil
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <!-- jQuery (necesario para Validate/Mask/SweetAlert2) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Validación y máscaras de formularios (global) -->
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/jquery.mask.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>

    <!-- Highlight.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('pre code').forEach((el) => {
                hljs.highlightElement(el);
            });
        });
    </script>

    <!-- Scripts adicionales -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const statusBadge = document.getElementById('thiscodeworksStatus');
            if (!statusBadge) return;

            const ENDPOINT = '{{ route('thiscodeworks.status') }}';

            const setState = (online, message, tooltip) => {
                statusBadge.classList.remove('text-bg-secondary', 'text-bg-success', 'text-bg-danger');
                statusBadge.classList.add(online ? 'text-bg-success' : 'text-bg-danger');
                statusBadge.innerHTML = '<i class="fas fa-lightbulb me-1"></i>' + message;
                statusBadge.title = tooltip;
            };

            const check = () => {
                fetch(ENDPOINT, { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(d => {
                        if (d.online) {
                            setState(true, 'thisCodeWorks online', 'thisCodeWorks responde — puedes publicar snippets y colecciones');
                        } else {
                            setState(false, 'thisCodeWorks caído', 'thisCodeWorks no responde ahora — no podrás publicar');
                        }
                    })
                    .catch(() => setState(false, 'thisCodeWorks caído', 'No se pudo comprobar: verifica tu conexión'));
            };

            check();
            setInterval(check, 30000);
        });
    </script>

    @stack('scripts')
</body>
</html>