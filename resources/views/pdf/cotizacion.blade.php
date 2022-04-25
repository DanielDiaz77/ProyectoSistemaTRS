<!DOCTYPE>
<html>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <title>Cotizacion</title>
    <style>
        @page { margin-bottom: 20px !important; }
        body {
        /*position: relative;*/
        /*width: 16cm;  */
        /*height: 29.7cm; */
        margin:0;
        margin-bottom: 20px !important;
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
            text-align: center;
        }
    </style>
    <body>
        @php
            setlocale(LC_TIME,'spanish.UTF-8');
        @endphp
        @foreach ($cotizacion as $c)
        <header>
            {{-- <div id="divIzq">aaaaaaa</div> --}}
            <div>
                <img id="logo" src="img/LogoFactura2.png" alt="TroyStoneLogo" id="imagen">
                <p style="float: right;"> <strong >{{ $c->tipo_comprobante }}</strong>  : {{$c->num_comprobante}}</p>
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
                    if($c->estado == 'Anulada'){
                        echo "Vencida";
                    }else{
                        echo "Vigente";
                    }
                    @endphp
                </p>
            </div>
        </header>

        <section>
            <div>
                <table id="facliente">
                    <thead>
                        <tr>
                            <th id="fac">Cliente</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><th>{{$c->nombre}}</th></tr>
                        <tr><th><strong>Presente</strong></th></tr>
                        <tr>
                        <th>Contacto: {{ $c->contacto }} {{ $c->tel_company }} <br>
                            RFC: {{ $c->rfc }}<br>
                            Domicilio: {{ $c->domicilio }} {{$c->ciudad}}<br>
                            Teléfono: {{ $c->telefono }}<br>
                            Correo-E: {{ $c->email  }}</p></th>
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
                            <th>PLACA</th>
                            <th>MATERIAL</th>
                            <th>ACABADO</th>
                            <th>BODEGA</th>
                            <th>MEDIDAS</th>
                            <th>MTS <sup>2</sup></th>
                            <th>P.U. x C</th>
                            {{-- <th>CANT.</th> --}}
                            {{-- <th>DESC</th> --}}
                            <th>SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detalles as $index => $det)


                        <tr>
                            <td class="td-b">{{ ($index + 1)}}</td>
                            <td class="td-b">{{ $det->codigo }}</td>
                            <td class="td-b">{{ $det->articulo }}</td>
                            <td class="td-b">{{ $det->terminado }}</td>
                            <td class="td-b">{{ $det->ubicacion }}</td>
                            <td class="td-b">{{ $det->largo }} : {{ $det->alto }}</td>
                            <td class="td-b">{{ $det->metros_cuadrados }}</td>
                            <td class="td-b">{{ $det->precio }} x {{ $det->cantidad }}</td>
                           {{--  <td class="td-b">{{ $det->cantidad }}</td> --}}
                            {{-- <td class="td-b">{{ $det->descuento }}</td> --}}
                            <td class="td-b">{{ number_format(((($det->precio * $det->cantidad) * $det->metros_cuadrados) - $det->descuento),2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @foreach ($cotizacion as $c)
                        <tr>
                            {{-- <th></th> --}}
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>

                            <th></th>
                            <th class="th-b">SUBTOTAL</th>
                            <td class="th-b">{{ number_format(round($c->total/($c->impuesto + 1),2),2)}}</td>
                        </tr>
                        <tr>
                            {{-- <th></th> --}}
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="th-b">IVA</th>
                            <td class="th-b">{{ number_format(round(($c->total/($c->impuesto + 1))*$c->impuesto,2),2) }}</td>
                        </tr>
                        <tr>
                            {{-- <th></th> --}}
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="th-b">TOTAL</th>
                            <td class="th-b">{{ number_format($c->total,2) }}</td>
                        </tr>
                        <tr><th class="th-b" colspan="11">Total m <sup>2:</sup> {{  $sumaMts }}</th></tr>
                        @endforeach
                    </tfoot>

                </table>
                @foreach ($cotizacion as $c)
                <p>
                    <strong>Vigencia: </strong>
                    <?php
                        //setlocale(LC_TIME, "spanish");
                        $mi_fecha = $c->vigencia;
                        $mi_fecha = str_replace("/", "-", $mi_fecha);
                        $Nueva_Fecha = date("d-m-Y", strtotime($mi_fecha));
                        $Mes_Anyo = strftime("%A, %d de %B de %Y", strtotime($Nueva_Fecha));
                        echo $Mes_Anyo;
                    ?> <br>
                    <strong>Nota: </strong>{{ $c->observacion }} <br>
                    <strong>Forma de pago: </strong>{{ $c->forma_pago }} <br>
                    <strong>Tiempo de entrega:</strong>{{ $c->tiempo_entrega }}<br>
                    <strong> Lugar de entrega: </strong>{{ $c->lugar_entrega }}
                </p> <br>
            @endforeach
            {{-- <p style="text-align:center";><strong>Atentamente, <br>
                Lic. Andrés Pérez Arenzana</strong></p> --}}
            <strong>Cuentas para depositar en pesos:</strong><br>
            <strong>Beneficiario: TROYSTONE, S.A. DE C.V. RFC: TRO 120511 B35 </strong><br><hr>
            <strong> - IXE / Banorte - Cuenta: 001750 8770 CLABE: <u> 07232 0000 1750 8770 5</u>  </strong><br>
            <strong> - Banamex - Suc.: 7005 Cuenta: 3393 800 CLABE: <u> 00232 07005 3393 800 9 </u> </strong><br><hr>
            <strong> - Santander - Cuenta: 6550 4375 654 CLABE: <u> 01432 06550 4375 654 2 </u> </strong><br><hr>
            <p style="font-size:7px;">*** SUGERIMOS A NUESTROS CLIENTES SELLAR SU MATERIAL CON PRODUCTOS LATICRETE
                    *** PRECIOS LAB EN BODEGA TROYSTONE ZMG *APLICA PARA VENTA DE MATERIAL*
                    *** TIEMPO DE ENTREGA SUJETO A DISPONIBILIDAD.
                    *** NUESTROS PRODUCTOS SON DE ORIGEN NATURAL, POR LO QUE PUEDEN PRESENTAR VARIACIONES EN SU TONALIDAD O EN SUS BETAS
                    *** CAMBIOS DE PRECIO SIN PREVIO AVISO SI EL MATERIAL NO ESTA LIQUIDADO AL 100%
                    *** EL TIPO DE CAMBIO SE TOMA DE BANAMEX A LA VENTA DEL DÍA DE LA OPERACIÓN, APLICA PARA VENTA EN USD.
                    ***UNA VEZ SALIDA LA MERCANCIA EL RIESGO CORRE POR EL CLIENTE, NO NOS HACEMOS CARGO DE DAÑOS, O ALGUN OTRO PERCANCE </p>
            </div>
        </section>
        <br>
        <br>
        <footer>
            <div id="gracias">
                <p>
                    <b>Gracias por su compra!</b><br>
                    <hr id="hr">
                    <b>www.troystone.com.mx</b><br>
                        ventas@troystone.com.mx <br>
                        Tel:(01 33) 36 92 81 92
                </p>
            </div>
        </footer>
    </body>
</html>
