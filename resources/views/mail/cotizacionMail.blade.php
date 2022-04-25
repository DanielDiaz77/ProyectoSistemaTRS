<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotizacion Mail</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"> --}}
    <!-- Styles -->
    <style>
        html,
        body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .par_conf {
            text-align: justify !important;
        }
        .box {
            margin: 20px;
        }
    </style>
</head>

<body>
    <br />
    <div class="container box" style="width: 970px;">
        <div class="row">
            <div class="col-12">
                <h1 style="margin-bottom:0px;" align="center"><strong>TROYSTONE MARMOLES Y GRANITOS</strong></h1>
                <P  style="margin:0;padding:0;" align="center"><u>La piedra de tus proyectos</u></P>
            </div>
            <div class="col-12 mt-3">
                <h4 style="text-align:center;"> Cotizacion {{ $num_cot }}</h4>
                <p> <u>Presente:</u> </p>
                <p> {{ $data['name'] }} </p>
                <p>Buen dia, le hago llegar su cotización, esperando su confirmación. <br> estamos a sus ordenes.</p>
            </div>
            <div class="col-12 mt-5">
                <p>Twitter: @TroystoneMarmol | Facebook: TroyStone Mármoles y Granitos</p>
                <p>Sitio Web:
                    <a href="https://www.troystone.com.mx" target="_blank">www.troystone.com.mx</a>
                </p>
                <p class="par_conf">Aviso de Confidencialidad: <br> Este correo electrónico y/o el material adjunto es para uso exclusivo de la persona o entidad a la que expresamente se le ha enviado, y puede contener información confidencial o material privilegiado. Si
                    usted no es el destinatario legítimo del mismo, por favor repórtelo inmediatamente al remitente del correo y bórrelo. Cualquier revisión, retransmisión, difusión o cualquier otro uso de este correo, por personas o entidades distintas
                    a las del destinatario legítimo, queda expresamente prohibido. Este correo electrónico no pretende ni debe ser considerado como constitutivo de ninguna relación legal, contractual o de otra índole similar.
                </p>
                <p class="par_conf">Notice of Confidentiality: <br> The information transmitted is intended only for the person or entity to which it is addressed and may contain confidential and/or privileged material. Any review, re-transmission, dissemination or other
                    use of, or taking of any action in reliance upon, this information by persons or entities other than the intended recipient is prohibited. If you received this in error, please contact the sender immediately by return electronic transmission
                    and then immediately delete this transmission, including all attachments, without copying, distributing or disclosing same.
                </p>
            </div>
        </div>
    </div>
</body>

</html>
