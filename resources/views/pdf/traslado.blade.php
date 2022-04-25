<!DOCTYPE>
<html>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <title>Comprobante de traslado</title>
    <!-- Latest compiled and minified CSS -->
    <style>
        @page { margin-bottom: 55px; }
        body {
            /*position: relative;*/
            /*width: 16cm;  */
            /*height: 29.7cm; */
            margin:0;
            padding:0;
            top:0;
            bottom:0;

            /*color: #555555;*/
            /*background: #FFFFFF; */
            font-family: Arial, sans-serif;
            font-size: 14px;
            /*font-family: SourceSansPro;*/
        }

        #logo{
            display:block;
            margin-top: 0%;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }

        #imagen{
            width: 100px;
        }

        #datos{
            float: left;
            margin-top: 0%;
            margin-left: 2%;
            margin-right: 2%;
        /*text-align: justify;*/
        }

        #encabezado{
        text-align: center;
        margin-left: 5%;
        margin-right: 35%;
        font-size: 15px;
        }

        #fact{
            /*position: relative;*/
            float: right;
            margin-top: 2%;
            margin-left: 2%;
            margin-right: 2%;
            margin-bottom: 2%
            font-size: 20px;
        }

        section{
        clear: left;
        }

        #cliente{
        text-align: left;
        }

        #facliente{
            width: 40%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 15px;
            border-color: black;
        }

        #fac, #fv, #fa{
            color: #FFFFFF;
            font-size: 15px;
        }

        #facliente thead{
            padding: 20px;
            background: #f3861c;
            text-align: left;
            border-bottom: 1px solid #FFFFFF;
        }

        #facvendedor{
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 15px;
        }

        #facvendedor thead{
            padding: 20px;
            background: #f3861c;
            text-align: center;
            border-bottom: 1px solid #FFFFFF;
        }

        #facarticulo{
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 15px;
        }

        #facarticulo thead{
            padding: 20px;
            background: #f3861c;
            text-align: center;
            border-bottom: 1px solid #FFFFFF;
        }

        #gracias{
            text-align: center;
            color: #FFFFFF;
            background-color: #333337;
            position: fixed;
            bottom: 65;
            width: 100%;
        }
        #hr{
            color: #f3861c;
        }
        #divPiedra{
            color: #FFFFFF;
            background-color: #333337;
            position: relative;
            top: 65;
            text-align: right;
            width: 100%;
            margin: 0;
        }
        #divIzq{
            background-color: #f3861c;
            color: #e4b178;
            height: 100%;
            float: left;
            /* height: 100%; */
            z-index: -100;
            /* transform: rotate(-90deg); */
        }
        td {
            text-align: center;
        },
        .table-b, .th-b, .td-b {
            border: 1px solid black;
        }
        .firmaEnv{
            float: left;
            /* background-color: #f3861c; */
            width: 45%;
            text-align:center;
            border-top: solid 2px #000;
            margin-left: 2px;
            padding: 0;
            margin-top: 0%;
        }
        .firmaRec{
            float: right;
            /* background-color: #e4b178; */
            width: 45%;
            text-align:center;
            border-top: solid 2px #000;
            margin-left: 2px;
            padding: 0;
            margin-top: 0%;
        }
    </style>
    <body>
        @php
            setlocale(LC_TIME,'spanish.UTF-8');
        @endphp
        @foreach ($traslado as $t)
        <header>
            {{-- <div id="divIzq">aaaaaaa</div> --}}
            <div>
                <img id="logo" src="img/LogoFactura2.png" alt="TroyStoneLogo" id="imagen">
                <p style="float: right;"> <strong >Traslado</strong>  : {{$t->num_comprobante}}</p>
            </div>
            <div id="divPiedra"> <b> La piedra de tus proyectos </b> </div><br><br><br><br><br>
            <div id="datos">
                <p id="encabezado">
                    {{-- <b>TroyStone&reg;</b><br>Calz. Lázaro Cardenas #2080 Int. 20. Col. Del Fresno, C.P. 44900, Guadalajara, Jalisco
                    <br>Telefono:(01 33) 36 92 81 92<br>Email:ventas@troystone.com.mx --}}
                    <b>Guadalajara, Jalisco
                    <?php
                        date_default_timezone_set('America/Mexico_City');
                        setlocale(LC_TIME, 'es_MX.UTF-8');
                        $fecha_actual=strftime("%Y-%m-%d");
                        $hora_actual=strftime("%A, %d de %B de %Y");
                        echo $hora_actual;
                    ?> </b>
                </p>
            </div>
            <div id="fact">
                    <p><strong>Estado: </strong>
                    @php
                    if($t->estado == 'Anulado'){
                        echo "Cancelado";
                    }else{
                        if($t->entregado){
                            echo "Registrado y Entregado";
                        }else{
                            echo "Registrado y no entregado";
                        }
                    }
                    @endphp
                    {{-- <p><strong>Estado: </strong> {{ $v->pagado? "Pagado" : "Pendiente de pago" }} --}}
                    {{-- @php
                        if ($v->entregado){
                            echo " y Entregado";
                        }else{
                            echo " y no entregado";
                        }
                    @endphp --}}
                </p>
            </div>
        </header>

        <section>
            <div>
                <table id="facliente">
                    <thead>
                        <tr>
                            <th id="fac">TRASLADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Realizó: {{$t->usuario}}<br>
                                Traslado a : {{ $t->nueva_ubicacion }}<br>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
        @endforeach
        <br>

        <section>
            <div>
                <table id="facarticulo" class="table-b">
                    <thead>
                        <tr id="fa">
                            <th>No°</th>
                            <th>MATERIAL</th>
                            <th>No° PLACA</th>
                            <th>SKU</th>
                            <th>MEDIDAS</th>
                            <th>M <sup>2</sup></th>
                            <th>TERM</th>
                            <th>Ubicacion Actual</th>
                            <th>Ubicacion Antes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detalles as $index => $det)


                        <tr>
                            <td class="td-b">{{ ($index + 1)}}</td>
                            <td class="td-b">{{ $det->categoria }}</td>
                            <td class="td-b">{{ $det->codigo }}</td>
                            <td class="td-b">{{ $det->articulo }}</td>
                            <td class="td-b">{{ $det->largo }} : {{ $det->alto }}</td>
                            <td class="td-b">{{ $det->metros_cuadrados }}</td>
                            <td class="td-b">{{ $det->terminado }}</td>
                            <td class="td-b">{{ $det->artubicacion }}</td>
                            <td class="td-b">{{ $det->ubicacion }}</td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
                <p><strong>Total m <sup>2:</sup> {{  $sumaMts }} </strong></p>
                @foreach ($traslado as $t)
                <p>

                    <strong>UNA VEZ SALIDA LA MERCANCÍA NO EXISTEN CAMBIOS NI DEVOLUCIONES.</strong><br>
                    <strong>Fecha de registro: </strong>
                    <?php
                        //setlocale(LC_TIME, "spanish");
                        $mi_fecha = $t->fecha_hora;
                        $mi_fecha = str_replace("/", "-", $mi_fecha);
                        $Nueva_Fecha = date("d-m-Y", strtotime($mi_fecha));
                        $Mes_Anyo = strftime("%A, %d de %B de %Y", strtotime($Nueva_Fecha));
                        echo $Mes_Anyo;
                    ?> <br>
                    <strong>Nota: </strong>{{ $t->comentario }} <br>
                </p> <br>
            @endforeach
            </div>
        </section>
        <br>
        <br>
        <section style="margin-top:50px;">
            <div class="firmaEnv">
                <p>Firma de quien entrega</p>
            </div>
            <div class="firmaRec">
                <p>Firma de quien recibe</p>
            </div>
        </section>
        <br>
        <footer>
            <div id="gracias">
                <p>
                    <b>Comprobante de traslado!</b><br>
                    <hr id="hr">
                    <b>www.troystone.com.mx</b><br>
                        ventas@troystone.com.mx <br>
                        Tel:(01 33) 36 92 81 92
                </p>
            </div>
        </footer>
    </body>
</html>
