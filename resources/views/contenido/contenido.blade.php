@extends('principal')
@section('contenido')

    @if(Auth::check())
        @if(Auth::user()->idrol == 1)
            <template v-if="menu==0">
                <dashboard></dashboard>
            </template>

            <template v-if="menu==1">
                <categoria></categoria>
            </template>

            <template v-if="menu==2">
                <articulo></articulo>
            </template>

            <template v-if="menu==41">
                <articuloeditar></articuloeditar>
            </template>

            <template v-if="menu==3">
                <ingreso></ingreso>
            </template>

            <template v-if="menu==4">
                <proveedor></proveedor>
            </template>

            <template v-if="menu==5">
                <venta></venta>
            </template>

            <template v-if="menu==13">
                <cotizacion></cotizacion>
            </template>

            <template v-if="menu==14">
                <entrega></entrega>
            </template>

            <template v-if="menu==15">
                <tarea></tarea>
            </template>

            <template v-if="menu==6">
                <cliente></cliente>
            </template>

            <template v-if="menu==7">
                <user></user>
            </template>

            <template v-if="menu==8">
                <rol></rol>
            </template>

            <template v-if="menu==9">
                <consultaingreso></consultaingreso>
            </template>

            <template v-if="menu==10">
                <consultaventa></consultaventa>
            </template>

            <template v-if="menu==16">
                <calendario></calendario>
            </template>

            <template v-if="menu==17">
                <traslado></traslado>
            </template>

            <template v-if="menu==18">
                <facturacion></facturacion>
            </template>

            <template v-if="menu==19">
                <consultaactividad></consultaactividad>
            </template>

            <template v-if="menu==20">
                <actividad></actividad>
            </template>

            <template v-if="menu==11">
                <ayuda></ayuda>
            </template>

            <template v-if="menu==12">
                <acerca></acerca>
            </template>

            <template v-if="menu==21">
                <recadero></recadero>
            </template>

            <template v-if="menu==22">
                <project></project>
            </template>

            <template v-if="menu==23">
                <galeria></galeria>
            </template>

            <template v-if="menu==24">
                <credito></credito>
            </template>

            <template v-if="menu==38">
                <facturacionpe></facturacionpe>
            </template>

            <template v-if="menu==40">
                <cotizacionp></cotizacionp>
            </template>

            <template v-if="menu==43">
                <articuloeditado></articuloeditado>
            </template>

            <template v-if="menu==44">
                <detalles></detalles>
            </template>

            <template v-if="menu==34">
                <javas></javas>
            </template>

            <template v-if="menu==35">
                <ingresojava></ingresojava>
            </template>

            <template v-if="menu==36">
                <ventajava></ventajava>
            </template>

            <template v-if="menu==49">
                <cotizacionjava></cotizacionjava>
            </template>

            <template v-if="menu==51">
                <trasladojava></trasladojava>
            </template>

            <template v-if="menu==37">
                <abrasivos></abrasivos>
            </template>

            <template v-if="menu==39">
                <ingresoabrasivo></ingresoabrasivo>
            </template>

            <template v-if="menu==42">
                <ventaabrasivo></ventaabrasivo>
            </template>

            <template v-if="menu==50">
                <cotizacionabrasivo></cotizacionabrasivo>
            </template>

            <template v-if="menu==52">
                <trasladoabrasivo></trasladoabrasivo>
            </template>

            <template v-if="menu==48">
                <bloques></bloques>
            </template>

            <template v-if="menu==55">
                <ingresobloque></ingresobloque>
            </template>

            <template v-if="menu==53">
                <facturacionjava></facturacionjava>
            </template>

            <template v-if="menu==54">
                <facturacionabrasivo></facturacionabrasivo>
            </template>

            <template v-if="menu==56">
                <plantilla></plantilla>
            </template>

            <template v-if="menu==57">
                <reclamo></reclamo>
            </template>


        @elseif(Auth::user()->idrol == 2)

            <template v-if="menu==0">
                <dashboard></dashboard>
            </template>

            <template v-if="menu==2">
                <articulo></articulo>
            </template>

            <template v-if="menu==41">
                <articuloeditar></articuloeditar>
            </template>

            <template v-if="menu==3">
                <ingreso></ingreso>
            </template>

            <template v-if="menu==4">
                <proveedor></proveedor>
            </template>

            <template v-if="menu==5">
                <venta></venta>
            </template>

            <template v-if="menu==14">
                <entrega></entrega>
            </template>

            <template v-if="menu==13">
                <cotizacion></cotizacion>
            </template>

            <template v-if="menu==15">
                <tarea></tarea>
            </template>

            <template v-if="menu==6">
                <cliente></cliente>
            </template>

            <template v-if="menu==10">
                <consultaventa></consultaventa>
            </template>

            <template v-if="menu==16">
                <calendario></calendario>
            </template>

            <template v-if="menu==17">
                <traslado></traslado>
            </template>

            <template v-if="menu==11">
                <ayuda></ayuda>
            </template>

            <template v-if="menu==12">
                <acerca></acerca>
            </template>

            <template v-if="menu==18">
                <facturacion></facturacion>
            </template>

            <template v-if="menu==20">
                <actividad></actividad>
            </template>

            <template v-if="menu==21">
                <recadero></recadero>
            </template>

            <template v-if="menu==22">
                <project></project>
            </template>

            <template v-if="menu==23">
                <galeria></galeria>
            </template>
            <template v-if="menu==38">
                <facturacionpe></facturacionpe>
            </template>

            <template v-if="menu==40">
                <cotizacionp></cotizacionp>
            </template>

            <template v-if="menu==34">
                <javas></javas>
            </template>

            <template v-if="menu==35">
                <ingresojava></ingresojava>
            </template>

            <template v-if="menu==36">
                <ventajava></ventajava>
            </template>

            <template v-if="menu==49">
                <cotizacionjava></cotizacionjava>
            </template>

            <template v-if="menu==51">
                <trasladojava></trasladojava>
            </template>

            <template v-if="menu==37">
                <abrasivos></abrasivos>
            </template>

            <template v-if="menu==37">
                <abrasivos></abrasivos>
            </template>

            <template v-if="menu==39">
                <ingresoabrasivo></ingresoabrasivo>
            </template>

            <template v-if="menu==42">
                <ventaabrasivo></ventaabrasivo>
            </template>

            <template v-if="menu==50">
                <cotizacionabrasivo></cotizacionabrasivo>
            </template>

            <template v-if="menu==52">
                <trasladoabrasivo></trasladoabrasivo>
            </template>

            <template v-if="menu==48">
                <bloques></bloques>
            </template>

            <template v-if="menu==55">
                <ingresobloque></ingresobloque>
            </template>

            <template v-if="menu==53">
                <facturacionjava></facturacionjava>
            </template>

            <template v-if="menu==54">
                <facturacionabrasivo></facturacionabrasivo>
            </template>

            <template v-if="menu==56">
                <plantilla></plantilla>
            </template>

        @elseif(Auth::user()->idrol == 3)
            <template v-if="menu==0">
                <dashboard></dashboard>
            </template>

            <template v-if="menu==1">
                <categoria></categoria>
            </template>

            <template v-if="menu==2">
                <articulo></articulo>
            </template>

            <template v-if="menu==3">
                <ingreso></ingreso>
            </template>

            <template v-if="menu==4">
                <proveedor></proveedor>
            </template>

            <template v-if="menu==9">
                <consultaingreso></consultaingreso>
            </template>

            <template v-if="menu==14">
                <entrega></entrega>
            </template>

            <template v-if="menu==16">
                <calendario></calendario>
            </template>

            <template v-if="menu==17">
                <traslado></traslado>
            </template>

            <template v-if="menu==11">
                <ayuda></ayuda>
            </template>

            <template v-if="menu==12">
                <acerca></acerca>
            </template>

            <template v-if="menu==20">
                <actividad></actividad>
            </template>
            <template v-if="menu==21">
                <recadero></recadero>
            </template>

            <template v-if="menu==23">
                <galeria></galeria>
            </template>

            <template v-if="menu=40">
                <cotizacionp></cotizacionp>
            </template>

        @elseif(Auth::user()->idrol == 4)

            <template v-if="menu==0">
                <dashboard></dashboard>
            </template>

            <template v-if="menu==2">
                <articulo></articulo>
            </template>

            <template v-if="menu==22">
                <project></project>
            </template>

            <template v-if="menu==16">
                <calendario></calendario>
            </template>

            <template v-if="menu==20">
                <actividad></actividad>
            </template>







        @else

        @endif
    @endif

@endsection

