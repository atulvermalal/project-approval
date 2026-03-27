<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Project Approval') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #2d343c;
            --sidebar-panel: #353c45;
            --sidebar-muted: #c9d0d8;
            --page-bg: #f4f6f9;
            --page-border: #d9dee5;
            --card-bg: #ffffff;
            --brand-blue: #007bff;
            --brand-green: #28a745;
            --brand-yellow: #ffc107;
            --brand-red: #dc3545;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--page-bg);
            color: #2d3436;
            font-family: 'Source Sans 3', sans-serif;
        }

        a {
            text-decoration: none;
        }

        .guest-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            background:
                radial-gradient(circle at top right, rgba(0, 123, 255, 0.08), transparent 24%),
                radial-gradient(circle at bottom left, rgba(40, 167, 69, 0.08), transparent 24%),
                #f4f6f9;
        }

        .topbar {
            height: 57px;
            background: #fff;
            border-bottom: 1px solid var(--page-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        .topbar-left,
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            color: #343a40;
            font-weight: 700;
        }

        .brand-logo {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #6c757d;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
        }

        .app-shell {
            display: flex;
            min-height: calc(100vh - 57px);
        }

        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            color: #fff;
            position: sticky;
            top: 57px;
            height: calc(100vh - 57px);
            overflow-y: auto;
            box-shadow: 2px 0 16px rgba(0, 0, 0, 0.08);
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.16);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .sidebar-menu {
            padding: 1rem 0.85rem 1.25rem;
        }

        .sidebar-title {
            color: rgba(255, 255, 255, 0.58);
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin: 0 0 0.75rem;
            padding: 0 0.35rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            color: var(--sidebar-muted);
            padding: 0.8rem 0.85rem;
            border-radius: 0.45rem;
            margin-bottom: 0.35rem;
            transition: 0.2s ease;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .sidebar-bullet {
            width: 0.9rem;
            height: 0.9rem;
            border: 2px solid rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            flex: 0 0 auto;
        }

        .sidebar-note {
            margin: 1rem 0.35rem 0;
            padding: 0.85rem 0.9rem;
            background: var(--sidebar-panel);
            border-radius: 0.6rem;
            color: rgba(255, 255, 255, 0.82);
            font-size: 0.95rem;
        }

        .page-wrap {
            flex: 1;
            min-width: 0;
        }

        .page-content {
            padding: 1.5rem;
        }

        .content-card {
            background: var(--card-bg);
            border: 1px solid var(--page-border);
            border-radius: 0.55rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        .section-title {
            margin-bottom: 1rem;
        }

        .small-box {
            color: #fff;
            border-radius: 0.4rem;
            padding: 1rem 1.1rem 0.8rem;
            position: relative;
            overflow: hidden;
            min-height: 132px;
        }

        .small-box h3 {
            font-size: 2.25rem;
            line-height: 1;
            margin: 0 0 0.4rem;
            font-weight: 800;
        }

        .small-box p {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
        }

        .small-box .metric-icon {
            position: absolute;
            right: 0.9rem;
            top: 0.75rem;
            font-size: 3.2rem;
            line-height: 1;
            opacity: 0.18;
            font-weight: 800;
        }

        .small-box-footer {
            display: block;
            margin: 0.9rem -1.1rem -0.8rem;
            padding: 0.55rem 1.1rem;
            color: rgba(255, 255, 255, 0.92);
            background: rgba(0, 0, 0, 0.12);
            font-weight: 600;
        }

        .bg-info { background: var(--brand-blue); }
        .bg-success-soft { background: var(--brand-green); }
        .bg-warning-soft { background: var(--brand-yellow); color: #1f2328; }
        .bg-warning-soft .small-box-footer { color: #1f2328; background: rgba(255, 255, 255, 0.18); }
        .bg-danger-soft { background: var(--brand-red); }

        .mini-stat {
            border: 1px solid var(--page-border);
            border-radius: 0.45rem;
            padding: 0.85rem 1rem;
            background: #fff;
        }

        .chart-placeholder,
        .map-placeholder {
            min-height: 320px;
            border-radius: 0.45rem;
            position: relative;
            overflow: hidden;
        }

        .chart-placeholder {
            background:
                linear-gradient(180deg, rgba(0, 123, 255, 0.08), rgba(0, 123, 255, 0.02)),
                #fff;
        }

        .chart-line {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(to top, rgba(0, 123, 255, 0.06) 0, rgba(0, 123, 255, 0.06) 1px, transparent 1px) 0 0 / 100% 25%,
                linear-gradient(to right, rgba(0, 0, 0, 0.03) 0, rgba(0, 0, 0, 0.03) 1px, transparent 1px) 0 0 / 20% 100%;
        }

        .chart-wave {
            position: absolute;
            left: 4%;
            right: 4%;
            bottom: 8%;
            height: 58%;
            border-bottom: 4px solid #007bff;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-radius: 45% 55% 18% 22% / 40% 35% 65% 60%;
            transform: skewX(-14deg);
            opacity: 0.85;
        }

        .map-placeholder {
            background:
                radial-gradient(circle at 15% 30%, rgba(255, 255, 255, 0.95) 0 12%, transparent 13%),
                radial-gradient(circle at 34% 44%, rgba(255, 255, 255, 0.95) 0 10%, transparent 11%),
                radial-gradient(circle at 58% 42%, rgba(255, 255, 255, 0.95) 0 12%, transparent 13%),
                radial-gradient(circle at 78% 35%, rgba(255, 255, 255, 0.95) 0 9%, transparent 10%),
                radial-gradient(circle at 72% 66%, rgba(255, 255, 255, 0.95) 0 8%, transparent 9%),
                linear-gradient(135deg, #2b7fff, #0f5fd8);
        }

        .tab-pane-panel {
            display: none;
        }

        .tab-pane-panel.active {
            display: block;
        }

        .form-control,
        .form-select {
            border-radius: 0.45rem;
        }

        .badge-soft {
            background: #e9ecef;
            color: #495057;
            font-size: 0.82rem;
            border-radius: 999px;
            padding: 0.35rem 0.7rem;
        }

        .table > :not(caption) > * > * {
            vertical-align: middle;
        }

        .realtime-toast-wrap {
            position: fixed;
            right: 1rem;
            bottom: 1rem;
            z-index: 1080;
            width: min(360px, calc(100vw - 2rem));
        }

        @media (max-width: 991.98px) {
            .app-shell {
                display: block;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: static;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
        <div class="topbar">
            <div class="topbar-left">
                <a class="topbar-brand" href="{{ route('dashboard') }}">
                    <span class="brand-logo">A</span>
                    <span>{{ config('app.name', 'Project Approval') }}</span>
                </a>
            </div>

            <div class="topbar-right">
                <span class="text-muted small d-none d-sm-inline">{{ now()->format('d M Y') }}</span>
                <span class="badge-soft text-capitalize">{{ auth()->user()->role_name }}</span>
                <div class="text-end d-none d-md-block">
                    <div class="fw-semibold">{{ auth()->user()->name }}</div>
                    <div class="small text-muted">{{ auth()->user()->email }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-secondary btn-sm" type="submit">Logout</button>
                </form>
            </div>
        </div>

        <div class="app-shell">
            <aside class="sidebar">
                <div class="sidebar-header">
                    <span class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <div>
                        <div class="fw-semibold">{{ auth()->user()->name }}</div>
                        <div class="small text-capitalize" style="color: rgba(255,255,255,.68);">{{ auth()->user()->role_name }}</div>
                    </div>
                </div>

                <div class="sidebar-menu">
                    <div class="sidebar-title">Dashboard</div>
                    <a class="sidebar-link {{ ($activeTab ?? 'dashboard') === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard', ['tab' => 'dashboard']) }}">
                        <span class="sidebar-bullet"></span>
                        <span>Dashboard</span>
                    </a>
                    <a class="sidebar-link {{ request()->routeIs('projects.*') || (($activeTab ?? 'dashboard') === 'projects') ? 'active' : '' }}" href="{{ route('projects.index') }}">
                        <span class="sidebar-bullet"></span>
                        <span>Projects</span>
                    </a>
                    @if (auth()->user()->hasPermission('users.manage'))
                        <a class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <span class="sidebar-bullet"></span>
                            <span>Users</span>
                        </a>
                    @endif
                    @if (auth()->user()->hasPermission('roles.manage'))
                        <a class="sidebar-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                            <span class="sidebar-bullet"></span>
                            <span>Roles</span>
                        </a>
                    @endif
                </div>
            </aside>

            <div class="page-wrap">
                <main class="page-content">
                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
        <div class="realtime-toast-wrap" id="realtimeToastWrap"></div>
    @else
        <main class="guest-shell">
            <div class="w-100" style="max-width: 1100px;">
                @if (session('status'))
                    <div class="alert alert-success mb-4">{{ session('status') }}</div>
                @endif

                @yield('content')
            </div>
        </main>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @auth
        @if (env('PUSHER_APP_KEY'))
            <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
            <script src="https://unpkg.com/laravel-echo@1.16.1/dist/echo.iife.js"></script>
            <script>
                (() => {
                    const toastWrap = document.getElementById('realtimeToastWrap');
                    const EchoConstructor = window.Echo;

                    if (!toastWrap || typeof Pusher === 'undefined' || typeof EchoConstructor === 'undefined') {
                        return;
                    }

                    window.Pusher = Pusher;

                    const echo = new EchoConstructor({
                        broadcaster: 'pusher',
                        key: @json(env('PUSHER_APP_KEY')),
                        cluster: @json(env('PUSHER_APP_CLUSTER')),
                        wsHost: @json(env('PUSHER_HOST') ?: 'ws-'.env('PUSHER_APP_CLUSTER').'.pusher.com'),
                        wsPort: Number(@json(env('PUSHER_PORT', 443))),
                        wssPort: Number(@json(env('PUSHER_PORT', 443))),
                        forceTLS: @json((env('PUSHER_SCHEME', 'https') === 'https')),
                        enabledTransports: ['ws', 'wss'],
                        authEndpoint: @json(url('/broadcasting/auth')),
                        auth: {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                            },
                        },
                    });

                    const renderToast = (payload) => {
                        const statusClass = {
                            submitted: 'primary',
                            pending: 'warning',
                            approved: 'success',
                            rejected: 'danger',
                        }[payload.status] ?? 'secondary';

                        const toast = document.createElement('div');
                        toast.className = 'toast border-0 shadow show mb-3';
                        toast.innerHTML = `
                            <div class="toast-header bg-${statusClass} text-white border-0">
                                <strong class="me-auto">${payload.projectTitle}</strong>
                                <small>${payload.timestamp}</small>
                                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast"></button>
                            </div>
                            <div class="toast-body bg-white">
                                <div class="fw-semibold text-capitalize mb-1">${payload.status}</div>
                                <div class="small text-muted mb-3">${payload.message}</div>
                                <a href="${payload.url}" class="btn btn-sm btn-outline-primary">Open</a>
                            </div>
                        `;

                        toastWrap.prepend(toast);
                        new bootstrap.Toast(toast, { delay: 6000 }).show();
                        toast.addEventListener('hidden.bs.toast', () => toast.remove());
                    };

                    echo.private(`users.{{ auth()->id() }}`)
                        .listen('.project.workflow.updated', renderToast);
                })();
            </script>
        @endif
    @endauth
    @stack('scripts')
</body>
</html>
