<!DOCTYPE >
<html>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <title>Comprobante de PresuspuestoES</title>
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
        #abonos{
            text-align: center;
            color: #333337;
            height: 35px;
        }
        #facpagos thead{
        padding: 20px;
        background: #f3861c;
        text-align: right;
        border-bottom: 1px solid #FFFFFF;
        }

        #pagos{
        text-align: right;
        }

        #fapagos{
            width: 40%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 15px;
            border-color: black;
        }

        .table, .td, .th{
            border: 1px solid black;
            text-align: right;

        }
        #facdescripcion{
            width: 40%;
            text-align: right;


            border-color: black;
        }
    </style>
    <body>
        @foreach ($projects as $v)
        @php
            setlocale(LC_TIME,'spanish.UTF-8');
        @endphp
        <header>
            <div>
                <img id="logo" src="img/LogoFactura2.png" alt="TroyStoneLogo" id="imagen">
                <p style="float: right;"> <strong >{{ $v->tipo_comprobante }}</strong>  : {{$v->num_comprobante}}</p>
            </div>
            <div id="divPiedra"> <b> La piedra de tus proyectos </b> </div><br><br><br><br><br>
            <div id="datos">
                <p id="encabezado">
                    <b>Guadalajara, Jalisco
                        <?php
                        date_default_timezone_set('America/Mexico_City');
                        setlocale(LC_TIME, 'es_MX.UTF-8');
                        $fecha_actual=strftime("%Y-%m-%d");
                        $hora_actual=strftime("%A, %d de %B de %Y");
                        echo $hora_actual;
                        ?>
                    </b>
                </p>
            </div>
            <div id="fact">
                <p><strong>Estado: </strong>
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
                </p>
            </div>
        </header>
        <br>
        <br>
        <br>
        <br>
        <section>
            <div>
                <table id="facliente" align="left" >
                    <thead>
                        <tr>
                            <th id="fac" > Cliente:</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>{{$v->cliente}}</th>
                        </tr>
                        <tr>
                            <th><span style="color: #f3861c" align="left">Contacto:</span>{{ $v->contacto }} {{ $v->tel_company }} <br>
                                <span style="color: #f3861c" align="left">Teléfono: </span>{{ $v->telefono }} <br>
                                <span style="color: #f3861c" align="left">RFC:</span> {{ $v->rfc }}
                                <span style="color: #f3861c" align="left">Uso del CFDI: </span>{{$v->cfdi}}<br>
                                <span style="color: #f3861c" align="left">Domicilio:</span>{{ $v->domicilio }} {{$v->ciudad}}<br>
                                <span style="color: #f3861c" align="left">Correo-E: </span> {{ $v->email  }} <br>
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
        <br>
        <br>
        <br>
        <br>
        <section>
            <table id="facliente" align="right">
            <thead >
                <tr >
                    <th id="fac" text>Abonos:</th>
                    <th id="fac">Pagos:</th>
                </tr>
            </thead>
            <tbody>
                    <tr>
                        <td class="td-b">Total:</td>
                        <td class="td-b"><p>${{number_format($v->total,2)}}</p></td>
                    </tr>
                    <tr>
                        <td class="td-b">Abonado:</td>
                        <td class="td-b"><p>${{number_format($abonos,2)}}</p></td>
                    </tr>
                    <tr>
                        <td class="td-b">Adeudo:</td>
                        <td class="td-b"><p>${{number_format($v->adeudo,2)}}</p></td>
                    </tr>
            </tbody>

            </table>
        </section>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <section>
            <div>
                <table id="facliente" align="left">
                    <thead>
                        <tr>
                            <th id="fac" >Nombre del Proyecto:</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>{{$v->title}}</th><br>
                        </tr>
                        <tr>
                            <th>
                                <span style="font-size: 15px ; color: #f3861c" align="left">Valor del Proyecto:</span> <span style="font-size: 15px" align="left"> ${{number_format($v->total,2)}} </span><br>
                                <strong style="font-size: 15px ; color: #f3861c" align="left">Descripcion del Proyecto:</strong> <br><span align="left">{{$v->content}}</span> <br>
                            </th>
                        </tr>

                    </tbody>
                </table>
                @foreach ($image as $pro )
                    @if ($pro->tipo != 'pdf')
                    <figure class="figure">
                        <img :src="projectfiles/pIf4KwvIzJlzatNd.jpeg" class="figure-img img-fluid rounded" alt="File CAPTION">
                        <figcaption class="figure-caption text-right" v-text="projectfiles/pIf4KwvIzJlzatNd.jpeg">{{$pro->url}}</figcaption>

                    </figure>

                    @endif


                @endforeach
            </div>
        </section>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>

        <strong id="abonos">Abonos</strong>
        <section>
            <div>
                @foreach($projects as $v)
                @foreach($capturas as $index => $depo)
                    @if ($v->id == $depo->depositable_id < 1)
                    <table id="facarticulo" class="table-b">
                        <thead>
                            <tr id="fa">
                                <th>No°</th>
                                <th>Abonos</th>
                                <th>Forma de Pago</th>
                                <th>Fecha</th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="td-b">{{ $index +1}}</td>
                                <td class="td-b"><p>${{number_format($depo->total,2) }}</p></td>
                                <td class="td-b">{{ $depo->forma_pago}}</td>
                                <td class="td-b">{{ $depo->fecha_hora}}</td>
                            </tr>
                        </tbody>
                    </table>
                    @elseif($v->id == $depo->depositable_id > 2)
                    <table id="facarticulo" class="table-b">
                        <thead>
                            <tr id="fa">
                                <th>No°</th>
                                <th>Abonos</th>
                                <th>Forma de Pago</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="td-b">{{ $index +1}}</td>
                                <td class="td-b">{{ $depo->total }}</td>
                                <td class="td-b">{{ $depo->forma_pago}}</td>
                                <td class="td-b">{{ $depo->fecha_hora}}</td>
                            </tr>
                        </tbody>
                    </table>
                    @endif
                @endforeach
                @endforeach
                <strong>Instalacion:</strong>
                @if ($v->instalacion == 1)
                    <label class="container">Si.
                        <input type="checkbox" checked="checked">
                        <span class="checkmark"></span>
                    </label><br> <br>
                <strong>Direcion Instalacion: </strong> {{$v->observacion}}

                  @elseif ($v->instalacion == 0)

                    <label class="container">No.
                        <input type="checkbox" checked="checked">
                        <span class="checkmark"></span>
                    </label><br>

                  @if ($v->flete == 1)

                  <strong>Flete:</strong>
                  <label class="container">Cuenta del Cliente.
                    <input type="checkbox" checked="checked">
                    <span class="checkmark"></span>
                  </label>

                  @elseif ($v->flete == 0)

                  <strong>Flete:</strong>
                  <label class="container">Cuenta del Proyecto.
                    <input type="checkbox" checked="checked">
                    <span class="checkmark"></span>
                  </label><br> <br>

                  <strong>Direcion del Flete: </strong> {{$v->observacion}}

                  @endif
                @endif <br>

                </div>
                <br>

                    <strong>Fecha de Realizacion:</strong>{{ $v->registro }}<br>
                    <strong>Fecha Compromiso: </strong> {{ $v->fin}} <br>
            </div>
        </section>
        <br>
        <br>
        @endforeach
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

        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
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
