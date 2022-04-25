<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exports\SalidasExport;
use App\Exports\SalidasExportDet;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\User;
use App\Herramienta;
use App\Salida;
use App\Deposit;
use App\Credit;
use App\DetalleSalida;
use Barryvdh\DomPDF\PDF;
use Exception;

class SalidaController extends Controller
{
    public function index(Request $request){
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $estadoS = $request->estado;
        $entregaEs = $request->estadoEntrega;
        $pagadoEs = $request->estadoPagado;
        $usrol = \Auth::user()->idrol;
        $usid = \Auth::user()->id;
        $usarea = \Auth::user()->area;

        if($usarea == 'SLP' && $usrol != 4){
            if($estadoS == "Anulada"){
                if($criterio == "cliente"){
                    $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                    ->join('users','salidas.idusuario','=','users.id')
                    ->leftJoin('personas as vendedores','vendedores.id','users.id')
                    ->leftJoin('personas as vendedores','vendedores.id','users.id')
                    ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                        'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                        'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                        'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                        'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                        'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                        'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                    ->where([
                        ['personas.nombre', 'like', '%'. $buscar . '%'],
                        ['slidas.estado',$estadoS]
                    ])
                    ->orderBy('salidas.id', 'desc')->paginate(12);
                }elseif($criterio == "user"){
                    $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                    ->join('users','salidas.idusuario','=','users.id')
                    ->leftJoin('personas as vendedores','vendedores.id','users.id')
                    ->leftJoin('personas as vendedores','vendedores.id','users.id')
                    ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                    'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                    'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                    'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                    'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                    'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                    'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                    ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado',$estadoS]])
                    ->orderBy('salidas.id', 'desc')->paginate(12);
                }else{
                    if ($buscar==''){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                        'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                        'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                        'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                        'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                        'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                        'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where('salidas.estado',$estadoS)
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }
                    else{
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                        'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                        'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                        'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                        'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                        'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                        'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([
                            ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['salidas.estado',$estadoS]
                        ])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }
                }

            }else{
                if($buscar==''){
                    if($entregaEs == 'entregado'){
                        if($pagadoEs == 'pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',1],
                                ['salidas.pagado',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',1],
                                ['salidas.pagado',0],['salidas.pago_parcial',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',1],
                                ['salidas.pagado',0],['salidas.pago_parcial',0]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }else{
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'entrega_parcial'){
                        if($pagadoEs == 'pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entrega_parcial',1],['salidas.pagado',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);

                        }elseif($pagadoEs == 'parcial'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entrega_parcial',1],
                                ['salidas.pagado',0],['salidas.pago_parcial',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);

                        }elseif($pagadoEs == 'nopagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entrega_parcial',1],
                                ['salidas.pagado',0],['salidas.pago_parcial',0]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }else{
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entrega_parcial',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'no_entregado'){
                        if($pagadoEs == 'pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',0],
                                ['salidas.entrega_parcial',0],['salidas.pagado',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',0],
                                ['salidas.entrega_parcial',0],['salidas.pagado',0],['salidas.pago_parcial',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',0],
                                ['salidas.entrega_parcial',0],['salidas.pagado',0],['salidas.pago_parcial',0]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }else{
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',0],['salidas.entrega_parcial',0]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }else{
                        if($pagadoEs == 'pagado'){
                            $$salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.pagado',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.pagado',0],['salidas.pago_parcial',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.pagado',0],['salidas.pago_parcial',0]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }else{
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado']])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }
                }
                else{
                    if($criterio == "cliente"){
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $$salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.entregado',1],
                                    ['salidas.estado','Registrado'],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.entregado',1],
                                    ['salidas.estado','Registrado'],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.entregado',1],
                                    ['salidas.estado','Registrado'],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.entregado',1],
                                    ['salidas.estado','Registrado']])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);

                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.pagado',0],
                                        ['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.pagado',0],
                                        ['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado']])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }
                    }elseif($criterio == "user"){
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.pagado',0],
                                    ['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.pagado',0],
                                    ['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado']])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }
                    }else{
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([ ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([ ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([ ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([ ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.pagado',0],
                                    ['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.pagado',0],
                                    ['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado']])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }
                    }
                }
            }
        }else{
            if($estadoS == "Anulada"){
                if($criterio == "cliente"){
                    $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                    ->join('users','salidas.idusuario','=','users.id')
                    ->leftJoin('personas as vendedores','vendedores.id','users.id')
                    ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                    'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                    'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                    'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                    'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                    'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                    'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                    ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado',$estadoS],
                        ['salidas.idusuario',$usid]])
                    ->orderBy('salidas.id', 'desc')->paginate(12);
                }elseif($criterio == "user"){
                    $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                    ->join('users','salidas.idusuario','=','users.id')
                    ->leftJoin('personas as vendedores','vendedores.id','users.id')
                    ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                    'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                    'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                    'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                    'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                    'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                    'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                    ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado',$estadoS],
                        ['salidas.idusuario',$usid]])
                    ->orderBy('salidas.id', 'desc')->paginate(12);
                }else{
                    if ($buscar==''){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                        'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                        'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                        'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                        'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                        'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                        'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([['salidas.estado',$estadoS],['salidas.idusuario',$usid]])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }
                    else{
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                        'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                        'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                        'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                        'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                        'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                        'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([
                            ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['salidas.estado',$estadoS],
                            ['salidas.idusuario',$usid]
                        ])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if($buscar==''){
                    if($entregaEs == 'entregado'){
                        if($pagadoEs == 'pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',1],['salidas.idusuario',$usid],
                                ['salidas.pagado',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',1],['salidas.idusuario',$usid],
                                ['salidas.pagado',0],['salidas.pago_parcial',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',1],['salidas.idusuario',$usid],
                                ['salidas.pagado',0],['salidas.pago_parcial',0]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }else{
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',1],['salidas.idusuario',$usid]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'entrega_parcial'){
                        if($pagadoEs == 'pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([ ['salidas.estado','Registrado'],['salidas.entrega_parcial',1],
                                ['salidas.idusuario',$usid],['salidas.pagado',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([ ['salidas.estado','Registrado'],['salidas.entrega_parcial',1],
                                ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([ ['salidas.estado','Registrado'],['salidas.entrega_parcial',1],
                                ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',0]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }else{
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([ ['salidas.estado','Registrado'],['salidas.entrega_parcial',1],
                                ['salidas.idusuario',$usid]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'no_entregado'){
                        if($pagadoEs == 'pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'], ['salidas.entregado',0],['salidas.entrega_parcial',0],
                                ['salidas.idusuario',$usid],['salidas.pagado',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'], ['salidas.entregado',0],['salidas.entrega_parcial',0],
                                ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'], ['salidas.entregado',0],['salidas.entrega_parcial',0],
                                ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',0]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }else{
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'], ['salidas.entregado',0],['salidas.entrega_parcial',0],
                                ['salidas.idusuario',$usid]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }else{
                        if($pagadoEs == 'pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.idusuario',$usid],['salidas.pagado',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.idusuario',$usid],
                                ['salidas.pagado',0],['salidas.pago_parcial',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.idusuario',$usid],
                                ['salidas.pagado',0],['salidas.pago_parcial',0]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }else{
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.idusuario',$usid]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }
                }
                else{
                    if($criterio == "cliente"){
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'], ['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.idusuario',$usid],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'], ['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'], ['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'], ['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([ ['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.idusuario',$usid],
                                    ['salidas.estado','Registrado'],['salidas.entrega_parcial',1],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([ ['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.idusuario',$usid],
                                    ['salidas.estado','Registrado'],['salidas.entrega_parcial',1],['salidas.pagado',0],
                                    ['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([ ['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.idusuario',$usid],
                                    ['salidas.estado','Registrado'],['salidas.entrega_parcial',1],['salidas.pagado',0],
                                    ['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([ ['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.idusuario',$usid],
                                    ['salidas.estado','Registrado'],['salidas.entrega_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.entregado',0],
                                    ['salidas.estado','Registrado'],['salidas.entrega_parcial',0],
                                    ['salidas.idusuario',$usid], ['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.entregado',0],
                                    ['salidas.estado','Registrado'],['salidas.entrega_parcial',0],
                                    ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.entregado',0],
                                    ['salidas.estado','Registrado'],['salidas.entrega_parcial',0],
                                    ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.entregado',0],
                                    ['salidas.estado','Registrado'],['salidas.entrega_parcial',0],
                                    ['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }
                    }elseif($criterio == "user"){
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.idusuario',$usid],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.idusuario',$usid],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.idusuario',$usid],
                                    ['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.idusuario',$usid],
                                    ['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.idusuario',$usid],
                                    ['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }
                    }else{
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.entregado',1],
                                    ['salidas.estado','Registrado'],['salidas.idusuario',$usid],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.entregado',1],
                                    ['salidas.estado','Registrado'],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.entregado',1],
                                    ['salidas.estado','Registrado'],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.entregado',1],
                                    ['salidas.estado','Registrado'],['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.entrega_parcial',1],
                                    ['salidas.estado','Registrado'],['salidas.idusuario',$usid],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.entrega_parcial',1],
                                    ['salidas.estado','Registrado'],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.entrega_parcial',1],
                                    ['salidas.estado','Registrado'],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.entrega_parcial',1],
                                    ['salidas.estado','Registrado'],['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.idusuario',$usid],
                                    ['salidas.entregado',0],['salidas.estado','Registrado'],['salidas.entrega_parcial',0],
                                    ['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.idusuario',$usid],
                                    ['salidas.entregado',0],['salidas.estado','Registrado'],['salidas.entrega_parcial',0],
                                    ['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.idusuario',$usid],
                                    ['salidas.entregado',0],['salidas.estado','Registrado'],['salidas.entrega_parcial',0],
                                    ['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.idusuario',$usid],
                                    ['salidas.entregado',0],['salidas.estado','Registrado'],['salidas.entrega_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }
                    }
                }
            }
        }
        if($usarea == 'GDL' && $usrol != 4){
            if($estadoS == "Anulada"){
                if($criterio == "cliente"){
                    $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                    ->join('users','salidas.idusuario','=','users.id')
                    ->leftJoin('personas as vendedores','vendedores.id','users.id')
                    ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                    'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                    'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                    'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                    'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                    'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                    'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                    ->where([
                        ['personas.nombre', 'like', '%'. $buscar . '%'],
                        ['salidas.estado',$estadoS]
                    ])
                    ->orderBy('salidas.id', 'desc')->paginate(12);
                }elseif($criterio == "user"){
                    $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                    ->join('users','salidas.idusuario','=','users.id')
                    ->leftJoin('personas as vendedores','vendedores.id','users.id')
                    ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                    'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                    'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                    'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                    'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                    'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                    'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                    ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado',$estadoS]])
                    ->orderBy('salidas.id', 'desc')->paginate(12);
                }else{
                    if ($buscar==''){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                        'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                        'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                        'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                        'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                        'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                        'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where('salidas.estado',$estadoS)
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }
                    else{
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                        'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                        'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                        'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                        'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                        'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                        'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([
                            ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['salidas.estado',$estadoS]
                        ])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }
                }

            }else{
                if($buscar==''){
                    if($entregaEs == 'entregado'){
                        if($pagadoEs == 'pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',1],
                                ['salidas.pagado',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',1],
                                ['salidas.pagado',0],['salidas.pago_parcial',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',1],
                                ['salidas.pagado',0],['salidas.pago_parcial',0]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }else{
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'entrega_parcial'){
                        if($pagadoEs == 'pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entrega_parcial',1],['salidas.pagado',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);

                        }elseif($pagadoEs == 'parcial'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entrega_parcial',1],
                                ['salidas.pagado',0],['salidas.pago_parcial',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);

                        }elseif($pagadoEs == 'nopagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entrega_parcial',1],
                                ['salidas.pagado',0],['salidas.pago_parcial',0]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }else{
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entrega_parcial',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'no_entregado'){
                        if($pagadoEs == 'pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',0],
                                ['salidas.entrega_parcial',0],['salidas.pagado',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',0],
                                ['salidas.entrega_parcial',0],['salidas.pagado',0],['salidas.pago_parcial',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',0],
                                ['salidas.entrega_parcial',0],['salidas.pagado',0],['salidas.pago_parcial',0]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }else{
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',0],['salidas.entrega_parcial',0]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }else{
                        if($pagadoEs == 'pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.pagado',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.pagado',0],['salidas.pago_parcial',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.pagado',0],['salidas.pago_parcial',0]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }else{
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado']])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }
                }
                else{
                    if($criterio == "cliente"){
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.entregado',1],
                                    ['salidas.estado','Registrado'],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.entregado',1],
                                    ['salidas.estado','Registrado'],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.entregado',1],
                                    ['salidas.estado','Registrado'],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.entregado',1],
                                    ['salidas.estado','Registrado']])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);

                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.pagado',0],
                                        ['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.pagado',0],
                                        ['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado']])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }
                    }elseif($criterio == "user"){
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.pagado',0],
                                    ['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.pagado',0],
                                    ['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado']])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }
                    }else{
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([ ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([ ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([ ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([ ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.pagado',0],
                                    ['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.pagado',0],
                                    ['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado']])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }
                    }
                }
            }
        }else{
            if($estadoS == "Anulada"){
                if($criterio == "cliente"){
                    $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                    ->join('users','salidas.idusuario','=','users.id')
                    ->leftJoin('personas as vendedores','vendedores.id','users.id')
                    ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                    'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                    'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                    'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                    'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                    'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                    'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                    ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado',$estadoS],
                        ['salidas.idusuario',$usid]])
                    ->orderBy('salidas.id', 'desc')->paginate(12);
                }elseif($criterio == "user"){
                    $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                    ->join('users','salidas.idusuario','=','users.id')
                    ->leftJoin('personas as vendedores','vendedores.id','users.id')
                    ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                    'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                    'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                    'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                    'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                    'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                    'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                    ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado',$estadoS],
                        ['salidas.idusuario',$usid]])
                    ->orderBy('salidas.id', 'desc')->paginate(12);
                }else{
                    if ($buscar==''){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                        'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                        'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                        'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                        'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                        'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                        'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([['salidas.estado',$estadoS],['salidas.idusuario',$usid]])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }
                    else{
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                        'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                        'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                        'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                        'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                        'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                        'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([
                            ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['salidas.estado',$estadoS],
                            ['salidas.idusuario',$usid]
                        ])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if($buscar==''){
                    if($entregaEs == 'entregado'){
                        if($pagadoEs == 'pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',1],['salidas.idusuario',$usid],
                                ['salidas.pagado',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',1],['salidas.idusuario',$usid],
                                ['salidas.pagado',0],['salidas.pago_parcial',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',1],['salidas.idusuario',$usid],
                                ['salidas.pagado',0],['salidas.pago_parcial',0]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }else{
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.entregado',1],['salidas.idusuario',$usid]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'entrega_parcial'){
                        if($pagadoEs == 'pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([ ['salidas.estado','Registrado'],['salidas.entrega_parcial',1],
                                ['salidas.idusuario',$usid],['salidas.pagado',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([ ['salidas.estado','Registrado'],['salidas.entrega_parcial',1],
                                ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([ ['salidas.estado','Registrado'],['salidas.entrega_parcial',1],
                                ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',0]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }else{
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([ ['salidas.estado','Registrado'],['salidas.entrega_parcial',1],
                                ['salidas.idusuario',$usid]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'no_entregado'){
                        if($pagadoEs == 'pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'], ['salidas.entregado',0],['salidas.entrega_parcial',0],
                                ['salidas.idusuario',$usid],['salidas.pagado',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'], ['salidas.entregado',0],['salidas.entrega_parcial',0],
                                ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'], ['salidas.entregado',0],['salidas.entrega_parcial',0],
                                ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',0]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }else{
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'], ['salidas.entregado',0],['salidas.entrega_parcial',0],
                                ['salidas.idusuario',$usid]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }else{
                        if($pagadoEs == 'pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.idusuario',$usid],['salidas.pagado',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.idusuario',$usid],
                                ['salidas.pagado',0],['salidas.pago_parcial',1]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.idusuario',$usid],
                                ['salidas.pagado',0],['salidas.pago_parcial',0]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }else{
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([['salidas.estado','Registrado'],['salidas.idusuario',$usid]])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }
                }
                else{
                    if($criterio == "cliente"){
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'], ['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.idusuario',$usid],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'], ['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'], ['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'], ['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([ ['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.idusuario',$usid],
                                    ['salidas.estado','Registrado'],['salidas.entrega_parcial',1],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([ ['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.idusuario',$usid],
                                    ['salidas.estado','Registrado'],['salidas.entrega_parcial',1],['salidas.pagado',0],
                                    ['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([ ['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.idusuario',$usid],
                                    ['salidas.estado','Registrado'],['salidas.entrega_parcial',1],['salidas.pagado',0],
                                    ['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([ ['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.idusuario',$usid],
                                    ['salidas.estado','Registrado'],['salidas.entrega_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.entregado',0],
                                    ['salidas.estado','Registrado'],['salidas.entrega_parcial',0],
                                    ['salidas.idusuario',$usid], ['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.entregado',0],
                                    ['salidas.estado','Registrado'],['salidas.entrega_parcial',0],
                                    ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.entregado',0],
                                    ['salidas.estado','Registrado'],['salidas.entrega_parcial',0],
                                    ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.entregado',0],
                                    ['salidas.estado','Registrado'],['salidas.entrega_parcial',0],
                                    ['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }
                    }elseif($criterio == "user"){
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.idusuario',$usid],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',1],['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.idusuario',$usid],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entrega_parcial',1],['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.idusuario',$usid],
                                    ['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.idusuario',$usid],
                                    ['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.idusuario',$usid],
                                    ['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.entregado',0],['salidas.entrega_parcial',0],['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }
                    }else{
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.entregado',1],
                                    ['salidas.estado','Registrado'],['salidas.idusuario',$usid],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.entregado',1],
                                    ['salidas.estado','Registrado'],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.entregado',1],
                                    ['salidas.estado','Registrado'],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.entregado',1],
                                    ['salidas.estado','Registrado'],['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.entrega_parcial',1],
                                    ['salidas.estado','Registrado'],['salidas.idusuario',$usid],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.entrega_parcial',1],
                                    ['salidas.estado','Registrado'],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.entrega_parcial',1],
                                    ['salidas.estado','Registrado'],['salidas.idusuario',$usid],['salidas.pagado',0],
                                    ['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.entrega_parcial',1],
                                    ['salidas.estado','Registrado'],['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.idusuario',$usid],
                                    ['salidas.entregado',0],['salidas.estado','Registrado'],['salidas.entrega_parcial',0],
                                    ['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.idusuario',$usid],
                                    ['salidas.entregado',0],['salidas.estado','Registrado'],['salidas.entrega_parcial',0],
                                    ['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.idusuario',$usid],
                                    ['salidas.entregado',0],['salidas.estado','Registrado'],['salidas.entrega_parcial',0],
                                    ['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.idusuario',$usid],
                                    ['salidas.entregado',0],['salidas.estado','Registrado'],['salidas.entrega_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid],['salidas.pagado',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',1]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid],['salidas.pagado',0],['salidas.pago_parcial',0]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }else{
                                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                                ->join('users','salidas.idusuario','=','users.id')
                                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                                ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.estado','Registrado'],
                                    ['salidas.idusuario',$usid]])
                                ->orderBy('salidas.id', 'desc')->paginate(12);
                            }
                        }
                    }
                }
            }
        }

        return [
            'pagination' => [
                'total'        => $salidas->total(),
                'current_page' => $salidas->currentPage(),
                'per_page'     => $salidas->perPage(),
                'last_page'    => $salidas->lastPage(),
                'from'         => $salidas->firstItem(),
                'to'           => $salidas->lastItem(),
            ],
            'salidas' => $salidas,
            'userrol' => $usrol,
            'usid' => $usid
        ];
    }
    public function store(Request $request){

        if(!$request->ajax()) return redirect('/');

        /* return ['observacion' => $request->observacion]; */

        $mytime = Carbon::now('America/Mexico_City');

        try{
            DB::beginTransaction();

            $salida = new Salida();
            $salida->idcliente = $request->idcliente;
            $salida->idusuario = \Auth::user()->id;
            $salida->tipo_comprobante = $request->tipo_comprobante;
            $salida->num_comprobante = $request->num_comprobante;
            $salida->fecha_hora = $mytime;
            $salida->impuesto = $request->impuesto;
            $salida->total = $request->total;
            $salida->adeudo = $request->total;
            $salida->forma_pago = $request->forma_pago;
            $salida->lugar_entrega = $request->lugar_entrega;
            $salida->tiempo_entrega = $request->tiempo_entrega;

            $salida->tipo_facturacion = $request->tipo_facturacion;
            $salida->observacion = $request->observacion;
            $salida->estado = 'Registrado';

            $salida->save();

            $detalles = $request->data;//Array de detalles

            //Recorro todos los elementos
            foreach($detalles as $ep=>$det)
            {
                $articulo = Herramienta::where('codigo','=',$det['codigo'])->select('id')->first();

                $detalle = new DetalleSalida();
                $detalle->idsalida = $salida->id;
                $detalle->idarticulo = $articulo->id;
                $detalle->cantidad = $det['cantidad'];
                $detalle->precio = $det['precio'];
                $detalle->descuento = $det['descuento'];
                $detalle->save();

            }

            DB::commit();

            $printid = $salida->id;

            return ['salida' => $printid];


        }catch(Exception $e){

            DB::rollBack();

        }
    }
    public function desactivar(Request $request){
        if (!$request->ajax()) return redirect('/');
       /*  $salida = Salida::findOrFail($request->id);
        $salida->estado = 'Anulado';
        $salida->save(); */

        try{
            DB::beginTransaction();

            $salida = Salida::findOrFail($request->id);
            $salida->estado = 'Anulado';
            $salida->entregado = 0;
            $salida->entregado_parcial = 0;
            $salida->pagado = 0;
            $salida->pagado_parcial = 0;
            $salida->facturado = 0;
            $salida->factura_env = 0;
            $salida->num_factura = null;
            $salida->save();

            $detalles = DetalleSalida::select('idarticulo','cantidad')
                ->where('idsalida',$request->id)->get();

            foreach($detalles as $ep=>$det){
                $herramienta = Herramienta::findOrFail($det['idarticulo']);
                $herramienta->stock += $det['cantidad'];
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

        $salida = Salida::join('personas','salidas.idcliente','=','personas.id')
        ->join('users','salidas.idusuario','=','users.id')
        ->leftJoin('personas as vendedores','vendedores.id','users.id')
        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
            'salidas.forma_pago','personas.nombre as cliente','salidas.observacion',
            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
            'salidas.factura_env','vendedores.nombre as vendedor','personas.rfc',
            'personas.email','salidas.adeudo','salidas.auto_entrega')
        ->where('salidas.id',$id)
        ->orderBy('salidas.id', 'desc')->take(1)->get();

        return ['salida' => $salida];
    }
    public function obtenerDetalles(Request $request){

        if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $detalles = DetalleSalida::join('herramientas','detalle_salidas.idarticulo','=','herramientas.id')
        ->select('detalle_salidas.cantidad','detalle_salidas.precio','detalle_salidas.descuento',
        'herramientas.sku','herramientas.codigo','herramientas.descripcion','herramientas.ubicacion',
        'herramientas.file')
        ->where('detalle_salidas.idsalida',$id)->get();
        return ['detalles' => $detalles];
    }
    public function getLastNum(Request $request){
        if (!$request->ajax()) return redirect('/');
        $lastNum = Salida::select('num_comprobante')->get()->last();
        if($lastNum != null){
            $noComp = explode('"',$lastNum);
            $SigNum = explode("-",$noComp[3]);
            return  $SigNum[2] + 1;
        }
        else{
            return 1;
        }
    }
    public function pdf(Request $request,$id){

        $salida = Salida::join('personas','salidas.idcliente','=','personas.id')
        ->join('users','salidas.idusuario','=','users.id')
        ->leftJoin('personas as vendedores','vendedores.id','users.id')
        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
        ->where('salidas.id',$id)
        ->orderBy('salidas.id', 'desc')->take(1)->get();

        $detalles = DetalleSalida::join('herramientas','detalle_salidas.idarticulo','=','herramientas.id')
        ->select('detalle_salidas.cantidad','detalle_salidas.precio','detalle_salidas.descuento',
        'herramientas.sku as articulo','herramientas.codigo','herramientas.descripcion','herramientas.ubicacion',
        'herramientas.file')
        ->where('detalle_salidas.idsalida',$id)->get();


        $numventa = Salida::select('num_comprobante')->where('id',$id)->get();

        $ivaagregado = Salida::select('impuesto')->where('id',$id)->get();

        $salidaD = Salida::findOrFail($id); //ID venta y sus depositos
        $deposits = $salidaD->deposits()
        ->select(DB::raw('SUM(deposits.total) as abonos'))
        ->get();

        $capturas = Deposit::with('venta')
        ->select('deposits.total','deposits.forma_pago','deposits.depositable_id','deposits.fecha_hora')
        ->where('deposits.depositable_id',$id)
        ->get();

        $pdf = \PDF::loadView('pdf.salidas',['venta' => $salida,'detalles'=>$detalles,'ivaVenta' =>$ivaagregado[0]->impuesto,
        'abonos' => $deposits[0]->abonos,'capturas' =>$capturas]);

        return $pdf->stream('salidas-'.$numventa[0]->num_comprobante.'.pdf');
    }
    public function actualizarObservacion(Request $request){
        if (!$request->ajax()) return redirect('/');
        $salida = Salida::findOrFail($request->id);
        $salida->observacion = $request->observacion;
        $salida->save();
    }
    public function indexFacturacion(Request $request){

        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $estado = $request->statusSalida;

        if($estado == ''){
            if($buscar == ''){
                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                ->join('users','salidas.idusuario','=','users.id')
                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                    'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                    'salidas.forma_pago','salidas.observacion','salidas.tiempo_entrega',
                    'salidas.lugar_entrega','salidas.tipo_facturacion','salidas.pagado',
                    'salidas.pagado_parcial','salidas.entregado','salidas.entregado_parcial',
                    'salidas.facturado','salidas.num_factura','salidas.factura_env',
                    'vendedores.nombre as vendedor','personas.nombre','users.usuario',
                    'personas.rfc','personas.email')
                ->where([['salidas.tipo_facturacion','Cliente'],['salidas.estado','Registrado']])
                ->orderBy('salidas.id', 'desc')->paginate(12);
            }else{
                if($criterio == 'cliente'){
                    $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                    ->join('users','salidas.idusuario','=','users.id')
                    ->leftJoin('personas as vendedores','vendedores.id','users.id')
                    ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                        'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                        'salidas.forma_pago','salidas.observacion','salidas.tiempo_entrega',
                        'salidas.lugar_entrega','salidas.tipo_facturacion','salidas.pagado',
                        'salidas.pagado_parcial','salidas.entregado','salidas.entregado_parcial',
                        'salidas.facturado','salidas.num_factura','salidas.factura_env',
                        'vendedores.nombre as vendedor','personas.nombre','users.usuario',
                        'personas.rfc','personas.email')
                    ->where([['personas.nombre', 'like', '%'. $buscar . '%'],
                        ['salidas.tipo_facturacion','Cliente'],['salidas.estado','Registrado']])
                    ->orderBy('salidas.id', 'desc')->paginate(12);
                }else{
                    $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                    ->join('users','salidas.idusuario','=','users.id')
                    ->leftJoin('personas as vendedores','vendedores.id','users.id')
                    ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                        'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                        'salidas.forma_pago','salidas.observacion','salidas.tiempo_entrega',
                        'salidas.lugar_entrega','salidas.tipo_facturacion','salidas.pagado',
                        'salidas.pagado_parcial','salidas.entregado','salidas.entregado_parcial',
                        'salidas.facturado','salidas.num_factura','salidas.factura_env',
                        'vendedores.nombre as vendedor','personas.nombre','users.usuario',
                        'personas.rfc','personas.email')
                    ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['salidas.tipo_facturacion','Cliente'],['salidas.estado','Registrado']])
                    ->orderBy('salidas.id', 'desc')->paginate(12);
                }
            }
        }elseif($estado == 'facturado'){
            if($buscar == ''){
                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                ->join('users','salidas.idusuario','=','users.id')
                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                    'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                    'salidas.forma_pago','salidas.observacion','salidas.tiempo_entrega',
                    'salidas.lugar_entrega','salidas.tipo_facturacion','salidas.pagado',
                    'salidas.pagado_parcial','salidas.entregado','salidas.entregado_parcial',
                    'salidas.facturado','salidas.num_factura','salidas.factura_env',
                    'vendedores.nombre as vendedor','personas.nombre','users.usuario',
                    'personas.rfc','personas.email')
                ->where([['salidas.facturado',1],['salidas.tipo_facturacion','Cliente'],
                    ['salidas.estado','Registrado']])
                ->orderBy('salidas.id', 'desc')->paginate(12);
            }else{
                if($criterio == 'cliente'){
                    $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                    ->join('users','salidas.idusuario','=','users.id')
                    ->leftJoin('personas as vendedores','vendedores.id','users.id')
                    ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                        'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                        'salidas.forma_pago','salidas.observacion','salidas.tiempo_entrega',
                        'salidas.lugar_entrega','salidas.tipo_facturacion','salidas.pagado',
                        'salidas.pagado_parcial','salidas.entregado','salidas.entregado_parcial',
                        'salidas.facturado','salidas.num_factura','salidas.factura_env',
                        'vendedores.nombre as vendedor','personas.nombre','users.usuario',
                        'personas.rfc','personas.email')
                    ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.facturado',1],
                        ['salidas.tipo_facturacion','Cliente'],['salidas.estado','Registrado']])
                    ->orderBy('salidas.id', 'desc')->paginate(12);
                }else{
                    $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                    ->join('users','salidas.idusuario','=','users.id')
                    ->leftJoin('personas as vendedores','vendedores.id','users.id')
                    ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                        'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                        'salidas.forma_pago','salidas.observacion','salidas.tiempo_entrega',
                        'salidas.lugar_entrega','salidas.tipo_facturacion','salidas.pagado',
                        'salidas.pagado_parcial','salidas.entregado','salidas.entregado_parcial',
                        'salidas.facturado','salidas.num_factura','salidas.factura_env',
                        'vendedores.nombre as vendedor','personas.nombre','users.usuario',
                        'personas.rfc','personas.email')
                    ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.facturado',1],
                        ['salidas.tipo_facturacion','Cliente'],['salidas.estado','Registrado']])
                    ->orderBy('salidas.id', 'desc')->paginate(12);
                }
            }
        }elseif($estado == 'nofacturado'){
            if($buscar == ''){
                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                ->join('users','salidas.idusuario','=','users.id')
                ->leftJoin('personas as vendedores','vendedores.id','users.id')
                ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                    'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                    'salidas.forma_pago','salidas.observacion','salidas.tiempo_entrega',
                    'salidas.lugar_entrega','salidas.tipo_facturacion','salidas.pagado',
                    'salidas.pagado_parcial','salidas.entregado','salidas.entregado_parcial',
                    'salidas.facturado','salidas.num_factura','salidas.factura_env',
                    'vendedores.nombre as vendedor','personas.nombre','users.usuario',
                    'personas.rfc','personas.email')
                ->where([['salidas.facturado',0],['salidas.tipo_facturacion','Cliente'],
                    ['salidas.estado','Registrado']])
                ->orderBy('salidas.id', 'desc')->paginate(12);
            }else{
                if($criterio == 'cliente'){
                    $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                    ->join('users','salidas.idusuario','=','users.id')
                    ->leftJoin('personas as vendedores','vendedores.id','users.id')
                    ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                        'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                        'salidas.forma_pago','salidas.observacion','salidas.tiempo_entrega',
                        'salidas.lugar_entrega','salidas.tipo_facturacion','salidas.pagado',
                        'salidas.pagado_parcial','salidas.entregado','salidas.entregado_parcial',
                        'salidas.facturado','salidas.num_factura','salidas.factura_env',
                        'vendedores.nombre as vendedor','personas.nombre','users.usuario',
                        'personas.rfc','personas.email')
                    ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['salidas.facturado',0],
                        ['salidas.tipo_facturacion','Cliente'],['salidas.estado','Registrado']])
                    ->orderBy('salidas.id', 'desc')->paginate(12);
                }else{
                    $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                    ->join('users','salidas.idusuario','=','users.id')
                    ->leftJoin('personas as vendedores','vendedores.id','users.id')
                    ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                        'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                        'salidas.forma_pago','salidas.observacion','salidas.tiempo_entrega',
                        'salidas.lugar_entrega','salidas.tipo_facturacion','salidas.pagado',
                        'salidas.pagado_parcial','salidas.entregado','salidas.entregado_parcial',
                        'salidas.facturado','salidas.num_factura','salidas.factura_env',
                        'vendedores.nombre as vendedor','personas.nombre','users.usuario',
                        'personas.rfc','personas.email')
                    ->where([['salidas.'.$criterio, 'like', '%'. $buscar . '%'],['salidas.facturado',0],
                        ['salidas.tipo_facturacion','Cliente'],['salidas.estado','Registrado']])
                    ->orderBy('salidas.id', 'desc')->paginate(12);
                }
            }
        }

        return [
            'pagination' => [
                'total'        => $salidas->total(),
                'current_page' => $salidas->currentPage(),
                'per_page'     => $salidas->perPage(),
                'last_page'    => $salidas->lastPage(),
                'from'         => $salidas->firstItem(),
                'to'           => $salidas->lastItem(),
            ],
            'facturas' => $salidas
        ];
    }
    public function cambiarFacturacion(Request $request){
        if (!$request->ajax()) return redirect('/');
        $salida = Salida::findOrFail($request->id);
        if($request->estado == 0){
            $salida->facturado = 0;
            $salida->factura_env = 0;
            $salida->num_factura = null;
        }else{
            $salida->facturado = $request->estado;
            $salida->num_factura = $request->numFact;
            $salida->factura_env = 0;
        }
        $salida->save();
    }
    public function cambiarFacturacionEnv(Request $request){
        if (!$request->ajax()) return redirect('/');
        $salida = Salida::findOrFail($request->id);
        $salida->factura_env = $request->estadoEn;
        $salida->save();
    }
    public function cambiarEntrega(Request $request){

        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $salida = Salida::findOrFail($request->id);
            $salida->entregado = $request->entregado;
            $salida->entrega_parcial = 0;
            $salida->save();

            $detalles = DetalleSalida::where('idsalida',$salida->id)->get();

            if($request->entregado == 1){
                foreach($detalles as $ep=>$det){
                    $detail = DetalleSalida::findOrFail($det['id']);
                    $detail->entregadas = $det['pendientes'];
                    $detail->pendientes = $det['pendientes']-$det['por_entregar'];
                    $detail->completado = 1;
                    $detail->save();
                }
            }else{
                foreach($detalles as $ep=>$det){
                    $detail = DetalleSalida::findOrFail($det['id']);
                    $detail->entregadas = 0;
                    $detail->pendientes = $det['por_entregar'];
                    $detail->completado = 0;
                    $detail->save();
                }
            }
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }

        /* return ['detalles' => $detalles]; */

    }
    public function cambiarEntregaParcial(Request $request){

        if (!$request->ajax()) return redirect('/');

        $salida = Salida::findOrFail($request->id);
        $salida->entrega_parcial = $request->entrega_parcial;
        $salida->entregado = 0;
        $salida->save();
    }
    public function cambiarPagado(Request $request){
        if (!$request->ajax()) return redirect('/');

        /* $pag_par = 0; */

        if($request->pagado == 1){
            $salida = Salida::findOrFail($request->id);
            $salida->pagado = $request->pagado;
            $salida->adeudo = 0;
            $salida->auto_entrega = 1;
            $salida->save();
        }else{
            $salida = Salida::findOrFail($request->id);
            $salida->pagado = $request->pagado;
            $salida->adeudo = $salida->total;
            $salida->auto_entrega = 0;
            $salida->save();
        }


    }
    public function crearDeposit(Request $request){

        $salida = Salida::findOrFail($request->id);

        $mytime = Carbon::now('America/Mexico_City');
        $adeudoAct = $salida->adeudo;

        if($request->total < $adeudoAct){
            try{
                DB::beginTransaction();
                $salida->adeudo = $salida->adeudo - $request->total;
                $salida->pago_parcial = 1;
                $salida->pagado = 0;
                $salida->save();
                $deposit = new Deposit(['total' => $request->total, 'fecha_hora' => $mytime,
                'forma_pago' => $request->forma_pago]);
                $salida->deposits()->save($deposit);
                DB::commit();

            }catch(Exception $e){
                DB::rollBack();
            }
        }elseif($request->total == $adeudoAct){
            try{
                DB::beginTransaction();
                $salida->adeudo = 0;
                $salida->pago_parcial = 1;
                $salida->pagado = 1;
                $salida->auto_entrega = 1;
                $salida->save();
                $deposit = new Deposit(['total' => $request->total,'fecha_hora' => $mytime,
                    'forma_pago' => $request->forma_pago]);
                $salida->deposits()->save($deposit);
                DB::commit();
            }catch(Exception $e){
                DB::rollBack();
            }
        }
    }
    public function deleteDeposit(Request $request){

        //if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();

            $deposit = Deposit::findOrFail($request->id);

            if($deposit->forma_pago == 'Nota de crédito'){
                $creditos = $deposit->credits()->select('credits.id')->get();
                foreach($creditos as $det){
                    $credit = Credit::findOrFail($det['id']);
                    $credit->estado = 'Vigente';
                    $credit->save();
                }
                $deposit->delete();
            }else{
                $deposit->delete();
            }
            $salida = Salida::findOrFail($request->idsalida);
            $numDeposits = $salida->deposits()->count();

            if($numDeposits <= 0){
                $salida->pago_parcial = 0;
                $salida->pagado = 0;
                $salida->adeudo = $salida->total;
                $salida->save();
            }else{
                $salida->adeudo = $salida->adeudo + $request->total;
                $salida->save();
            }
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function autorizarEntrega(Request $request){
        if(!$request->ajax()) return redirect('/');

        $salida = Salida::findOrFail($request->id);
        $salida->auto_entrega = $request->auto_entrega;
        $salida->save();
    }
    public function obtenerDepositos(Request $request){

       $id = $request->id;
       $depositos = Deposit::with('salida')
            ->join('salidas','deposits.depositable_id','=','salidas.id')
            ->select('deposits.id','deposits.total','deposits.fecha_hora as fecha','deposits.forma_pago','salidas.id')
            ->where('deposits.depositable_id','=',$id)
            ->get();

            return ['depositos' => $depositos];


    }
    public function cambiarPagadoParcial(Request $request){
        if (!$request->ajax()) return redirect('/');

        /* $pag_par = 0; */

        if($request->pago_parcial == 1){
            $salida = Salida::findOrFail($request->id);
            $salida->pagado = 0;
            $salida->adeudo = $request->total;
            $salida->auto_entrega = 1;
            $salida->save();
        }else{
            $salida = Salida::findOrFail($request->id);
            $salida->pagado = $request->pagado;
            $salida->adeudo = $salida->total;
            $salida->auto_entrega = 0;
            $salida->save();
        }


    }
    public function getDeposits(Request $request){

        //if (!$request->ajax()) return redirect('/');

        $salida = Salida::findOrFail($request->id);
        $id = $request->id; //ID salida y sus depositos

        $deposits = $salida->deposits()
        ->join('salidas','deposits.depositable_id','=','salidas.id')
        ->select('deposits.id','deposits.total','deposits.fecha_hora as fecha','deposits.forma_pago','deposits.depositable_type',
        'deposits.depositable_id')
        ->where('deposits.depositable_id','=',$id)
        ->orderBy('deposits.fecha_hora','desc')
        ->get();

        /* $tot = $venta->deposits()->count(); */

        return [
            'abonos' => $deposits,
            /* 'total'  => $tot */
        ];
    }
    public function ListarExcel(Request $request){
        $inicio = $request->inicio;
        $fin = $request->fin;
        $usuarios = $request->usuarios;
        $ArrUsuarios = explode(",",$usuarios);

        return Excel::download(new SalidasExport($inicio,$fin,$ArrUsuarios), 'presupuestos-'.$inicio.'-'.$fin.'.xlsx');
    }
    public function ListarExcelDet(Request $request){
        $inicio = $request->inicio;
        $fin = $request->fin;
        $usuarios = $request->usuarios;
        $ArrUsuarios = explode(",",$usuarios);
        return Excel::download(new SalidasExportDet($inicio,$fin,$ArrUsuarios), 'DetallePresupuestos-'.$inicio.'-'.$fin.'.xlsx');
    }
    public function indexDeposit(Request $request){
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $estadoV = $request->estado;
        $entregaEs = $request->estadoEntrega;
        $usrol = \Auth::user()->idrol;
        $usid = \Auth::user()->id;
        $estadoAdeu = $request->estadoAdeudo;

        if($estadoV == "Anulada"){
            if($criterio == "cliente"){
                $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                    ->join('users','salidas.idusuario','=','users.id')
                    ->leftJoin('personas as vendedores','vendedores.id','users.id')
                    ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                        'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                        'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                        'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                        'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                        'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                        'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                ->where([
                    ['personas.nombre', 'like', '%'. $buscar . '%'],
                    ['salidas.estado',$estadoV]
                ])
                ->orderBy('salidas.id', 'desc')->paginate(12);
            }else{
                if ($buscar==''){
                    $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                    ->join('users','salidas.idusuario','=','users.id')
                    ->leftJoin('personas as vendedores','vendedores.id','users.id')
                    ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                        'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                        'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                        'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                        'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                        'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                        'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                    ->where('salidas.estado',$estadoV)
                    ->orderBy('salidas.id', 'desc')->paginate(12);
                }else{
                    $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                    ->join('users','salidas.idusuario','=','users.id')
                    ->leftJoin('personas as vendedores','vendedores.id','users.id')

                    ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                        'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                        'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                        'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                        'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                        'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                        'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                    ->where([
                        ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['salidas.estado',$estadoV]
                    ])
                    ->orderBy('salidas.id', 'desc')->paginate(12);
                }
            }
        }else{
            if($buscar==''){
                if($entregaEs == 'entregado'){
                    if($estadoAdeu == ''){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([
                            ['salidas.estado','Registrado'],
                            ['salidas.entregado',1]
                        ])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Pagado'){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([
                            ['salidas.estado','Registrado'],
                            ['salidas.entregado',1],
                            ['salidas.pagado',1],['salidas.adeudo',0]
                        ])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Abonado'){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([
                            ['salidas.estado','Registrado'],
                            ['salidas.entregado',1],
                            ['salidas.pago_parcial',1]
                        ])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'NoAbono'){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([
                            ['salidas.estado','Registrado'],
                            ['salidas.entregado',1],
                            ['salidas.adeudo','=','salidas.total']
                        ])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }
                }elseif($entregaEs == 'entrega_parcial'){
                    if($estadoAdeu == ''){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([
                            ['salidas.estado','Registrado'],
                            ['salidas.entrega_parcial',1]
                        ])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Pagado'){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([
                            ['salidas.estado','Registrado'],
                            ['salidas.entrega_parcial',1],
                            ['salidas.pagado',1],['salidas.adeudo',0]
                        ])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Abonado'){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([
                            ['salidas.estado','Registrado'],
                            ['salidas.entrega_parcial',1],
                            ['salidas.pago_parcial',1]
                        ])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'NoAbono'){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([
                            ['salidas.estado','Registrado'],
                            ['salidas.entrega_parcial',1],
                            ['salidas.adeudo','=','salidas.total']
                        ])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }
                }elseif($entregaEs == 'no_entregado'){
                    if($estadoAdeu == ''){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([
                            ['salidas.estado','Registrado'],
                            ['salidas.entregado',0],
                            ['salidas.entrega_parcial',0]
                        ])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Pagado'){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([
                            ['salidas.estado','Registrado'],
                            ['salidas.entregado',0],
                            ['salidas.entrega_parcial',0],
                            ['salidas.pagado',1],['salidas.adeudo',0]
                        ])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Abonado'){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([
                            ['salidas.estado','Registrado'],
                            ['salidas.entregado',0],
                            ['salidas.entrega_parcial',0],
                            ['salidas.pago_parcial',1]

                        ])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'NoAbono'){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([
                            ['salidas.estado','Registrado'],
                            ['salidas.entregado',0],
                            ['salidas.entrega_parcial',0],
                            ['salidas.adeudo','=','salidas.total']
                        ])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }
                }else{
                    if($estadoAdeu == ''){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([['salidas.estado','Registrado']])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Pagado'){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([['salidas.estado','Registrado'],['salidas.pagado',1],['salidas.adeudo',0]])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Abonado'){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([['salidas.estado','Registrado'],['salidas.pago_parcial',1]])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'NoAbono'){
                        $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                        ->join('users','salidas.idusuario','=','users.id')
                        ->leftJoin('personas as vendedores','vendedores.id','users.id')
                        ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                            'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                            'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                            'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                            'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                            'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                            'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                        ->where([['salidas.estado','Registrado'],['salidas.total','salidas.adeudo']])
                        ->orderBy('salidas.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if($criterio == "cliente"){
                    if($entregaEs == 'entregado'){
                        if($estadoAdeu == ''){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entregado',1]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entregado',1],
                                ['salidas.pagado',1],['salidas.adeudo',0]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entregado',1],
                                ['salidas.pago_parcial',1]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entregado',1],
                                ['salidas.adeudo','=','salidas.total']
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }

                    }elseif($entregaEs == 'entrega_parcial'){
                        if($estadoAdeu == ''){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entrega_parcial',1]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entrega_parcial',1],
                                ['salidas.pagado',1],['salidas.adeudo',0]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);

                        }elseif($estadoAdeu == 'Abonado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entrega_parcial',1],
                                ['salidas.pago_parcial',1]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);

                        }elseif($estadoAdeu == 'NoAbono'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entrega_parcial',1],
                                ['salidas.adeudo','=','salidas.total']
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'no_entregado'){
                        if($estadoAdeu == ''){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entregado',0],
                                ['salidas.entrega_parcial',0]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entregado',0],
                                ['salidas.entrega_parcial',0],
                                ['salidas.pagado',1],['salidas.adeudo',0]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entregado',0],
                                ['salidas.entrega_parcial',0],
                                ['salidas.pago_parcial',1]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entregado',0],
                                ['salidas.entrega_parcial',0],
                                ['salidas.adeudo','=','salidas.total']
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }else{
                        if($estadoAdeu == ''){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado']
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.pagado',1],['salidas.adeudo',0]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.pago_parcial',1]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.adeudo','=','salidas.total']
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }
                }else{
                    if($entregaEs == 'entregado'){
                        if($estadoAdeu == ''){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entregado',1]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entregado',1],
                                ['salidas.pagado',1],['salidas.adeudo',0]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entregado',1],
                                ['salidas.pago_parcial',1]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entregado',1],
                                ['salidas.adeudo','=','salidas.total']
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'entrega_parcial'){
                        if($estadoAdeu == ''){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entrega_parcial',1]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entrega_parcial',1],
                                ['salidas.pagado',1],['salidas.adeudo',0]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entrega_parcial',1],
                                ['salidas.pago_parcial',1]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entrega_parcial',1],
                                ['salidas.adeudo','=','salidas.total']
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'no_entregado'){
                        if($estadoAdeu == ''){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entregado',0],
                                ['salidas.entrega_parcial',0]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entregado',0],
                                ['salidas.entrega_parcial',0],
                                ['salidas.pagado',1],['salidas.adeudo',0]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entregado',0],
                                ['salidas.entrega_parcial',0],
                                ['salidas.pago_parcial',1]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.entregado',0],
                                ['salidas.entrega_parcial',0],
                                ['salidas.adeudo','=','salidas.total']
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }else{
                        if($estadoAdeu == ''){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado']
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.pagado',1],['salidas.adeudo',0]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.pago_parcial',1]
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $salidas = Salida::join('personas','salidas.idcliente','=','personas.id')
                            ->join('users','salidas.idusuario','=','users.id')
                            ->leftJoin('personas as vendedores','vendedores.id','users.id')
                            ->select('salidas.id','salidas.tipo_comprobante','salidas.num_comprobante',
                                'salidas.fecha_hora','salidas.impuesto','salidas.total','salidas.estado',
                                'salidas.forma_pago','personas.nombre','users.usuario','salidas.observacion',
                                'salidas.tiempo_entrega','salidas.lugar_entrega','salidas.tipo_facturacion',
                                'salidas.pagado','salidas.pagado_parcial','salidas.entregado',
                                'salidas.entregado_parcial','salidas.facturado','salidas.num_factura',
                                'salidas.factura_env','vendedores.nombre as vendedor','salidas.adeudo','salidas.auto_entrega')
                            ->where([
                                ['salidas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['salidas.estado','Registrado'],
                                ['salidas.adeudo','=','salidas.total']
                            ])
                            ->orderBy('salidas.id', 'desc')->paginate(12);
                        }
                    }
                }
            }
        }

        return [
            'pagination' => [
                'total'        => $salidas->total(),
                'current_page' => $salidas->currentPage(),
                'per_page'     => $salidas->perPage(),
                'last_page'    => $salidas->lastPage(),
                'from'         => $salidas->firstItem(),
                'to'           => $salidas->lastItem(),
            ],
            'salidas' => $salidas,
            'userrol' => $usrol
        ];
    }
    public function crearDepositCredit(Request $request){

        if (!$request->ajax()) return redirect('/');

        $mytime = Carbon::now('America/Mexico_City');

        $salida = Salida::findOrFail($request->id); //Venta a depositar
        $adeudoAct = $salida->adeudo;

        $creditos = $request->creditos;

        if($request->total < $adeudoAct){
            try{
                DB::beginTransaction();
                $salida->adeudo = $salida->adeudo - $request->total;
                $salida->pago_parcial = 1;
                $salida->pagado = 0;
                $salida->save();
                $deposit = new Deposit(['total' => $request->total,'fecha_hora' => $mytime,
                    'forma_pago' => $request->forma_pago]);
                $salida->deposits()->save($deposit);

                $deposit->credits()->attach($creditos);

                foreach($creditos as $det){
                    $credit = Credit::findOrFail($det);
                    $credit->estado = 'Abonada';
                    $credit->save();
                }

                DB::commit();

            }catch(Exception $e){
                DB::rollBack();
            }
        }elseif($request->total == $adeudoAct){
            try{
                DB::beginTransaction();
                $salida->adeudo = 0;
                $salida->pago_parcial = 1;
                $salida->pagado = 1;
                $salida->auto_entrega = 1;
                $salida->save();
                $deposit = new Deposit(['total' => $request->total,'fecha_hora' => $mytime,
                    'forma_pago' => $request->forma_pago]);
                $salida->deposits()->save($deposit);
                $deposit->credits()->attach($creditos);

                foreach($creditos as $det){
                    $credit = Credit::findOrFail($det);
                    $credit->estado = 'Abonada';
                    $credit->save();
                }

                DB::commit();
            }catch(Exception $e){
                DB::rollBack();
            }
        }

    }





}


