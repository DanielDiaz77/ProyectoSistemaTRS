<!DOCTYPE html>
<html lang="es">
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Sistema de control de inventarios, ventas, ingresos, cotizaciones, TroyStone MX">
  <meta name="author" content="troystone.com.mx">
  <meta name="keyword" content="Sistema de control de inventarios, ventas, ingresos, cotizaciones, TroyStone MX">

  <title>Inventarios - TroyStone&reg;</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="css/plantilla.css" rel="stylesheet">

</head>

<body class="app flex-row align-items-center">
  <div class="container">

    @yield('login')

  </div>

    {{-- <script src="js/app.js"></script> --}}
    <script src="js/plantilla.js"></script>
    <style>
        .troybackg {
            background-color: #f5861c !important;
        }
        .troybackg-light {
            background-color: #fc9c21 !important;
        }
    </style>
</body>
</html>
