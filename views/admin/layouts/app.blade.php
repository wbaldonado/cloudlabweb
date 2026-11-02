<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - CloudLab Latam')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style nonce="{{ $cspNonce ?? '' }}">
        body {
            font-family: 'Plus Jakarta Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar,
        .sidebar.offcanvas,
        .sidebar.offcanvas-md,
        .sidebar .offcanvas-body {
            background-color: #05070a !important;
        }
        .sidebar {
            min-height: 100vh;
        }
        @media (min-width: 768px) {
            .sidebar.offcanvas-md {
                background-color: #05070a !important;
            }
        }
        .sidebar .nav-link {
            color: #ecf0f1;
        }
        .sidebar .nav-link:hover {
            background-color: #121c2e;
            color: #fff;
        }
        .sidebar .nav-link.active {
            background-color: #0a6d80;
            color: #fff;
        }
        .sidebar .nav-link[data-bs-toggle="collapse"] {
            cursor: pointer;
        }
        .sidebar .collapse .nav-link {
            padding-left: 2rem;
            font-size: 0.9rem;
        }
        .admin-mobile-bar {
            position: sticky;
            top: 0;
            z-index: 1030;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #05070a;
            color: #fff;
            padding: 0.7rem 1rem;
        }
        .admin-mobile-bar .btn {
            color: #fff;
            border-color: rgba(255, 255, 255, 0.35);
        }
        .btn-primary {
            background-color: #05070a;
            border-color: #05070a;
        }
        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #0a1220;
            border-color: #00a8c5;
        }
        .btn-outline-primary {
            color: #0a6d80;
            border-color: #00a8c5;
        }
        .btn-outline-primary:hover {
            background-color: #05070a;
            border-color: #05070a;
            color: #fff;
        }
        @media (max-width: 767.98px) {
            .sidebar.offcanvas-md {
                --bs-offcanvas-width: min(280px, 86vw);
                --bs-offcanvas-bg: #05070a;
                background-color: #05070a !important;
            }
            .sidebar .offcanvas-title {
                color: #fff;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="admin-mobile-bar d-md-none">
        <button type="button" class="btn btn-sm" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar" aria-label="Abrir menú">
            <i class="bi bi-list fs-4"></i>
        </button>
        <strong>Admin Panel</strong>
    </div>
    <div class="container-fluid">
        <div class="row">
            <nav id="adminSidebar" class="col-md-3 col-lg-2 sidebar offcanvas-md offcanvas-start" tabindex="-1" aria-labelledby="adminSidebarLabel">
                <div class="offcanvas-header d-md-none">
                    <h5 class="offcanvas-title" id="adminSidebarLabel">Admin Panel</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar" aria-label="Cerrar"></button>
                </div>
                <div class="offcanvas-body p-0">
                <div class="position-sticky pt-3">
                    <h4 class="text-white px-3 mb-3 d-none d-md-block">Admin Panel</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}" href="{{ route('admin.blogs.index') }}">
                                <i class="bi bi-journal-text"></i> Blogs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.blog-comments.*') ? 'active' : '' }}" href="{{ route('admin.blog-comments.index') }}">
                                <i class="bi bi-chat-dots"></i> Comments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}" href="{{ route('admin.projects.index') }}">
                                <i class="bi bi-folder"></i> Projects
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.technologies.*') ? 'active' : '' }}" href="{{ route('admin.technologies.index') }}">
                                <i class="bi bi-cpu"></i> Technologies
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.knowledge-base.*') ? 'active' : '' }}" href="{{ route('admin.knowledge-base.index') }}">
                                <i class="bi bi-chat-quote"></i> Knowledge Base
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.password.*') ? 'active' : '' }}" href="{{ route('admin.password.edit') }}">
                                <i class="bi bi-shield-lock"></i> Reset Password
                            </a>
                        </li>
                        <li class="nav-item">
                            @php($crmUrl = config('app.crm_flask_url') ?: route('admin.crm.dashboard'))
                            <a class="nav-link {{ !config('app.crm_flask_url') && request()->routeIs('admin.crm.dashboard') ? 'active' : '' }}" href="{{ $crmUrl }}" @if(config('app.crm_flask_url')) target="_blank" rel="noopener" @endif>
                                <i class="bi bi-people"></i> CRM Dashboard
                                @if(config('app.crm_flask_url'))
                                <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                                @endif
                            </a>
                            <a class="nav-link {{ request()->routeIs('admin.crm.campaigns.*') ? 'active' : '' }}" href="{{ \Illuminate\Support\Facades\Route::has('admin.crm.campaigns.index') ? route('admin.crm.campaigns.index') : url('/admin/crm/campaigns') }}">
                                <i class="bi bi-megaphone"></i> Campañas email
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}" target="_blank">
                                <i class="bi bi-house"></i> View Site
                            </a>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="nav-link border-0 bg-transparent text-white w-100 text-start">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                </div>
            </nav>

            <main class="col-md-9 col-lg-10 px-md-4">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>

