<?php

namespace App\Http\Controllers;

use App\Abrasivo;
use App\VentaAbrasivo;
use App\Credit;
use App\DetalleVentaAbrasivo;
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

class VentaAbrasivoController extends Controller
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
                    $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                    ->join('users','venta_abrasivos.idusuario','=','users.id')
                    ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                        'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                        'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                        'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                        'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                        'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                        'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega'
                        ,'special')
                    ->where([
                        ['personas.nombre', 'like', '%'. $buscar . '%'],
                        ['venta_abrasivos.estado',$estadoV]
                    ])
                    ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                }elseif($criterio == "user"){
                    $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                    ->join('users','venta_abrasivos.idusuario','=','users.id')
                    ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                        'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                        'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                        'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                        'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                        'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                        'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega'
                        ,'special')
                    ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado',$estadoV]])
                    ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                }else{
                    if ($buscar==''){
                        $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                        ->join('users','venta_abrasivos.idusuario','=','users.id')
                        ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                        'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                        'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                        'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                        'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                        'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                        'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega'
                        ,'special')
                        ->where('venta_abrasivos.estado',$estadoV)
                        ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                    }
                    else{
                        $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                        ->join('users','venta_abrasivos.idusuario','=','users.id')
                        ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                        'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                        'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                        'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                        'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                        'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                        'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega'
                        ,'special')
                        ->where([
                            ['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['venta_abrasivos.estado',$estadoV]
                        ])
                        ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                    }
                }

            }else{
                if($buscar==''){
                    if($entregaEs == 'entregado'){
                        if($pagadoEs == 'pagado'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega'
                                ,'special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entregado',1],
                                ['venta_abrasivos.pagado',1]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega'
                                ,'special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entregado',1],
                                ['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega'
                                ,'special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entregado',1],
                                ['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }else{
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega'
                                ,'special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entregado',1]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'entrega_parcial'){
                        if($pagadoEs == 'pagado'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',1],['venta_abrasivos.pagado',1]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);

                        }elseif($pagadoEs == 'parcial'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',1],
                                ['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);

                        }elseif($pagadoEs == 'nopagado'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',1],
                                ['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }else{
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',1]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'no_entregado'){
                        if($pagadoEs == 'pagado'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entregado',0],
                                ['venta_abrasivos.entrega_parcial',0],['venta_abrasivos.pagado',1]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entregado',0],
                                ['venta_abrasivos.entrega_parcial',0],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entregado',0],
                                ['venta_abrasivos.entrega_parcial',0],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }else{
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }
                    }else{
                        if($pagadoEs == 'pagado'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.pagado',1]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }else{
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado']])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }
                    }
                }
                else{
                    if($criterio == "cliente"){
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.entregado',1],
                                    ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.entregado',1],
                                    ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.entregado',1],
                                    ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.entregado',1],
                                    ['venta_abrasivos.estado','Registrado']])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entrega_parcial',1],['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);

                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','users.area','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entrega_parcial',1],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entrega_parcial',1],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entrega_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0],['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0],['venta_abrasivos.pagado',0],
                                        ['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0],['venta_abrasivos.pagado',0],
                                        ['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado']])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }
                    }elseif($criterio == "user"){
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',1],['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',1],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',1],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entrega_parcial',1],['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entrega_parcial',1],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entrega_parcial',1],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entrega_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0],['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0],['venta_abrasivos.pagado',0],
                                    ['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0],['venta_abrasivos.pagado',0],
                                    ['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado']])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }
                    }else{
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([ ['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',1],['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([ ['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',1],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([ ['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',1],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([ ['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entrega_parcial',1],['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entrega_parcial',1],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entrega_parcial',1],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entrega_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0],['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0],['venta_abrasivos.pagado',0],
                                    ['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0],['venta_abrasivos.pagado',0],
                                    ['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado']])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }
                    }
                }
            }
        }else{
            if($estadoV == "Anulada"){
                if($criterio == "cliente"){
                    $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                    ->join('users','venta_abrasivos.idusuario','=','users.id')
                    ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                        'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                        'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                        'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                        'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                        'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                        'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                    ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado',$estadoV],
                        ['venta_abrasivos.idusuario',$usid]])
                    ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                }elseif($criterio == "user"){
                    $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                    ->join('users','venta_abrasivos.idusuario','=','users.id')
                    ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                        'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                        'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                        'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                        'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                        'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                        'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                    ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado',$estadoV],
                        ['venta_abrasivos.idusuario',$usid]])
                    ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                }else{
                    if ($buscar==''){
                        $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                        ->join('users','venta_abrasivos.idusuario','=','users.id')
                        ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                            'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                            'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                            'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                            'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                            'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                            'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                        ->where([['venta_abrasivos.estado',$estadoV],['venta_abrasivos.idusuario',$usid]])
                        ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                    }
                    else{
                        $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                        ->join('users','venta_abrasivos.idusuario','=','users.id')
                        ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                        'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                        'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                        'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                        'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                        'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                        'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                        ->where([
                            ['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['venta_abrasivos.estado',$estadoV],
                            ['venta_abrasivos.idusuario',$usid]
                        ])
                        ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if($buscar==''){
                    if($entregaEs == 'entregado'){
                        if($pagadoEs == 'pagado'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entregado',1],['venta_abrasivos.idusuario',$usid],
                                ['venta_abrasivos.pagado',1]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entregado',1],['venta_abrasivos.idusuario',$usid],
                                ['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entregado',1],['venta_abrasivos.idusuario',$usid],
                                ['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }else{
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entregado',1],['venta_abrasivos.idusuario',$usid]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'entrega_parcial'){
                        if($pagadoEs == 'pagado'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([ ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',1],
                                ['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',1]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([ ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',1],
                                ['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([ ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',1],
                                ['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }else{
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([ ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',1],
                                ['venta_abrasivos.idusuario',$usid]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'no_entregado'){
                        if($pagadoEs == 'pagado'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'], ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0],
                                ['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',1]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'], ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0],
                                ['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'], ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0],
                                ['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }else{
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'], ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0],
                                ['venta_abrasivos.idusuario',$usid]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }
                    }else{
                        if($pagadoEs == 'pagado'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',1]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'parcial'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.idusuario',$usid],
                                ['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }elseif($pagadoEs == 'nopagado'){
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.idusuario',$usid],
                                ['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }else{
                            $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                            ->join('users','venta_abrasivos.idusuario','=','users.id')
                            ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                            ->where([['venta_abrasivos.estado','Registrado'],['venta_abrasivos.idusuario',$usid]])
                            ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                        }
                    }
                }
                else{
                    if($criterio == "cliente"){
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'], ['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',1],['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'], ['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',1],['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],
                                    ['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'], ['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',1],['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],
                                    ['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'], ['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',1],['venta_abrasivos.idusuario',$usid]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([ ['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.idusuario',$usid],
                                    ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',1],['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([ ['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.idusuario',$usid],
                                    ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',1],['venta_abrasivos.pagado',0],
                                    ['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([ ['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.idusuario',$usid],
                                    ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',1],['venta_abrasivos.pagado',0],
                                    ['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([ ['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.idusuario',$usid],
                                    ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.entregado',0],
                                    ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',0],
                                    ['venta_abrasivos.idusuario',$usid], ['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.entregado',0],
                                    ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',0],
                                    ['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.entregado',0],
                                    ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',0],
                                    ['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.entregado',0],
                                    ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',0],
                                    ['venta_abrasivos.idusuario',$usid]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.idusuario',$usid]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }
                    }elseif($criterio == "user"){
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',1],['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',1],['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],
                                    ['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',1],['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],
                                    ['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',1],['venta_abrasivos.idusuario',$usid]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entrega_parcial',1],['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entrega_parcial',1],['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],
                                    ['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entrega_parcial',1],['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],
                                    ['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entrega_parcial',1],['venta_abrasivos.idusuario',$usid]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0],['venta_abrasivos.idusuario',$usid],
                                    ['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0],['venta_abrasivos.idusuario',$usid],
                                    ['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0],['venta_abrasivos.idusuario',$usid],
                                    ['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.entrega_parcial',0],['venta_abrasivos.idusuario',$usid]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['users.usuario', 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.idusuario',$usid]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }
                    }else{
                        if($entregaEs == 'entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.entregado',1],
                                    ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.entregado',1],
                                    ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],
                                    ['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.entregado',1],
                                    ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],
                                    ['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.entregado',1],
                                    ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.idusuario',$usid]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'entrega_parcial'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.entrega_parcial',1],
                                    ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.entrega_parcial',1],
                                    ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],
                                    ['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.entrega_parcial',1],
                                    ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],
                                    ['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.entrega_parcial',1],
                                    ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.idusuario',$usid]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }elseif($entregaEs == 'no_entregado'){
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.idusuario',$usid],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',0],
                                    ['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.idusuario',$usid],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',0],
                                    ['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.idusuario',$usid],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',0],
                                    ['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.idusuario',$usid],
                                    ['venta_abrasivos.entregado',0],['venta_abrasivos.estado','Registrado'],['venta_abrasivos.entrega_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }
                        }else{
                            if($pagadoEs == 'pagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'parcial'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',1]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }elseif($pagadoEs == 'nopagado'){
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.idusuario',$usid],['venta_abrasivos.pagado',0],['venta_abrasivos.pago_parcial',0]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                            }else{
                                $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                                ->join('users','venta_abrasivos.idusuario','=','users.id')
                                ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                                    'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                                    'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                                    'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                                    'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                                    'venta_abrasivos.tipo_facturacion','users.usuario','users.area','observacionpriv','venta_abrasivos.facturado',
                                    'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega','special')
                                ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['venta_abrasivos.estado','Registrado'],
                                    ['venta_abrasivos.idusuario',$usid]])
                                ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
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
                    $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                    ->join('users','venta_abrasivos.idusuario','=','users.id')
                    ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                        'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                        'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                        'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                        'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                        'venta_abrasivos.tipo_facturacion','users.usuario','observacionpriv','venta_abrasivos.facturado',
                        'venta_abrasivos.factura_env','personas.rfc as rfccliente','venta_abrasivos.adeudo',
                        'venta_abrasivos.num_factura','venta_abrasivos.auto_entrega')
                    ->where([['venta_abrasivos.facturado',$estadoV],
                        ['venta_abrasivos.tipo_facturacion',$tipoFact],['venta_abrasivos.estado','Registrado'],
                        ['venta_abrasivos.idusuario',$usid]])
                    ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                }elseif($criterio == 'cliente'){
                    $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                    ->join('users','venta_abrasivos.idusuario','=','users.id')
                    ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                        'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                        'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                        'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                        'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                        'venta_abrasivos.tipo_facturacion','users.usuario','observacionpriv','venta_abrasivos.facturado',
                        'venta_abrasivos.factura_env','venta_abrasivos.adeudo','venta_abrasivos.num_factura','venta_abrasivos.auto_entrega')
                    ->where([['personas.nombre', 'like', '%'. $buscar . '%'],
                        ['venta_abrasivos.facturado',$estadoV],['venta_abrasivos.tipo_facturacion',$tipoFact],
                        ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.idusuario',$usid]])
                    ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                }else{
                    $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                    ->join('users','venta_abrasivos.idusuario','=','users.id')
                    ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                        'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                        'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                        'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                        'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                        'venta_abrasivos.tipo_facturacion','users.usuario','observacionpriv','venta_abrasivos.facturado',
                        'venta_abrasivos.factura_env','venta_abrasivos.adeudo','venta_abrasivos.num_factura','venta_abrasivos.auto_entrega')
                    ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['venta_abrasivos.facturado',$estadoV],['venta_abrasivos.tipo_facturacion',$tipoFact],
                        ['venta_abrasivos.estado','Registrado'],['venta_abrasivos.idusuario',$usid]])
                    ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                }
            }else{
                if($buscar==''){
                    $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                    ->join('users','venta_abrasivos.idusuario','=','users.id')
                    ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                        'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                        'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                        'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                        'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                        'venta_abrasivos.tipo_facturacion','users.usuario','observacionpriv','venta_abrasivos.facturado',
                        'venta_abrasivos.factura_env','personas.rfc as rfccliente','venta_abrasivos.adeudo',
                        'venta_abrasivos.num_factura','venta_abrasivos.auto_entrega')
                    ->where([['venta_abrasivos.facturado',$estadoV],
                        ['venta_abrasivos.tipo_facturacion',$tipoFact],['venta_abrasivos.estado','Registrado']])
                    ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                }elseif($criterio == 'cliente'){
                    $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                    ->join('users','venta_abrasivos.idusuario','=','users.id')
                    ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                        'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                        'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                        'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                        'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                        'venta_abrasivos.tipo_facturacion','users.usuario','observacionpriv','venta_abrasivos.facturado',
                        'venta_abrasivos.factura_env','venta_abrasivos.adeudo','venta_abrasivos.num_factura','venta_abrasivos.auto_entrega')
                    ->where([['personas.nombre', 'like', '%'. $buscar . '%'],
                        ['venta_abrasivos.facturado',$estadoV],['venta_abrasivos.tipo_facturacion',$tipoFact],
                        ['venta_abrasivos.estado','Registrado']])
                    ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
                }else{
                    $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
                    ->join('users','venta_abrasivos.idusuario','=','users.id')
                    ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
                        'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
                        'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
                        'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
                        'venta_abrasivos.entrega_parcial','venta_abrasivos.num_cheque','venta_abrasivos.pagado','personas.nombre',
                        'venta_abrasivos.tipo_facturacion','users.usuario','observacionpriv','venta_abrasivos.facturado',
                        'venta_abrasivos.factura_env','venta_abrasivos.adeudo','venta_abrasivos.num_factura','venta_abrasivos.auto_entrega')
                    ->where([['venta_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['venta_abrasivos.facturado',$estadoV],['venta_abrasivos.tipo_facturacion',$tipoFact],
                        ['venta_abrasivos.estado','Registrado']
                    ])
                    ->orderBy('venta_abrasivos.id', 'desc')->paginate(12);
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

            $venta = new VentaAbrasivo();
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
                $articulos = Abrasivo::where('codigo','=',$det['codigo'])->select('id')->first();
                $detalle = new DetalleVentaAbrasivo();
                $detalle->idventaabrasivo = $venta->id;
                $detalle->idabrasivo = $articulos->id;
                $detalle->cantidad = $det['cantidad'];
                $detalle->por_entregar = $det['cantidad'];
                $detalle->entregadas = 0;
                $detalle->pendientes = $det['cantidad'];
                $detalle->precio = $det['precio'];
                $detalle->descuento = $det['descuento'];
                $detalle->save();
            }


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

            $venta = VentaAbrasivo::findOrFail($request->id);
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
            $detalles = DetalleVentaAbrasivo::select('idabrasivo','cantidad')
                ->where('idventaabrasivo',$request->id)->get();

            foreach($detalles as $ep=>$det){
                $articulo = Abrasivo::findOrFail($det['idabrasivo']);
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
        $venta = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
        ->join('users','venta_abrasivos.idusuario','=','users.id')
        ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
            'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
            'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
            'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
            'venta_abrasivos.entrega_parcial','venta_abrasivos.tipo_facturacion', 'venta_abrasivos.pagado','users.usuario',
            'venta_abrasivos.num_cheque','venta_abrasivos.file','venta_abrasivos.observacionpriv','venta_abrasivos.facturado','venta_abrasivos.num_factura',
            'venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','personas.nombre as cliente',
            'personas.tipo','personas.rfc','personas.cfdi','personas.telefono','venta_abrasivos.auto_entrega',
            'personas.company as contacto','personas.tel_company as tel_contacto',
            'personas.id as idcliente','personas.email as EmailC','special')
        ->where('venta_abrasivos.id','=',$id)
        ->orderBy('venta_abrasivos.id', 'desc')->take(1)->get();

        return ['venta' => $venta];


        return ['venta' => $venta ];

    }
    public function obtenerDetalles(Request $request){

        //if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $detalles = DetalleVentaAbrasivo::join('abrasivos','detalle_venta_abrasivos.idabrasivo','=','abrasivos.id')
        ->select('detalle_venta_abrasivos.cantidad','detalle_venta_abrasivos.precio','detalle_venta_abrasivos.descuento',
            'detalle_venta_abrasivos.por_entregar','detalle_venta_abrasivos.pendientes',
            'detalle_venta_abrasivos.entregadas','detalle_venta_abrasivos.id','abrasivos.sku','abrasivos.codigo',
            'abrasivos.descripcion','abrasivos.ubicacion','abrasivos.file','abrasivos.descripcion','abrasivos.condicion')
        ->where('detalle_venta_abrasivos.idventaabrasivo',$id)
        ->orderBy('detalle_venta_abrasivos.id','desc')->get();

        return ['detalles' => $detalles];
    }
    public function pdf(Request $request,$id){

        $ventas =  VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
        ->join('users','venta_abrasivos.idusuario','=','users.id')
        ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
            'venta_abrasivos.created_at','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
            'venta_abrasivos.forma_pago','venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega',
            'venta_abrasivos.entregado','venta_abrasivos.moneda','venta_abrasivos.tipo_cambio', 'venta_abrasivos.observacion',
            'venta_abrasivos.num_cheque','venta_abrasivos.banco','venta_abrasivos.tipo_facturacion','venta_abrasivos.pagado',
            'personas.nombre','personas.rfc','personas.domicilio','personas.ciudad',
            'personas.telefono','personas.email','users.usuario','venta_abrasivos.entrega_parcial',
            'personas.company as contacto','personas.tel_company',
            'venta_abrasivos.observacionpriv','venta_abrasivos.facturado','venta_abrasivos.factura_env',
            'venta_abrasivos.pago_parcial','venta_abrasivos.adeudo','venta_abrasivos.auto_entrega')
        ->where('venta_abrasivos.id',$id)->take(1)->get();

        $detalles = DetalleVentaAbrasivo::join('abrasivos','detalle_venta_abrasivos.idabrasivo','=','abrasivos.id')
        ->select('detalle_venta_abrasivos.cantidad','detalle_venta_abrasivos.precio','detalle_venta_abrasivos.descuento',
            'abrasivos.sku as articulo','abrasivos.codigo','abrasivos.ubicacion')
        ->where('detalle_venta_abrasivos.idventaabrasivo',$id)
        ->orderBy('detalle_venta_abrasivos.id','desc')->get();

        $numventa = VentaAbrasivo::select('num_comprobante')->where('id',$id)->get();

        $ivaagregado = VentaAbrasivo::select('impuesto')->where('id',$id)->get();

        $ventaD = VentaAbrasivo::findOrFail($id); //ID venta y sus depositos
        $deposits = $ventaD->deposits()
        ->select(DB::raw('SUM(deposits.total) as abonos'))
        ->get();

        $capturas = Deposit::with('ventas')
        ->select('deposits.total','deposits.forma_pago','deposits.depositable_id','deposits.fecha_hora')
        ->where('deposits.depositable_id',$id)
        ->get();

        $pdf = \PDF::loadView('pdf.ventaAbrasivo',['ventas' => $ventas,'detalles'=> $detalles,
            'ivaVenta' => $ivaagregado[0]->impuesto, 'abonos' => $deposits[0]->abonos,
            'capturas' =>$capturas]);

        return $pdf->stream('venta-'.$numventa[0]->num_comprobante.'.pdf');


    }
    public function cambiarEntrega(Request $request){

        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $venta = VentaAbrasivo::findOrFail($request->id);
            $venta->entregado = $request->entregado;
            $venta->entrega_parcial = 0;
            $venta->save();

            $detalles = DetalleVentaAbrasivo::where('idventaabrasivo',$venta->id)->get();

            if($request->entregado == 1){
                foreach($detalles as $ep=>$det){
                    $detail = DetalleVentaAbrasivo::findOrFail($det['id']);
                    $detail->entregadas = $det['pendientes'];
                    $detail->pendientes = $det['pendientes']-$det['por_entregar'];
                    $detail->completado = 1;
                    $detail->save();
                }
            }else{
                foreach($detalles as $ep=>$det){
                    $detail = DetalleVentaAbrasivo::findOrFail($det['id']);
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
        $venta = VentaAbrasivo::findOrFail($request->id);
        $venta->entrega_parcial = $request->entrega_parcial;
        $venta->entregado = 0;
        $venta->save();
    }
    public function cambiarPagado(Request $request){
        if (!$request->ajax()) return redirect('/');

        /* $pag_par = 0; */

        if($request->pagado == 1){
            $venta = VentaAbrasivo::findOrFail($request->id);
            $venta->pagado = $request->pagado;
            $venta->adeudo = 0;
            $venta->auto_entrega = 1;
            $venta->save();
        }else{
            $venta = VentaAbrasivo::findOrFail($request->id);
            $venta->pagado = $request->pagado;
            $venta->adeudo = $venta->total;
            $venta->auto_entrega = 0;
            $venta->save();
        }


    }
    public function actualizarObservacion(Request $request){
        if (!$request->ajax()) return redirect('/');
        $venta = VentaAbrasivo::findOrFail($request->id);
        $venta->observacion = $request->observacion;
        $venta->save();
    }
    public function actualizarObservacionPriv(Request $request){
        if (!$request->ajax()) return redirect('/');
        $venta = VentaAbrasivo::findOrFail($request->id);
        $venta->observacionpriv = $request->observacionpriv;
        $venta->save();
    }
    public function getLastNum(Request $request){

        if (!$request->ajax()) return redirect('/');

        $lastNum = VentaAbrasivo::select('num_comprobante')->get()->last();

        if($lastNum != null){
            $noComp = explode('"',$lastNum);
            $SigNum = explode("-",$noComp[3]);
            return  $SigNum[2] + 1;
        }else{
            return 1;
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

                $vent= VentaAbrasivo::findOrFail($request->id);
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

            $venta = VentaAbrasivo::findOrFail($request->id);
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

        $ventas = VentaAbrasivo::join('personas','venta_abrasivos.idcliente','=','personas.id')
        ->join('users','venta_abrasivos.idusuario','=','users.id')
        ->select('venta_abrasivos.id','venta_abrasivos.tipo_comprobante','venta_abrasivos.num_comprobante',
            'venta_abrasivos.fecha_hora','venta_abrasivos.impuesto','venta_abrasivos.total','venta_abrasivos.estado',
            'venta_abrasivos.moneda','venta_abrasivos.tipo_cambio','venta_abrasivos.observacion','venta_abrasivos.forma_pago',
            'venta_abrasivos.tiempo_entrega','venta_abrasivos.lugar_entrega','venta_abrasivos.entregado','venta_abrasivos.banco',
            'venta_abrasivos.entrega_parcial','venta_abrasivos.tipo_facturacion', 'venta_abrasivos.pagado','users.usuario',
            'venta_abrasivos.num_cheque','personas.nombre','venta_abrasivos.file','venta_abrasivos.observacionpriv',
            'venta_abrasivos.facturado','venta_abrasivos.factura_env','venta_abrasivos.pago_parcial','venta_abrasivos.adeudo',
            'venta_abrasivos.auto_entrega')
        ->where([['venta_abrasivos.idcliente',$idcliente],['venta_abrasivos.estado','Registrado']])
        ->orderBy('venta_abrasivos.fecha_hora','desc')->paginate(5);

        return ['venta_abrasivos' => $ventas];
    }
    public function eliminarImagen(Request $request){
        if(!$request->ajax()) return redirect('/');

        $directoryName = 'entregas';
        //Check if the directory already exists.
        if(!is_dir($directoryName)){
            //Directory does not exist, so lets create it.
            mkdir($directoryName, 0777);
        }

        $art= VentaAbrasivo::findOrFail($request->id);
        $img = $art->file;

        if($img != null){
            $image_path = public_path($directoryName).'/'.$img;
            if(file_exists($image_path)){
                unlink($image_path);
                $fileName = null;
            }
        }
        $venta = VentaAbrasivo::findOrFail($request->id);
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
        $venta = VentaAbrasivo::findOrFail($request->id);
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
        $venta =VentaAbrasivo::findOrFail($request->id);

        $venta->facturado= $request->facturado;
            $venta->pagado = 0;
            $venta->save();

    }
    public function cambiarFacturacionEnv(Request $request){
        if (!$request->ajax()) return redirect('/');
        $venta = VentaAbrasivo::findOrFail($request->id);
        $venta->factura_env = $request->estadoEn;
        $venta->save();
    }
    public function crearDeposit(Request $request){

        if (!$request->ajax()) return redirect('/');

        $mytime = Carbon::now('America/Mexico_City');

        $venta = VentaAbrasivo::findOrFail($request->id); //Venta a depositar
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

        $venta = VentaAbrasivo::findOrFail($request->id); //ID venta y sus depositos

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
            $venta = VentaAbrasivo::findOrFail($request->idventa);
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

        $ventas= Venta::findOrFail($request->id); //ID del project
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

            $venta = new Venta();
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
                $detalle = new DetalleVenta();
                $detalle->idventa = $venta->id;
                $detalle->idarticulo = $det['idarticulo'];
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
                $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                ->join('users','ventas.idusuario','=','users.id')
                ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                ->where([
                    ['personas.nombre', 'like', '%'. $buscar . '%'],
                    ['ventas.estado',$estadoV]
                ])
                ->orderBy('ventas.id', 'desc')->paginate(12);
            }else{
                if ($buscar==''){
                    $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                    ->join('users','ventas.idusuario','=','users.id')
                    ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                    'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                    'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                    'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                    'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                    'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                    'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                    ->where('ventas.estado',$estadoV)
                    ->orderBy('ventas.id', 'desc')->paginate(12);
                }else{
                    $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                    ->join('users','ventas.idusuario','=','users.id')
                    ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                    'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                    'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                    'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                    'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                    'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                    'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                    ->where([
                        ['ventas.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['ventas.estado',$estadoV]
                    ])
                    ->orderBy('ventas.id', 'desc')->paginate(12);
                }
            }
        }else{
            if($buscar==''){
                if($entregaEs == 'entregado'){
                    if($estadoAdeu == ''){
                        $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                        ->join('users','ventas.idusuario','=','users.id')
                        ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                        ->where([
                            ['ventas.estado','Registrado'],
                            ['ventas.entregado',1]
                        ])
                        ->orderBy('ventas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Pagado'){
                        $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                        ->join('users','ventas.idusuario','=','users.id')
                        ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                        ->where([
                            ['ventas.estado','Registrado'],
                            ['ventas.entregado',1],
                            ['ventas.pagado',1],['ventas.adeudo',0]
                        ])
                        ->orderBy('ventas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Abonado'){
                        $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                        ->join('users','ventas.idusuario','=','users.id')
                        ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                        ->where([
                            ['ventas.estado','Registrado'],
                            ['ventas.entregado',1],
                            ['ventas.pago_parcial',1]
                        ])
                        ->orderBy('ventas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'NoAbono'){
                        $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                        ->join('users','ventas.idusuario','=','users.id')
                        ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                        ->where([
                            ['ventas.estado','Registrado'],
                            ['ventas.entregado',1],
                            ['ventas.adeudo','=','ventas.total']
                        ])
                        ->orderBy('ventas.id', 'desc')->paginate(12);
                    }
                }elseif($entregaEs == 'entrega_parcial'){
                    if($estadoAdeu == ''){
                        $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                        ->join('users','ventas.idusuario','=','users.id')
                        ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                        ->where([
                            ['ventas.estado','Registrado'],
                            ['ventas.entrega_parcial',1]
                        ])
                        ->orderBy('ventas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Pagado'){
                        $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                        ->join('users','ventas.idusuario','=','users.id')
                        ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                        ->where([
                            ['ventas.estado','Registrado'],
                            ['ventas.entrega_parcial',1],
                            ['ventas.pagado',1],['ventas.adeudo',0]
                        ])
                        ->orderBy('ventas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Abonado'){
                        $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                        ->join('users','ventas.idusuario','=','users.id')
                        ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                        ->where([
                            ['ventas.estado','Registrado'],
                            ['ventas.entrega_parcial',1],
                            ['ventas.pago_parcial',1]
                        ])
                        ->orderBy('ventas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'NoAbono'){
                        $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                        ->join('users','ventas.idusuario','=','users.id')
                        ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                        ->where([
                            ['ventas.estado','Registrado'],
                            ['ventas.entrega_parcial',1],
                            ['ventas.adeudo','=','ventas.total']
                        ])
                        ->orderBy('ventas.id', 'desc')->paginate(12);
                    }
                }elseif($entregaEs == 'no_entregado'){
                    if($estadoAdeu == ''){
                        $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                        ->join('users','ventas.idusuario','=','users.id')
                        ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                        ->where([
                            ['ventas.estado','Registrado'],
                            ['ventas.entregado',0],
                            ['ventas.entrega_parcial',0]
                        ])
                        ->orderBy('ventas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Pagado'){
                        $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                        ->join('users','ventas.idusuario','=','users.id')
                        ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                        ->where([
                            ['ventas.estado','Registrado'],
                            ['ventas.entregado',0],
                            ['ventas.entrega_parcial',0],
                            ['ventas.pagado',1],['ventas.adeudo',0]
                        ])
                        ->orderBy('ventas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Abonado'){
                        $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                        ->join('users','ventas.idusuario','=','users.id')
                        ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                        ->where([
                            ['ventas.estado','Registrado'],
                            ['ventas.entregado',0],
                            ['ventas.entrega_parcial',0],
                            ['ventas.pago_parcial',1]

                        ])
                        ->orderBy('ventas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'NoAbono'){
                        $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                        ->join('users','ventas.idusuario','=','users.id')
                        ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                        ->where([
                            ['ventas.estado','Registrado'],
                            ['ventas.entregado',0],
                            ['ventas.entrega_parcial',0],
                            ['ventas.adeudo','=','ventas.total']
                        ])
                        ->orderBy('ventas.id', 'desc')->paginate(12);
                    }
                }else{
                    if($estadoAdeu == ''){
                        $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                        ->join('users','ventas.idusuario','=','users.id')
                        ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                        ->where([['ventas.estado','Registrado']])
                        ->orderBy('ventas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Pagado'){
                        $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                        ->join('users','ventas.idusuario','=','users.id')
                        ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                        ->where([['ventas.estado','Registrado'],['ventas.pagado',1],['ventas.adeudo',0]])
                        ->orderBy('ventas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'Abonado'){
                        $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                        ->join('users','ventas.idusuario','=','users.id')
                        ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                        ->where([['ventas.estado','Registrado'],['ventas.pago_parcial',1]])
                        ->orderBy('ventas.id', 'desc')->paginate(12);
                    }elseif($estadoAdeu == 'NoAbono'){
                        $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                        ->join('users','ventas.idusuario','=','users.id')
                        ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                        ->where([['ventas.estado','Registrado'],['ventas.total','ventas.adeudo']])
                        ->orderBy('ventas.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if($criterio == "cliente"){
                    if($entregaEs == 'entregado'){
                        if($estadoAdeu == ''){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entregado',1]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entregado',1],
                                ['ventas.pagado',1],['ventas.adeudo',0]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entregado',1],
                                ['ventas.pago_parcial',1]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entregado',1],
                                ['ventas.adeudo','=','ventas.total']
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }

                    }elseif($entregaEs == 'entrega_parcial'){
                        if($estadoAdeu == ''){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entrega_parcial',1]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entrega_parcial',1],
                                ['ventas.pagado',1],['ventas.adeudo',0]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);

                        }elseif($estadoAdeu == 'Abonado'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                                'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                                'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                                'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entrega_parcial',1],
                                ['ventas.pago_parcial',1]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);

                        }elseif($estadoAdeu == 'NoAbono'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entrega_parcial',1],
                                ['ventas.adeudo','=','ventas.total']
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'no_entregado'){
                        if($estadoAdeu == ''){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entregado',0],
                                ['ventas.entrega_parcial',0]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entregado',0],
                                ['ventas.entrega_parcial',0],
                                ['ventas.pagado',1],['ventas.adeudo',0]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entregado',0],
                                ['ventas.entrega_parcial',0],
                                ['ventas.pago_parcial',1]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entregado',0],
                                ['ventas.entrega_parcial',0],
                                ['ventas.adeudo','=','ventas.total']
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }
                    }else{
                        if($estadoAdeu == ''){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado']
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.pagado',1],['ventas.adeudo',0]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.pago_parcial',1]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['personas.nombre', 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.adeudo','=','ventas.total']
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }
                    }
                }else{
                    if($entregaEs == 'entregado'){
                        if($estadoAdeu == ''){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                                'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                                'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                                'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['ventas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entregado',1]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                                'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                                'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                                'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['ventas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entregado',1],
                                ['ventas.pagado',1],['ventas.adeudo',0]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                                'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                                'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                                'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['ventas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entregado',1],
                                ['ventas.pago_parcial',1]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                                'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                                'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                                'ventas.factura_env','ventas.pago_parcial','ventas.adeudo')
                            ->where([
                                ['ventas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entregado',1],
                                ['ventas.adeudo','=','ventas.total']
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'entrega_parcial'){
                        if($estadoAdeu == ''){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                                'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                                'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                                'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['ventas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entrega_parcial',1]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                                'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                                'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                                'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['ventas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entrega_parcial',1],
                                ['ventas.pagado',1],['ventas.adeudo',0]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                                'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                                'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                                'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['ventas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entrega_parcial',1],
                                ['ventas.pago_parcial',1]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                                'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                                'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                                'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['ventas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entrega_parcial',1],
                                ['ventas.adeudo','=','ventas.total']
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }
                    }elseif($entregaEs == 'no_entregado'){
                        if($estadoAdeu == ''){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                                'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                                'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                                'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['ventas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entregado',0],
                                ['ventas.entrega_parcial',0]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                                'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                                'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                                'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['ventas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entregado',0],
                                ['ventas.entrega_parcial',0],
                                ['ventas.pagado',1],['ventas.adeudo',0]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                                'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                                'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                                'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['ventas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entregado',0],
                                ['ventas.entrega_parcial',0],
                                ['ventas.pago_parcial',1]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                                'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                                'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                                'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['ventas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.entregado',0],
                                ['ventas.entrega_parcial',0],
                                ['ventas.adeudo','=','ventas.total']
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }
                    }else{
                        if($estadoAdeu == ''){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                                'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                                'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                                'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['ventas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado']
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Pagado'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                                'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                                'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                                'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['ventas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.pagado',1],['ventas.adeudo',0]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'Abonado'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                                'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                                'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                                'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['ventas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.pago_parcial',1]
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
                        }elseif($estadoAdeu == 'NoAbono'){
                            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
                            ->join('users','ventas.idusuario','=','users.id')
                            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                                'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
                                'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
                                'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
                            ->where([
                                ['ventas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['ventas.estado','Registrado'],
                                ['ventas.adeudo','=','ventas.total']
                            ])
                            ->orderBy('ventas.id', 'desc')->paginate(12);
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
            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
            ->join('users','ventas.idusuario','=','users.id')
            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                'ventas.entrega_parcial','ventas.tipo_facturacion', 'ventas.pagado','users.usuario',
                'ventas.num_cheque','personas.nombre','ventas.file','ventas.observacionpriv',
                'ventas.facturado','ventas.factura_env','ventas.pago_parcial','ventas.adeudo',
                'ventas.auto_entrega','ventas.special')
            ->where([
                ['ventas.estado','Registrado'],
                ['ventas.'.$criterio, 'like', '%'. $buscar . '%'],
                ['ventas.special', 1]
            ])
            ->orderBy('ventas.fecha_hora','desc')->paginate(10);
        }else{
            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
            ->join('users','ventas.idusuario','=','users.id')
            ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
                'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
                'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
                'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
                'ventas.entrega_parcial','ventas.tipo_facturacion', 'ventas.pagado','users.usuario',
                'ventas.num_cheque','personas.nombre','ventas.file','ventas.observacionpriv',
                'ventas.facturado','ventas.factura_env','ventas.pago_parcial','ventas.adeudo',
                'ventas.auto_entrega','ventas.special')
            ->where([
                    ['ventas.idcliente','=',$idcliente],
                    ['ventas.estado','Registrado'],
                    ['ventas.special', 1]
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

            $venta = Venta::findOrFail($request->id);
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
                $venta = Venta::findOrFail($request->id);
                $venta->documents()->save($docum);
                DB::commit();
            }

        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function getDocs(Request $request){
        if (!$request->ajax()) return redirect('/');
        $venta = Venta::findOrFail($request->id); //ID dueño de los archivos
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

        $venta = Venta::findOrFail($request->id); //Venta a depositar
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

        $ventas =  Venta::join('personas','ventas.idcliente','=','personas.id')
        ->join('users','ventas.idusuario','=','users.id')
        ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
            'ventas.forma_pago','ventas.tiempo_entrega','ventas.lugar_entrega',
            'ventas.entregado','ventas.moneda','ventas.tipo_cambio', 'ventas.observacion',
            'ventas.num_cheque','ventas.banco','ventas.tipo_facturacion','ventas.pagado',
            'personas.nombre','personas.rfc','personas.domicilio','personas.ciudad',
            'personas.telefono','personas.email','users.usuario','ventas.entrega_parcial',
            'personas.company as contacto','personas.tel_company',
            'ventas.observacionpriv','ventas.facturado','ventas.factura_env',
            'ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
        ->where([['ventas.idcliente',$id],['ventas.estado','Registrado']])
        ->whereBetween('ventas.fecha_hora', [$fullDate1, $fullDate2])
        ->orderBy('ventas.pagado','asc')->orderBy('ventas.entregado','asc')->get();

        $detalles = DetalleVenta::join('articulos','detalle_ventas.idarticulo','=','articulos.id')
        ->Leftjoin('ventas','ventas.id','detalle_ventas.idventa')
        ->select('detalle_ventas.idventa','detalle_ventas.cantidad','detalle_ventas.precio','detalle_ventas.descuento',
            'articulos.sku as articulo','articulos.largo','articulos.alto','articulos.terminado',
            'articulos.metros_cuadrados','articulos.codigo','articulos.ubicacion')
        ->where([['ventas.idcliente',$id],['ventas.estado','Registrado']])
        ->whereBetween('ventas.fecha_hora', [$fullDate1, $fullDate2])
        ->orderBy('ventas.pagado','asc')->orderBy('ventas.entregado','asc')->get();

        $sumaVentas =  Venta::select(DB::raw('SUM(total) as total'))
        ->where([['ventas.idcliente',$id],['ventas.estado','Registrado']])
        ->whereBetween('ventas.fecha_hora', [$fullDate1, $fullDate2])->get();

        $adedudadas =  Venta::join('personas','ventas.idcliente','=','personas.id')
        ->join('users','ventas.idusuario','=','users.id')
        ->select('ventas.id','ventas.pagado','ventas.tipo_comprobante','ventas.num_comprobante',
            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
            'ventas.forma_pago','ventas.tiempo_entrega','ventas.lugar_entrega',
            'personas.nombre as cliente','ventas.entregado','ventas.tipo_facturacion',
            'ventas.pago_parcial','ventas.adeudo','users.usuario')
        ->where([['ventas.idcliente',$id],['ventas.estado','Registrado'],
            ['ventas.pagado',0],['ventas.pago_parcial',0]])
        ->orderBy('ventas.pagado','asc')->orderBy('ventas.entregado','asc')
        ->whereBetween('ventas.fecha_hora', [$fullDate1, $fullDate2])->get();

        $sumaAdedudadas =  Venta::select(DB::raw('SUM(total) as total'))
        ->where([['ventas.idcliente',$id],['ventas.estado','Registrado'],
            ['ventas.pagado',0],['ventas.pago_parcial',0]])
        ->whereBetween('ventas.fecha_hora', [$fullDate1, $fullDate2])->get();

        $parciales =  Venta::join('personas','ventas.idcliente','=','personas.id')
        ->join('users','ventas.idusuario','=','users.id')
        ->select('ventas.id','ventas.pagado','ventas.tipo_comprobante','ventas.num_comprobante',
            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
            'ventas.forma_pago','ventas.tiempo_entrega','ventas.lugar_entrega',
            'ventas.entregado','ventas.tipo_facturacion','ventas.pago_parcial',
            'ventas.adeudo','users.usuario','personas.nombre as cliente')
        ->where([['ventas.idcliente',$id],['ventas.estado','Registrado'],
            ['ventas.pagado',0],['ventas.pago_parcial',1]])
        ->whereBetween('ventas.fecha_hora', [$fullDate1, $fullDate2])
        ->orderBy('ventas.pagado','asc')->orderBy('ventas.entregado','asc')->get();

        $sumaParciales =  Venta::select(DB::raw('SUM(adeudo) as adeudo'))
        ->where([['ventas.idcliente',$id],['ventas.estado','Registrado'],
            ['ventas.pagado',0],['ventas.pago_parcial',1]])
        ->whereBetween('ventas.fecha_hora', [$fullDate1, $fullDate2])->get();

        $pdf = \PDF::loadView('pdf.ventasCliente',['ventas' => $ventas,'cliente' => $cliente,'detalles' => $detalles,
            'sumaVentas' => $sumaVentas,'adedudadas' => $adedudadas,'sumaAdedudadas' => $sumaAdedudadas,
            'parciales' => $parciales,'sumaParciales' => $sumaParciales,'inicio' => $fullDate1, 'fin' => $fullDate2]);

        return $pdf->stream('ventas-'.$cliente->nombre.'-'.$cliente->num_documento.'.pdf');

        //return ['parciales' => $parciales,'sumaParciales' => $sumaParciales];
    }
    public function ventasUsuariosExcel(Request $request){

        $ArrUsuarios = [1,9];

        $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
        ->join('users','ventas.idusuario','=','users.id')
        ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
            'ventas.entrega_parcial','ventas.num_cheque','ventas.pagado','personas.nombre',
            'ventas.tipo_facturacion','users.usuario','observacionpriv','ventas.facturado',
            'ventas.factura_env','ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
        ->Users($ArrUsuarios)
        ->orderBy('ventas.idusuario', 'asc')->paginate(10);

        return ['ventas' => $ventas];
    }
    public function enviarPresupuestoMail(Request $request){

        $venta =  Venta::join('personas','ventas.idcliente','=','personas.id')
        ->join('users','ventas.idusuario','=','users.id')
        ->select('ventas.id','ventas.tipo_comprobante','ventas.num_comprobante',
            'ventas.created_at','ventas.impuesto','ventas.total','ventas.estado',
            'ventas.forma_pago','ventas.tiempo_entrega','ventas.lugar_entrega',
            'ventas.entregado','ventas.moneda','ventas.tipo_cambio', 'ventas.observacion',
            'ventas.num_cheque','ventas.banco','ventas.tipo_facturacion','ventas.pagado',
            'personas.nombre','personas.rfc','personas.domicilio','personas.ciudad',
            'personas.telefono','personas.email','users.usuario','ventas.entrega_parcial',
            'personas.company as contacto','personas.tel_company',
            'ventas.observacionpriv','ventas.facturado','ventas.factura_env',
            'ventas.pago_parcial','ventas.adeudo','ventas.auto_entrega')
        ->where('ventas.id',$request->id)->take(1)->get();

        $detalles = DetalleVenta::join('articulos','detalle_ventas.idarticulo','=','articulos.id')
        ->select('detalle_ventas.cantidad','detalle_ventas.precio','detalle_ventas.descuento',
            'articulos.sku as articulo','articulos.largo','articulos.alto','articulos.terminado',
            'articulos.metros_cuadrados','articulos.codigo','articulos.ubicacion')
        ->where('detalle_ventas.idventa',$request->id)
        ->orderBy('detalle_ventas.id','desc')->get();

        $numventa = Venta::select('num_comprobante')->where('id',$request->id)->get();

        $ivaagregado = Venta::select('impuesto')->where('id',$request->id)->get();

        $sumaMts = DB::table('articulos')
        ->select(DB::raw('SUM(metros_cuadrados) as metros'))
        ->leftJoin('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
        ->where('detalle_ventas.idventa',$request->id)
        ->get();

        $ventaD = Venta::findOrFail($request->id); //ID venta y sus depositos
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
        $numventa = Venta::select('num_comprobante')->where('id',$request->id)->get();
        $numPre = $numventa[0]->num_comprobante;
        $usid = \Auth::user()->id;
        $mailUs = Persona::select('email')->where('id',$usid)->get();
        $emit = $mailUs[0]->email;
        $path = 'facturasfiles/' . $request->fileUrl;
        Mail::to($email)->send(new MailFactura($path,$data,$numPre,$emit));
    }
    public function cambiarSpecial(Request $request){

        if (!$request->ajax()) return redirect('/');
        $venta = Venta::findOrFail($request->id);
        $venta->special = $request->especial;
        $venta->save();
    }
}
