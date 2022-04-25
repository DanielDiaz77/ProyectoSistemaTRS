<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistema de control de inventarios, ventas, ingresos, cotizaciones, TroyStone MX">
    <meta name="author" content="troystone.com.mx">
    <meta name="keyword" content="Sistema de control de inventarios, ventas, ingresos, cotizaciones, TroyStone MX">
    <meta name="userId" content="{{ Auth::check() ? Auth::user()->id : ''}}">
    <link rel="shortcut icon" href="img/favicon.png">
    <title>Inventarios - TroyStone&reg;</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Icons -->
    <link href="css/plantilla.css" rel="stylesheet">
</head>

<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
    <div id="app">
        <header class="app-header navbar">
            <button class="navbar-toggler mobile-sidebar-toggler d-lg-none mr-auto" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="#"></a>
            <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>
            {{-- <ul class="nav navbar-nav d-md-down-none">
                <li class="nav-item px-3">
                    <a class="nav-link" href="/">Escritorio</a>
                </li>
                <li class="nav-item px-3">
                    <a class="nav-link" href="#">Configuraciones</a>
                </li>
            </ul> --}}
            <ul class="nav navbar-nav ml-auto mr-4">
                <notification :notifications ="notifications" ></notification>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button"
                        aria-haspopup="true" aria-expanded="false">
                        {{-- <img src="img/favicon.png" class="img-avatar" alt="admin@bootstrapmaster.com"> --}}
                        <i class="fa fa-sign-out" aria-hidden="true"></i>
                        <span class="d-md-down-none">{{Auth::user()->usuario}} </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <div class="dropdown-header text-center">
                            <strong>Cuenta</strong>
                        </div>
                        <a class="dropdown-item" href="{{ route('logout')}}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa fa-lock"></i> Cerrar sesión</a>
                        <form action="{{ route('logout')}}" id="logout-form" method="POST" style="display:none;">
                            {{ csrf_field() }}
                        </form>
                    </div>
                </li>
            </ul>
        </header>

        <div class="app-body">
            @if(Auth::check())
                @if(Auth::user()->idrol == 1)
                    @include('plantilla.sidebaradministrador')
                @elseif(Auth::user()->idrol == 2)
                    @include('plantilla.sidebarvendedor')
                @elseif(Auth::user()->idrol == 3)
                    @include('plantilla.sidebaralmacenero')
                @elseif(Auth::user()->idrol == 4)
                    @include('plantilla.sidebarcomisionista')
                @else
                @endif
            @endif
            <!-- Contenido Principal -->
            @yield('contenido')
            <!-- /Fin del contenido principal -->
        </div>
    </div>
    <footer class="app-footer fixed-bottom bg-light text-muted" style="height:40px;">
        <div class="container text-left"> <span class="ml-auto">Desarrollado por <a target="_blank" href="http://www.troystone.com.mx/">Sistemas TroyStone</a></span> </div>
        <span><a target="_blank" href="http://www.troystone.com.mx/">TroyStone&reg;</a> 2019</span>
    </footer>


    <script src="js/app.js"></script>
    <script src="js/plantilla.js"></script>

{{-- <style>
    html {
        min-height: 100%;
        position: relative;
    }
    body {
        margin: 0;
        margin-bottom: 40px;
    }
    footer {
        background-color: black;
        position: absolute;
        bottom: 0;
        width: 100%;
        height: 40px;
        color: white;
    }
</style> --}}
</body>

</html>

