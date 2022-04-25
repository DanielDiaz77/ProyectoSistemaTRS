<?php

namespace App\Http\Controllers;

use App\Java;
use App\VentaJava;
use App\Credit;
use App\DetalleVentaJava;
use App\User;
use App\Deposit;
use App\Document;
use App\Exports\VentasClienteExport;
use App\Notifications\NotifyAdmin;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VentasExport;
use App\Exports\VentasExportDet;
use App\Exports\AbonosExport;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailPresupuesto;
use App\Mail\MailFactura;
use App\Persona;
use App\Fletero;
use App\Project;
use Exception;


class VentaJavaController extends Controller
{
    public function index(Request $request){
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $estadoV = $request->estado;
        $entregaEs = $request->estadoEntrega;
        $pagadoEs = $request->estadoPagado;
        $usrol = \Auth::user()->idrol;
        $usid = \Auth::user()->id;
        $usarea = \Auth::user()->area;

        if($usarea != 'SLP' && $usarea != 'AGS' && $usrol != 4){
            if($estadoV == "Anulada"){
                if($criterio == "cliente"){
                    $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                    ->join('users','venta_javas.idusuario','=','users.id')
                    ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                        'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                        'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                        'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                        'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                        'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                        'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega'
                        ,'special')
                    ->where([
                        ['personas.nombre', 'like', '%'. $buscar . '%'],
                        ['venta_javas.estado',$estadoV]
                    ])
                    ->orderBy('venta_javas.id', 'desc')->paginate(12);
                }elseif($criterio == "user"){
                    $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                    ->join('users','venta_javas.idusuario','=','users.id')
                    ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                        'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                        'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                        'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                        'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                        'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                        'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega'
                        ,'special')
                    ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado',$estadoV]])
                    ->orderBy('venta_javas.id', 'desc')->paginate(12);
                }else{
                    if ($buscar==''){
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                        'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                        'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                        'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                        'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                        'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                        'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega'
                        ,'special')
                        ->where('venta_javas.estado',$estadoV)
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }
                    else{
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                        'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                        'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                        'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                        'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                        'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                        'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega'
                        ,'special')
                        ->where([
                            ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['venta_javas.estado',$estadoV]
                        ])
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }
                }

            }else{
                if($buscar==''){
                    if($entregaEs == 'entregado'){
                        if($pagadoEs == 'pagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega'
                                ,'special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.entregado',1],
                                ['venta_javas.pagado',1]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega'
                                ,'special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.entregado',1],
                                ['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega'
                                ,'special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.entregado',1],
                                ['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }else{
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega'
                                ,'special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.entregado',1]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'entrega_parcial'){
                        if($pagadoEs == 'pagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',1],['venta_javas.pagado',1]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);

                        }elseif($pagadoEs == 'parcial'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',1],
                                ['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);

                        }elseif($pagadoEs == 'nopagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',1],
                                ['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }else{
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',1]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'no_entregado'){
                        if($pagadoEs == 'pagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.entregado',0],
                                ['venta_javas.entrega_parcial',0],['venta_javas.pagado',1]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.entregado',0],
                                ['venta_javas.entrega_parcial',0],['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.entregado',0],
                                ['venta_javas.entrega_parcial',0],['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }else{
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.entregado',0],['venta_javas.entrega_parcial',0]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }
                    }else{
                        if($pagadoEs == 'pagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.pagado',1]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }else{
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado']])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }
                    }
                }
                else{
                    if($criterio == "cliente"){
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.entregado',1],
                                    ['venta_javas.estado','Registrado'],['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.entregado',1],
                                    ['venta_javas.estado','Registrado'],['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.entregado',1],
                                    ['venta_javas.estado','Registrado'],['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.entregado',1],
                                    ['venta_javas.estado','Registrado']])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entrega_parcial',1],['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);

                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','users.area','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entrega_parcial',1],['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entrega_parcial',1],['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entrega_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0],['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0],['venta_javas.pagado',0],
                                        ['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0],['venta_javas.pagado',0],
                                        ['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado']])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }
                    }elseif($criterio == "user"){
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',1],['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',1],['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',1],['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entrega_parcial',1],['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entrega_parcial',1],['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entrega_parcial',1],['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entrega_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0],['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0],['venta_javas.pagado',0],
                                    ['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0],['venta_javas.pagado',0],
                                    ['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado']])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }
                    }else{
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([ ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',1],['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([ ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',1],['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([ ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',1],['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([ ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entrega_parcial',1],['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entrega_parcial',1],['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entrega_parcial',1],['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entrega_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0],['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0],['venta_javas.pagado',0],
                                    ['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0],['venta_javas.pagado',0],
                                    ['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado']])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }
                    }
                }
            }
        }else{
            if($estadoV == "Anulada"){
                if($criterio == "cliente"){
                    $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                    ->join('users','venta_javas.idusuario','=','users.id')
                    ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                        'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                        'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                        'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                        'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                        'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                        'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                    ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.estado',$estadoV],
                        ['venta_javas.idusuario',$usid]])
                    ->orderBy('venta_javas.id', 'desc')->paginate(12);
                }elseif($criterio == "user"){
                    $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                    ->join('users','venta_javas.idusuario','=','users.id')
                    ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                        'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                        'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                        'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                        'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                        'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                        'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                    ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado',$estadoV],
                        ['venta_javas.idusuario',$usid]])
                    ->orderBy('venta_javas.id', 'desc')->paginate(12);
                }else{
                    if ($buscar==''){
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                        ->where([['venta_javas.estado',$estadoV],['venta_javas.idusuario',$usid]])
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }
                    else{
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                        'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                        'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                        'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                        'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                        'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                        'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                        ->where([
                            ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['venta_javas.estado',$estadoV],
                            ['venta_javas.idusuario',$usid]
                        ])
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if($buscar==''){
                    if($entregaEs == 'entregado'){
                        if($pagadoEs == 'pagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.entregado',1],['venta_javas.idusuario',$usid],
                                ['venta_javas.pagado',1]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.entregado',1],['venta_javas.idusuario',$usid],
                                ['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.entregado',1],['venta_javas.idusuario',$usid],
                                ['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }else{
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.entregado',1],['venta_javas.idusuario',$usid]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'entrega_parcial'){
                        if($pagadoEs == 'pagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([ ['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',1],
                                ['venta_javas.idusuario',$usid],['venta_javas.pagado',1]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([ ['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',1],
                                ['venta_javas.idusuario',$usid],['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([ ['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',1],
                                ['venta_javas.idusuario',$usid],['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }else{
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([ ['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',1],
                                ['venta_javas.idusuario',$usid]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'no_entregado'){
                        if($pagadoEs == 'pagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'], ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0],
                                ['venta_javas.idusuario',$usid],['venta_javas.pagado',1]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'], ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0],
                                ['venta_javas.idusuario',$usid],['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'], ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0],
                                ['venta_javas.idusuario',$usid],['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }else{
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'], ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0],
                                ['venta_javas.idusuario',$usid]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }
                    }else{
                        if($pagadoEs == 'pagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.idusuario',$usid],['venta_javas.pagado',1]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.idusuario',$usid],
                                ['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.idusuario',$usid],
                                ['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }else{
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                            ->where([['venta_javas.estado','Registrado'],['venta_javas.idusuario',$usid]])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }
                    }
                }
                else{
                    if($criterio == "cliente"){
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'], ['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',1],['venta_javas.idusuario',$usid],['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'], ['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',1],['venta_javas.idusuario',$usid],['venta_javas.pagado',0],
                                    ['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'], ['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',1],['venta_javas.idusuario',$usid],['venta_javas.pagado',0],
                                    ['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'], ['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',1],['venta_javas.idusuario',$usid]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([ ['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.idusuario',$usid],
                                    ['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',1],['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([ ['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.idusuario',$usid],
                                    ['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',1],['venta_javas.pagado',0],
                                    ['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([ ['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.idusuario',$usid],
                                    ['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',1],['venta_javas.pagado',0],
                                    ['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([ ['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.idusuario',$usid],
                                    ['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.entregado',0],
                                    ['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',0],
                                    ['venta_javas.idusuario',$usid], ['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.entregado',0],
                                    ['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',0],
                                    ['venta_javas.idusuario',$usid],['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.entregado',0],
                                    ['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',0],
                                    ['venta_javas.idusuario',$usid],['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.entregado',0],
                                    ['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',0],
                                    ['venta_javas.idusuario',$usid]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.idusuario',$usid],['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.idusuario',$usid],['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.idusuario',$usid],['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.idusuario',$usid]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }
                    }elseif($criterio == "user"){
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',1],['venta_javas.idusuario',$usid],['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',1],['venta_javas.idusuario',$usid],['venta_javas.pagado',0],
                                    ['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',1],['venta_javas.idusuario',$usid],['venta_javas.pagado',0],
                                    ['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',1],['venta_javas.idusuario',$usid]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entrega_parcial',1],['venta_javas.idusuario',$usid],['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entrega_parcial',1],['venta_javas.idusuario',$usid],['venta_javas.pagado',0],
                                    ['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entrega_parcial',1],['venta_javas.idusuario',$usid],['venta_javas.pagado',0],
                                    ['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entrega_parcial',1],['venta_javas.idusuario',$usid]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0],['venta_javas.idusuario',$usid],
                                    ['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0],['venta_javas.idusuario',$usid],
                                    ['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0],['venta_javas.idusuario',$usid],
                                    ['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.entregado',0],['venta_javas.entrega_parcial',0],['venta_javas.idusuario',$usid]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.idusuario',$usid],['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.idusuario',$usid],['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.idusuario',$usid],['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.idusuario',$usid]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }
                    }else{
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.entregado',1],
                                    ['venta_javas.estado','Registrado'],['venta_javas.idusuario',$usid],['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.entregado',1],
                                    ['venta_javas.estado','Registrado'],['venta_javas.idusuario',$usid],['venta_javas.pagado',0],
                                    ['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.entregado',1],
                                    ['venta_javas.estado','Registrado'],['venta_javas.idusuario',$usid],['venta_javas.pagado',0],
                                    ['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.entregado',1],
                                    ['venta_javas.estado','Registrado'],['venta_javas.idusuario',$usid]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.entrega_parcial',1],
                                    ['venta_javas.estado','Registrado'],['venta_javas.idusuario',$usid],['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.entrega_parcial',1],
                                    ['venta_javas.estado','Registrado'],['venta_javas.idusuario',$usid],['venta_javas.pagado',0],
                                    ['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.entrega_parcial',1],
                                    ['venta_javas.estado','Registrado'],['venta_javas.idusuario',$usid],['venta_javas.pagado',0],
                                    ['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.entrega_parcial',1],
                                    ['venta_javas.estado','Registrado'],['venta_javas.idusuario',$usid]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.idusuario',$usid],
                                    ['venta_javas.entregado',0],['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',0],
                                    ['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.idusuario',$usid],
                                    ['venta_javas.entregado',0],['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',0],
                                    ['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.idusuario',$usid],
                                    ['venta_javas.entregado',0],['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',0],
                                    ['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.idusuario',$usid],
                                    ['venta_javas.entregado',0],['venta_javas.estado','Registrado'],['venta_javas.entrega_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.idusuario',$usid],['venta_javas.pagado',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.idusuario',$usid],['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.idusuario',$usid],['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                                ->join('users','venta_javas.idusuario','=','users.id')
                                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                    'venta_javas.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_javas.facturado',
                                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega','special')
                                ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],['venta_javas.estado','Registrado'],
                                    ['venta_javas.idusuario',$usid]])
                                ->orderBy('venta_javas.id', 'desc')->paginate(12);
                            }
                        }
                    }
                }
            }
        }

        return [
            'pagination' => [
                'total'        => $ventas->total(),
                'current_page' => $ventas->currentPage(),
                'per_page'     => $ventas->perPage(),
                'last_page'    => $ventas->lastPage(),
                'from'         => $ventas->firstItem(),
                'to'           => $ventas->lastItem(),
            ],
            'ventas' => $ventas,
            'userrol' => $usrol,
            'usid' => $usid
        ];
    }
    public function facturacionInvo(Request $request){

            if (!$request->ajax()) return redirect('/');

            $buscar = $request->buscar;
            $criterio = $request->criterio;
            $estadoV = $request->estado;
            $tipoFact = $request->tipofact;
            $pagadoEs = $request->estadoPagado;

            $usrol = \Auth::user()->idrol;
            $usarea = \Auth::user()->area;
            $usid = \Auth::user()->id;


            if($usarea == 'SLP'){
                if ($buscar==''){
                    $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                    ->join('users','venta_javas.idusuario','=','users.id')
                    ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                        'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                        'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                        'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                        'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                        'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                        'venta_javas.factura_env','personas.rfc as rfccliente','venta_javas.adeudo',
                        'venta_javas.num_factura','venta_javas.auto_entrega')
                    ->where([['venta_javas.facturado',$estadoV],
                        ['venta_javas.tipo_facturacion',$tipoFact],['venta_javas.estado','Registrado'],
                        ['venta_javas.idusuario',$usid]])
                    ->orderBy('venta_javas.id', 'desc')->paginate(12);
                }elseif($criterio == 'cliente'){
                    $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                    ->join('users','venta_javas.idusuario','=','users.id')
                    ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                        'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                        'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                        'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                        'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                        'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                        'venta_javas.factura_env','venta_javas.adeudo','venta_javas.num_factura','venta_javas.auto_entrega')
                    ->where([['personas.nombre', 'like', '%'. $buscar . '%'],
                        ['venta_javas.facturado',$estadoV],['venta_javas.tipo_facturacion',$tipoFact],
                        ['venta_javas.estado','Registrado'],['venta_javas.idusuario',$usid]])
                    ->orderBy('venta_javas.id', 'desc')->paginate(12);
                }else{
                    $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                    ->join('users','venta_javas.idusuario','=','users.id')
                    ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                        'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                        'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                        'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                        'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                        'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                        'venta_javas.factura_env','venta_javas.adeudo','venta_javas.num_factura','venta_javas.auto_entrega')
                    ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['venta_javas.facturado',$estadoV],['venta_javas.tipo_facturacion',$tipoFact],
                        ['venta_javas.estado','Registrado'],['venta_javas.idusuario',$usid]])
                    ->orderBy('venta_javas.id', 'desc')->paginate(12);
                }
            }else{
                if($buscar==''){
                    $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                    ->join('users','venta_javas.idusuario','=','users.id')
                    ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                        'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                        'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                        'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                        'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                        'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                        'venta_javas.factura_env','personas.rfc as rfccliente','venta_javas.adeudo',
                        'venta_javas.num_factura','venta_javas.auto_entrega')
                    ->where([['venta_javas.facturado',$estadoV],
                        ['venta_javas.tipo_facturacion',$tipoFact],['venta_javas.estado','Registrado']])
                    ->orderBy('venta_javas.id', 'desc')->paginate(12);
                }elseif($criterio == 'cliente'){
                    $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                    ->join('users','venta_javas.idusuario','=','users.id')
                    ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                        'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                        'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                        'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                        'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                        'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                        'venta_javas.factura_env','venta_javas.adeudo','venta_javas.num_factura','venta_javas.auto_entrega')
                    ->where([['personas.nombre', 'like', '%'. $buscar . '%'],
                        ['venta_javas.facturado',$estadoV],['venta_javas.tipo_facturacion',$tipoFact],
                        ['venta_javas.estado','Registrado']])
                    ->orderBy('venta_javas.id', 'desc')->paginate(12);
                }else{
                    $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                    ->join('users','venta_javas.idusuario','=','users.id')
                    ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                        'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                        'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                        'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                        'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                        'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                        'venta_javas.factura_env','venta_javas.adeudo','venta_javas.num_factura','venta_javas.auto_entrega')
                    ->where([['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['venta_javas.facturado',$estadoV],['venta_javas.tipo_facturacion',$tipoFact],
                        ['venta_javas.estado','Registrado']
                    ])
                    ->orderBy('venta_javas.id', 'desc')->paginate(12);
                }
            }
            return [
                'pagination' => [
                    'total'        => $ventas->total(),
                    'current_page' => $ventas->currentPage(),
                    'per_page'     => $ventas->perPage(),
                    'last_page'    => $ventas->lastPage(),
                    'from'         => $ventas->firstItem(),
                    'to'           => $ventas->lastItem(),
                ],
                'ventas' => $ventas,
                'userrol' => $usrol
            ];

    }
    public function store(Request $request){
        if(!$request->ajax()) return redirect('/');

        $mytime = Carbon::now('America/Mexico_City');

        try{
            DB::beginTransaction();

            $venta = new VentaJava();
            $venta->idcliente = $request->idcliente;
            $venta->idusuario = \Auth::user()->id;
            $venta->tipo_comprobante = $request->tipo_comprobante;
            $venta->num_comprobante = $request->num_comprobante;
            $venta->fecha_hora = $mytime;
            $venta->impuesto = $request->impuesto;
            $venta->total = $request->total;
            $venta->adeudo = $request->total;
            $venta->forma_pago = $request->forma_pago;
            $venta->tiempo_entrega = $request->tiempo_entrega;
            $venta->lugar_entrega = $request->lugar_entrega;
            $venta->entregado = 0;
            $venta->entrega_parcial = 0;
            $venta->pago_parcial = 0;
            $venta->estado = 'Registrado';
            $venta->moneda = $request->moneda;
            $venta->tipo_cambio = $request->tipo_cambio;
            $venta->observacion = $request->observacion;
            $venta->observacionpriv = $request->observacionpriv;
            $venta->num_cheque = $request->num_cheque;
            $venta->banco = $request->banco;
            $venta->tipo_facturacion = $request->tipo_facturacion;
            $venta->facturado = 0;
            $venta->factura_env = 0;
            $venta->auto_entrega = 0;
            $venta->special = $request->special;
            $venta->save();

            $detalles = $request->data;//Array de detalles

            //Recorro todos los elementos
            foreach($detalles as $ep=>$det)
            {
                $articulos = Java::where('codigo','=',$det['codigo'])->select('id')->first();
                $detalle = new DetalleVentaJava();
                $detalle->idventajava = $venta->id;
                $detalle->idjava = $articulos->id;
                $detalle->cantidad = $det['cantidad'];
                $detalle->por_entregar = $det['cantidad'];
                $detalle->entregadas = 0;
                $detalle->pendientes = $det['cantidad'];
                $detalle->precio = $det['precio'];
                $detalle->descuento = $det['descuento'];
                $detalle->save();
            }

            /* $fechaActual= date('Y-m-d');
            $numVentas = DB::table('ventas')->whereDate('created_at', $fechaActual)->count();
            $numIngresos = DB::table('ingresos')->whereDate('created_at',$fechaActual)->count();
            $arregloDatos = [
            'ventas' => [
                        'numero' => $numVentas,
                        'msj' => 'Ventas'
                    ],
            'ingresos' => [
                        'numero' => $numIngresos,
                        'msj' => 'Ingresos'
                    ]
            ];
            $allUsers = User::all();
            foreach ($allUsers as $notificar) {
                User::findOrFail($notificar->id)->notify(new NotifyAdmin($arregloDatos));
            } */

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }

        return $request->total;
    }
    public function desactivar(Request $request){

        if (!$request->ajax()) return redirect('/');
        $mytime = Carbon::now('America/Mexico_City');
        $resp = '';
        try{
            DB::beginTransaction();

            $venta = VentaJava::findOrFail($request->id);
            if($venta->pagado != 0){
                $venta->estado = 'Anulada';
                $venta->entregado = 0;
                $venta->entrega_parcial = 0;
                $venta->pagado = 0;
                $venta->pago_parcial = 0;
                $venta->facturado = 0;
                $venta->factura_env = 0;
                $venta->auto_entrega = 0;
                $venta->save();

                if ($request->genNc) {
                    $randomNum = substr(str_shuffle("0123456789"), 0, 10);
                    $obsNota = 'Generado apartir de la nota '. $venta->num_comprobante;

                    $cliente = Persona::findOrFail($venta->idcliente);
                    $credit = new Credit(['num_documento' => $randomNum,'total' => $venta->total,
                    'forma_pago' => $venta->forma_pago,'fecha_hora' => $mytime,
                    'observacion' => $obsNota,'estado' => 'Vigente', 'idusuario' => \Auth::user()->id]);
                    $cliente->credits()->save($credit);

                    $resp = 'Se ha generado una nota de credito para el cliente '. $cliente->nombre;
                } else {
                    $resp = 'Se ha eliminado una nota pagada y no ha sido acreditada';
                }

            }else {
                $venta->estado = 'Anulada';
                $venta->entregado = 0;
                $venta->entrega_parcial = 0;
                $venta->pagado = 0;
                $venta->pago_parcial = 0;
                $venta->facturado = 0;
                $venta->factura_env = 0;
                $venta->auto_entrega = 0;
                $venta->save();

                $resp = '';
            }
            $detalles = DetalleVentaJava::select('idjava','cantidad')
                ->where('idventajava',$request->id)->get();

            foreach($detalles as $ep=>$det){
                $articulo = Java::findOrFail($det['idjava']);
                $articulo->stock += $det['cantidad'];
                $articulo->save();
            }

            DB::commit();
            return $resp;

        } catch(Exception $e){
            DB::rollBack();
        }
    }
    public function obtenerCabecera(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $venta = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
        ->join('users','venta_javas.idusuario','=','users.id')
        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
            'venta_javas.entrega_parcial','venta_javas.tipo_facturacion', 'venta_javas.pagado','users.usuario',
            'venta_javas.num_cheque','venta_javas.file','venta_javas.observacionpriv','venta_javas.facturado','venta_javas.num_factura',
            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','personas.nombre as cliente',
            'personas.tipo','personas.rfc','personas.cfdi','personas.telefono','venta_javas.auto_entrega',
            'personas.company as contacto','personas.tel_company as tel_contacto',
            'personas.id as idcliente','personas.email as EmailC','special')
        ->where('venta_javas.id','=',$id)
        ->orderBy('venta_javas.id', 'desc')->take(1)->get();

        return ['venta' => $venta];


        return ['venta' => $venta ];

    }
    public function obtenerDetalles(Request $request){

        if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $detalles = DetalleVentaJava::join('javas','detalle_venta_javas.idjava','=','javas.id')
        ->leftJoin('categorias','javas.idcategoria','=','categorias.id')
        ->select('detalle_venta_javas.cantidad','detalle_venta_javas.precio','detalle_venta_javas.descuento',
            'detalle_venta_javas.por_entregar','detalle_venta_javas.pendientes',
            'detalle_venta_javas.entregadas','detalle_venta_javas.id','javas.sku',
            'javas.codigo','javas.espesor','javas.largo','javas.alto','javas.metros_cuadrados',
            'javas.descripcion','javas.idcategoria','javas.terminado','javas.ubicacion',
            'javas.file','javas.origen','categorias.nombre as categoria',
            'javas.contenedor','javas.fecha_llegada','javas.observacion','javas.condicion')
        ->where('detalle_venta_javas.idventajava',$id)
        ->orderBy('detalle_venta_javas.id','desc')->get();

        return ['detalles' => $detalles];
    }
    public function pdf(Request $request,$id){

        $venta =  VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
        ->join('users','venta_javas.idusuario','=','users.id')
        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
            'venta_javas.created_at','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
            'venta_javas.forma_pago','venta_javas.tiempo_entrega','venta_javas.lugar_entrega',
            'venta_javas.entregado','venta_javas.moneda','venta_javas.tipo_cambio', 'venta_javas.observacion',
            'venta_javas.num_cheque','venta_javas.banco','venta_javas.tipo_facturacion','venta_javas.pagado',
            'personas.nombre','personas.rfc','personas.domicilio','personas.ciudad',
            'personas.telefono','personas.email','users.usuario','venta_javas.entrega_parcial',
            'personas.company as contacto','personas.tel_company',
            'venta_javas.observacionpriv','venta_javas.facturado','venta_javas.factura_env',
            'venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
        ->where('venta_javas.id',$id)->take(1)->get();

        $detalles = DetalleVentaJava::join('javas','detalle_venta_javas.idjava','=','javas.id')
        ->select('detalle_venta_javas.cantidad','detalle_venta_javas.precio','detalle_venta_javas.descuento',
            'javas.sku as articulo','javas.largo','javas.alto','javas.terminado',
            'javas.metros_cuadrados','javas.codigo','javas.ubicacion')
        ->where('detalle_venta_javas.idventajava',$id)
        ->orderBy('detalle_venta_javas.id','desc')->get();

        $numventa = VentaJava::select('num_comprobante')->where('id',$id)->get();

        $ivaagregado = VentaJava::select('impuesto')->where('id',$id)->get();

        $sumaMts = DB::table('javas')
        ->select(DB::raw('SUM(metros_cuadrados) as metros'))
        ->leftJoin('detalle_venta_javas','detalle_venta_javas.idjava','javas.id')
        ->where('detalle_venta_javas.idventajava',$id)
        ->get();

        $ventaD = VentaJava::findOrFail($id); //ID venta y sus depositos
        $deposits = $ventaD->deposits()
        ->select(DB::raw('SUM(deposits.total) as abonos'))
        ->get();

        $capturas = Deposit::with('venta')
        ->join('venta_javas','deposits.depositable_id','=','venta_javas.id')
        ->select('deposits.total','deposits.forma_pago','deposits.depositable_id','deposits.fecha_hora')
        ->where('deposits.depositable_id',$id)
        ->get();

        $pdf = \PDF::loadView('pdf.ventaJava',['venta' => $venta,'detalles'=> $detalles,
            'ivaVenta' => $ivaagregado[0]->impuesto,'sumaMts' => $sumaMts[0]->metros,'abonos' => $deposits[0]->abonos,
            'capturas' =>$capturas]);

        return $pdf->stream('venta-'.$numventa[0]->num_comprobante.'.pdf');


    }
    public function cambiarEntrega(Request $request){

        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $venta = VentaJava::findOrFail($request->id);
            $venta->entregado = $request->entregado;
            $venta->entrega_parcial = 0;
            $venta->save();

            $detalles = DetalleVentaJava::where('idventajava',$venta->id)->get();

            if($request->entregado == 1){
                foreach($detalles as $ep=>$det){
                    $detail = DetalleVentaJava::findOrFail($det['id']);
                    $detail->entregadas = $det['pendientes'];
                    $detail->pendientes = $det['pendientes']-$det['por_entregar'];
                    $detail->completado = 1;
                    $detail->save();
                }
            }else{
                foreach($detalles as $ep=>$det){
                    $detail = DetalleVentaJava::findOrFail($det['id']);
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
        $venta = VentaJava::findOrFail($request->id);
        $venta->entrega_parcial = $request->entrega_parcial;
        $venta->entregado = 0;
        $venta->save();
    }
    public function cambiarPagado(Request $request){
        if (!$request->ajax()) return redirect('/');

        /* $pag_par = 0; */

        if($request->pagado == 1){
            $venta = VentaJava::findOrFail($request->id);
            $venta->pagado = $request->pagado;
            $venta->adeudo = 0;
            $venta->auto_entrega = 1;
            $venta->save();
        }else{
            $venta = VentaJava::findOrFail($request->id);
            $venta->pagado = $request->pagado;
            $venta->adeudo = $venta->total;
            $venta->auto_entrega = 0;
            $venta->save();
        }


    }
    public function actualizarObservacion(Request $request){
        if (!$request->ajax()) return redirect('/');
        $venta = VentaJava::findOrFail($request->id);
        $venta->observacion = $request->observacion;
        $venta->save();
    }
    public function actualizarObservacionPriv(Request $request){
        if (!$request->ajax()) return redirect('/');
        $venta = VentaJava::findOrFail($request->id);
        $venta->observacionpriv = $request->observacionpriv;
        $venta->save();
    }
    public function getLastNum(){

        $lastNum = VentaJava::select('num_comprobante')->get()->last();

        if($lastNum != null){
            $noComp = explode('"',$lastNum);
            $SigNum = explode("-",$noComp[3]);
            return  $SigNum[1] + 1;
        }else{
            return 1;
        }
    }
    public function indexEntregas(Request $request){
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $entregaEs = $request->estadoEntrega;
        $usrol = \Auth::user()->idrol;

        if($entregaEs == ''){
            if($buscar == ''){
                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                ->join('users','venta_javas.idusuario','=','users.id')
                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                    'venta_javas.num_cheque','venta_javas.tipo_facturacion','venta_javas.pagado','personas.nombre',
                    'venta_javas.entrega_parcial','users.usuario','venta_javas.observacionpriv','venta_javas.facturado',
                    'venta_javas.factura_env','venta_javas.adeudo','venta_javas.auto_entrega')
                ->where([['venta_javas.auto_entrega',1],['venta_javas.estado','!=','Anulada']])
                ->orderBy('venta_javas.id', 'desc')->paginate(12);
            }elseif($criterio == 'cliente'){
                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                ->join('users','venta_javas.idusuario','=','users.id')
                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                    'venta_javas.num_cheque','venta_javas.tipo_facturacion','venta_javas.pagado','personas.nombre',
                    'venta_javas.entrega_parcial','users.usuario','venta_javas.observacionpriv','venta_javas.facturado',
                    'venta_javas.factura_env','venta_javas.adeudo','venta_javas.auto_entrega')
                ->where([['venta_javas.auto_entrega',1],['venta_javas.estado','!=','Anulada'],
                    ['personas.nombre', 'like', '%'. $buscar . '%']])
                ->orderBy('venta_javas.id', 'desc')->paginate(12);
            }else{
                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                ->join('users','venta_javas.idusuario','=','users.id')
                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                    'venta_javas.num_cheque','venta_javas.tipo_facturacion','venta_javas.pagado','personas.nombre',
                    'venta_javas.entrega_parcial','users.usuario','venta_javas.observacionpriv','venta_javas.facturado',
                    'venta_javas.factura_env','venta_javas.adeudo','venta_javas.auto_entrega')
                ->where([['venta_javas.auto_entrega',1],['venta_javas.estado','!=','Anulada'],
                    ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%']])
                ->orderBy('venta_javas.id', 'desc')->paginate(12);
            }
        }elseif($entregaEs == 'entregado'){
            if($buscar == ''){
                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                ->join('users','venta_javas.idusuario','=','users.id')
                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                    'venta_javas.num_cheque','venta_javas.tipo_facturacion','venta_javas.pagado','personas.nombre',
                    'venta_javas.entrega_parcial','users.usuario','venta_javas.observacionpriv','venta_javas.facturado',
                    'venta_javas.factura_env','venta_javas.adeudo','venta_javas.auto_entrega')
                ->where([['venta_javas.auto_entrega',1],['venta_javas.estado','!=','Anulada'],['venta_javas.entregado',1]])
                ->orderBy('venta_javas.id', 'desc')->paginate(12);
            }elseif($criterio == 'cliente'){
                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                ->join('users','venta_javas.idusuario','=','users.id')
                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                    'venta_javas.num_cheque','venta_javas.tipo_facturacion','venta_javas.pagado','personas.nombre',
                    'venta_javas.entrega_parcial','users.usuario','venta_javas.observacionpriv','venta_javas.facturado',
                    'venta_javas.factura_env','venta_javas.adeudo','venta_javas.auto_entrega')
                ->where([['venta_javas.auto_entrega',1],['venta_javas.estado','!=','Anulada'],['venta_javas.entregado',1],
                    ['personas.nombre', 'like', '%'. $buscar . '%']])
                ->orderBy('venta_javas.id', 'desc')->paginate(12);
            }else{
                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                ->join('users','venta_javas.idusuario','=','users.id')
                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                    'venta_javas.num_cheque','venta_javas.tipo_facturacion','venta_javas.pagado','personas.nombre',
                    'venta_javas.entrega_parcial','users.usuario','venta_javas.observacionpriv','venta_javas.facturado',
                    'venta_javas.factura_env','venta_javas.adeudo','venta_javas.auto_entrega')
                ->where([['venta_javas.auto_entrega',1],['venta_javas.estado','!=','Anulada'],['venta_javas.entregado',1],
                    ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%']])
                ->orderBy('venta_javas.id', 'desc')->paginate(12);
            }
        }elseif($entregaEs == 'entrega_parcial'){
            if($buscar == ''){
                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                ->join('users','venta_javas.idusuario','=','users.id')
                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                    'venta_javas.num_cheque','venta_javas.tipo_facturacion','venta_javas.pagado','personas.nombre',
                    'venta_javas.entrega_parcial','users.usuario','venta_javas.observacionpriv','venta_javas.facturado',
                    'venta_javas.factura_env','venta_javas.adeudo','venta_javas.auto_entrega')
                ->where([['venta_javas.auto_entrega',1],['venta_javas.estado','!=','Anulada'],['venta_javas.entrega_parcial',1]])
                ->orderBy('venta_javas.id', 'desc')->paginate(12);
            }elseif($criterio == 'cliente'){
                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                ->join('users','venta_javas.idusuario','=','users.id')
                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                    'venta_javas.num_cheque','venta_javas.tipo_facturacion','venta_javas.pagado','personas.nombre',
                    'venta_javas.entrega_parcial','users.usuario','venta_javas.observacionpriv','venta_javas.facturado',
                    'venta_javas.factura_env','venta_javas.adeudo','venta_javas.auto_entrega')
                ->where([['venta_javas.auto_entrega',1],['venta_javas.estado','!=','Anulada'],['venta_javas.entrega_parcial',1],
                    ['personas.nombre', 'like', '%'. $buscar . '%']])
                ->orderBy('venta_javas.id', 'desc')->paginate(12);
            }else{
                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                ->join('users','venta_javas.idusuario','=','users.id')
                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                    'venta_javas.num_cheque','venta_javas.tipo_facturacion','venta_javas.pagado','personas.nombre',
                    'venta_javas.entrega_parcial','users.usuario','venta_javas.observacionpriv','venta_javas.facturado',
                    'venta_javas.factura_env','venta_javas.adeudo','venta_javas.auto_entrega')
                ->where([['venta_javas.auto_entrega',1],['venta_javas.estado','!=','Anulada'],['venta_javas.entrega_parcial',1],
                    ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%']])
                ->orderBy('venta_javas.id', 'desc')->paginate(12);
            }
        }elseif($entregaEs == 'no_entregado'){
            if($buscar == ''){
                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                ->join('users','venta_javas.idusuario','=','users.id')
                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                    'venta_javas.num_cheque','venta_javas.tipo_facturacion','venta_javas.pagado','personas.nombre',
                    'venta_javas.entrega_parcial','users.usuario','venta_javas.observacionpriv','venta_javas.facturado',
                    'venta_javas.factura_env','venta_javas.adeudo','venta_javas.auto_entrega')
                ->where([['venta_javas.auto_entrega',1],['venta_javas.estado','!=','Anulada'],['venta_javas.entrega_parcial',0],
                    ['venta_javas.entregado',0]])
                ->orderBy('venta_javas.id', 'desc')->paginate(12);
            }elseif($criterio == 'cliente'){
                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                ->join('users','venta_javas.idusuario','=','users.id')
                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                    'venta_javas.num_cheque','venta_javas.tipo_facturacion','venta_javas.pagado','personas.nombre',
                    'venta_javas.entrega_parcial','users.usuario','venta_javas.observacionpriv','venta_javas.facturado',
                    'venta_javas.factura_env','venta_javas.adeudo','venta_javas.auto_entrega')
                ->where([['venta_javas.auto_entrega',1],['venta_javas.estado','!=','Anulada'],['venta_javas.entrega_parcial',0],
                    ['venta_javas.entregado',0],['personas.nombre', 'like', '%'. $buscar . '%']])
                ->orderBy('venta_javas.id', 'desc')->paginate(12);
            }else{
                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                ->join('users','venta_javas.idusuario','=','users.id')
                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                    'venta_javas.num_cheque','venta_javas.tipo_facturacion','venta_javas.pagado','personas.nombre',
                    'venta_javas.entrega_parcial','users.usuario','venta_javas.observacionpriv','venta_javas.facturado',
                    'venta_javas.factura_env','venta_javas.adeudo','venta_javas.auto_entrega')
                ->where([['venta_javas.auto_entrega',1],['venta_javas.estado','!=','Anulada'],['venta_javas.entrega_parcial',0],
                    ['venta_javas.entregado',0],['venta_javas.'.$criterio, 'like', '%'. $buscar . '%']])
                ->orderBy('venta_javas.id', 'desc')->paginate(12);
            }
        }

        return [
            'pagination' => [
                'total'        => $ventas->total(),
                'current_page' => $ventas->currentPage(),
                'per_page'     => $ventas->perPage(),
                'last_page'    => $ventas->lastPage(),
                'from'         => $ventas->firstItem(),
                'to'           => $ventas->lastItem(),
            ],
            'ventas' => $ventas
        ];
    }
    public function obtenerDetallesEntrega(Request $request){

        if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $detalles = DetalleVentaJava::join('javas','detalle_venta_javas.idjava','=','javas.id')
        ->leftJoin('categorias','javas.idcategoria','=','categorias.id')
        ->select('detalle_venta_javas.cantidad','detalle_venta_javas.precio','detalle_venta_javas.descuento',
            'detalle_venta_javas.por_entregar','detalle_venta_javas.pendientes','detalle_venta_javas.boleta','detalle_venta_javas.fletero',
            'detalle_venta_javas.entregadas','detalle_venta_javas.fecha','detalle_venta_javas.matricula',
            'detalle_venta_javas.id','javas.sku','javas.codigo','javas.espesor','javas.largo',
            'javas.alto','javas.metros_cuadrados','javas.descripcion','javas.idcategoria',
            'javas.terminado','javas.ubicacion','javas.file','javas.origen','categorias.nombre as categoria',
            'javas.contenedor','javas.fecha_llegada','javas.observacion','javas.condicion')
        ->where([
            ['detalle_venta_javas.idventajava',$id],
            ['detalle_venta_javas.completado',0]
        ])
        ->orderBy('detalle_venta_javas.id','desc')->get();

        return ['detalles' => $detalles];
    }
    public function pdfEntrega(Request $request,$id){

        $venta =  VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
        ->join('users','venta_javas.idusuario','=','users.id')
        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
            'venta_javas.created_at','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
            'venta_javas.forma_pago','venta_javas.tiempo_entrega','venta_javas.lugar_entrega',
            'venta_javas.entregado','venta_javas.moneda','venta_javas.tipo_cambio', 'venta_javas.observacion',
            'venta_javas.num_cheque','venta_javas.banco','venta_javas.tipo_facturacion','venta_javas.pagado',
            'personas.nombre','personas.rfc','personas.domicilio','personas.ciudad',
            'personas.telefono','personas.email','users.usuario','venta_javas.entrega_parcial',
            'personas.company as contacto','personas.tel_company','venta_javas.facturado',
            'venta_javas.factura_env','venta_javas.auto_entrega')
        ->where('venta_javas.id',$id)->take(1)->get();

        $detalles = DetalleVentaJava::join('articulos','detalle_venta_javas.idjava','=','articulos.id')
            ->select('detalle_venta_javas.cantidad','detalle_venta_javas.precio','detalle_venta_javas.descuento','detalle_venta_javas.boleta',
                'detalle_venta_javas.entregadas','detalle_venta_javas.pendientes','articulos.sku as articulo',
                'detalle_venta_javas.fletero','detalle_venta_javas.fecha','detalle_venta_javas.matricula',
                'detalle_venta_javas.entregadas','detalle_venta_javas.pendientes','articulos.sku as articulo',
                'articulos.largo','articulos.alto','articulos.metros_cuadrados', 'articulos.codigo',
                'articulos.ubicacion')
            ->where('detalle_venta_javas.idventajava',$id)
            ->orderBy('detalle_venta_javas.id','desc')->get();

        $numventa = VentaJava::select('num_comprobante')->where('id',$id)->get();

        $ivaagregado = VentaJava::select('impuesto')->where('id',$id)->get();

        $pdf = \PDF::loadView('pdf.entregaflete',['venta' => $venta,'detalles'=>$detalles,'ivaVenta' =>$ivaagregado[0]->impuesto]);


        return $pdf->stream('entregaflete-'.$numventa[0]->num_comprobante.'.pdf');
    }
    public function updDetalle(Request $request){
        try{
            DB::beginTransaction();

            $detalles = $request->data;//Array de detalles

            //Recorro todos los elementos
            foreach($detalles as $ep=>$det)
            {
                $Stcomplete = 0;


                if($det['entregadas'] == $det['cantidad']){
                    $Stcomplete = 1;
                }

                $detalle = DetalleVentaJava::findOrFail($det['id']);
                $detalle->fletero= $det['fletero'];
                $detalle->boleta= $det['boleta'];
                $detalle->fecha= $det['fecha'];
                $detalle->matricula= $det['matricula'];
                $detalle->entregadas = $det['entregadas'];
                $detalle->pendientes = $det['pendientes']-$det['entregadas'];
                $detalle->completado = $Stcomplete;

                $detalle->save();
            }

            $venta = VentaJava::findOrFail($request->idventajava);

            if($request->totales == 0){
                $venta->entrega_parcial = 0;
                $venta->entregado = 1;
                $venta->save();
            }else{
                $venta->entrega_parcial = 1;
                $venta->entregado = 0;
                $venta->save();
            }

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function updImage(Request $request){

        if(!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $fileName ="";

            if($request->file != ""){

                //The name of the directory that we need to create.
                $directoryName = 'entregas';

                if(!is_dir($directoryName)){
                    //Directory does not exist, so lets create it.
                    mkdir($directoryName, 0777);
                }

                $vent= VentaJava::findOrFail($request->id);
                $img = $vent->file;

                if($img != null){
                    $image_path = public_path($directoryName).'/'.$img;

                    if(file_exists($image_path)){
                        unlink($image_path);
                    }
                }

                $exploded = explode(',', $request->file);
                $decoded = base64_decode($exploded[1]);

                if(str_contains($exploded[0],'jpeg'))
                    $extension = 'jpg';
                else
                    $extension = 'png';

                $fileName = str_random().'.'.$extension;

                $path = public_path($directoryName).'/'.$fileName;

                file_put_contents($path,$decoded);
            }

            $venta = VentaJava::findOrFail($request->id);
            $venta->file = $fileName;
            $venta->save();

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }

    }
    public function obtenerVentasCliente(Request $request){

        if (!$request->ajax()) return redirect('/');
        $idcliente = $request->idcliente;

        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
        ->join('users','venta_javas.idusuario','=','users.id')
        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
            'venta_javas.entrega_parcial','venta_javas.tipo_facturacion', 'venta_javas.pagado','users.usuario',
            'venta_javas.num_cheque','personas.nombre','venta_javas.file','venta_javas.observacionpriv',
            'venta_javas.facturado','venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo',
            'venta_javas.auto_entrega')
        ->where([['venta_javas.idcliente',$idcliente],['venta_javas.estado','Registrado']])
        ->orderBy('venta_javas.fecha_hora','desc')->paginate(5);

        return ['ventas' => $ventas];
    }
    public function eliminarImagen(Request $request){
        if(!$request->ajax()) return redirect('/');

        $directoryName = 'entregas';
        //Check if the directory already exists.
        if(!is_dir($directoryName)){
            //Directory does not exist, so lets create it.
            mkdir($directoryName, 0777);
        }

        $art= VentaJava::findOrFail($request->id);
        $img = $art->file;

        if($img != null){
            $image_path = public_path($directoryName).'/'.$img;
            if(file_exists($image_path)){
                unlink($image_path);
                $fileName = null;
            }
        }
        $venta = VentaJava::findOrFail($request->id);
        $venta->file = $fileName;
        $venta->save();

    }
    public function ListarExcel(Request $request){
        $inicio = $request->inicio;
        $fin = $request->fin;
        $usuarios = $request->usuarios;
        $ArrUsuarios = explode(",",$usuarios);

        return Excel::download(new VentasExport($inicio,$fin,$ArrUsuarios), 'presupuestos-'.$inicio.'-'.$fin.'.xlsx');
    }
    public function ListarAbonosExcel(Request $request){

        $inicio = $request->inicio;
        $fin = $request->fin;

        return Excel::download(new AbonosExport($inicio,$fin), 'Abonos-'.$inicio.'-'.$fin.'.xlsx');
    }
    public function ListarExcelDet(Request $request){
        $inicio = $request->inicio;
        $fin = $request->fin;
        $usuarios = $request->usuarios;
        $ArrUsuarios = explode(",",$usuarios);
        return Excel::download(new VentasExportDet($inicio,$fin,$ArrUsuarios), 'DetallePresupuestos-'.$inicio.'-'.$fin.'.xlsx');
    }
    public function cambiarFacturacion(Request $request){
        if (!$request->ajax()) return redirect('/');
        $venta = VentaJava::findOrFail($request->id);
        if($request->estado == 0){
            $venta->facturado = 0;
            $venta->factura_env = 0;
            $venta->num_factura = null;
        }else{
            $venta->facturado = $request->estado;
            $venta->num_factura = $request->numFact;
            $venta->factura_env = 0;
        }
        $venta->save();
    }
    public function cambiarFactura(Request $request){
        if(!$request->ajax()) return redirect('/');
        $venta =VentaJava::findOrFail($request->id);

        $venta->facturado= $request->facturado;
            $venta->pagado = 0;
            $venta->save();

    }
    public function cambiarFacturacionEnv(Request $request){
        if (!$request->ajax()) return redirect('/');
        $venta = VentaJava::findOrFail($request->id);
        $venta->factura_env = $request->estadoEn;
        $venta->save();
    }
    public function crearDeposit(Request $request){

        if (!$request->ajax()) return redirect('/');

        $mytime = Carbon::now('America/Mexico_City');

        $venta = VentaJava::findOrFail($request->id); //Venta a depositar
        $adeudoAct = $venta->adeudo;

        if($request->total < $adeudoAct){
            try{
                DB::beginTransaction();
                $venta->adeudo = $venta->adeudo - $request->total;
                $venta->pago_parcial = 1;
                $venta->pagado = 0;
                $venta->save();
                $deposit = new Deposit(['total' => $request->total,'fecha_hora' => $mytime,
                    'forma_pago' => $request->forma_pago]);
                $venta->deposits()->save($deposit);
                DB::commit();
            }catch(Exception $e){
                DB::rollBack();
            }
        }elseif($request->total == $adeudoAct){
            try{
                DB::beginTransaction();
                $venta->adeudo = 0;
                $venta->pago_parcial = 1;
                $venta->pagado = 1;
                $venta->auto_entrega = 1;
                $venta->save();
                $deposit = new Deposit(['total' => $request->total,'fecha_hora' => $mytime,
                    'forma_pago' => $request->forma_pago]);
                $venta->deposits()->save($deposit);
                DB::commit();
            }catch(Exception $e){
                DB::rollBack();
            }
        }
    }
    public function getDeposits(Request $request){

        if (!$request->ajax()) return redirect('/');

        $venta = VentaJava::findOrFail($request->id); //ID venta y sus depositos

        $deposits = $venta->deposits()
        ->select('deposits.id','deposits.total','deposits.fecha_hora as fecha','deposits.forma_pago')
        ->orderBy('deposits.fecha_hora','desc')
        ->get();

        /* $tot = $venta->deposits()->count(); */

        return [
            'abonos' => $deposits,
            /* 'total'  => $tot */
        ];

    }
    public function deleteDeposit(Request $request){
        if (!$request->ajax()) return redirect('/');
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
            $venta = VentaJava::findOrFail($request->idventajava);
            $numDeposits = $venta->deposits()->count();

            if($numDeposits <= 0){
                $venta->pago_parcial = 0;
                $venta->pagado = 0;
                $venta->adeudo = $venta->total;
                $venta->save();
            }else{
                $venta->adeudo = $venta->adeudo + $request->total;
                $venta->save();
            }
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function getProject(Request $request){
        if (!$request->ajax()) return redirect('/');

        $ventas= VentaJava::findOrFail($request->id); //ID del project
        $project = $ventas->project()
        ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
        ->leftJoin('users','projects.idusuario','=','users.id')
        ->select('projects.id as idproject','projects.tipo_comprobante','projects.num_comprobante',
            'projects.fecha_hora','projects.impuesto','projects.total','projects.estado',
            'projects.moneda','projects.tipo_cambio','projects.observacion','projects.forma_pago',
            'projects.lugar_entrega','projects.entregado','projects.entrega_parcial','projects.tipo_facturacion',
            'projects.pagado','users.usuario','clientes.nombre as cliente','projects.file','projects.observacionpriv',
            'projects.facturado','projects.factura_env','projects.pago_parcial','projects.adeudo')
        ->get();
        return [
            'project' => $project
        ];
    }
    public function update(Request $request){
        if(!$request->ajax()) return redirect('/');

        $presupuestos = $request->presupuestos;//Array de presupuestos seleccionados (id)

        if(!$request->ajax()) return redirect('/');

        $mytime = Carbon::now('America/Mexico_City');

        try{
            DB::beginTransaction();

            $venta = new VentaJava();
            $venta->idcliente = $request->idcliente;
            $venta->idusuario = \Auth::user()->id;
            $venta->tipo_comprobante = $request->tipo_comprobante;
            $venta->num_comprobante = $request->num_comprobante;
            $venta->fecha_hora = $mytime;
            $venta->impuesto = $request->impuesto;
            $venta->total = $request->total;
            $venta->adeudo = $request->adeudo;
            $venta->forma_pago = $request->forma_pago;
            $venta->tiempo_entrega = $request->tiempo_entrega;
            $venta->lugar_entrega = $request->lugar_entrega;
            $venta->entregado = 0;
            $venta->entrega_parcial = 0;
            $venta->pago_parcial = 0;
            $venta->estado = 'Registrado';
            $venta->moneda = $request->moneda;
            $venta->tipo_cambio = $request->tipo_cambio;
            $venta->observacion = $request->observacion;
            $venta->observacionpriv = $request->observacionpriv;
            $venta->num_cheque = $request->num_cheque;
            $venta->banco = $request->banco;
            $venta->tipo_facturacion = $request->tipo_facturacion;
            $venta->facturado = 0;
            $venta->factura_env = 0;
            $venta->auto_entrega = 0;
            $venta->special = $request->special;
            $venta->save();
            $venta->projects()->sync($presupuestos);

            $detalles = $request->data;//Array de detalles

            //Recorro todos los elementos
            foreach($detalles as $ep=>$det)
            {
                $projects = Project::where('id','=',$det['id'])->select('id')->first();
                $detalle = new DetalleVentaJava();
                $detalle->idventajava = $venta->id;
                $detalle->idjava = $det['idjava'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->por_entregar = $det['cantidad'];
                $detalle->entregadas = 0;
                $detalle->pendientes = $det['cantidad'];
                $detalle->precio = $det['precio'];
                $detalle->descuento = $det['descuento'];
                $detalle->project_id = $projects->id;
                $detalle->save();
            }


            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }

        return $request->total;
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
                $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                ->join('users','venta_javas.idusuario','=','users.id')
                ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                ->where([
                    ['personas.nombre', 'like', '%'. $buscar . '%'],
                    ['venta_javas.estado',$estadoV]
                ])
                ->orderBy('venta_javas.id', 'desc')->paginate(12);
            }else{
                if ($buscar==''){
                    $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                    ->join('users','venta_javas.idusuario','=','users.id')
                    ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                    'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                    ->where('venta_javas.estado',$estadoV)
                    ->orderBy('venta_javas.id', 'desc')->paginate(12);
                }else{
                    $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                    ->join('users','venta_javas.idusuario','=','users.id')
                    ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                    'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                    'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                    'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                    'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                    'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                    'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                    ->where([
                        ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['venta_javas.estado',$estadoV]
                    ])
                    ->orderBy('venta_javas.id', 'desc')->paginate(12);
                }
            }
        }else{
            if($buscar==''){
                if($entregaEs == 'entregado'){
                    if($estadoAdeu == ''){
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                        ->where([
                            ['venta_javas.estado','Registrado'],
                            ['venta_javas.entregado',1]
                        ])
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Pagado'){
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                        ->where([
                            ['venta_javas.estado','Registrado'],
                            ['venta_javas.entregado',1],
                            ['venta_javas.pagado',1],['venta_javas.adeudo',0]
                        ])
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Abonado'){
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                        ->where([
                            ['venta_javas.estado','Registrado'],
                            ['venta_javas.entregado',1],
                            ['venta_javas.pago_parcial',1]
                        ])
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'NoAbono'){
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                        ->where([
                            ['venta_javas.estado','Registrado'],
                            ['venta_javas.entregado',1],
                            ['venta_javas.adeudo','=','venta_javas.total']
                        ])
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }
                }elseif($entregaEs == 'entrega_parcial'){
                    if($estadoAdeu == ''){
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                        ->where([
                            ['venta_javas.estado','Registrado'],
                            ['venta_javas.entrega_parcial',1]
                        ])
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Pagado'){
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                        ->where([
                            ['venta_javas.estado','Registrado'],
                            ['venta_javas.entrega_parcial',1],
                            ['venta_javas.pagado',1],['venta_javas.adeudo',0]
                        ])
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Abonado'){
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                        ->where([
                            ['venta_javas.estado','Registrado'],
                            ['venta_javas.entrega_parcial',1],
                            ['venta_javas.pago_parcial',1]
                        ])
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'NoAbono'){
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                        ->where([
                            ['venta_javas.estado','Registrado'],
                            ['venta_javas.entrega_parcial',1],
                            ['venta_javas.adeudo','=','venta_javas.total']
                        ])
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }
                }elseif($entregaEs == 'no_entregado'){
                    if($estadoAdeu == ''){
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                        ->where([
                            ['venta_javas.estado','Registrado'],
                            ['venta_javas.entregado',0],
                            ['venta_javas.entrega_parcial',0]
                        ])
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Pagado'){
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                        ->where([
                            ['venta_javas.estado','Registrado'],
                            ['venta_javas.entregado',0],
                            ['venta_javas.entrega_parcial',0],
                            ['venta_javas.pagado',1],['venta_javas.adeudo',0]
                        ])
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Abonado'){
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                        ->where([
                            ['venta_javas.estado','Registrado'],
                            ['venta_javas.entregado',0],
                            ['venta_javas.entrega_parcial',0],
                            ['venta_javas.pago_parcial',1]

                        ])
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'NoAbono'){
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                        ->where([
                            ['venta_javas.estado','Registrado'],
                            ['venta_javas.entregado',0],
                            ['venta_javas.entrega_parcial',0],
                            ['venta_javas.adeudo','=','venta_javas.total']
                        ])
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }
                }else{
                    if($estadoAdeu == ''){
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                        ->where([['venta_javas.estado','Registrado']])
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Pagado'){
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                        ->where([['venta_javas.estado','Registrado'],['venta_javas.pagado',1],['venta_javas.adeudo',0]])
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Abonado'){
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                        ->where([['venta_javas.estado','Registrado'],['venta_javas.pago_parcial',1]])
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'NoAbono'){
                        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                        ->join('users','venta_javas.idusuario','=','users.id')
                        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                        ->where([['venta_javas.estado','Registrado'],['venta_javas.total','venta_javas.adeudo']])
                        ->orderBy('venta_javas.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if($criterio == "cliente"){
                    if($entregaEs == 'entregado'){
                        if($estadoAdeu == ''){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entregado',1]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entregado',1],
                                ['venta_javas.pagado',1],['venta_javas.adeudo',0]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entregado',1],
                                ['venta_javas.pago_parcial',1]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entregado',1],
                                ['venta_javas.adeudo','=','venta_javas.total']
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }

                    }elseif($entregaEs == 'entrega_parcial'){
                        if($estadoAdeu == ''){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entrega_parcial',1]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entrega_parcial',1],
                                ['venta_javas.pagado',1],['venta_javas.adeudo',0]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);

                        }elseif($estadoAdeu == 'Abonado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entrega_parcial',1],
                                ['venta_javas.pago_parcial',1]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);

                        }elseif($estadoAdeu == 'NoAbono'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entrega_parcial',1],
                                ['venta_javas.adeudo','=','venta_javas.total']
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'no_entregado'){
                        if($estadoAdeu == ''){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entregado',0],
                                ['venta_javas.entrega_parcial',0]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entregado',0],
                                ['venta_javas.entrega_parcial',0],
                                ['venta_javas.pagado',1],['venta_javas.adeudo',0]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entregado',0],
                                ['venta_javas.entrega_parcial',0],
                                ['venta_javas.pago_parcial',1]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entregado',0],
                                ['venta_javas.entrega_parcial',0],
                                ['venta_javas.adeudo','=','venta_javas.total']
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }
                    }else{
                        if($estadoAdeu == ''){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado']
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.pagado',1],['venta_javas.adeudo',0]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.pago_parcial',1]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.adeudo','=','venta_javas.total']
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }
                    }
                }else{
                    if($entregaEs == 'entregado'){
                        if($estadoAdeu == ''){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entregado',1]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entregado',1],
                                ['venta_javas.pagado',1],['venta_javas.adeudo',0]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entregado',1],
                                ['venta_javas.pago_parcial',1]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo')
                            ->where([
                                ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entregado',1],
                                ['venta_javas.adeudo','=','venta_javas.total']
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'entrega_parcial'){
                        if($estadoAdeu == ''){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entrega_parcial',1]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entrega_parcial',1],
                                ['venta_javas.pagado',1],['venta_javas.adeudo',0]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entrega_parcial',1],
                                ['venta_javas.pago_parcial',1]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entrega_parcial',1],
                                ['venta_javas.adeudo','=','venta_javas.total']
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'no_entregado'){
                        if($estadoAdeu == ''){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entregado',0],
                                ['venta_javas.entrega_parcial',0]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entregado',0],
                                ['venta_javas.entrega_parcial',0],
                                ['venta_javas.pagado',1],['venta_javas.adeudo',0]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entregado',0],
                                ['venta_javas.entrega_parcial',0],
                                ['venta_javas.pago_parcial',1]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.entregado',0],
                                ['venta_javas.entrega_parcial',0],
                                ['venta_javas.adeudo','=','venta_javas.total']
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }
                    }else{
                        if($estadoAdeu == ''){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado']
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.pagado',1],['venta_javas.adeudo',0]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.pago_parcial',1]
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
                            ->join('users','venta_javas.idusuario','=','users.id')
                            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                                'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
                                'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
                                'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
                            ->where([
                                ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['venta_javas.estado','Registrado'],
                                ['venta_javas.adeudo','=','venta_javas.total']
                            ])
                            ->orderBy('venta_javas.id', 'desc')->paginate(12);
                        }
                    }
                }
            }
        }

        return [
            'pagination' => [
                'total'        => $ventas->total(),
                'current_page' => $ventas->currentPage(),
                'per_page'     => $ventas->perPage(),
                'last_page'    => $ventas->lastPage(),
                'from'         => $ventas->firstItem(),
                'to'           => $ventas->lastItem(),
            ],
            'ventas' => $ventas,
            'userrol' => $usrol
        ];
    }
    public function getVentasClienteProject(Request $request){

        if (!$request->ajax()) return redirect('/');

        $idcliente = $request->idcliente;
        $buscar = $request->buscar;
        $criterio = $request->criterio;


        if($buscar != ''){
            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
            ->join('users','venta_javas.idusuario','=','users.id')
            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                'venta_javas.entrega_parcial','venta_javas.tipo_facturacion', 'venta_javas.pagado','users.usuario',
                'venta_javas.num_cheque','personas.nombre','venta_javas.file','venta_javas.observacionpriv',
                'venta_javas.facturado','venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo',
                'venta_javas.auto_entrega','venta_javas.special')
            ->where([
                ['venta_javas.estado','Registrado'],
                ['venta_javas.'.$criterio, 'like', '%'. $buscar . '%'],
                ['venta_javas.special', 1]
            ])
            ->orderBy('venta_javas.fecha_hora','desc')->paginate(10);
        }else{
            $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
            ->join('users','venta_javas.idusuario','=','users.id')
            ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
                'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
                'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
                'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
                'venta_javas.entrega_parcial','venta_javas.tipo_facturacion', 'venta_javas.pagado','users.usuario',
                'venta_javas.num_cheque','personas.nombre','venta_javas.file','venta_javas.observacionpriv',
                'venta_javas.facturado','venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo',
                'venta_javas.auto_entrega','venta_javas.special')
            ->where([
                    ['venta_javas.idcliente','=',$idcliente],
                    ['venta_javas.estado','Registrado'],
                    ['venta_javas.special', 1]
                ])
            ->orderBy('ventas.fecha_hora','desc')->paginate(10);
        }


        return [
            'pagination' => [
                'total'        => $ventas->total(),
                'current_page' => $ventas->currentPage(),
                'per_page'     => $ventas->perPage(),
                'last_page'    => $ventas->lastPage(),
                'from'         => $ventas->firstItem(),
                'to'           => $ventas->lastItem(),
            ],
            'ventas' => $ventas
        ];
    }
    public function autorizarEntrega(Request $request){
            if (!$request->ajax()) return redirect('/');

            $venta = VentaJava::findOrFail($request->id);
            $venta->auto_entrega = $request->auto_entrega;
            $venta->save();

    }
    public function filesUppload(Request $request){

        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();
            $directoryName = 'facturasfiles';

            if(!is_dir($directoryName)){
                mkdir($directoryName, 0777);
            }

            $archivos = $request->filesdata;//Array de archivos

            $docs = array();
            foreach($archivos as $ar=>$file){
                $exploded = explode(',', $file['url']);
                $decoded = base64_decode($exploded[1]);
                $extn = explode('/', $file['tipo']);
                $extension = $extn[1];
                $fileName = str_random().'.'.$extension;
                $path = public_path($directoryName).'/'.$fileName;
                file_put_contents($path,$decoded);

                $docum = new Document(['url' => $fileName, 'tipo' => $extension ]);
                $venta = VentaJava::findOrFail($request->id);
                $venta->documents()->save($docum);
                DB::commit();
            }

        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function getDocs(Request $request){
        if (!$request->ajax()) return redirect('/');
        $venta = VentaJava::findOrFail($request->id); //ID dueño de los archivos
        $files = $venta->documents()->get();

        return [
            'documentos' => $files
        ];
    }
    public function eliminarDoc(Request $request){
        if (!$request->ajax()) return redirect('/');
        $directoryName = 'facturasfiles';
        try{
            DB::beginTransaction();

            $doc= Document::findOrFail($request->id);
            $img = $doc->url;


            if($img != null){
                $image_path = public_path($directoryName).'/'.$img;
                if(file_exists($image_path)){
                    unlink($image_path);
                }
            }

            $doc->delete();
            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function crearDepositCredit(Request $request){

        if (!$request->ajax()) return redirect('/');

        $mytime = Carbon::now('America/Mexico_City');

        $venta = VentaJava::findOrFail($request->id); //Venta a depositar
        $adeudoAct = $venta->adeudo;

        $creditos = $request->creditos;

        if($request->total < $adeudoAct){
            try{
                DB::beginTransaction();
                $venta->adeudo = $venta->adeudo - $request->total;
                $venta->pago_parcial = 1;
                $venta->pagado = 0;
                $venta->save();
                $deposit = new Deposit(['total' => $request->total,'fecha_hora' => $mytime,
                    'forma_pago' => $request->forma_pago]);
                $venta->deposits()->save($deposit);

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
                $venta->adeudo = 0;
                $venta->pago_parcial = 1;
                $venta->pagado = 1;
                $venta->auto_entrega = 1;
                $venta->save();
                $deposit = new Deposit(['total' => $request->total,'fecha_hora' => $mytime,
                    'forma_pago' => $request->forma_pago]);
                $venta->deposits()->save($deposit);
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
    public function ventasClienteExcel($id,$date1,$date2){
        $idcliente = $id;
        $cliente = Persona::select('nombre','num_documento')->where('personas.id',$id)->first();
        return Excel::download(new VentasClienteExport($idcliente,$date1,$date2),
            'presupuestos-'.$cliente->nombre.'-'.$cliente->num_documento.' del '.$date1.' al '.$date2.'.xlsx');
    }
    public function ventasClientePDF($id,$date1,$date2){

        $cliente = Persona::select('nombre','num_documento','tipo')->where('personas.id',$id)->first();
        $fullDate1 = $date1.' 00:00:00';
        $fullDate2 = $date2.' 23:59:59';

        $ventas =  VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
        ->join('users','venta_javas.idusuario','=','users.id')
        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
            'venta_javas.forma_pago','venta_javas.tiempo_entrega','venta_javas.lugar_entrega',
            'venta_javas.entregado','venta_javas.moneda','venta_javas.tipo_cambio', 'venta_javas.observacion',
            'venta_javas.num_cheque','venta_javas.banco','venta_javas.tipo_facturacion','venta_javas.pagado',
            'personas.nombre','personas.rfc','personas.domicilio','personas.ciudad',
            'personas.telefono','personas.email','users.usuario','venta_javas.entrega_parcial',
            'personas.company as contacto','personas.tel_company',
            'venta_javas.observacionpriv','venta_javas.facturado','venta_javas.factura_env',
            'venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
        ->where([['venta_javas.idcliente',$id],['venta_javas.estado','Registrado']])
        ->whereBetween('venta_javas.fecha_hora', [$fullDate1, $fullDate2])
        ->orderBy('venta_javas.pagado','asc')->orderBy('venta_javas.entregado','asc')->get();

        $detalles = DetalleVentaJava::join('abrasivos','detalle_venta_javas.idjava','=','abrasivos.id')
        ->Leftjoin('venta_javas','venta_javas.id','detalle_venta_javas.idventajava')
        ->select('detalle_venta_javas.idventajava','detalle_venta_javas.cantidad','detalle_venta_javas.precio','detalle_venta_javas.descuento',
            'abrasivos.sku as articulo','abrasivos.largo','abrasivos.alto','abrasivos.terminado',
            'abrasivos.metros_cuadrados','abrasivos.codigo','abrasivos.ubicacion')
        ->where([['venta_javas.idcliente',$id],['venta_javas.estado','Registrado']])
        ->whereBetween('venta_javas.fecha_hora', [$fullDate1, $fullDate2])
        ->orderBy('venta_javas.pagado','asc')->orderBy('venta_javas.entregado','asc')->get();

        $sumaVentas =  VentaJava::select(DB::raw('SUM(total) as total'))
        ->where([['venta_javas.idcliente',$id],['venta_javas.estado','Registrado']])
        ->whereBetween('venta_javas.fecha_hora', [$fullDate1, $fullDate2])->get();

        $adedudadas =  VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
        ->join('users','venta_javas.idusuario','=','users.id')
        ->select('venta_javas.id','venta_javas.pagado','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
            'venta_javas.forma_pago','venta_javas.tiempo_entrega','venta_javas.lugar_entrega',
            'personas.nombre as cliente','venta_javas.entregado','venta_javas.tipo_facturacion',
            'venta_javas.pago_parcial','venta_javas.adeudo','users.usuario')
        ->where([['venta_javas.idcliente',$id],['venta_javas.estado','Registrado'],
            ['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
        ->orderBy('venta_javas.pagado','asc')->orderBy('venta_javas.entregado','asc')
        ->whereBetween('venta_javas.fecha_hora', [$fullDate1, $fullDate2])->get();

        $sumaAdedudadas =  VentaJava::select(DB::raw('SUM(total) as total'))
        ->where([['venta_javas.idcliente',$id],['venta_javas.estado','Registrado'],
            ['venta_javas.pagado',0],['venta_javas.pago_parcial',0]])
        ->whereBetween('venta_javas.fecha_hora', [$fullDate1, $fullDate2])->get();

        $parciales =  VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
        ->join('users','venta_javas.idusuario','=','users.id')
        ->select('venta_javas.id','venta_javas.pagado','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
            'venta_javas.forma_pago','venta_javas.tiempo_entrega','venta_javas.lugar_entrega',
            'venta_javas.entregado','venta_javas.tipo_facturacion','venta_javas.pago_parcial',
            'venta_javas.adeudo','users.usuario','personas.nombre as cliente')
        ->where([['venta_javas.idcliente',$id],['venta_javas.estado','Registrado'],
            ['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
        ->whereBetween('venta_javas.fecha_hora', [$fullDate1, $fullDate2])
        ->orderBy('venta_javas.pagado','asc')->orderBy('venta_javas.entregado','asc')->get();

        $sumaParciales =  VentaJava::select(DB::raw('SUM(adeudo) as adeudo'))
        ->where([['venta_javas.idcliente',$id],['venta_javas.estado','Registrado'],
            ['venta_javas.pagado',0],['venta_javas.pago_parcial',1]])
        ->whereBetween('venta_javas.fecha_hora', [$fullDate1, $fullDate2])->get();

        $pdf = \PDF::loadView('pdf.ventasCliente',['ventas' => $ventas,'cliente' => $cliente,'detalles' => $detalles,
            'sumaVentas' => $sumaVentas,'adedudadas' => $adedudadas,'sumaAdedudadas' => $sumaAdedudadas,
            'parciales' => $parciales,'sumaParciales' => $sumaParciales,'inicio' => $fullDate1, 'fin' => $fullDate2]);

        return $pdf->stream('ventas-'.$cliente->nombre.'-'.$cliente->num_documento.'.pdf');
    }
    public function ventasUsuariosExcel(Request $request){

        $ArrUsuarios = [1,9];

        $ventas = VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
        ->join('users','venta_javas.idusuario','=','users.id')
        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
            'venta_javas.fecha_hora','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
            'venta_javas.moneda','venta_javas.tipo_cambio','venta_javas.observacion','venta_javas.forma_pago',
            'venta_javas.tiempo_entrega','venta_javas.lugar_entrega','venta_javas.entregado','venta_javas.banco',
            'venta_javas.entrega_parcial','venta_javas.num_cheque','venta_javas.pagado','personas.nombre',
            'venta_javas.tipo_facturacion','users.usuario','observacionpriv','venta_javas.facturado',
            'venta_javas.factura_env','venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
        ->Users($ArrUsuarios)
        ->orderBy('venta_javas.idusuario', 'asc')->paginate(10);

        return ['ventas' => $ventas];
    }
    public function enviarPresupuestoMail(Request $request){

        $venta =  VentaJava::join('personas','venta_javas.idcliente','=','personas.id')
        ->join('users','venta_javas.idusuario','=','users.id')
        ->select('venta_javas.id','venta_javas.tipo_comprobante','venta_javas.num_comprobante',
            'venta_javas.created_at','venta_javas.impuesto','venta_javas.total','venta_javas.estado',
            'venta_javas.forma_pago','venta_javas.tiempo_entrega','venta_javas.lugar_entrega',
            'venta_javas.entregado','venta_javas.moneda','venta_javas.tipo_cambio', 'venta_javas.observacion',
            'venta_javas.num_cheque','venta_javas.banco','venta_javas.tipo_facturacion','venta_javas.pagado',
            'personas.nombre','personas.rfc','personas.domicilio','personas.ciudad',
            'personas.telefono','personas.email','users.usuario','venta_javas.entrega_parcial',
            'personas.company as contacto','personas.tel_company',
            'venta_javas.observacionpriv','venta_javas.facturado','venta_javas.factura_env',
            'venta_javas.pago_parcial','venta_javas.adeudo','venta_javas.auto_entrega')
        ->where('venta_javas.id',$request->id)->take(1)->get();

        $detalles = DetalleVentaJava::join('javas','detalle_venta_javas.idjava','=','javas.id')
        ->select('detalle_venta_javas.cantidad','detalle_venta_javas.precio','detalle_venta_javas.descuento',
            'javas.sku as articulo','javas.largo','javas.alto','javas.terminado',
            'javas.metros_cuadrados','javas.codigo','javas.ubicacion')
        ->where('detalle_venta_javas.idventajava',$request->id)
        ->orderBy('detalle_venta_javas.id','desc')->get();

        $numventa = VentaJava::select('num_comprobante')->where('id',$request->id)->get();

        $ivaagregado = VentaJava::select('impuesto')->where('id',$request->id)->get();

        $sumaMts = DB::table('javas')
        ->select(DB::raw('SUM(metros_cuadrados) as metros'))
        ->leftJoin('detalle_venta_javas','detalle_venta_javas.idjava','javas.id')
        ->where('detalle_venta_javas.idventajava',$request->id)
        ->get();

        $ventaD = VentaJava::findOrFail($request->id); //ID venta y sus depositos
        $deposits = $ventaD->deposits()
        ->select(DB::raw('SUM(deposits.total) as abonos'))
        ->get();

        $pdf = \PDF::loadView('pdf.venta',
            ['venta' => $venta,'detalles'=>$detalles,
            'ivaVenta' =>$ivaagregado[0]->impuesto,
            'sumaMts' => $sumaMts[0]->metros,
            'abonos' => $deposits[0]->abonos]);

        $data = array(
            'name'      =>  $request->name
        );

        $email = $request->mail;

        $numPre = $numventa[0]->num_comprobante;

        $usid = \Auth::user()->id;

        $mailUs = Persona::select('email')->where('id',$usid)->get();

        $emit = $mailUs[0]->email;

        Mail::to($email)->send(new MailPresupuesto($pdf->output(),$data,$numPre,$emit));
    }
    public function enviarFacturaMail(Request $request){
        $email = $request->mail;
        $data = array(
            'name'      =>  $request->name
        );
        $numventa = VentaJava::select('num_comprobante')->where('id',$request->id)->get();
        $numPre = $numventa[0]->num_comprobante;
        $usid = \Auth::user()->id;
        $mailUs = Persona::select('email')->where('id',$usid)->get();
        $emit = $mailUs[0]->email;
        $path = 'facturasfiles/' . $request->fileUrl;
        Mail::to($email)->send(new MailFactura($path,$data,$numPre,$emit));
    }
    public function cambiarSpecial(Request $request){

        if (!$request->ajax()) return redirect('/');
        $venta = VentaJava::findOrFail($request->id);
        $venta->special = $request->especial;
        $venta->save();
    }
}
