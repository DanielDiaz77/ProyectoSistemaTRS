<?php

namespace App\Http\Controllers;

use App\CotizacionJava;
use App\DetalleCotizacionJava;
use App\Java;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Persona;
use Barryvdh\DomPDF\PDF;
use App\Mail\MailCotizacion;
use Exception;
use Illuminate\Support\Facades\Mail;

class CotizacionJavaController extends Controller
{
    public function index(Request $request){
        //if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $estadoC = $request->estado;

        $usrol = \Auth::user()->idrol;
        $usid = \Auth::user()->id;

        if($usrol == 4){
            if($estadoC == "Anulada"){
                if ($buscar==''){
                    $cotizaciones = CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
                    ->join('users','cotizacion_javas.idusuario','=','users.id')
                    ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
                    'cotizacion_javas.fecha_hora','cotizacion_javas.vigencia','cotizacion_javas.impuesto','cotizacion_javas.total',
                    'cotizacion_javas.estado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio','cotizacion_javas.observacion',
                    'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
                    'cotizacion_javas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where([['cotizacion_javas.estado','Anulada'],['cotizacion_javas.idusuario',$usid]])
                    ->orderBy('cotizacion_javas.id', 'desc')->paginate(12);
                }
                else{
                    if($criterio == 'cliente'){
                        $cotizaciones = CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
                        ->join('users','cotizacion_javas.idusuario','=','users.id')
                        ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
                        'cotizacion_javas.fecha_hora','cotizacion_javas.vigencia','cotizacion_javas.impuesto','cotizacion_javas.total',
                        'cotizacion_javas.estado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio','cotizacion_javas.observacion',
                        'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
                        'cotizacion_javas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['personas.nombre', 'like', '%'. $buscar . '%'],
                            ['cotizacion_javas.estado','Anulada'],['cotizacion_javas.idusuario',$usid]
                        ])
                        ->orderBy('cotizacion_javas.id', 'desc')->paginate(12);
                    }else{
                        $cotizaciones = CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
                        ->join('users','cotizacion_javas.idusuario','=','users.id')
                        ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
                        'cotizacion_javas.fecha_hora','cotizacion_javas.vigencia','cotizacion_javas.impuesto','cotizacion_javas.total',
                        'cotizacion_javas.estado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio','cotizacion_javas.observacion',
                        'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
                        'cotizacion_javas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['cotizacion_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['cotizacion_javas.estado','Anulada'],['cotizacion_javas.idusuario',$usid]
                        ])
                        ->orderBy('cotizacion_javas.id', 'desc')->paginate(12);
                    }
                }
            }elseif($estadoC == "Vendida"){
                if ($buscar==''){
                    $cotizaciones = CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
                    ->join('users','cotizacion_javas.idusuario','=','users.id')
                    ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
                    'cotizacion_javas.fecha_hora','cotizacion_javas.vigencia','cotizacion_javas.impuesto','cotizacion_javas.total',
                    'cotizacion_javas.estado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio','cotizacion_javas.observacion',
                    'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
                    'cotizacion_javas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where([['cotizacion_javas.estado','Vendida'],['cotizacion_javas.idusuario',$usid]])
                    ->orderBy('cotizacion_javas.id', 'desc')->paginate(12);
                }
                else{
                    if($criterio == 'cliente'){
                        $cotizaciones = CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
                        ->join('users','cotizacion_javas.idusuario','=','users.id')
                        ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
                        'cotizacion_javas.fecha_hora','cotizacion_javas.vigencia','cotizacion_javas.impuesto','cotizacion_javas.total',
                        'cotizacion_javas.estado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio','cotizacion_javas.observacion',
                        'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
                        'cotizacion_javas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['personas.nombre', 'like', '%'. $buscar . '%'],
                            ['cotizacion_javas.estado','Vendida'],['cotizacion_javas.idusuario',$usid]
                        ])
                        ->orderBy('cotizacion_javas.id', 'desc')->paginate(12);
                    }else{
                        $cotizaciones = CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
                        ->join('users','cotizacion_javas.idusuario','=','users.id')
                        ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
                        'cotizacion_javas.fecha_hora','cotizacion_javas.vigencia','cotizacion_javas.impuesto','cotizacion_javas.total',
                        'cotizacion_javas.estado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio','cotizacion_javas.observacion',
                        'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
                        'cotizacion_javas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['cotizacion_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['cotizacion_javas.estado','Vendida'],['cotizacion_javas.idusuario',$usid]
                        ])
                        ->orderBy('cotizacion_javas.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if ($buscar==''){
                    $cotizaciones = CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
                    ->join('users','cotizacion_javas.idusuario','=','users.id')
                    ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
                    'cotizacion_javas.fecha_hora','cotizacion_javas.vigencia','cotizacion_javas.impuesto','cotizacion_javas.total',
                    'cotizacion_javas.estado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio','cotizacion_javas.observacion',
                    'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
                    'cotizacion_javas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where([['cotizacion_javas.estado','Registrado'],['cotizacion_javas.idusuario',$usid]])
                    ->orderBy('cotizacion_javas.id', 'desc')->paginate(12);
                }
                else{
                    if($criterio == 'cliente'){
                        $cotizaciones = CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
                        ->join('users','cotizacion_javas.idusuario','=','users.id')
                        ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
                        'cotizacion_javas.fecha_hora','cotizacion_javas.vigencia','cotizacion_javas.impuesto','cotizacion_javas.total',
                        'cotizacion_javas.estado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio','cotizacion_javas.observacion',
                        'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
                        'cotizacion_javas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['personas.nombre', 'like', '%'. $buscar . '%'],
                            ['cotizacion_javas.estado','Registrado'],['cotizacion_javas.idusuario',$usid]
                        ])
                        ->orderBy('cotizacion_javas.id', 'desc')->paginate(12);
                    }else{
                        $cotizaciones = CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
                        ->join('users','cotizacion_javas.idusuario','=','users.id')
                        ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
                        'cotizacion_javas.fecha_hora','cotizacion_javas.vigencia','cotizacion_javas.impuesto','cotizacion_javas.total',
                        'cotizacion_javas.estado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio','cotizacion_javas.observacion',
                        'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
                        'cotizacion_javas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['cotizacion_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['cotizacion_javas.estado','Registrado'],['cotizacion_javas.idusuario',$usid]
                        ])
                        ->orderBy('cotizacion_javas.id', 'desc')->paginate(12);
                    }
                }
            }
        }else{
            if($estadoC == "Anulada"){
                if ($buscar==''){
                    $cotizaciones = CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
                    ->join('users','cotizacion_javas.idusuario','=','users.id')
                    ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
                    'cotizacion_javas.fecha_hora','cotizacion_javas.vigencia','cotizacion_javas.impuesto','cotizacion_javas.total',
                    'cotizacion_javas.estado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio','cotizacion_javas.observacion',
                    'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
                    'cotizacion_javas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where('cotizacion_javas.estado','Anulada')
                    ->orderBy('cotizacion_javas.id', 'desc')->paginate(12);
                }
                else{
                    if($criterio == 'cliente'){
                        $cotizaciones = CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
                        ->join('users','cotizacion_javas.idusuario','=','users.id')
                        ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
                        'cotizacion_javas.fecha_hora','cotizacion_javas.vigencia','cotizacion_javas.impuesto','cotizacion_javas.total',
                        'cotizacion_javas.estado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio','cotizacion_javas.observacion',
                        'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
                        'cotizacion_javas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['personas.nombre', 'like', '%'. $buscar . '%'],
                            ['cotizacion_javas.estado','Anulada']
                        ])
                        ->orderBy('cotizacion_javas.id', 'desc')->paginate(12);
                    }else{
                        $cotizaciones = CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
                        ->join('users','cotizacion_javas.idusuario','=','users.id')
                        ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
                        'cotizacion_javas.fecha_hora','cotizacion_javas.vigencia','cotizacion_javas.impuesto','cotizacion_javas.total',
                        'cotizacion_javas.estado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio','cotizacion_javas.observacion',
                        'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
                        'cotizacion_javas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['cotizacion_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['cotizacion_javas.estado','Anulada']
                        ])
                        ->orderBy('cotizacion_javas.id', 'desc')->paginate(12);
                    }
                }
            }elseif($estadoC == "Vendida"){
                if ($buscar==''){
                    $cotizaciones = CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
                    ->join('users','cotizacion_javas.idusuario','=','users.id')
                    ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
                    'cotizacion_javas.fecha_hora','cotizacion_javas.vigencia','cotizacion_javas.impuesto','cotizacion_javas.total',
                    'cotizacion_javas.estado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio','cotizacion_javas.observacion',
                    'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
                    'cotizacion_javas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where('cotizacion_javas.estado','Vendida')
                    ->orderBy('cotizacion_javas.id', 'desc')->paginate(12);
                }
                else{
                    if($criterio == 'cliente'){
                        $cotizaciones = CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
                        ->join('users','cotizacion_javas.idusuario','=','users.id')
                        ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
                        'cotizacion_javas.fecha_hora','cotizacion_javas.vigencia','cotizacion_javas.impuesto','cotizacion_javas.total',
                        'cotizacion_javas.estado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio','cotizacion_javas.observacion',
                        'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
                        'cotizacion_javas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['personas.nombre', 'like', '%'. $buscar . '%'],
                            ['cotizacion_javas.estado','Vendida']
                        ])
                        ->orderBy('cotizacion_javas.id', 'desc')->paginate(12);
                    }else{
                        $cotizaciones = CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
                        ->join('users','cotizacion_javas.idusuario','=','users.id')
                        ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
                        'cotizacion_javas.fecha_hora','cotizacion_javas.vigencia','cotizacion_javas.impuesto','cotizacion_javas.total',
                        'cotizacion_javas.estado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio','cotizacion_javas.observacion',
                        'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
                        'cotizacion_javas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['cotizacion_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['cotizacion_javas.estado','Vendida']
                        ])
                        ->orderBy('cotizacion_javas.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if ($buscar==''){
                    $cotizaciones = CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
                    ->join('users','cotizacion_javas.idusuario','=','users.id')
                    ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
                    'cotizacion_javas.fecha_hora','cotizacion_javas.vigencia','cotizacion_javas.impuesto','cotizacion_javas.total',
                    'cotizacion_javas.estado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio','cotizacion_javas.observacion',
                    'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
                    'cotizacion_javas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where('cotizacion_javas.estado','Registrado')
                    ->orderBy('cotizacion_javas.id', 'desc')->paginate(12);
                }
                else{
                    if($criterio == 'cliente'){
                        $cotizaciones = CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
                        ->join('users','cotizacion_javas.idusuario','=','users.id')
                        ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
                        'cotizacion_javas.fecha_hora','cotizacion_javas.vigencia','cotizacion_javas.impuesto','cotizacion_javas.total',
                        'cotizacion_javas.estado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio','cotizacion_javas.observacion',
                        'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
                        'cotizacion_javas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['personas.nombre', 'like', '%'. $buscar . '%'],
                            ['cotizacion_javas.estado','Registrado']
                        ])
                        ->orderBy('cotizacion_javas.id', 'desc')->paginate(12);
                    }else{
                        $cotizaciones = CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
                        ->join('users','cotizacion_javas.idusuario','=','users.id')
                        ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
                        'cotizacion_javas.fecha_hora','cotizacion_javas.vigencia','cotizacion_javas.impuesto','cotizacion_javas.total',
                        'cotizacion_javas.estado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio','cotizacion_javas.observacion',
                        'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
                        'cotizacion_javas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['cotizacion_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['cotizacion_javas.estado','Registrado']
                        ])
                        ->orderBy('cotizacion_javas.id', 'desc')->paginate(12);
                    }
                }
            }
        }

        return [
            'pagination' => [
                'total'        => $cotizaciones->total(),
                'current_page' => $cotizaciones->currentPage(),
                'per_page'     => $cotizaciones->perPage(),
                'last_page'    => $cotizaciones->lastPage(),
                'from'         => $cotizaciones->firstItem(),
                'to'           => $cotizaciones->lastItem(),
            ],
            'cotizaciones' => $cotizaciones
        ];
    }
    public function store(Request $request){
        if(!$request->ajax()) return redirect('/');

        $mytime = Carbon::now('America/Mexico_City');

        try{
            DB::beginTransaction();

            $cotizacion = new CotizacionJava();
            $cotizacion->idcliente = $request->idcliente;
            $cotizacion->idusuario = \Auth::user()->id;
            $cotizacion->tipo_comprobante = $request->tipo_comprobante;
            $cotizacion->num_comprobante = $request->num_comprobante;
            $cotizacion->fecha_hora = $mytime;
            $cotizacion->vigencia = $request->vigencia;
            $cotizacion->impuesto = $request->impuesto;
            $cotizacion->total = $request->total;
            $cotizacion->forma_pago = $request->forma_pago;
            $cotizacion->tiempo_entrega = $request->tiempo_entrega;
            $cotizacion->lugar_entrega = $request->lugar_entrega;
            $cotizacion->aceptado = 0;
            $cotizacion->estado = 'Registrado';
            $cotizacion->moneda = $request->moneda;
            $cotizacion->tipo_cambio = $request->tipo_cambio;
            $cotizacion->observacion = $request->observacion;
            $cotizacion->save();

            $detalles = $request->data;//Array de detalles

            //Recorro todos los elementos
            foreach($detalles as $ep=>$det)
            {

                $javas = Java::where('codigo','=',$det['codigo'])->select('id')->first();
                $detalle = new DetalleCotizacionJava();
                $detalle->idcotizacionjava = $cotizacion->id;
                $detalle->idjava = $javas->id;
                $detalle->cantidad = $det['cantidad'];
                $detalle->precio = $det['precio'];
                $detalle->descuento = $det['descuento'];
                $detalle->save();
            }

            DB::commit();

        }catch(Exception $e){

            DB::rollBack();

        }
    }
    public function desactivar(Request $request){
        if (!$request->ajax()) return redirect('/');
        try{

            DB::beginTransaction();

            $cotizacion = CotizacionJava::findOrFail($request->id);
            $cotizacion->estado = 'Anulada';
            $cotizacion->aceptado = 0;
            $cotizacion->save();

            $detalles = DetalleCotizacionJava::select('idjava')
                    ->where('idcotizacionjava',$request->id)->get();

            foreach($detalles as $ep=>$det){
                $articulo = Java::findOrFail($det['idjava']);
                $articulo->comprometido = 0;
                $articulo->save();
            }

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function desactivarVenta(Request $request){
        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();
            $cotizacion = CotizacionJava::findOrFail($request->id);
            $cotizacion->estado = 'Vendida';
            $cotizacion->aceptado = 1;
            $cotizacion->save();

            $detalles = DetalleCotizacionJava::select('idjava')
                    ->where('idcotizacionjava',$request->id)->get();

            foreach($detalles as $ep=>$det){
                $articulo = Java::findOrFail($det['idjava']);
                $articulo->comprometido = 0;
                $articulo->save();
            }

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function obtenerCabecera(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $cotizacion = CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
        ->join('users','cotizacion_javas.idusuario','=','users.id')
        ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
        'cotizacion_javas.fecha_hora','cotizacion_javas.impuesto','cotizacion_javas.total','cotizacion_javas.estado',
        'cotizacion_javas.moneda','cotizacion_javas.tipo_cambio','cotizacion_javas.observacion','cotizacion_javas.forma_pago',
        'cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega','cotizacion_javas.aceptado','cotizacion_javas.vigencia',
        'personas.id as idcliente','personas.nombre','personas.tipo','personas.rfc','personas.company','personas.tel_company'
        ,'users.usuario','personas.email as EmailC')
        ->where('cotizacion_javas.id','=',$id)
        ->orderBy('cotizacion_javas.id', 'desc')->take(1)->get();

        return ['cotizacion' => $cotizacion];
    }
    public function obtenerDetalles(Request $request){

        if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $detalles = DetalleCotizacionJava::join('javas','detalle_cotizacion_javas.idjava','=','javas.id')
        ->leftJoin('categorias','javas.idcategoria','=','categorias.id')
        ->select('detalle_cotizacion_javas.cantidad','detalle_cotizacion_javas.precio','detalle_cotizacion_javas.descuento',
            'javas.sku','javas.codigo','javas.espesor','javas.largo','javas.alto',
            'javas.metros_cuadrados','javas.descripcion','javas.idcategoria','javas.terminado',
            'javas.ubicacion','javas.file','javas.origen','javas.contenedor','javas.fecha_llegada',
            'javas.observacion','javas.condicion','javas.stock','javas.id as idjava',
            'categorias.nombre as categoria')
        ->where('detalle_cotizacion_javas.idcotizacionjava',$id)
        ->orderBy('detalle_cotizacion_javas.id','desc')->get();

        return ['detalles' => $detalles];
    }
    public function pdf(Request $request,$id){

        $cotizacion =  CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
        ->join('users','cotizacion_javas.idusuario','=','users.id')
        ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
            'cotizacion_javas.created_at','cotizacion_javas.impuesto','cotizacion_javas.total','cotizacion_javas.estado',
            'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
            'cotizacion_javas.aceptado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio', 'cotizacion_javas.observacion',
            'cotizacion_javas.vigencia','personas.nombre','personas.rfc','personas.domicilio','personas.ciudad',
            'personas.telefono','personas.email','personas.company as contacto','personas.tel_company','users.usuario')
        ->where('cotizacion_javas.id',$id)->take(1)->get();

        $detalles = DetalleCotizacionJava::join('javas','detalle_cotizacion_javas.idjava','=','javas.id')
            ->select('detalle_cotizacion_javas.cantidad','detalle_cotizacion_javas.precio','detalle_cotizacion_javas.descuento',
                'javas.sku as articulo','javas.largo','javas.alto','javas.metros_cuadrados',
                'javas.terminado','javas.codigo','javas.ubicacion')
            ->where('detalle_cotizacion_javas.idcotizacionjavas',$id)
            ->orderBy('detalle_cotizacion_javas.id','desc')->get();

        $numcotizacion = CotizacionJava::select('num_comprobante')->where('id',$id)->get();

        $ivaagregado = CotizacionJava::select('impuesto')->where('id',$id)->get();

        $sumaMts = DB::table('javas')
        ->select(DB::raw('SUM(metros_cuadrados) as metros'))
        ->leftJoin('detalle_cotizacion_javas','detalle_cotizacion_javas.idjava','javas.id')
        ->where('detalle_cotizacion_javas.idcotizacionjava',$id)
        ->get();

        $pdf = \PDF::loadView('pdf.cotizacion',
            ['cotizacion' => $cotizacion,'detalles'=>$detalles,
            'ivaCotizacion' =>$ivaagregado[0]->impuesto,
            'sumaMts' => $sumaMts[0]->metros]);


        return $pdf->stream('cotizacion-'.$numcotizacion[0]->num_comprobante.'.pdf');
    }
    public function aceptarCotizacion(Request $request){
        if (!$request->ajax()) return redirect('/');
        $cotizacion = CotizacionJava::findOrFail($request->id);
        $cotizacion->aceptado = $request->aceptado;
        $cotizacion->save();
    }
    public function getLastNum(){

        $lastNum = CotizacionJava::select('num_comprobante')->get()->last();
        if($lastNum != null){
            $noComp = explode('"',$lastNum);
            $SigNum = explode("-",$noComp[3]);
            return  $SigNum[2] + 1;
        }else{
            return 1;
        }
    }
    public function actualizarObservacion(Request $request){
        if (!$request->ajax()) return redirect('/');
        $cotizacion = CotizacionJava::findOrFail($request->id);
        $cotizacion->observacion = $request->observacion;
        $cotizacion->save();
    }
    public function enviarCotizacionMail(Request $request){

        $cotizacion =  CotizacionJava::join('personas','cotizacion_javas.idcliente','=','personas.id')
        ->join('users','cotizacion_javas.idusuario','=','users.id')
        ->select('cotizacion_javas.id','cotizacion_javas.tipo_comprobante','cotizacion_javas.num_comprobante',
            'cotizacion_javas.created_at','cotizacion_javas.impuesto','cotizacion_javas.total','cotizacion_javas.estado',
            'cotizacion_javas.forma_pago','cotizacion_javas.tiempo_entrega','cotizacion_javas.lugar_entrega',
            'cotizacion_javas.aceptado','cotizacion_javas.moneda','cotizacion_javas.tipo_cambio', 'cotizacion_javas.observacion',
            'cotizacion_javas.vigencia','personas.nombre','personas.rfc','personas.domicilio','personas.ciudad',
            'personas.telefono','personas.email','personas.company as contacto','personas.tel_company','users.usuario')
        ->where('cotizacion_javas.id',$request->id)->take(1)->get();

        $detalles = DetalleCotizacionJava::join('javas','detalle_cotizacion_javas.idjava','=','javas.id')
        ->select('detalle_cotizacion_javas.cantidad','detalle_cotizacion_javas.precio','detalle_cotizacion_javas.descuento',
            'javas.sku as articulo','javas.largo','javas.alto','javas.metros_cuadrados',
            'javas.terminado','javas.codigo','javas.ubicacion')
        ->where('detalle_cotizacion_javas.idcotizacionjava',$request->id)
        ->orderBy('detalle_cotizacion_javas.id','desc')->get();

        $numcotizacion = CotizacionJava::select('num_comprobante')->where('id',$request->id)->get();

        $ivaagregado = CotizacionJava::select('impuesto')->where('id',$request->id)->get();

        $sumaMts = DB::table('javas')
        ->select(DB::raw('SUM(metros_cuadrados) as metros'))
        ->leftJoin('detalle_cotizacion_javas','detalle_cotizacion_javas.idarticulojava','javas.id')
        ->where('detalle_cotizacion_javas.idcotizacionjava',$request->id)
        ->get();

        $pdf = \PDF::loadView('pdf.cotizacion',
            ['cotizacion' => $cotizacion,'detalles'=>$detalles,
            'ivaCotizacion' =>$ivaagregado[0]->impuesto,
            'sumaMts' => $sumaMts[0]->metros]);

        $data = array(
            'name'      =>  $request->name
        );

        $email = $request->mail;

        $numCot = $numcotizacion[0]->num_comprobante;

        $usid = \Auth::user()->id;

        $mailUs = Persona::select('email')->where('id',$usid)->get();

        $emit = $mailUs[0]->email;

        Mail::to($email)->send(new MailCotizacion($pdf->output(),$data,$numCot,$emit));
    }
}
