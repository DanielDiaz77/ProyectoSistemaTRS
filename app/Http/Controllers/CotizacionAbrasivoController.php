<?php

namespace App\Http\Controllers;

use App\Abrasivo;
use App\CotizacionAbrasivo;
use App\DetalleCotizacionAbrasivo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\PDF;
use Exception;

class CotizacionAbrasivoController extends Controller
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
                    $cotizaciones = CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
                    ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
                    ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
                    'cotizacion_abrasivos.fecha_hora','cotizacion_abrasivos.vigencia','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total',
                    'cotizacion_abrasivos.estado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio','cotizacion_abrasivos.observacion',
                    'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
                    'cotizacion_abrasivos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where([['cotizacion_abrasivos.estado','Anulada'],['cotizacion_abrasivos.idusuario',$usid]])
                    ->orderBy('cotizacion_abrasivos.id', 'desc')->paginate(12);
                }
                else{
                    if($criterio == 'cliente'){
                        $cotizaciones = CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
                        ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
                        ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
                        'cotizacion_abrasivos.fecha_hora','cotizacion_abrasivos.vigencia','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total',
                        'cotizacion_abrasivos.estado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio','cotizacion_abrasivos.observacion',
                        'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
                        'cotizacion_abrasivos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['personas.nombre', 'like', '%'. $buscar . '%'],
                            ['cotizacion_abrasivos.estado','Anulada'],['cotizacion_abrasivos.idusuario',$usid]
                        ])
                        ->orderBy('cotizacion_abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $cotizaciones = CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
                        ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
                        ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
                        'cotizacion_abrasivos.fecha_hora','cotizacion_abrasivos.vigencia','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total',
                        'cotizacion_abrasivos.estado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio','cotizacion_abrasivos.observacion',
                        'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
                        'cotizacion_abrasivos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['cotizacion_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['cotizacion_abrasivos.estado','Anulada'],['cotizacion_abrasivos.idusuario',$usid]
                        ])
                        ->orderBy('cotizacion_abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }elseif($estadoC == "Vendida"){
                if ($buscar==''){
                    $cotizaciones = CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
                    ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
                    ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
                    'cotizacion_abrasivos.fecha_hora','cotizacion_abrasivos.vigencia','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total',
                    'cotizacion_abrasivos.estado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio','cotizacion_abrasivos.observacion',
                    'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
                    'cotizacion_abrasivos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where([['cotizacion_abrasivos.estado','Vendida'],['cotizacion_abrasivos.idusuario',$usid]])
                    ->orderBy('cotizacion_abrasivos.id', 'desc')->paginate(12);
                }
                else{
                    if($criterio == 'cliente'){
                        $cotizaciones = CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
                        ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
                        ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
                        'cotizacion_abrasivos.fecha_hora','cotizacion_abrasivos.vigencia','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total',
                        'cotizacion_abrasivos.estado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio','cotizacion_abrasivos.observacion',
                        'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
                        'cotizacion_abrasivos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['personas.nombre', 'like', '%'. $buscar . '%'],
                            ['cotizacion_abrasivos.estado','Vendida'],['cotizacion_abrasivos.idusuario',$usid]
                        ])
                        ->orderBy('cotizacion_abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $cotizaciones = CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
                        ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
                        ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
                        'cotizacion_abrasivos.fecha_hora','cotizacion_abrasivos.vigencia','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total',
                        'cotizacion_abrasivos.estado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio','cotizacion_abrasivos.observacion',
                        'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
                        'cotizacion_abrasivos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['cotizacion_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['cotizacion_abrasivos.estado','Vendida'],['cotizacion_abrasivos.idusuario',$usid]
                        ])
                        ->orderBy('cotizacion_abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if ($buscar==''){
                    $cotizaciones = CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
                    ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
                    ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
                    'cotizacion_abrasivos.fecha_hora','cotizacion_abrasivos.vigencia','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total',
                    'cotizacion_abrasivos.estado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio','cotizacion_abrasivos.observacion',
                    'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
                    'cotizacion_abrasivos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where([['cotizacion_abrasivos.estado','Registrado'],['cotizacion_abrasivos.idusuario',$usid]])
                    ->orderBy('cotizacion_abrasivos.id', 'desc')->paginate(12);
                }
                else{
                    if($criterio == 'cliente'){
                        $cotizaciones = CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
                        ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
                        ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
                        'cotizacion_abrasivos.fecha_hora','cotizacion_abrasivos.vigencia','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total',
                        'cotizacion_abrasivos.estado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio','cotizacion_abrasivos.observacion',
                        'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
                        'cotizacion_abrasivos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['personas.nombre', 'like', '%'. $buscar . '%'],
                            ['cotizacion_abrasivos.estado','Registrado'],['cotizacion_abrasivos.idusuario',$usid]
                        ])
                        ->orderBy('cotizacion_abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $cotizaciones = CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
                        ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
                        ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
                        'cotizacion_abrasivos.fecha_hora','cotizacion_abrasivos.vigencia','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total',
                        'cotizacion_abrasivos.estado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio','cotizacion_abrasivos.observacion',
                        'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
                        'cotizacion_abrasivos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['cotizacion_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['cotizacion_abrasivos.estado','Registrado'],['cotizacion_abrasivos.idusuario',$usid]
                        ])
                        ->orderBy('cotizacion_abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }
        }else{
            if($estadoC == "Anulada"){
                if ($buscar==''){
                    $cotizaciones = CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
                    ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
                    ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
                    'cotizacion_abrasivos.fecha_hora','cotizacion_abrasivos.vigencia','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total',
                    'cotizacion_abrasivos.estado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio','cotizacion_abrasivos.observacion',
                    'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
                    'cotizacion_abrasivos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where('cotizacion_abrasivos.estado','Anulada')
                    ->orderBy('cotizacion_abrasivos.id', 'desc')->paginate(12);
                }
                else{
                    if($criterio == 'cliente'){
                        $cotizaciones = CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
                        ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
                        ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
                        'cotizacion_abrasivos.fecha_hora','cotizacion_abrasivos.vigencia','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total',
                        'cotizacion_abrasivos.estado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio','cotizacion_abrasivos.observacion',
                        'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
                        'cotizacion_abrasivos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['personas.nombre', 'like', '%'. $buscar . '%'],
                            ['cotizacion_abrasivos.estado','Anulada']
                        ])
                        ->orderBy('cotizacion_abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $cotizaciones = CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
                        ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
                        ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
                        'cotizacion_abrasivos.fecha_hora','cotizacion_abrasivos.vigencia','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total',
                        'cotizacion_abrasivos.estado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio','cotizacion_abrasivos.observacion',
                        'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
                        'cotizacion_abrasivos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['cotizacion_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['cotizacion_abrasivos.estado','Anulada']
                        ])
                        ->orderBy('cotizacion_abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }elseif($estadoC == "Vendida"){
                if ($buscar==''){
                    $cotizaciones = CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
                    ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
                    ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
                    'cotizacion_abrasivos.fecha_hora','cotizacion_abrasivos.vigencia','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total',
                    'cotizacion_abrasivos.estado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio','cotizacion_abrasivos.observacion',
                    'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
                    'cotizacion_abrasivos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where('cotizacion_abrasivos.estado','Vendida')
                    ->orderBy('cotizacion_abrasivos.id', 'desc')->paginate(12);
                }
                else{
                    if($criterio == 'cliente'){
                        $cotizaciones = CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
                        ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
                        ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
                        'cotizacion_abrasivos.fecha_hora','cotizacion_abrasivos.vigencia','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total',
                        'cotizacion_abrasivos.estado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio','cotizacion_abrasivos.observacion',
                        'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
                        'cotizacion_abrasivos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['personas.nombre', 'like', '%'. $buscar . '%'],
                            ['cotizacion_abrasivos.estado','Vendida']
                        ])
                        ->orderBy('cotizacion_abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $cotizaciones = CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
                        ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
                        ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
                        'cotizacion_abrasivos.fecha_hora','cotizacion_abrasivos.vigencia','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total',
                        'cotizacion_abrasivos.estado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio','cotizacion_abrasivos.observacion',
                        'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
                        'cotizacion_abrasivos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['cotizacion_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['cotizacion_abrasivos.estado','Vendida']
                        ])
                        ->orderBy('cotizacion_abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if ($buscar==''){
                    $cotizaciones = CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
                    ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
                    ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
                    'cotizacion_abrasivos.fecha_hora','cotizacion_abrasivos.vigencia','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total',
                    'cotizacion_abrasivos.estado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio','cotizacion_abrasivos.observacion',
                    'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
                    'cotizacion_abrasivos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where('cotizacion_abrasivos.estado','Registrado')
                    ->orderBy('cotizacion_abrasivos.id', 'desc')->paginate(12);
                }
                else{
                    if($criterio == 'cliente'){
                        $cotizaciones = CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
                        ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
                        ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
                        'cotizacion_abrasivos.fecha_hora','cotizacion_abrasivos.vigencia','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total',
                        'cotizacion_abrasivos.estado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio','cotizacion_abrasivos.observacion',
                        'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
                        'cotizacion_abrasivos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['personas.nombre', 'like', '%'. $buscar . '%'],
                            ['cotizacion_abrasivos.estado','Registrado']
                        ])
                        ->orderBy('cotizacion_abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $cotizaciones = CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
                        ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
                        ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
                        'cotizacion_abrasivos.fecha_hora','cotizacion_abrasivos.vigencia','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total',
                        'cotizacion_abrasivos.estado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio','cotizacion_abrasivos.observacion',
                        'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
                        'cotizacion_abrasivos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['cotizacion_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['cotizacion_abrasivos.estado','Registrado']
                        ])
                        ->orderBy('cotizacion_abrasivos.id', 'desc')->paginate(12);
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

            $cotizacion = new CotizacionAbrasivo();
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
                $articulos = Abrasivo::where('codigo','=',$det['codigo'])->select('id')->first();
                $detalle = new DetalleCotizacionAbrasivo();
                $detalle->idcotizacionabrasivo = $cotizacion->id;
                $detalle->idabrasivo = $articulos->id;
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

            $cotizacion = CotizacionAbrasivo::findOrFail($request->id);
            $cotizacion->estado = 'Anulada';
            $cotizacion->aceptado = 0;
            $cotizacion->save();

            $detalles = DetalleCotizacionAbrasivo::select('idabrasivo')
                    ->where('idcotizacionabrasivo',$request->id)->get();

            foreach($detalles as $ep=>$det){
                $articulo = Abrasivo::findOrFail($det['idabrasivo']);
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
            $cotizacion = CotizacionAbrasivo::findOrFail($request->id);
            $cotizacion->estado = 'Vendida';
            $cotizacion->aceptado = 1;
            $cotizacion->save();

            $detalles = DetalleCotizacionAbrasivo::select('idabrasivo')
                    ->where('idcotizacionabrasivo',$request->id)->get();

            foreach($detalles as $ep=>$det){
                $articulo = Abrasivo::findOrFail($det['idabrasivo']);
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
        $cotizacion = CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
        ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
        ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
        'cotizacion_abrasivos.fecha_hora','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total','cotizacion_abrasivos.estado',
        'cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio','cotizacion_abrasivos.observacion','cotizacion_abrasivos.forma_pago',
        'cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega','cotizacion_abrasivos.aceptado','cotizacion_abrasivos.vigencia',
        'personas.id as idcliente','personas.nombre','personas.tipo','personas.rfc','personas.company','personas.tel_company'
        ,'users.usuario','personas.email as EmailC')
        ->where('cotizacion_abrasivos.id','=',$id)
        ->orderBy('cotizacion_abrasivos.id', 'desc')->take(1)->get();

        return ['cotizacion' => $cotizacion];
    }
    public function obtenerDetalles(Request $request){

        if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $detalles = DetalleCotizacionAbrasivo::join('abrasivos','detalle_cotizacion_abrasivos.idabrasivo','=','abrasivos.id')
        ->select('detalle_cotizacion_abrasivos.cantidad','detalle_cotizacion_abrasivos.precio','detalle_cotizacion_abrasivos.descuento',
            'abrasivos.sku','abrasivos.codigo','abrasivos.descripcion','abrasivos.terminado',
            'abrasivos.ubicacion','abrasivos.file','abrasivos.origen','abrasivos.condicion','abrasivos.stock','abrasivos.id as idarticulo')
        ->where('detalle_cotizacion_abrasivos.idcotizacionabrasivo',$id)
        ->orderBy('detalle_cotizacion_abrasivos.id','desc')->get();

        return ['detalles' => $detalles];
    }
    public function pdf(Request $request,$id){

        $cotizacion =  CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
        ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
        ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
            'cotizacion_abrasivos.created_at','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total','cotizacion_abrasivos.estado',
            'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
            'cotizacion_abrasivos.aceptado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio', 'cotizacion_abrasivos.observacion',
            'cotizacion_abrasivos.vigencia','personas.nombre','personas.rfc','personas.domicilio','personas.ciudad',
            'personas.telefono','personas.email','personas.company as contacto','personas.tel_company','users.usuario')
        ->where('cotizacion_abrasivos.id',$id)->take(1)->get();

        $detalles = DetalleCotizacionAbrasivo::join('abrasivos','detalle_cotizacion_abrasivos.idabrasivo','=','abrasivos.id')
            ->select('detalle_cotizacion_abrasivos.cantidad','detalle_cotizaciones.precio','detalle_cotizaciones.descuento',
                'abrasivos.sku as articulo','abrasivos.codigo','abrasivos.ubicacion')
            ->where('detalle_cotizacion_abrasivos.idcotizacionabrasivo',$id)
            ->orderBy('detalle_cotizacion_abrasivos.id','desc')->get();

        $numcotizacion = CotizacionAbrasivo::select('num_comprobante')->where('id',$id)->get();

        $ivaagregado = CotizacionAbrasivo::select('impuesto')->where('id',$id)->get();

        $pdf = \PDF::loadView('pdf.cotizacion',
            ['cotizacion' => $cotizacion,'detalles'=>$detalles,
            'ivaCotizacion' =>$ivaagregado[0]->impuesto]);


        return $pdf->stream('cotizacion-'.$numcotizacion[0]->num_comprobante.'.pdf');
    }
    public function aceptarCotizacion(Request $request){
        if (!$request->ajax()) return redirect('/');
        $cotizacion = CotizacionAbrasivo::findOrFail($request->id);
        $cotizacion->aceptado = $request->aceptado;
        $cotizacion->save();
    }
    public function getLastNum(){

        $lastNum = CotizacionAbrasivo::select('num_comprobante')->get()->last();
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
        $cotizacion = CotizacionAbrasivo::findOrFail($request->id);
        $cotizacion->observacion = $request->observacion;
        $cotizacion->save();
    }
    public function enviarCotizacionMail(Request $request){

        $cotizacion =  CotizacionAbrasivo::join('personas','cotizacion_abrasivos.idcliente','=','personas.id')
        ->join('users','cotizacion_abrasivos.idusuario','=','users.id')
        ->select('cotizacion_abrasivos.id','cotizacion_abrasivos.tipo_comprobante','cotizacion_abrasivos.num_comprobante',
            'cotizacion_abrasivos.created_at','cotizacion_abrasivos.impuesto','cotizacion_abrasivos.total','cotizacion_abrasivos.estado',
            'cotizacion_abrasivos.forma_pago','cotizacion_abrasivos.tiempo_entrega','cotizacion_abrasivos.lugar_entrega',
            'cotizacion_abrasivos.aceptado','cotizacion_abrasivos.moneda','cotizacion_abrasivos.tipo_cambio', 'cotizacion_abrasivos.observacion',
            'cotizacion_abrasivos.vigencia','personas.nombre','personas.rfc','personas.domicilio','personas.ciudad',
            'personas.telefono','personas.email','personas.company as contacto','personas.tel_company','users.usuario')
        ->where('cotizacion_abrasivos.id',$request->id)->take(1)->get();

        $detalles = DetalleCotizacionAbrasivo::join('abrasivos','detalle_cotizacion_abrasivos.idabrasivo','=','abrasivos.id')
        ->select('detalle_cotizacion_abrasivos.cantidad','detalle_cotizacion_abrasivos.precio','detalle_cotizacion_abrasivos.descuento',
            'abrasivos.sku as articulo','abrasivos.codigo','abrasivos.ubicacion')
        ->where('detalle_cotizacion_abrasivos.idcotizacionabrasivo',$request->id)
        ->orderBy('detalle_cotizacion_abrasivos.id','desc')->get();

        $numcotizacion = CotizacionAbrasivo::select('num_comprobante')->where('id',$request->id)->get();

        $ivaagregado = CotizacionAbrasivo::select('impuesto')->where('id',$request->id)->get();


        $pdf = \PDF::loadView('pdf.cotizacion',
            ['cotizacion' => $cotizacion,'detalles'=>$detalles,
            'ivaCotizacion' =>$ivaagregado[0]->impuesto]);

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
