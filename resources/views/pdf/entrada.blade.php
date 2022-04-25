<!DOCTYPE>
<html>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <title>Comprobante de ingreso</title>
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

        .tableArts {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
            /* table-layout:fixed; */
        }

        .td-arts, .th-arts {
            border: 1px solid #000;
            text-align: left;
            padding: 4px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }

        .headArts {
            color: #FFFFFF;
            font-size: 15px;
            background-color: #f3861c;
        }
    </style>
    <body>
        @php
            setlocale(LC_TIME,'spanish.UTF-8');
        @endphp
        @foreach ($entrada as $in)
        <header>
            {{-- <div id="divIzq">aaaaaaa</div> --}}
            <div>
                <img id="logo" src="img/LogoFactura2.png" alt="TroyStoneLogo" id="imagen">
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
                    ?> </b>
                </p>
            </div>
        </header>

        <section>
            <div>
                <table id="facliente">
                    <thead>
                        <tr>
                            <th id="fac">INGRESO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>{{$in->num_comprobante}} <br> Realizó: {{$in->usuario}} <strong style="float: right !important">Estado: @php
                                if($in->estado == 'Anulado'){
                                    echo "Cancelado";
                                }else{
                                    echo "Registrado";
                                }
                                @endphp</strong></th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
        @endforeach
        <br>

        <section>
            <div>
                <table class="tableArts">
                    <tr class="headArts">
                        <th class="th-arts" style="width: 15px !important">No°</th>
                        <th class="th-arts">CODIGO</th>
                        <th class="th-arts">SKU</th>
                        <th class="th-arts" style="width: 20px !important">CANT.</th>
                        <th class="th-arts">UBICACION</th>
                    </tr>
                    <tbody>
                        @foreach ($detalles as $index => $det)
                        <tr>
                            <td class="td-arts" style="width: 20px !important">{{ ($index + 1)}}</td>
                            <td class="td-arts">{{ $det->codigo }}</td>
                            <td class="td-arts" style="width: 90% !important">{{ $det->sku }}</td>
                            <td class="td-arts" style="width: 40px !important; text-align: center;">{{ $det->cantidad }}</td>
                            <td class="td-arts">{{ $det->ubicacion }}</td>
                        </tr>
                    @endforeach
                    </tbody>

                  </table>

                @foreach ($entrada as $in)
                <p>

                    <strong>UNA VEZ SALIDA LA MERCANCÍA NO EXISTEN CAMBIOS NI DEVOLUCIONES.</strong><br>
                    <strong>Fecha de registro: </strong> <br>
                    <?php
                        //setlocale(LC_TIME, "spanish");
                        $mi_fecha = $in->fecha_hora;
                        $mi_fecha = str_replace("/", "-", $mi_fecha);
                        $Nueva_Fecha = date("d-m-Y", strtotime($mi_fecha));
                        $Mes_Anyo = strftime("%A, %d de %B de %Y", strtotime($Nueva_Fecha));
                        echo $Mes_Anyo;
                    ?> <br>
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
                    <b>Comprobante de ingreso!</b><br>
                    <hr id="hr">
                    <b>www.troystone.com.mx</b><br>
                        ventas@troystone.com.mx <br>
                        Tel:(01 33) 36 92 81 92
                </p>
            </div>
        </footer>
    </body>
</html>
