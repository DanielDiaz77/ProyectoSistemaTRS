<?php

namespace App\Http\Controllers;

use App\Articulo;
use App\CotizacionFormato;
use App\CotizacionProyecto;
use App\DetalleCotizacionProyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;
use App\User;
use App\Persona;

use App\Mail\MailCotizacion;
use Exception;
use Illuminate\Support\Facades\Mail;

class CotizacionProyectoController extends Controller
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
                    $cotizaciones = CotizacionProyecto::join('personas','cotizacion_proyectos.idcliente','=','personas.id')
                    ->join('users','cotizacion_proyectos.idusuario','=','users.id')
                    ->select('cotizacion_proyectos.id','cotizacion_proyectos.tipo_comprobante','cotizacion_proyectos.num_comprobante',
                    'cotizacion_proyectos.fecha_hora','cotizacion_proyectos.vigencia','cotizacion_proyectos.impuesto','cotizacion_proyectos.total',
                    'cotizacion_proyectos.estado','cotizacion_proyectos.moneda','cotizacion_proyectos.tipo_cambio','cotizacion_proyectos.observacion',
                    'cotizacion_proyectos.forma_pago','cotizacion_proyectos.tiempo_entrega','cotizacion_proyectos.lugar_entrega','cotizacion_proyectos.neto',
                    'cotizacion_proyectos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where([['cotizacion_proyectos.estado','Anulada'],['cotizacion_proyectos.idusuario',$usid]])
                    ->orderBy('cotizacion_proyectos.id', 'desc')->paginate(12);
                }
                else{
                    if($criterio == 'cliente'){
                        $cotizaciones = CotizacionProyecto::join('personas','cotizacion_proyectos.idcliente','=','personas.id')
                        ->join('users','cotizacion_proyectos.idusuario','=','users.id')
                        ->select('cotizacion_proyectos.id','cotizacion_proyectos.tipo_comprobante','cotizacion_proyectos.num_comprobante',
                        'cotizacion_proyectos.fecha_hora','cotizacion_proyectos.vigencia','cotizacion_proyectos.impuesto','cotizacion_proyectos.total',
                        'cotizacion_proyectos.estado','cotizacion_proyectos.moneda','cotizacion_proyectos.tipo_cambio','cotizacion_proyectos.observacion',
                        'cotizacion_proyectos.forma_pago','cotizacion_proyectos.tiempo_entrega','cotizacion_proyectos.lugar_entrega','cotizacion_proyectos.neto',
                        'cotizacion_proyectos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['personas.nombre', 'like', '%'. $buscar . '%'],
                            ['cotizacion_proyectos.estado','Anulada'],['cotizacion_proyectos.idusuario',$usid]
                        ])
                        ->orderBy('cotizacion_proyectos.id', 'desc')->paginate(12);
                    }else{
                        $cotizaciones = CotizacionProyecto::join('personas','cotizacion_proyectos.idcliente','=','personas.id')
                        ->join('users','cotizacion_proyectos.idusuario','=','users.id')
                        ->select('cotizacion_proyectos.id','cotizacion_proyectos.tipo_comprobante','cotizacion_proyectos.num_comprobante',
                        'cotizacion_proyectos.fecha_hora','cotizacion_proyectos.vigencia','cotizacion_proyectos.impuesto','cotizacion_proyectos.total',
                        'cotizacion_proyectos.estado','cotizacion_proyectos.moneda','cotizacion_proyectos.tipo_cambio','cotizacion_proyectos.observacion',
                        'cotizacion_proyectos.forma_pago','cotizacion_proyectos.tiempo_entrega','cotizacion_proyectos.lugar_entrega','cotizacion_proyectos.neto',
                        'cotizacion_proyectos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['cotizacion_proyectos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['cotizacion_proyectos.estado','Anulada'],['cotizacion_proyectos.idusuario',$usid]
                        ])
                        ->orderBy('cotizacion_proyectos.id', 'desc')->paginate(12);
                    }
                }
            }elseif($estadoC == "Vendida"){
                if ($buscar==''){
                    $cotizaciones = CotizacionProyecto::join('personas','cotizacion_proyectos.idcliente','=','personas.id')
                    ->join('users','cotizacion_proyectos.idusuario','=','users.id')
                    ->select('cotizacion_proyectos.id','cotizacion_proyectos.tipo_comprobante','cotizacion_proyectos.num_comprobante',
                    'cotizacion_proyectos.fecha_hora','cotizacion_proyectos.vigencia','cotizacion_proyectos.impuesto','cotizacion_proyectos.total',
                    'cotizacion_proyectos.estado','cotizacion_proyectos.moneda','cotizacion_proyectos.tipo_cambio','cotizacion_proyectos.observacion',
                    'cotizacion_proyectos.forma_pago','cotizacion_proyectos.tiempo_entrega','cotizacion_proyectos.lugar_entrega','cotizacion_proyectos.neto',
                    'cotizacion_proyectos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where([['cotizacion_proyectos.estado','Vendida'],['cotizacion_proyectos.idusuario',$usid]])
                    ->orderBy('cotizacion_proyectos.id', 'desc')->paginate(12);
                }
                else{
                    if($criterio == 'cliente'){
                        $cotizaciones = CotizacionProyecto::join('personas','cotizacion_proyectos.idcliente','=','personas.id')
                        ->join('users','cotizacion_proyectos.idusuario','=','users.id')
                        ->select('cotizacion_proyectos.id','cotizacion_proyectos.tipo_comprobante','cotizacion_proyectos.num_comprobante',
                        'cotizacion_proyectos.fecha_hora','cotizacion_proyectos.vigencia','cotizacion_proyectos.impuesto','cotizacion_proyectos.total',
                        'cotizacion_proyectos.estado','cotizacion_proyectos.moneda','cotizacion_proyectos.tipo_cambio','cotizacion_proyectos.observacion',
                        'cotizacion_proyectos.forma_pago','cotizacion_proyectos.tiempo_entrega','cotizacion_proyectos.lugar_entrega','cotizacion_proyectos.neto',
                        'cotizacion_proyectos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['personas.nombre', 'like', '%'. $buscar . '%'],
                            ['cotizacion_proyectos.estado','Vendida'],['cotizacion_proyectos.idusuario',$usid]
                        ])
                        ->orderBy('cotizacion_proyectos.id', 'desc')->paginate(12);
                    }else{
                        $cotizaciones = CotizacionProyecto::join('personas','cotizacion_proyectos.idcliente','=','personas.id')
                        ->join('users','cotizacion_proyectos.idusuario','=','users.id')
                        ->select('cotizacion_proyectos.id','cotizacion_proyectos.tipo_comprobante','cotizacion_proyectos.num_comprobante',
                        'cotizacion_proyectos.fecha_hora','cotizacion_proyectos.vigencia','cotizacion_proyectos.impuesto','cotizacion_proyectos.total',
                        'cotizacion_proyectos.estado','cotizacion_proyectos.moneda','cotizacion_proyectos.tipo_cambio','cotizacion_proyectos.observacion',
                        'cotizacion_proyectos.forma_pago','cotizacion_proyectos.tiempo_entrega','cotizacion_proyectos.lugar_entrega','cotizacion_proyectos.neto',
                        'cotizacion_proyectos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['cotizacion_proyectos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['cotizacion_proyectos.estado','Vendida'],['cotizacion_proyectos.idusuario',$usid]
                        ])
                        ->orderBy('cotizacion_proyectos.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if ($buscar==''){
                    $cotizaciones = CotizacionProyecto::join('personas','cotizacion_proyectos.idcliente','=','personas.id')
                    ->join('users','cotizacion_proyectos.idusuario','=','users.id')
                    ->select('cotizacion_proyectos.id','cotizacion_proyectos.tipo_comprobante','cotizacion_proyectos.num_comprobante',
                    'cotizacion_proyectos.fecha_hora','cotizacion_proyectos.vigencia','cotizacion_proyectos.impuesto','cotizacion_proyectos.total',
                    'cotizacion_proyectos.estado','cotizacion_proyectos.moneda','cotizacion_proyectos.tipo_cambio','cotizacion_proyectos.observacion',
                    'cotizacion_proyectos.forma_pago','cotizacion_proyectos.tiempo_entrega','cotizacion_proyectos.lugar_entrega','cotizacion_proyectos.neto',
                    'cotizacion_proyectos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where([['cotizacion_proyectos.estado','Registrado'],['cotizacion_proyectos.idusuario',$usid]])
                    ->orderBy('cotizacion_proyectos.id', 'desc')->paginate(12);
                }
                else{
                    if($criterio == 'cliente'){
                        $cotizaciones = CotizacionProyecto::join('personas','cotizacion_proyectos.idcliente','=','personas.id')
                        ->join('users','cotizacion_proyectos.idusuario','=','users.id')
                        ->select('cotizacion_proyectos.id','cotizacion_proyectos.tipo_comprobante','cotizacion_proyectos.num_comprobante',
                        'cotizacion_proyectos.fecha_hora','cotizacion_proyectos.vigencia','cotizacion_proyectos.impuesto','cotizacion_proyectos.total',
                        'cotizacion_proyectos.estado','cotizacion_proyectos.moneda','cotizacion_proyectos.tipo_cambio','cotizacion_proyectos.observacion',
                        'cotizacion_proyectos.forma_pago','cotizacion_proyectos.tiempo_entrega','cotizacion_proyectos.lugar_entrega','cotizacion_proyectos.neto',
                        'cotizacion_proyectos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['personas.nombre', 'like', '%'. $buscar . '%'],
                            ['cotizacion_proyectos.estado','Registrado'],['cotizacion_proyectos.idusuario',$usid]
                        ])
                        ->orderBy('cotizacion_proyectos.id', 'desc')->paginate(12);
                    }else{
                        $cotizaciones = CotizacionProyecto::join('personas','cotizacion_proyectos.idcliente','=','personas.id')
                        ->join('users','cotizacion_proyectos.idusuario','=','users.id')
                        ->select('cotizacion_proyectos.id','cotizacion_proyectos.tipo_comprobante','cotizacion_proyectos.num_comprobante',
                        'cotizacion_proyectos.fecha_hora','cotizacion_proyectos.vigencia','cotizacion_proyectos.impuesto','cotizacion_proyectos.total',
                        'cotizacion_proyectos.estado','cotizacion_proyectos.moneda','cotizacion_proyectos.tipo_cambio','cotizacion_proyectos.observacion',
                        'cotizacion_proyectos.forma_pago','cotizacion_proyectos.tiempo_entrega','cotizacion_proyectos.lugar_entrega','cotizacion_proyectos.neto',
                        'cotizacion_proyectos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['cotizacion_proyectos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['cotizacion_proyectos.estado','Registrado'],['cotizacion_proyectos.idusuario',$usid]
                        ])
                        ->orderBy('cotizacion_proyectos.id', 'desc')->paginate(12);
                    }
                }
            }
        }else{
            if($estadoC == "Anulada"){
                if ($buscar==''){
                    $cotizaciones = CotizacionProyecto::join('personas','cotizacion_proyectos.idcliente','=','personas.id')
                    ->join('users','cotizacion_proyectos.idusuario','=','users.id')
                    ->select('cotizacion_proyectos.id','cotizacion_proyectos.tipo_comprobante','cotizacion_proyectos.num_comprobante',
                    'cotizacion_proyectos.fecha_hora','cotizacion_proyectos.vigencia','cotizacion_proyectos.impuesto','cotizacion_proyectos.total',
                    'cotizacion_proyectos.estado','cotizacion_proyectos.moneda','cotizacion_proyectos.tipo_cambio','cotizacion_proyectos.observacion',
                    'cotizacion_proyectos.forma_pago','cotizacion_proyectos.tiempo_entrega','cotizacion_proyectos.lugar_entrega','cotizacion_proyectos.neto',
                    'cotizacion_proyectos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where('cotizacion_proyectos.estado','Anulada')
                    ->orderBy('cotizacion_proyectos.id', 'desc')->paginate(12);
                }
                else{
                    if($criterio == 'cliente'){
                        $cotizaciones = CotizacionProyecto::join('personas','cotizacion_proyectos.idcliente','=','personas.id')
                        ->join('users','cotizacion_proyectos.idusuario','=','users.id')
                        ->select('cotizacion_proyectos.id','cotizacion_proyectos.tipo_comprobante','cotizacion_proyectos.num_comprobante',
                        'cotizacion_proyectos.fecha_hora','cotizacion_proyectos.vigencia','cotizacion_proyectos.impuesto','cotizacion_proyectos.total',
                        'cotizacion_proyectos.estado','cotizacion_proyectos.moneda','cotizacion_proyectos.tipo_cambio','cotizacion_proyectos.observacion',
                        'cotizacion_proyectos.forma_pago','cotizacion_proyectos.tiempo_entrega','cotizacion_proyectos.lugar_entrega','cotizacion_proyectos.neto',
                        'cotizacion_proyectos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['personas.nombre', 'like', '%'. $buscar . '%'],
                            ['cotizacion_proyectos.estado','Anulada']
                        ])
                        ->orderBy('cotizacion_proyectos.id', 'desc')->paginate(12);
                    }else{
                        $cotizaciones = CotizacionProyecto::join('personas','cotizacion_proyectos.idcliente','=','personas.id')
                        ->join('users','cotizacion_proyectos.idusuario','=','users.id')
                        ->select('cotizacion_proyectos.id','cotizacion_proyectos.tipo_comprobante','cotizacion_proyectos.num_comprobante',
                        'cotizacion_proyectos.fecha_hora','cotizacion_proyectos.vigencia','cotizacion_proyectos.impuesto','cotizacion_proyectos.total',
                        'cotizacion_proyectos.estado','cotizacion_proyectos.moneda','cotizacion_proyectos.tipo_cambio','cotizacion_proyectos.observacion',
                        'cotizacion_proyectos.forma_pago','cotizacion_proyectos.tiempo_entrega','cotizacion_proyectos.lugar_entrega','cotizacion_proyectos.neto',
                        'cotizacion_proyectos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['cotizacion_proyectos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['cotizacion_proyectos.estado','Anulada']
                        ])
                        ->orderBy('cotizacion_proyectos.id', 'desc')->paginate(12);
                    }
                }
            }elseif($estadoC == "Vendida"){
                if ($buscar==''){
                    $cotizaciones = CotizacionProyecto::join('personas','cotizacion_proyectos.idcliente','=','personas.id')
                    ->join('users','cotizacion_proyectos.idusuario','=','users.id')
                    ->select('cotizacion_proyectos.id','cotizacion_proyectos.tipo_comprobante','cotizacion_proyectos.num_comprobante',
                    'cotizacion_proyectos.fecha_hora','cotizacion_proyectos.vigencia','cotizacion_proyectos.impuesto','cotizacion_proyectos.total',
                    'cotizacion_proyectos.estado','cotizacion_proyectos.moneda','cotizacion_proyectos.tipo_cambio','cotizacion_proyectos.observacion',
                    'cotizacion_proyectos.forma_pago','cotizacion_proyectos.tiempo_entrega','cotizacion_proyectos.lugar_entrega','cotizacion_proyectos.neto',
                    'cotizacion_proyectos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where('cotizacion_proyectos.estado','Vendida')
                    ->orderBy('cotizacion_proyectos.id', 'desc')->paginate(12);
                }
                else{
                    if($criterio == 'cliente'){
                        $cotizaciones = CotizacionProyecto::join('personas','cotizacion_proyectoss.idcliente','=','personas.id')
                        ->join('users','cotizacion_proyectoss.idusuario','=','users.id')
                        ->select('cotizacion_proyectoss.id','cotizacion_proyectoss.tipo_comprobante','cotizacion_proyectoss.num_comprobante',
                        'cotizacion_proyectoss.fecha_hora','cotizacion_proyectoss.vigencia','cotizacion_proyectoss.impuesto','cotizacion_proyectoss.total',
                        'cotizacion_proyectoss.estado','cotizacion_proyectoss.moneda','cotizacion_proyectoss.tipo_cambio','cotizacion_proyectoss.observacion',
                        'cotizacion_proyectoss.forma_pago','cotizacion_proyectoss.tiempo_entrega','cotizacion_proyectoss.lugar_entrega','cotizacion_proyectos.neto',
                        'cotizacion_proyectoss.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['personas.nombre', 'like', '%'. $buscar . '%'],
                            ['cotizacion_proyectoss.estado','Vendida']
                        ])
                        ->orderBy('cotizacion_proyectoss.id', 'desc')->paginate(12);
                    }else{
                        $cotizaciones = CotizacionProyecto::join('personas','cotizacion_proyectos.idcliente','=','personas.id')
                        ->join('users','cotizacion_proyectos.idusuario','=','users.id')
                        ->select('cotizacion_proyectos.id','cotizacion_proyectos.tipo_comprobante','cotizacion_proyectos.num_comprobante',
                        'cotizacion_proyectos.fecha_hora','cotizacion_proyectos.vigencia','cotizacion_proyectos.impuesto','cotizacion_proyectos.total',
                        'cotizacion_proyectos.estado','cotizacion_proyectos.moneda','cotizacion_proyectos.tipo_cambio','cotizacion_proyectos.observacion',
                        'cotizacion_proyectos.forma_pago','cotizacion_proyectos.tiempo_entrega','cotizacion_proyectos.lugar_entrega','cotizacion_proyectos.neto',
                        'cotizacion_proyectos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['cotizacion_proyectos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['cotizacion_proyectos.estado','Vendida']
                        ])
                        ->orderBy('cotizacion_proyectos.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if ($buscar==''){
                    $cotizaciones = CotizacionProyecto::join('personas','cotizacion_proyectos.idcliente','=','personas.id')
                    ->join('users','cotizacion_proyectos.idusuario','=','users.id')
                    ->select('cotizacion_proyectos.id','cotizacion_proyectos.tipo_comprobante','cotizacion_proyectos.num_comprobante',
                    'cotizacion_proyectos.fecha_hora','cotizacion_proyectos.vigencia','cotizacion_proyectos.impuesto','cotizacion_proyectos.total',
                    'cotizacion_proyectos.estado','cotizacion_proyectos.moneda','cotizacion_proyectos.tipo_cambio','cotizacion_proyectos.observacion',
                    'cotizacion_proyectos.forma_pago','cotizacion_proyectos.tiempo_entrega','cotizacion_proyectos.lugar_entrega','cotizacion_proyectos.neto',
                    'cotizacion_proyectos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where('cotizacion_proyectos.estado','Registrado')
                    ->orderBy('cotizacion_proyectos.id', 'desc')->paginate(12);
                }
                else{
                    if($criterio == 'cliente'){
                        $cotizaciones = CotizacionProyecto::join('personas','cotizacion_proyectos.idcliente','=','personas.id')
                        ->join('users','cotizacion_proyectos.idusuario','=','users.id')
                        ->select('cotizacion_proyectos.id','cotizacion_proyectos.tipo_comprobante','cotizacion_proyectos.num_comprobante',
                        'cotizacion_proyectos.fecha_hora','cotizacion_proyectos.vigencia','cotizacion_proyectos.impuesto','cotizacion_proyectos.total',
                        'cotizacion_proyectos.estado','cotizacion_proyectos.moneda','cotizacion_proyectos.tipo_cambio','cotizacion_proyectos.observacion',
                        'cotizacion_proyectos.forma_pago','cotizacion_proyectos.tiempo_entrega','cotizacion_proyectos.lugar_entrega','cotizacion_proyectos.neto',
                        'cotizacion_proyectos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['personas.nombre', 'like', '%'. $buscar . '%'],
                            ['cotizacion_proyectos.estado','Registrado']
                        ])
                        ->orderBy('cotizacion_proyectos.id', 'desc')->paginate(12);
                    }else{
                        $cotizaciones = CotizacionProyecto::join('personas','cotizacion_proyectos.idcliente','=','personas.id')
                        ->join('users','cotizacion_proyectos.idusuario','=','users.id')
                        ->select('cotizacion_proyectos.id','cotizacion_proyectos.tipo_comprobante','cotizacion_proyectos.num_comprobante',
                        'cotizacion_proyectos.fecha_hora','cotizacion_proyectos.vigencia','cotizacion_proyectos.impuesto','cotizacion_proyectos.total',
                        'cotizacion_proyectos.estado','cotizacion_proyectos.moneda','cotizacion_proyectos.tipo_cambio','cotizacion_proyectos.observacion',
                        'cotizacion_proyectos.forma_pago','cotizacion_proyectos.tiempo_entrega','cotizacion_proyectos.lugar_entrega','cotizacion_proyectos.neto',
                        'cotizacion_proyectos.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                        ->where([
                            ['cotizacion_proyectos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['cotizacion_proyectos.estado','Registrado']
                        ])
                        ->orderBy('cotizacion_proyectos.id', 'desc')->paginate(12);
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

            $cotizacion = new CotizacionProyecto();
            $cotizacion->idcliente = $request->idcliente;
            $cotizacion->idusuario = \Auth::user()->id;
            $cotizacion->tipo_comprobante = $request->tipo_comprobante;
            $cotizacion->num_comprobante = $request->num_comprobante;
            $cotizacion->title = $request->title;
            $cotizacion->neto = $request->neto;
            $cotizacion->content =  $request->content;
            $cotizacion->flete = $request->flet;
            $cotizacion->instalacion = $request->insta;
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
                $detalle = new DetalleCotizacionProyecto();
                $detalle->idcotizacionp = $cotizacion->id;
                $detalle->idarticulo = $det['idarticulo'];
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

            $cotizacion = CotizacionProyecto::findOrFail($request->id);
            $cotizacion->estado = 'Anulada';
            $cotizacion->aceptado = 0;
            $cotizacion->save();

            $detalles = DetalleCotizacionProyecto::select('idarticulo')
                    ->where('idcotizacionp',$request->id)->get();

            foreach($detalles as $ep=>$det){
                $articulo = Articulo::findOrFail($det['idarticulo']);
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
            $cotizacion = CotizacionProyecto::findOrFail($request->id);
            $cotizacion->estado = 'Vendida';
            $cotizacion->aceptado = 1;
            $cotizacion->save();

            $detalles = DetalleCotizacionProyecto::select('idarticulo')
                    ->where('idcotizacionp',$request->id)->get();

            foreach($detalles as $ep=>$det){
                $articulo = Articulo::findOrFail($det['idarticulo']);
                $articulo->comprometido = 0;
                $articulo->save();
            }

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }
    }

    public function obtenerCabecera(Request $request){
        //if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $cotizacion = CotizacionProyecto::join('personas','cotizacion_proyectos.idcliente','=','personas.id')
        ->join('users','cotizacion_proyectos.idusuario','=','users.id')
        ->select('cotizacion_proyectos.id','cotizacion_proyectos.tipo_comprobante','cotizacion_proyectos.num_comprobante',
        'cotizacion_proyectos.fecha_hora','cotizacion_proyectos.impuesto','cotizacion_proyectos.total','cotizacion_proyectos.estado','cotizacion_proyectos.title',
        'cotizacion_proyectos.moneda','cotizacion_proyectos.tipo_cambio','cotizacion_proyectos.observacion','cotizacion_proyectos.forma_pago','cotizacion_proyectos.content',
        'cotizacion_proyectos.tiempo_entrega','cotizacion_proyectos.lugar_entrega','cotizacion_proyectos.aceptado','cotizacion_proyectos.vigencia',
        'personas.id as idcliente','personas.nombre','personas.tipo','personas.rfc','personas.company','personas.tel_company','cotizacion_proyectos.neto',
        'users.usuario','personas.email as EmailC')
        ->where('cotizacion_proyectos.id','=',$id)
        ->orderBy('cotizacion_proyectos.id', 'desc')->take(1)->get();

        return ['cotizacion' => $cotizacion];
    }

    public function obtenerDetalles(Request $request){

        //if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $detalles = DetalleCotizacionProyecto::join('articulos','detalle_cotizacion_proyectos.idarticulo','=','articulos.id')
        ->leftJoin('categorias','articulos.idcategoria','=','categorias.id')
        ->select('detalle_cotizacion_proyectos.cantidad','detalle_cotizacion_proyectos.precio','detalle_cotizacion_proyectos.descuento',
            'articulos.sku','articulos.codigo','articulos.espesor','articulos.largo','articulos.alto',
            'articulos.metros_cuadrados','articulos.descripcion','articulos.idcategoria','articulos.terminado',
            'articulos.ubicacion','articulos.file','articulos.origen','articulos.contenedor','articulos.fecha_llegada',
            'articulos.observacion','articulos.condicion','articulos.stock','articulos.id as idarticulo',
            'categorias.nombre as categoria')
        ->where('detalle_cotizacion_proyectos.idcotizacionp',$id)
        ->orderBy('detalle_cotizacion_proyectos.id','desc')->get();

        return ['detalles' => $detalles];
    }

    public function pdf(Request $request,$id){

        $cotizacion =  CotizacionProyecto::join('personas','cotizacion_proyectos.idcliente','=','personas.id')
        ->join('users','cotizacion_proyectos.idusuario','=','users.id')
        ->select('cotizacion_proyectos.id','cotizacion_proyectos.tipo_comprobante','cotizacion_proyectos.num_comprobante',
            'cotizacion_proyectos.created_at','cotizacion_proyectos.impuesto','cotizacion_proyectos.total','cotizacion_proyectos.estado','cotizacion_proyectos.title',
            'cotizacion_proyectos.forma_pago','cotizacion_proyectos.tiempo_entrega','cotizacion_proyectos.lugar_entrega','cotizacion_proyectos.neto',
            'cotizacion_proyectos.aceptado','cotizacion_proyectos.moneda','cotizacion_proyectos.tipo_cambio', 'cotizacion_proyectos.observacion','cotizacion_proyectos.content',
            'cotizacion_proyectos.vigencia','personas.nombre','personas.rfc','personas.domicilio','personas.ciudad','cotizacion_proyectos.neto',
            'personas.telefono','personas.email','personas.company as contacto','personas.tel_company','users.usuario')
        ->where('cotizacion_proyectos.id',$id)->take(1)->get();

        $detalles = DetalleCotizacionProyecto::join('articulos','detalle_cotizacion_proyectos.idarticulo','=','articulos.id')
            ->select('detalle_cotizacion_proyectos.cantidad','detalle_cotizacion_proyectos.precio','detalle_cotizacion_proyectos.descuento',
                'articulos.sku as articulo','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                'articulos.terminado','articulos.codigo','articulos.ubicacion')
            ->where('detalle_cotizacion_proyectos.idcotizacionp',$id)
            ->orderBy('detalle_cotizacion_proyectos.id','desc')->get();

        $numcotizacion = CotizacionProyecto::select('num_comprobante')->where('id',$id)->get();

        $ivaagregado = CotizacionProyecto::select('impuesto')->where('id',$id)->get();

        $sumaMts = DB::table('articulos')
        ->select(DB::raw('SUM(metros_cuadrados) as metros'))
        ->leftJoin('detalle_cotizacion_proyectos','detalle_cotizacion_proyectos.idarticulo','articulos.id')
        ->where('detalle_cotizacion_proyectos.idcotizacionp',$id)
        ->get();

        $pdf = \PDF::loadView('pdf.cotizacionEspecial',
            ['cotizacion' => $cotizacion,'detalles'=>$detalles,
            'ivaCotizacion' =>$ivaagregado[0]->impuesto,
            'sumaMts' => $sumaMts[0]->metros]);


        return $pdf->stream('cotizacion-'.$numcotizacion[0]->num_comprobante.'.pdf');
    }

    public function aceptarCotizacion(Request $request){
        if (!$request->ajax()) return redirect('/');
        $cotizacion = CotizacionProyecto::findOrFail($request->id);
        $cotizacion->aceptado = $request->aceptado;
        $cotizacion->save();
    }

    public function getLastNum(){

        $lastNum = CotizacionProyecto::select('num_comprobante')->get()->last();
        $noComp = explode('"',$lastNum);
        $SigNum = explode("-",$noComp[0]);
        return ['SigNum' => $SigNum[0]];
    }

    public function actualizarObservacion(Request $request){
        if (!$request->ajax()) return redirect('/');
        $cotizacion = CotizacionProyecto::findOrFail($request->id);
        $cotizacion->observacion = $request->observacion;
        $cotizacion->save();
    }

    public function enviarCotizacionMail(Request $request){

        $cotizacion =  CotizacionProyecto::join('personas','cotizaciones.idcliente','=','personas.id')
        ->join('users','cotizaciones.idusuario','=','users.id')
        ->select('cotizaciones.id','cotizaciones.tipo_comprobante','cotizaciones.num_comprobante',
            'cotizaciones.created_at','cotizaciones.impuesto','cotizaciones.total','cotizaciones.estado',
            'cotizaciones.forma_pago','cotizaciones.tiempo_entrega','cotizaciones.lugar_entrega',
            'cotizaciones.aceptado','cotizaciones.moneda','cotizaciones.tipo_cambio', 'cotizaciones.observacion',
            'cotizaciones.vigencia','personas.nombre','personas.rfc','personas.domicilio','personas.ciudad',
            'personas.telefono','personas.email','personas.company as contacto','personas.tel_company','users.usuario')
        ->where('cotizaciones.id',$request->id)->take(1)->get();

        $detalles = DetalleCotizacionProyecto::join('articulos','detalle_cotizaciones.idarticulo','=','articulos.id')
        ->select('detalle_cotizaciones.cantidad','detalle_cotizaciones.precio','detalle_cotizaciones.descuento',
            'articulos.sku as articulo','articulos.largo','articulos.alto','articulos.metros_cuadrados',
            'articulos.terminado','articulos.codigo','articulos.ubicacion')
        ->where('detalle_cotizaciones.idcotizacion',$request->id)
        ->orderBy('detalle_cotizaciones.id','desc')->get();

        $numcotizacion = CotizacionProyecto::select('num_comprobante')->where('id',$request->id)->get();

        $ivaagregado = CotizacionProyecto::select('impuesto')->where('id',$request->id)->get();

        $sumaMts = DB::table('articulos')
        ->select(DB::raw('SUM(metros_cuadrados) as metros'))
        ->leftJoin('detalle_cotizaciones','detalle_cotizaciones.idarticulo','articulos.id')
        ->where('detalle_cotizaciones.idcotizacion',$request->id)
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
