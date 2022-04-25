<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProjectsExport;
use App\Document;
use App\Project;
use App\Deposit;
use App\DetalleVenta;
use App\Venta;
use App\User;
use App\Credit;
use App\Exports\AbonosProjectExport;
use App\Persona;
use App\Mail\MailFactura;
use Exception;
use Illuminate\Support\Facades\Mail;

class ProjectController extends Controller
{
    public function index(Request $request){
        if (!$request->ajax()) return redirect('/');

        $estado = $request->estado;
        $entrega = $request->estadoEntrega;
        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $usrol = \Auth::user()->idrol;

        if($estado == ''){
            if($entrega == ''){
                if($buscar == ''){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }elseif($criterio == 'cliente'){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['clientes.nombre', 'like', '%'. $buscar . '%']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }else{
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.'.$criterio, 'like', '%'. $buscar . '%']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }
            }elseif($entrega == 'parcial'){
                if($buscar == ''){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.entregado_parcial',1]])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }elseif($criterio == 'cliente'){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.entregado_parcial',1],['clientes.nombre', 'like', '%'. $buscar . '%']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }else{
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.entregado_parcial',1],['projects.'.$criterio, 'like', '%'. $buscar . '%']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }
            }elseif($entrega == 'completa'){
                if($buscar == ''){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.entregado',1]])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }elseif($criterio == 'cliente'){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.entregado',1],['clientes.nombre', 'like', '%'. $buscar . '%']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }else{
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.entregado',1],['projects.'.$criterio, 'like', '%'. $buscar . '%']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }
            }elseif($entrega == 'no_entregado'){
                if($buscar == ''){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.entregado',0],['projects.entregado_parcial',0]])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }elseif($criterio == 'cliente'){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['clientes.nombre', 'like', '%'. $buscar . '%'],['projects.entregado',0],
                        ['projects.entregado_parcial',0]])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }else{
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.'.$criterio, 'like', '%'. $buscar . '%'],['projects.entregado',0],
                        ['projects.entregado_parcial',0]])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }
            }
        }elseif($estado == 'Registradas'){
            if($entrega == ''){
                if($buscar == ''){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Registrado']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }elseif($criterio == 'cliente'){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Registrado'],['clientes.nombre', 'like', '%'. $buscar . '%']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }else{
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Registrado'],['projects.'.$criterio, 'like', '%'. $buscar . '%']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }
            }elseif($entrega == 'parcial'){
                if($buscar == ''){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Registrado'],['projects.entregado_parcial',1]])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }elseif($criterio == 'cliente'){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Registrado'],['projects.entregado_parcial',1],
                        ['clientes.nombre', 'like', '%'. $buscar . '%']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }else{
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Registrado'],['projects.entregado_parcial',1],
                        ['projects.'.$criterio, 'like', '%'. $buscar . '%']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }
            }elseif($entrega == 'completa'){
                if($buscar == ''){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Registrado'],['projects.entregado',1]])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }elseif($criterio == 'cliente'){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Registrado'],['projects.entregado',1],
                        ['clientes.nombre', 'like', '%'. $buscar . '%']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }else{
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Registrado'],['projects.entregado',1],
                        ['projects.'.$criterio, 'like', '%'. $buscar . '%']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }
            }elseif($entrega == 'no_entregado'){
                if($buscar == ''){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Registrado'],['projects.entregado',0],
                        ['projects.entregado_parcial',0]])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }elseif($criterio == 'cliente'){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Registrado'],['clientes.nombre', 'like', '%'. $buscar . '%'],
                        ['projects.entregado',0],['projects.entregado_parcial',0]])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }else{
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Registrado'],['projects.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['projects.entregado',0],['projects.entregado_parcial',0]])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }
            }
        }elseif($estado == 'Anuladas'){
            if($entrega == ''){
                if($buscar == ''){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Anulado']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }elseif($criterio == 'cliente'){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Anulado'],['clientes.nombre', 'like', '%'. $buscar . '%']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }else{
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Anulado'],['projects.'.$criterio, 'like', '%'. $buscar . '%']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }
            }elseif($entrega == 'parcial'){
                if($buscar == ''){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Anulado'],['projects.entregado_parcial',1]])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }elseif($criterio == 'cliente'){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Anulado'],['projects.entregado_parcial',1],
                        ['clientes.nombre', 'like', '%'. $buscar . '%']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }else{
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Anulado'],['projects.entregado_parcial',1],
                        ['projects.'.$criterio, 'like', '%'. $buscar . '%']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }
            }elseif($entrega == 'completa'){
                if($buscar == ''){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Anulado'],['projects.entregado',1]])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }elseif($criterio == 'cliente'){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Anulado'],['projects.entregado',1],
                        ['clientes.nombre', 'like', '%'. $buscar . '%']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }else{
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Anulado'],['projects.entregado',1],
                        ['projects.'.$criterio, 'like', '%'. $buscar . '%']])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }
            }elseif($entrega == 'no_entregado'){
                if($buscar == ''){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Anulado'],['projects.entregado',0],
                        ['projects.entregado_parcial',0]])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }elseif($criterio == 'cliente'){
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Anulado'],['clientes.nombre', 'like', '%'. $buscar . '%'],
                        ['projects.entregado',0],['projects.entregado_parcial',0]])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }else{
                    $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
                    ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
                    ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                        'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                        'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                        'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion','projects.facturado',
                        'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                        'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                        'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                        'projects.created_at as registro')
                    ->where([['projects.estado','Anulado'],['projects.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['projects.entregado',0],['projects.entregado_parcial',0]])
                    ->orderBy('projects.id', 'desc')->paginate(12);
                }
            }
        }

        return [
            'pagination' => [
                'total'        => $projects->total(),
                'current_page' => $projects->currentPage(),
                'per_page'     => $projects->perPage(),
                'last_page'    => $projects->lastPage(),
                'from'         => $projects->firstItem(),
                'to'           => $projects->lastItem(),
            ],
            'projects' => $projects,
            'userrol' => $usrol
        ];
    }
    public function indexProject(Request $request){
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $estadoV = $request->estado;
        $tipoFact = $request->tipofact;

        $usrol = \Auth::user()->idrol;
        $usarea = \Auth::user()->area;
        $usid = \Auth::user()->id;

        if($usarea == 'SLP'){
            if ($buscar==''){
                $projects = Project::join('personas','projects.idcliente','=','personas.id')
                ->join('users','projects.idusuario','=','users.id')
                ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante',
                    'projects.impuesto','projects.total','projects.estado',
                    'projects.observacion','projects.forma_pago','projects.fin',
                    'projects.lugar_entrega','projects.entregado','projects.entregado_parcial',
                    'projects.pagado','personas.nombre','projects.tipo_facturacion','users.usuario',
                    'observacionpriv','projects.facturado', 'projects.factura_env','users.area',
                    'personas.rfc as rfccliente','projects.adeudo', 'projects.num_factura')
                ->where([['projects.adeudo',0],['projects.facturado',$estadoV],
                    ['projects.tipo_facturacion',$tipoFact],['projects.estado','Registrado'],
                    ['projects.idusuario',$usid]])
                ->orderBy('projects.id', 'desc')->paginate(12);
            }elseif($criterio == 'cliente'){
                $projects = Project::join('personas','projects.idcliente','=','personas.id')
                ->join('users','projects.idusuario','=','users.id')
                ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante',
                'projects.impuesto','projects.total','projects.estado',
                'projects.observacion','projects.forma_pago','projects.fin',
                'projects.lugar_entrega','projects.entregado','projects.entregado_parcial',
                'projects.pagado','personas.nombre','projects.tipo_facturacion','users.usuario',
                'observacionpriv','projects.facturado', 'projects.factura_env','users.area',
                'personas.rfc as rfccliente','projects.adeudo', 'projects.num_factura')
                ->where([['projects.adeudo',0],['personas.nombre', 'like', '%'. $buscar . '%'],
                    ['projects.facturado',$estadoV],['projects.tipo_facturacion',$tipoFact],
                    ['projects.estado','Registrado'],['projects.idusuario',$usid]])
                ->orderBy('projects.id', 'desc')->paginate(12);
            }else{
                $projects = Project::join('personas','projects.idcliente','=','personas.id')
                ->join('users','projects.idusuario','=','users.id')
                ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante',
                'projects.impuesto','projects.total','projects.estado',
                'projects.observacion','projects.forma_pago','projects.fin',
                'projects.lugar_entrega','projects.entregado','projects.entregado_parcial',
                'projects.pagado','personas.nombre','projects.tipo_facturacion','users.usuario',
                'observacionpriv','projects.facturado', 'projects.factura_env','users.area',
                'personas.rfc as rfccliente','projects.adeudo', 'projects.num_factura')
                ->where([['projects.adeudo',0],['projects.'.$criterio, 'like', '%'. $buscar . '%'],
                    ['projects.facturado',$estadoV],['projects.tipo_facturacion',$tipoFact],
                    ['projects.estado','Registrado'],['projects.idusuario',$usid]])
                ->orderBy('projects.id', 'desc')->paginate(12);
            }
        }else{
            if($buscar==''){
                $projects = Project::join('personas','projects.idcliente','=','personas.id')
                ->join('users','projects.idusuario','=','users.id')
                ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante',
                'projects.impuesto','projects.total','projects.estado',
                'projects.observacion','projects.forma_pago','projects.fin',
                'projects.lugar_entrega','projects.entregado','projects.entregado_parcial',
                'projects.pagado','personas.nombre','projects.tipo_facturacion','users.usuario',
                'observacionpriv','projects.facturado', 'projects.factura_env','users.area',
                'personas.rfc as rfccliente','projects.adeudo', 'projects.num_factura')
                ->where([['projects.adeudo',0],['projects.facturado',$estadoV],
                    ['projects.tipo_facturacion',$tipoFact],['projects.estado','Registrado']])
                ->orderBy('projects.id', 'desc')->paginate(12);
            }elseif($criterio == 'cliente'){
                $projects = Project::join('personas','projects.idcliente','=','personas.id')
                ->join('users','projects.idusuario','=','users.id')
                ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante',
                'projects.impuesto','projects.total','projects.estado',
                'projects.observacion','projects.forma_pago','projects.fin',
                'projects.lugar_entrega','projects.entregado','projects.entregado_parcial',
                'projects.pagado','personas.nombre','projects.tipo_facturacion','users.usuario',
                'observacionpriv','projects.facturado', 'projects.factura_env','users.area',
                'personas.rfc as rfccliente','projects.adeudo', 'projects.num_factura')
                ->where([['projects.adeudo',0],['personas.nombre', 'like', '%'. $buscar . '%'],
                    ['projects.facturado',$estadoV],['projects.tipo_facturacion',$tipoFact],
                    ['projects.estado','Registrado']])
                ->orderBy('projects.id', 'desc')->paginate(12);
            }else{
                $projects = Project::join('personas','projects.idcliente','=','personas.id')
                ->join('users','projects.idusuario','=','users.id')
                ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante',
                'projects.impuesto','projects.total','projects.estado',
                'projects.observacion','projects.forma_pago','projects.fin',
                'projects.lugar_entrega','projects.entregado','projects.entregado_parcial',
                'projects.pagado','personas.nombre','projects.tipo_facturacion','users.usuario',
                'observacionpriv','projects.facturado', 'projects.factura_env','users.area',
                'personas.rfc as rfccliente','projects.adeudo', 'projects.num_factura')
                ->where([['projects.adeudo',0],['projects.'.$criterio, 'like', '%'. $buscar . '%'],
                    ['projects.facturado',$estadoV],['projects.tipo_facturacion',$tipoFact],
                    ['projects.estado','Registrado']
                ])
                ->orderBy('projects.id', 'desc')->paginate(12);
            }
        }
        return [
            'pagination' => [
                'total'        => $projects->total(),
                'current_page' => $projects->currentPage(),
                'per_page'     => $projects->perPage(),
                'last_page'    => $projects->lastPage(),
                'from'         => $projects->firstItem(),
                'to'           => $projects->lastItem(),
            ],
            'projects' => $projects,
            'userrol' => $usrol
        ];

    }
    public function store(Request $request){
        if(!$request->ajax()) return redirect('/');

        $presupuestos = $request->presupuestos;//Array de presupuestos seleccionados (id)

        try{
            DB::beginTransaction();

            $project = new Project();
            $project->idcliente = $request->idcliente;
            $project->idusuario = \Auth::user()->id;
            $project->tipo_comprobante = $request->tipo_comprobante;
            $project->num_comprobante = $request->num_comprobante;
            $project->title = $request->title;
            $project->titular= \Auth::user()->id;
            $project->content =  $request->content;
            $project->inicio = $request->inicio;
            $project->fin = $request->fin;
            $project->impuesto =  $request->impuesto;
            $project->total =  $request->total;
            $project->adeudo =  $request->total;
            $project->forma_pago =  $request->forma_pago;
            $project->lugar_entrega =  $request->lugar_entrega;
            $project->estado = 'Registrado';
            $project->pagado = 0;
            $project->pagado_parcial = 0;
            $project->entregado = 0;
            $project->entregado_parcial = 0;
            $project->flete = $request->flet;
            $project->instalacion = $request->insta;
            $project->area =  $request->area;
            $project->tipo_facturacion =  $request->tipo_facturacion;
            $project->observacion = $request->observacion;
            $project->observacionpriv = $request->observacionpriv;
            $project->save();
            $project->ventas()->attach($presupuestos);

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function update(Request $request){

        if(!$request->ajax()) return redirect('/');

        $presupuestos = $request->presupuestos;//Array de presupuestos seleccionados (id)

        try{
            DB::beginTransaction();

            $project = Project::findOrFail($request->id);
            $project->idcliente = $request->idcliente;
            $project->idusuario = \Auth::user()->id;
            $project->tipo_comprobante = $request->tipo_comprobante;
            $project->num_comprobante = $request->num_comprobante;
            $project->title = $request->title;
            $project->titular = $request->titular;
            $project->content =  $request->content;
            $project->inicio = $request->inicio;
            $project->fin = $request->fin;
            $project->impuesto =  $request->impuesto;
            $project->total =  $request->total;
            $project->adeudo =  $request->adeudo;
            $project->forma_pago =  $request->forma_pago;
            $project->lugar_entrega =  $request->lugar_entrega;
            $project->flete = $request->flet;
            $project->instalacion = $request->insta;
            $project->area =  $request->area;
            $project->tipo_facturacion =  $request->tipo_facturacion;
            $project->save();
            $project->ventas()->sync($presupuestos);

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function getVentas(Request $request){

        if (!$request->ajax()) return redirect('/');
        $project = Project::findOrFail($request->id); //ID del project
        $ventas = $project->ventas()
        ->leftjoin('personas AS clientes', 'clientes.id','=','ventas.idcliente')
        ->leftJoin('users','ventas.idusuario','=','users.id')
        ->select('ventas.id as idventa','ventas.tipo_comprobante','ventas.num_comprobante',
            'ventas.fecha_hora','ventas.impuesto','ventas.total','ventas.estado',
            'ventas.moneda','ventas.tipo_cambio','ventas.observacion','ventas.forma_pago',
            'ventas.tiempo_entrega','ventas.lugar_entrega','ventas.entregado','ventas.banco',
            'ventas.entrega_parcial','ventas.tipo_facturacion', 'ventas.pagado','users.usuario',
            'ventas.num_cheque','clientes.nombre as cliente','ventas.file','ventas.observacionpriv',
            'ventas.facturado','ventas.factura_env','ventas.pago_parcial','ventas.adeudo')
        ->get();
        return [
            'ventas' => $ventas
        ];
    }
    public function desactivar(Request $request){

        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();

            $project = Project::findOrFail($request->id);
            $project->estado = 'Anulado';
            $project->entregado = 0;
            $project->entregado_parcial = 0;
            $project->pagado = 0;
            $project->pagado_parcial = 0;
            $project->save();

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function cambiarEntrega(Request $request){
        if (!$request->ajax()) return redirect('/');
        $project = Project::findOrFail($request->id);
        $project->entregado = $request->entregado;
        $project->entregado_parcial = 0;
        $project->save();
    }
    public function cambiarEntregaParcial(Request $request){
        if (!$request->ajax()) return redirect('/');
        $project = Project::findOrFail($request->id);
        $project->entregado_parcial = $request->entregado_parcial;
        $project->entregado = 0;
        $project->save();
    }
    public function cambiarPagado(Request $request){

        if (!$request->ajax()) return redirect('/');

        if($request->pagado == 1){
            $project = Project::findOrFail($request->id);
            $project->pagado = $request->pagado;
            $project->adeudo = 0;
            $project->save();
        }else{
            $project = Project::findOrFail($request->id);
            $project->pagado = $request->pagado;
            $project->adeudo = $project->total;
            $project->save();
        }


    }
    public function actualizarObservacion(Request $request){
        if (!$request->ajax()) return redirect('/');
        $project = Project::findOrFail($request->id);
        $project->observacion = $request->observacion;
        $project->save();
    }
    public function actualizarObservacionPriv(Request $request){
        if (!$request->ajax()) return redirect('/');
        $project = Project::findOrFail($request->id);
        $project->observacionpriv = $request->observacionpriv;
        $project->save();
    }
    public function crearDeposit(Request $request){

        if (!$request->ajax()) return redirect('/');

        $mytime = Carbon::now('America/Mexico_City');

        $project = Project::findOrFail($request->id); //Project a depositar

        $adeudoAct = $project->adeudo;

        if($request->total < $adeudoAct){
            try{
                DB::beginTransaction();
                $project->adeudo = $project->adeudo - $request->total;
                $project->pagado_parcial = 1;
                $project->pagado = 0;
                $project->save();
                $deposit = new Deposit(['total' => $request->total,'fecha_hora' => $mytime,
                    'forma_pago' => $request->forma_pago]);
                $project->deposits()->save($deposit);
                DB::commit();
            }catch(Exception $e){
                DB::rollBack();
            }
        }elseif($request->total == $adeudoAct){
            try{
                DB::beginTransaction();
                $project->adeudo = 0;
                $project->pagado_parcial = 1;
                $project->pagado = 1;
                $project->save();
                $deposit = new Deposit(['total' => $request->total,'fecha_hora' => $mytime,
                    'forma_pago' => $request->forma_pago]);
                $project->deposits()->save($deposit);
                DB::commit();
            }catch(Exception $e){
                DB::rollBack();
            }
        }
    }
    public function getDeposits(Request $request){

        if (!$request->ajax()) return redirect('/');

        $project = Project::findOrFail($request->id); //ID project y sus depositos

        $deposits = $project->deposits()
        ->select('deposits.id','deposits.total','deposits.fecha_hora as fecha','deposits.forma_pago')
        ->orderBy('deposits.fecha_hora','desc')
        ->get();

        return [
            'abonos' => $deposits
        ];

    }
    public function deleteDeposit(Request $request){
        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $deposit = Deposit::findOrFail($request->id);
            //$deposit->delete();

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

            $project = Project::findOrFail($request->idproject);
            $numDeposits = $project->deposits()->count();

            if($numDeposits <= 0){
                $project->pagado_parcial = 0;
                $project->pagado = 0;
                $project->adeudo = $project->total;
                $project->save();
            }else{
                $project->adeudo = $project->adeudo + $request->total;
                $project->save();
            }

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function refreshProject(Request $request){
        if (!$request->ajax()) return redirect('/');
        $usrol = \Auth::user()->idrol;

        $project = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
        ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
        ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
            'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
            'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
            'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
            'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
            'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
            'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
            'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
            'projects.created_at as registro')
        ->where('projects.id',$request->id)
        ->first();
        return [ 'project' => $project , 'userrol' => $usrol ];
    }
    public function filesUppload(Request $request){

        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();

            //The name of the directory that we need to create.
            $directoryName = 'projectfiles';

            if(!is_dir($directoryName)){
                //Directory does not exist, so lets create it.
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
                $project = Project::findOrFail($request->id); //ID project
                $project->documents()->save($docum);
                DB::commit();
            }

        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function getDocs(Request $request){

        if (!$request->ajax()) return redirect('/');
        $project = Project::findOrFail($request->id); //ID dueño de los archivos
        $files = $project->documents()->get();

        return [
            'documentos' => $files
        ];
    }
    public function eliminarDoc(Request $request){
        if (!$request->ajax()) return redirect('/');

        //The name of the directory that we need to create.
        $directoryName = 'projectfiles';

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

        $project = Project::findOrFail($request->id); //Project a depositar

        $adeudoAct = $project->adeudo;

        $creditos = $request->creditos;

        if($request->total < $adeudoAct){
            try{
                DB::beginTransaction();
                $project->adeudo = $project->adeudo - $request->total;
                $project->pagado_parcial = 1;
                $project->pagado = 0;
                $project->save();
                $deposit = new Deposit(['total' => $request->total,'fecha_hora' => $mytime,
                    'forma_pago' => $request->forma_pago]);
                $project->deposits()->save($deposit);

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
                $project->adeudo = 0;
                $project->pagado_parcial = 1;
                $project->pagado = 1;
                $project->save();
                $deposit = new Deposit(['total' => $request->total,'fecha_hora' => $mytime,
                    'forma_pago' => $request->forma_pago]);
                $project->deposits()->save($deposit);

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
    public function ListarExcel(Request $request){
        $inicio = $request->inicio;
        $fin = $request->fin;
        $usuarios = $request->usuarios;
        $ArrUsuarios = explode(",",$usuarios);

        return Excel::download(new ProjectsExport($inicio,$fin,$ArrUsuarios), 'presupuestosEspeciales-'.$inicio.'-'.$fin.'.xlsx');
    }
    public function pdf(Request $request,$id){

        $projects = Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
            ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
            ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
                'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
                'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
                'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
                'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
                'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
                'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
                'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
                'projects.created_at as registro')
            ->where('projects.id','=',$id)
            ->orderBy('projects.id', 'desc')->take(1)->get();


        $numventa = Project::select('num_comprobante')->where('id',$id)->get();

        $ventaD = Project::findOrFail($id); //ID venta y sus depositos
        $deposits = $ventaD->deposits()
        ->select(DB::raw('SUM(deposits.total) as abonos'))
        ->get();


        $image = Document::with('project')
        ->select('documents.id','documents.url','documents.documentable_type','documents.documentable_id','documents.tipo')
        ->where('documents.documentable_id',$id)
        ->get();

        $capturas = Deposit::with('project')
        ->select('deposits.total','deposits.forma_pago','deposits.depositable_id','deposits.fecha_hora')
        ->where('deposits.depositable_id',$id)
        ->get();

        $pdf = \PDF::loadView('pdf.project',['projects' => $projects,'abonos' => $deposits[0]->abonos,
        'capturas' =>$capturas, 'image' =>$image]);

        return $pdf->stream('venta-'.$numventa[0]->num_comprobante.'.pdf');


    }
    public function getVentasClienteProject(Request $request){

        if (!$request->ajax()) return redirect('/');

        $idcliente = $request->idcliente;
        $buscar = $request->buscar;
        $criterio = $request->criterio;


        if($buscar != ''){
            $ventas = Venta::join('personas AS clientes', 'clientes.id','=','projects.idcliente')
            ->join('personas AS creador','creador.id','=','projects.idusuario')
            ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
            'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
            'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
            'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
            'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
            'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
            'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
            'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
            'projects.created_at as registro')
            ->where([
                ['projects.estado','Registrado'],
                ['projects.'.$criterio, 'like', '%'. $buscar . '%']

            ])
            ->orderBy('projects.registro','desc')->paginate(10);
        }else{
            $ventas = Venta::join('personas','projects.idcliente','=','personas.id')
            ->join('users','projects.idusuario','=','users.id')
            ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante','projects.title',
            'projects.content','projects.inicio','projects.fin','projects.impuesto','projects.total',
            'projects.adeudo','projects.forma_pago','projects.lugar_entrega','projects.estado',
            'projects.pagado','projects.pagado_parcial','projects.entregado','projects.entregado_parcial',
            'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
            'projects.observacion','projects.observacionpriv','creador.nombre as usuario',
            'clientes.nombre as cliente','clientes.tipo','clientes.telefono','clientes.rfc',
            'clientes.cfdi', 'clientes.company','clientes.tel_company','clientes.id as idcliente',
            'projects.created_at as registro')
            ->where([
                    ['projects.idcliente','=',$idcliente],
                    ['projects.estado','Registrado']
                ])
            ->orderBy('projects.registro','desc')->paginate(10);
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
    public function cambiarFacturacion(Request $request){
        if (!$request->ajax()) return redirect('/');
        $project = Project::findOrFail($request->id);
        if($request->estado == 0){
            $project->facturado = 0;
            $project->factura_env = 0;
            $project->num_factura = null;
        }else{
            $project->facturado = $request->estado;
            $project->num_factura = $request->numFact;
            $project->factura_env = 0;
        }
        $project->save();
    }
    public function cambiarFacturacionEnv(Request $request){
        if (!$request->ajax()) return redirect('/');
        $project = Project::findOrFail($request->id);
        $project->factura_env = $request->estadoEn;
        $project->save();
    }
    public function enviarFacturaMail(Request $request){
        $email = $request->mail;
        $data = array(
            'name'      =>  $request->name
        );
        $numventa = Project::select('num_comprobante')->where('id',$request->id)->get();
        $numPre = $numventa[0]->num_comprobante;
        $usid = \Auth::user()->id;
        $mailUs = Persona::select('email')->where('id',$usid)->get();
        $emit = $mailUs[0]->email;
        $path = 'facturasfiles/' . $request->fileUrl;
        Mail::to($email)->send(new MailFactura($path,$data,$numPre,$emit));
    }
    public function obtenerCabecera(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $projects = Project::join('personas','projects.idcliente','=','personas.id')
        ->join('users','projects.idusuario','=','users.id')
        ->select('projects.id','projects.tipo_comprobante','projects.num_comprobante',
            'projects.impuesto','projects.total','projects.estado', 'projects.observacion','projects.instalacion',
            'projects.forma_pago','projects.lugar_entrega','projects.entregado','projects.inicio','projects.flete',
            'projects.entregado_parcial','projects.tipo_facturacion', 'projects.pagado','users.usuario',
            'projects.observacionpriv','projects.facturado','projects.factura_env','projects.pagado_parcial',
            'projects.adeudo','personas.nombre as cliente','projects.title','personas.tipo','personas.rfc',
            'personas.cfdi','personas.telefono','personas.company as contacto','personas.tel_company as tel_contacto',
            'personas.id as idcliente','personas.email as EmailC','projects.content')
        ->where('projects.id','=',$id)
        ->orderBy('projects.id', 'desc')->take(1)->get();

        return ['projects' => $projects];


        return ['projects' => $projects ];

    }
    public function ListarAbonosProjectExcel(Request $request){

        $inicio = $request->inicio;
        $fin = $request->fin;

        return Excel::download(new AbonosProjectExport($inicio,$fin), 'Abonos-'.$inicio.'-'.$fin.'.xlsx');
    }
}
