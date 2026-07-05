@php
    $filterRoute = $section === 'dashboard' ? route('admin.dashboard') : route("admin.{$section}.index");
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin | {{ config('app.name') }}</title>
    <link href="{{ asset('vendor/sb-admin-2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700,800,900" rel="stylesheet">
    <link href="{{ asset('vendor/sb-admin-2/css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
                <div class="sidebar-brand-icon"><i class="fas fa-heartbeat"></i></div>
                <div class="sidebar-brand-text mx-3">Health Admin</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item @if($section === 'dashboard') active @endif"><a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Operacao</div>
            <li class="nav-item @if($section === 'applications') active @endif"><a class="nav-link" href="{{ route('admin.applications.index') }}"><i class="fas fa-fw fa-mobile-alt"></i><span>Aplicativos</span></a></li>
            <li class="nav-item @if($section === 'professionals') active @endif"><a class="nav-link" href="{{ route('admin.professionals.index') }}"><i class="fas fa-fw fa-user-md"></i><span>Profissionais</span></a></li>
            <li class="nav-item @if($section === 'devices') active @endif"><a class="nav-link" href="{{ route('admin.devices.index') }}"><i class="fas fa-fw fa-tablet-alt"></i><span>Dispositivos</span></a></li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Bootstrap API</div>
            <li class="nav-item @if($section === 'versions') active @endif"><a class="nav-link" href="{{ route('admin.versions.index') }}"><i class="fas fa-fw fa-code-branch"></i><span>Versoes</span></a></li>
            <li class="nav-item @if($section === 'flags') active @endif"><a class="nav-link" href="{{ route('admin.flags.index') }}"><i class="fas fa-fw fa-toggle-on"></i><span>Flags</span></a></li>
            <li class="nav-item @if($section === 'notices') active @endif"><a class="nav-link" href="{{ route('admin.notices.index') }}"><i class="fas fa-fw fa-bullhorn"></i><span>Avisos</span></a></li>
            <li class="nav-item @if($section === 'notifications') active @endif"><a class="nav-link" href="{{ route('admin.notifications.index') }}"><i class="fas fa-fw fa-paper-plane"></i><span>Push</span></a></li>
            <li class="nav-item @if($section === 'support') active @endif"><a class="nav-link" href="{{ route('admin.support.index') }}"><i class="fas fa-fw fa-headset"></i><span>Suporte</span></a></li>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline"><button class="rounded-circle border-0" id="sidebarToggle"></button></div>
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
                    <form method="GET" action="{{ $filterRoute }}" class="form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <select name="app" class="form-control bg-light border-0 small">
                                <option value="">Todos aplicativos</option>
                                @foreach ($applications as $application)
                                    <option value="{{ $application->id }}" @selected($selectedApplication === $application->id)>{{ $application->name }}</option>
                                @endforeach
                            </select>
                            <input name="search" value="{{ $search }}" class="form-control bg-light border-0 small" placeholder="Buscar profissional">
                            <div class="input-group-append"><button class="btn btn-primary"><i class="fas fa-search fa-sm"></i></button></div>
                        </div>
                    </form>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="nav-link" href="/api/v1/apps/fitcheck/bootstrap?platform=android"><i class="fas fa-plug fa-fw"></i> API</a></li>
                    </ul>
                </nav>

                <div class="container-fluid" id="dashboard">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">{{ $pageTitle }}</h1>
                        <a href="{{ $filterRoute }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-sync-alt fa-sm text-white-50"></i> Atualizar</a>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success shadow-sm">{{ session('status') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger shadow-sm"><strong>Revise os campos:</strong> {{ $errors->first() }}</div>
                    @endif

                    <div class="row">
                        @foreach ($stats as $label => $value)
                            <div class="col-xl-2 col-md-4 mb-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ str_replace('_', ' ', $label) }}</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $value }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($section === 'dashboard')
                        <div class="row">
                            <div class="col-lg-6">@include('admin.partials.professionals')</div>
                            <div class="col-lg-6">@include('admin.partials.devices')</div>
                        </div>
                    @elseif ($section === 'applications')
                        @include('admin.partials.applications')
                    @elseif ($section === 'professionals')
                        @include('admin.partials.professionals')
                    @elseif ($section === 'devices')
                        @include('admin.partials.devices')
                    @elseif ($section === 'versions')
                        @include('admin.partials.versions')
                    @elseif ($section === 'flags')
                        @include('admin.partials.flags')
                    @elseif ($section === 'notices')
                        @include('admin.partials.notices')
                    @elseif ($section === 'notifications')
                        @include('admin.partials.notifications')
                    @elseif ($section === 'support')
                        @include('admin.partials.support')
                    @endif
                </div>
            </div>
            <footer class="sticky-footer bg-white"><div class="container my-auto"><div class="copyright text-center my-auto"><span>Relationship Platform Health</span></div></div></footer>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
    <script src="{{ asset('vendor/sb-admin-2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/sb-admin-2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/sb-admin-2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('vendor/sb-admin-2/js/sb-admin-2.min.js') }}"></script>
</body>
</html>
