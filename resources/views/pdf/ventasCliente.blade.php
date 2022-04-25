<!DOCTYPE>
<html>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <title>Estado de cuenta - {{ $cliente->nombre }} </title>
    <style>
        @page { margin-bottom: 20px !important; }
        body {
            margin:0;
            margin-bottom: 20px !important;
            padding:0;
            top:0;
            bottom:0;
            font-family: Arial, sans-serif;
            font-size: 14px;
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
            margin-right: 0%;
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
            text-align: center;
        }
        .EstTitle{
            float: left;
            margin-top: 5px;
        }
        .ventaTable{
            border-collapse: separate;
            border-spacing: 20px 0;
            margin-bottom: 10px;
        }
    </style>
    <body>
        @php
            setlocale(LC_TIME,'spanish.UTF-8');
        @endphp
        <header style="margin-bottom: 40px;">
            <div>
                <img id="logo" src="img/LogoFactura2.png" alt="TroyStoneLogo" id="imagen">
                {{-- <p style="float: right;"> <strong >{{ $cliente->nombre }}</strong>  : {{$cliente->num_documento}}</p> --}}
            </div>
            <div id="divPiedra"> <b> La piedra de tus proyectos </b> </div><br><br><br><br><br>
            <div id="datos">
                <p id="encabezado">
                    <b style="float: right;">Guadalajara, Jalisco
                        <?php
                            date_default_timezone_set('America/Mexico_City');
                            setlocale(LC_TIME, 'es_MX.UTF-8');
                            $fecha_actual=strftime("%Y-%m-%d");
                            $hora_actual=strftime("%A, %d de %B de %Y");
                            echo $hora_actual;
                        ?>
                    </b><br>
                   <h3 class="EstTitle"> Estado de cuenta : {{ $cliente->nombre }} - {{$cliente->num_documento}}</h3><br>
                   <h4 class="EstTitle"> Periodo:
                    <?php
                        date_default_timezone_set('America/Mexico_City');
                        setlocale(LC_TIME, 'es_MX.UTF-8');
                        $fechaInicio=strftime("%d de %B de %Y",strtotime($inicio));
                        echo $fechaInicio;
                    ?>
                    al
                    <?php
                        date_default_timezone_set('America/Mexico_City');
                        setlocale(LC_TIME, 'es_MX.UTF-8');
                        $fechaFin=strftime("%d de %B de %Y",strtotime($fin));
                        echo $fechaFin;
                    ?>
                    </h4><br>
                </p>
            </div>
        </header>
        <section>
                @foreach ($ventas as $v)
                <table class="ventaTable">
                    <thead align="left">
                        <tr>
                            <th>Cliente</th>
                            <th>No° Presupuesto</th>
                            <th>Forma de Pago</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody align="center">
                        <tr>
                            <td>{{ $cliente->nombre }}</td>
                            <td>{{ $v->num_comprobante }}</td>
                            <td>{{ $v->forma_pago }}</td>
                            <td>
                                @php
                                if($v->estado == 'Anulada'){
                                    echo "Anulado";
                                }else{
                                    if($v->adeudo == 0){
                                        echo "Pagado";
                                    }elseif($v->adeudo == $v->total){
                                        echo "Pendiente de pago";
                                    }elseif(($v->total - $v->adeudo) < $v->total){
                                        echo "Pagado parcialmente";
                                    }
                                    if($v->entregado){
                                        echo " y Entregado";
                                    }elseif ($v->entrega_parcial) {
                                        echo " y Entregado Parcialmente";
                                    }else{
                                        echo " y no entregado";
                                    }
                                }
                                @endphp
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="ventaTable">
                    <thead align="left">
                        <tr>
                            <th>Fecha de registro</th>
                            <th>Tipo de facturación</th>
                            <th>Atendió</th>
                        </tr>
                    </thead>
                    <tbody align="center">
                        <tr>
                            <td>
                                <?php
                                    date_default_timezone_set('America/Mexico_City');
                                    setlocale(LC_TIME, 'es_MX.UTF-8');
                                    $hora_actual=strftime("%A, %d de %B de %Y",strtotime($v->fecha_hora));
                                    echo $hora_actual;
                                ?>
                            </td>
                            <td>{{ $v->tipo_facturacion }}</td>
                            <td>{{ $v->usuario }}</td>
                        </tr>
                    </tbody>
                </table>
                <table id="facarticulo" class="table-b">
                    <thead>
                        <tr id="fa">
                            <th>MATERIAL</th>
                            <th>No° PLACA</th>
                            <th>MEDIDAS</th>
                            <th>M<sup>2</sup></th>
                            <th>BODEGA</th>
                            <th>P U</th>
                            <th width="15px">CANT.</th>
                            <th width="15px">DESC</th>
                            <th>SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detalles as $index => $det)
                        @if($det->idventa == $v->id)
                        <tr>
                            <td class="td-b">{{ $det->articulo }} - {{ $det->terminado }}</td>
                            <td class="td-b">{{ $det->codigo }}</td>
                            <td class="td-b">{{ $det->largo }} : {{ $det->alto }}</td>
                            <td class="td-b">{{ $det->metros_cuadrados }}</td>
                            <td class="td-b">{{ $det->ubicacion }}</td>
                            <td class="td-b">{{ $det->precio }}</td>
                            <td class="td-b">{{ $det->cantidad }}</td>
                            <td class="td-b">{{ $det->descuento }}</td>
                            <td class="td-b">{{ number_format(((($det->precio * $det->cantidad) * $det->metros_cuadrados) - $det->descuento),6) }}</td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="th-b">SUBTOTAL</th>
                            <td class="th-b">{{ number_format($v->total/($v->impuesto + 1),6)}}</td>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="th-b">IVA</th>
                            <td class="th-b">{{ number_format(($v->total/($v->impuesto + 1)*$v->impuesto),6) }}</td>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="th-b">TOTAL</th>
                            <td class="th-b">{{ number_format($v->total,4) }}</td>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="th-b">Adeudo</th>
                            <td class="th-b">{{ number_format($v->adeudo,4) }}</td>
                        </tr>
                    </tfoot>
                </table>
                @endforeach
        </section>
        <section>
            <h2>Ventas con adeudos del 100%</h2>
            <table id="facarticulo" class="table-b">
                <thead>
                    <tr id="fa">
                        <th>No° de Presupuesto</th>
                        <th>Atendió</th>
                        <th>Cliente</th>
                        <th>Fecha Hora</th>
                        <th>Impuesto</th>
                        <th>Total</th>
                        <th>Forma de pago</th>
                        <th>Facturación</th>
                        <th>Adeudo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($adedudadas as $v)
                    <tr>
                        <td class="td-b">{{ $v->num_comprobante }}</td>
                        <td class="td-b">{{ $v->usuario }}</td>
                        <td class="td-b">{{ $v->cliente }}</td>
                        <td class="td-b">
                            <?php
                                date_default_timezone_set('America/Mexico_City');
                                setlocale(LC_TIME, 'es_MX.UTF-8');
                                $hora_actual=strftime("%A, %d de %B de %Y",strtotime($v->fecha_hora));
                                echo $hora_actual;
                            ?>
                        </td>
                        <td class="td-b">{{ $v->impuesto }}</td>
                        <td class="td-b">{{ $v->total }}</td>
                        <td class="td-b">{{ $v->forma_pago }}</td>
                        <td class="td-b">{{ $v->tipo_facturacion }}</td>
                        <td class="td-b">{{ $v->adeudo }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <h3 style="float:right;">Total Adeudo: {{ $sumaAdedudadas[0]->total }}</h3>
        </section>
        <section>
            <h2>Ventas con pagos parciales</h2>
            <table id="facarticulo" class="table-b">
                <thead>
                    <tr id="fa">
                        <th>No° de Presupuesto</th>
                        <th>Atendió</th>
                        <th>Cliente</th>
                        <th>Fecha Hora</th>
                        <th>Impuesto</th>
                        <th>Total</th>
                        <th>Forma de pago</th>
                        <th>Facturación</th>
                        <th>Adeudo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($parciales as $v)
                    <tr>
                        <td class="td-b">{{ $v->num_comprobante }}</td>
                        <td class="td-b">{{ $v->usuario }}</td>
                        <td class="td-b">{{ $v->cliente }}</td>
                        <td class="td-b">
                            <?php
                                date_default_timezone_set('America/Mexico_City');
                                setlocale(LC_TIME, 'es_MX.UTF-8');
                                $hora_actual=strftime("%A, %d de %B de %Y",strtotime($v->fecha_hora));
                                echo $hora_actual;
                            ?>
                        </td>
                        <td class="td-b">{{ $v->impuesto }}</td>
                        <td class="td-b">{{ $v->total }}</td>
                        <td class="td-b">{{ $v->forma_pago }}</td>
                        <td class="td-b">{{ $v->tipo_facturacion }}</td>
                        <td class="td-b">{{ $v->adeudo }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <h3 style="float:right;">Total Adeudo: {{ $sumaParciales[0]->adeudo }}</h3>
        </section>
        <section>
            <h2>Cliente : {{ $cliente->nombre }} - {{$cliente->num_documento}} - {{ $cliente->tipo }}</h2>
            <h2>Total de ventas : {{ number_format( $sumaVentas[0]->total,4)}}</h2>
            <h2>Adeudo general : {{ number_format( $sumaAdedudadas[0]->total + $sumaParciales[0]->adeudo,4)}}</h2>

        </section>
    </body>
</html>
