<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use App\Herramienta;
use App\Oferta;
use App\DetalleOferta;
use Barryvdh\DomPDF\PDF;

class OfertaController extends Controller
{
    public function index(Request $request){
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $estadoC = $request->statusOferta;

        if($estadoC == "Anulada"){
            if ($buscar==''){
                $ofertas = Oferta::join('personas','ofertas.idcliente','=','personas.id')
                ->join('users','ofertas.idusuario','=','users.id')
                ->select('ofertas.id','ofertas.tipo_comprobante','ofertas.num_comprobante',
                    'ofertas.fecha_hora','ofertas.vigencia','ofertas.impuesto','ofertas.total',
                    'ofertas.estado','ofertas.moneda','ofertas.tipo_cambio','ofertas.observacion',
                    'ofertas.forma_pago','ofertas.tiempo_entrega','ofertas.lugar_entrega',
                    'ofertas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                ->where('ofertas.estado','Anulada')
                ->orderBy('ofertas.id', 'desc')->paginate(12);
            }
            else{
                if($criterio == 'cliente'){
                    $ofertas = Oferta::join('personas','ofertas.idcliente','=','personas.id')
                    ->join('users','ofertas.idusuario','=','users.id')
                    ->select('ofertas.id','ofertas.tipo_comprobante','ofertas.num_comprobante',
                        'ofertas.fecha_hora','ofertas.vigencia','ofertas.impuesto','ofertas.total',
                        'ofertas.estado','ofertas.moneda','ofertas.tipo_cambio','ofertas.observacion',
                        'ofertas.forma_pago','ofertas.tiempo_entrega','ofertas.lugar_entrega',
                        'ofertas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['ofertas.estado','Anulada']])
                    ->orderBy('ofertas.id', 'desc')->paginate(12);
                }else{
                    $ofertas = Oferta::join('personas','ofertas.idcliente','=','personas.id')
                    ->join('users','ofertas.idusuario','=','users.id')
                    ->select('ofertas.id','ofertas.tipo_comprobante','ofertas.num_comprobante',
                        'ofertas.fecha_hora','ofertas.vigencia','ofertas.impuesto','ofertas.total',
                        'ofertas.estado','ofertas.moneda','ofertas.tipo_cambio','ofertas.observacion',
                        'ofertas.forma_pago','ofertas.tiempo_entrega','ofertas.lugar_entrega',
                        'ofertas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where([['ofertas.'.$criterio, 'like', '%'. $buscar . '%'],['ofertas.estado','Anulada']])
                    ->orderBy('ofertas.id', 'desc')->paginate(12);
                }
            }
        }elseif($estadoC == "Vendida"){
            if ($buscar==''){
                $ofertas = Oferta::join('personas','ofertas.idcliente','=','personas.id')
                ->join('users','ofertas.idusuario','=','users.id')
                ->select('ofertas.id','ofertas.tipo_comprobante','ofertas.num_comprobante',
                    'ofertas.fecha_hora','ofertas.vigencia','ofertas.impuesto','ofertas.total',
                    'ofertas.estado','ofertas.moneda','ofertas.tipo_cambio','ofertas.observacion',
                    'ofertas.forma_pago','ofertas.tiempo_entrega','ofertas.lugar_entrega',
                    'ofertas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                ->where('ofertas.estado','Vendida')
                ->orderBy('ofertas.id', 'desc')->paginate(12);
            }
            else{
                if($criterio == 'cliente'){
                    $ofertas = Oferta::join('personas','ofertas.idcliente','=','personas.id')
                    ->join('users','ofertas.idusuario','=','users.id')
                    ->select('ofertas.id','ofertas.tipo_comprobante','ofertas.num_comprobante',
                        'ofertas.fecha_hora','ofertas.vigencia','ofertas.impuesto','ofertas.total',
                        'ofertas.estado','ofertas.moneda','ofertas.tipo_cambio','ofertas.observacion',
                        'ofertas.forma_pago','ofertas.tiempo_entrega','ofertas.lugar_entrega',
                        'ofertas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['ofertas.estado','Vendida']])
                    ->orderBy('ofertas.id', 'desc')->paginate(12);
                }else{
                    $ofertas = Oferta::join('personas','ofertas.idcliente','=','personas.id')
                    ->join('users','ofertas.idusuario','=','users.id')
                    ->select('ofertas.id','ofertas.tipo_comprobante','ofertas.num_comprobante',
                        'ofertas.fecha_hora','ofertas.vigencia','ofertas.impuesto','ofertas.total',
                        'ofertas.estado','ofertas.moneda','ofertas.tipo_cambio','ofertas.observacion',
                        'ofertas.forma_pago','ofertas.tiempo_entrega','ofertas.lugar_entrega',
                        'ofertas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where([['ofertas.'.$criterio, 'like', '%'. $buscar . '%'],['ofertas.estado','Vendida']])
                    ->orderBy('ofertas.id', 'desc')->paginate(12);
                }
            }
        }elseif($estadoC == "Registrada"){
            if ($buscar==''){
                $ofertas = Oferta::join('personas','ofertas.idcliente','=','personas.id')
                ->join('users','ofertas.idusuario','=','users.id')
                ->select('ofertas.id','ofertas.tipo_comprobante','ofertas.num_comprobante',
                    'ofertas.fecha_hora','ofertas.vigencia','ofertas.impuesto','ofertas.total',
                    'ofertas.estado','ofertas.moneda','ofertas.tipo_cambio','ofertas.observacion',
                    'ofertas.forma_pago','ofertas.tiempo_entrega','ofertas.lugar_entrega',
                    'ofertas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                ->where('ofertas.estado','Registrado')
                ->orderBy('ofertas.id', 'desc')->paginate(12);
            }
            else{
                if($criterio == 'cliente'){
                    $ofertas = Oferta::join('personas','ofertas.idcliente','=','personas.id')
                    ->join('users','ofertas.idusuario','=','users.id')
                    ->select('ofertas.id','ofertas.tipo_comprobante','ofertas.num_comprobante',
                        'ofertas.fecha_hora','ofertas.vigencia','ofertas.impuesto','ofertas.total',
                        'ofertas.estado','ofertas.moneda','ofertas.tipo_cambio','ofertas.observacion',
                        'ofertas.forma_pago','ofertas.tiempo_entrega','ofertas.lugar_entrega',
                        'ofertas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['ofertas.estado','Registrado']])
                    ->orderBy('ofertas.id', 'desc')->paginate(12);
                }else{
                    $ofertas = Oferta::join('personas','ofertas.idcliente','=','personas.id')
                    ->join('users','ofertas.idusuario','=','users.id')
                    ->select('ofertas.id','ofertas.tipo_comprobante','ofertas.num_comprobante',
                        'ofertas.fecha_hora','ofertas.vigencia','ofertas.impuesto','ofertas.total',
                        'ofertas.estado','ofertas.moneda','ofertas.tipo_cambio','ofertas.observacion',
                        'ofertas.forma_pago','ofertas.tiempo_entrega','ofertas.lugar_entrega',
                        'ofertas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where([['ofertas.'.$criterio, 'like', '%'. $buscar . '%'],['ofertas.estado','Registrado']])
                    ->orderBy('ofertas.id', 'desc')->paginate(12);
                }
            }
        }else{
            if ($buscar==''){
                $ofertas = Oferta::join('personas','ofertas.idcliente','=','personas.id')
                ->join('users','ofertas.idusuario','=','users.id')
                ->select('ofertas.id','ofertas.tipo_comprobante','ofertas.num_comprobante',
                    'ofertas.fecha_hora','ofertas.vigencia','ofertas.impuesto','ofertas.total',
                    'ofertas.estado','ofertas.moneda','ofertas.tipo_cambio','ofertas.observacion',
                    'ofertas.forma_pago','ofertas.tiempo_entrega','ofertas.lugar_entrega',
                    'ofertas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                ->orderBy('ofertas.id', 'desc')->paginate(12);
            }
            else{
                if($criterio == 'cliente'){
                    $ofertas = Oferta::join('personas','ofertas.idcliente','=','personas.id')
                    ->join('users','ofertas.idusuario','=','users.id')
                    ->select('ofertas.id','ofertas.tipo_comprobante','ofertas.num_comprobante',
                        'ofertas.fecha_hora','ofertas.vigencia','ofertas.impuesto','ofertas.total',
                        'ofertas.estado','ofertas.moneda','ofertas.tipo_cambio','ofertas.observacion',
                        'ofertas.forma_pago','ofertas.tiempo_entrega','ofertas.lugar_entrega',
                        'ofertas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where([['personas.nombre', 'like', '%'. $buscar . '%']])
                    ->orderBy('ofertas.id', 'desc')->paginate(12);
                }else{
                    $ofertas = Oferta::join('personas','ofertas.idcliente','=','personas.id')
                    ->join('users','ofertas.idusuario','=','users.id')
                    ->select('ofertas.id','ofertas.tipo_comprobante','ofertas.num_comprobante',
                        'ofertas.fecha_hora','ofertas.vigencia','ofertas.impuesto','ofertas.total',
                        'ofertas.estado','ofertas.moneda','ofertas.tipo_cambio','ofertas.observacion',
                        'ofertas.forma_pago','ofertas.tiempo_entrega','ofertas.lugar_entrega',
                        'ofertas.aceptado','personas.nombre','users.usuario','personas.email as EmailC')
                    ->where([['ofertas.'.$criterio, 'like', '%'. $buscar . '%']])
                    ->orderBy('ofertas.id', 'desc')->paginate(12);
                }
            }
        }

        return [
            'pagination' => [
                'total'        => $ofertas->total(),
                'current_page' => $ofertas->currentPage(),
                'per_page'     => $ofertas->perPage(),
                'last_page'    => $ofertas->lastPage(),
                'from'         => $ofertas->firstItem(),
                'to'           => $ofertas->lastItem(),
            ],
            'ofertas' => $ofertas
        ];
    }

    public function store(Request $request){
        if(!$request->ajax()) return redirect('/');

        $mytime = Carbon::now('America/Mexico_City');

        try{
            DB::beginTransaction();

            $oferta = new Oferta();
            $oferta->idcliente = $request->idcliente;
            $oferta->idusuario = \Auth::user()->id;
            $oferta->tipo_comprobante = $request->tipo_comprobante;
            $oferta->num_comprobante = $request->num_comprobante;
            $oferta->fecha_hora = $mytime;
            $oferta->vigencia = $request->vigencia;
            $oferta->impuesto = $request->impuesto;
            $oferta->total = $request->total;
            $oferta->forma_pago = $request->forma_pago;
            $oferta->tiempo_entrega = $request->tiempo_entrega;
            $oferta->lugar_entrega = $request->lugar_entrega;
            $oferta->aceptado = 0;
            $oferta->estado = 'Registrado';
            $oferta->moneda = $request->moneda;
            $oferta->tipo_cambio = $request->tipo_cambio;
            $oferta->observacion = $request->observacion;

            $oferta->save();

            $detalles = $request->data;//Array de detalles

            //Recorro todos los elementos
            foreach($detalles as $ep=>$det){
                $detalle = new DetalleOferta();
                $detalle->idoferta = $oferta->id;
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

            $oferta = Oferta::findOrFail($request->id);
            $oferta->estado = 'Anulada';
            $oferta->aceptado = 0;
            $oferta->save();

            $detalles = DetalleOferta::select('idarticulo','cantidad')
                    ->where('idoferta',$request->id)->get();

            foreach($detalles as $ep=>$det){
                $herramienta = Herramienta::findOrFail($det['idarticulo']);
                $herramienta->comprometido = $herramienta->comprometido - $det['cantidad'];
                $herramienta->save();
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
            $oferta = Oferta::findOrFail($request->id);
            $oferta->estado = 'Vendida';
            $oferta->aceptado = 1;
            $oferta->save();

            $detalles = DetalleOferta::select('idarticulo')
                    ->where('idoferta',$request->id)->get();

            foreach($detalles as $ep=>$det){
                $herramienta = Herramienta::findOrFail($det['idarticulo']);
                $herramienta->comprometido = $herramienta->comprometido - $det['cantidad'];
                $herramienta->save();
            }

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }
    }

    public function obtenerCabecera(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $oferta = Oferta::join('personas','ofertas.idcliente','=','personas.id')
        ->join('users','ofertas.idusuario','=','users.id')
        ->leftJoin('personas as vendedores','vendedores.id','users.id')
        ->select('ofertas.id','ofertas.tipo_comprobante','ofertas.num_comprobante',
            'ofertas.fecha_hora','ofertas.impuesto','ofertas.total','ofertas.estado',
            'ofertas.moneda','ofertas.tipo_cambio','ofertas.observacion','ofertas.forma_pago',
            'ofertas.tiempo_entrega','ofertas.lugar_entrega','ofertas.aceptado','ofertas.vigencia',
            'personas.id as idcliente','personas.nombre as cliente','personas.tipo','personas.rfc',
            'personas.tel_company','users.usuario','personas.email as EmailC','personas.company',
            'vendedores.nombre as vendedor','personas.rfc','personas.email')
        ->where('ofertas.id','=',$id)
        ->orderBy('ofertas.id', 'desc')->take(1)->get();

        return ['oferta' => $oferta];
    }

    public function obtenerDetalles(Request $request){

        if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $detalles = DetalleOferta::join('herramientas','detalle_ofertas.idarticulo','=','herramientas.id')
        ->select('detalle_ofertas.cantidad','detalle_ofertas.precio','detalle_ofertas.descuento',
            'herramientas.sku','herramientas.codigo','herramientas.precio_venta','herramientas.stock',
            'herramientas.descripcion','herramientas.ubicacion','herramientas.file',
            'herramientas.condicion','herramientas.comprometido')
        ->where('detalle_ofertas.idoferta',$id)
        ->orderBy('detalle_ofertas.id','desc')->get();

        return ['detalles' => $detalles];
    }

    public function pdf(Request $request,$id){

        $oferta =  Oferta::join('personas','ofertas.idcliente','=','personas.id')
        ->join('users','ofertas.idusuario','=','users.id')
        ->select('ofertas.id','ofertas.tipo_comprobante','ofertas.num_comprobante',
            'ofertas.created_at','ofertas.impuesto','ofertas.total','ofertas.estado',
            'ofertas.forma_pago','ofertas.tiempo_entrega','ofertas.lugar_entrega',
            'ofertas.aceptado','ofertas.moneda','ofertas.tipo_cambio', 'ofertas.observacion',
            'ofertas.vigencia','personas.nombre','personas.rfc','personas.domicilio','personas.ciudad',
            'personas.telefono','personas.email','personas.company as contacto','personas.tel_company','users.usuario')
        ->where('ofertas.id',$id)->take(1)->get();

        $detalles = DetalleOferta::join('herramientas','detalle_ofertas.idarticulo','=','herramientas.id')
            ->select('detalle_ofertas.cantidad','detalle_ofertas.precio','detalle_ofertas.descuento',
                'herramientas.sku','herramientas.codigo','herramientas.precio_venta','herramientas.stock',
                'herramientas.descripcion','herramientas.ubicacion')
            ->where('detalle_ofertas.idoferta',$id)
            ->orderBy('detalle_ofertas.id','desc')->get();

        $numcotizacion = Oferta::select('num_comprobante')->where('id',$id)->get();

        $ivaagregado = Oferta::select('impuesto')->where('id',$id)->get();

        $pdf = \PDF::loadView('pdf.cotizacionOferta',
            ['oferta' => $oferta,'detalles'=>$detalles,
            'ivaCotizacion' =>$ivaagregado[0]->impuesto]);


        return $pdf->stream('cotizacion-'.$numcotizacion[0]->num_comprobante.'.pdf');
    }

    public function aceptarCotizacion(Request $request){
        if (!$request->ajax()) return redirect('/');
        $oferta = Oferta::findOrFail($request->id);
        $oferta->aceptado = $request->aceptado;
        $oferta->save();
    }

    public function getLastNum(){

        $lastNum = Oferta::select('num_comprobante')->get()->last();

        if($lastNum){
            $noComp = explode('"',$lastNum);
            $SigNum = explode("-",$noComp[3]);
            return ['SigNum' => $SigNum[2]];
        }else{
            return ['SigNum' => 1 ];
        }

    }

    public function actualizarObservacion(Request $request){
        if (!$request->ajax()) return redirect('/');
        $oferta = Oferta::findOrFail($request->id);
        $oferta->observacion = $request->observacion;
        $oferta->save();
    }
}
