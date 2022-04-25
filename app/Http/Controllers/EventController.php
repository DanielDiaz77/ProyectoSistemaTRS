<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Exports\ActividadesExport;

use Carbon\Carbon;
use App\Event;
use App\Project;
use App\User;
use App\Persona;

class EventController extends Controller
{
    public function index(Request $request){

        //if(!$request->ajax()) return redirect('/');

        $zona = $request->zona;

        $usrol = \Auth::user()->idrol;
        $usarea = \Auth::user()->area;

        if($usrol == 1){
            if($zona == ''){
                $eventos = Event::join('users','users.id','=','events.idusuario')
                ->join('personas','personas.id','=','events.idcliente')
                ->select('users.id as userid','users.usuario as user','events.id','events.start',
                    'events.end','events.title','events.content','events.title','events.class','personas.rfc',
                    'events.estado','personas.id as idcliente','personas.nombre as cliente','personas.domicilio',
                    'personas.telefono','personas.ciudad','personas.observacion','personas.email','personas.tipo',
                    'personas.company','personas.tel_company','events.area')
                ->orderby('events.id')
                ->get();
            }else{
                $eventos = Event::join('users','users.id','=','events.idusuario')
                ->join('personas','personas.id','=','events.idcliente')
                ->select('users.id as userid','users.usuario as user','events.id','events.start',
                    'events.end','events.title','events.content','events.title','events.class','personas.rfc',
                    'events.estado','personas.id as idcliente','personas.nombre as cliente','personas.domicilio',
                    'personas.telefono','personas.ciudad','personas.observacion','personas.email','personas.tipo',
                    'personas.company','personas.tel_company','events.area')
                ->where('events.area',$zona)
                ->orderby('events.id')
                ->get();
            }

        }else{
            $eventos = Event::join('users','users.id','=','events.idusuario')
            ->join('personas','personas.id','=','events.idcliente')
            ->select('users.id as userid','users.usuario as user','events.id','events.start',
                'events.end','events.title','events.content','events.title','events.class','personas.rfc',
                'events.estado','personas.id as idcliente','personas.nombre as cliente','personas.domicilio',
                'personas.telefono','personas.ciudad','personas.observacion','personas.email','personas.tipo',
                'personas.company','personas.tel_company','events.area')
            ->where('events.area',$usarea)
            ->orderby('events.id')
            ->get();
        }

        return ['eventos' => $eventos,'userrol' => $usrol,'userarea' => $usarea];
    }
    public function store(Request $request){

        if(!$request->ajax()) return redirect('/');

        $event = new Event();
        $event->start = $request->start;
        $event->end = $request->end;
        $event->title = $request->title;
        $event->content = $request->content;
        $event->class = $request->clase;
        $event->idusuario = \Auth::user()->id;
        $event->idcliente = $request->idcliente;
        $event->area = $request->area;
        $event->estado = '0';
        $event->save();
    }
    public function update(Request $request){

        if(!$request->ajax()) return redirect('/');

        $event = Event::findOrFail($request->id);
        $event->start = $request->start;
        $event->end = $request->end;
        $event->title = $request->title;
        $event->content = $request->content;
        $event->class = $request->clase;
        $event->idusuario = $request->idusuario;
        $event->idcliente = $request->idcliente;
        $event->area = $request->area;
        $event->estado = $request->estado;
        $event->save();

    }
    public function destroy(Request $request){
        if(!$request->ajax()) return redirect('/');
        $event = Event::findOrFail($request->id);
        $event->delete();
    }
    public function completar(Request $request){
        if(!$request->ajax()) return redirect('/');
        $event = Event::findOrFail($request->id);
        $event->estado = $request->estado;
        $event->save();
    }
    public function obtenerEventsCliente(Request $request){

        if (!$request->ajax()) return redirect('/');

        $idcliente = $request->idcliente;

        $eventos = Event::join('users','users.id','=','events.idusuario')
        ->join('personas','personas.id','=','events.idcliente')
        ->select('users.usuario as user','events.id','events.start',
        'events.end','events.title','events.content','events.title','events.class',
        'events.estado')
        ->where('events.idcliente',$idcliente)
        /* ->where([
            ['events.idcliente','=',$idcliente],
            ['events.estado',0]
        ]) */
        ->orderBy('events.start','desc')->paginate(1);

        return [
            'pagination' => [
                'total'         => $eventos->total(),
                'current_page'  => $eventos->currentPage(),
                'per_page'      => $eventos->perPage(),
                'last_page'     => $eventos->lastPage(),
                'from'          => $eventos->firstItem(),
                'to'            => $eventos->lastItem(),
            ],
            'actividades' => $eventos
        ];
    }
    public function listarEventos(Request $request){

        if(!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        $estado = $request->estado;
        $area = $request->area;

        if($area != ''){
            if($estado != ''){
                if($buscar!=''){
                    if($criterio == 'cliente'){
                        $eventos = Event::join('users','users.id','=','events.idusuario')
                        ->join('personas','personas.id','=','events.idcliente')
                        ->select('users.usuario as user','events.id','events.start',
                        'events.end','events.title','events.content','events.title','events.class',
                        'events.estado','events.area','personas.nombre as cliente')
                        ->where([
                            ['events.area',$area],
                            ['events.estado',$estado],
                            ['personas.nombre', 'like', '%'. $buscar . '%']])
                        ->orderBy('events.start','desc')->paginate(12);
                    }else{
                        $eventos = Event::join('users','users.id','=','events.idusuario')
                        ->join('personas','personas.id','=','events.idcliente')
                        ->select('users.usuario as user','events.id','events.start',
                        'events.end','events.title','events.content','events.title','events.class',
                        'events.estado','events.area','personas.nombre as cliente')
                        ->where([
                            ['events.area',$area],
                            ['events.estado',$estado],
                            ['events.'.$criterio, 'like', '%'. $buscar . '%']])
                        ->orderBy('events.start','desc')->paginate(12);
                    }
                }else{
                    $eventos = Event::join('users','users.id','=','events.idusuario')
                    ->join('personas','personas.id','=','events.idcliente')
                    ->select('users.usuario as user','events.id','events.start',
                    'events.end','events.title','events.content','events.title','events.class',
                    'events.estado','events.area','personas.nombre as cliente')
                    ->where([['events.area',$area],['events.estado',$estado]])
                    ->orderBy('events.start','desc')->paginate(12);
                }
            }else{
                if($buscar!=''){
                    if($criterio=='cliente'){
                        $eventos = Event::join('users','users.id','=','events.idusuario')
                        ->join('personas','personas.id','=','events.idcliente')
                        ->select('users.usuario as user','events.id','events.start',
                        'events.end','events.title','events.content','events.title','events.class',
                        'events.estado','events.area','personas.nombre as cliente')
                        ->where([
                            ['events.area',$area],
                            ['personas.nombre','like', '%'. $buscar . '%']])
                        ->orderBy('events.start','desc')->paginate(12);

                    }else{
                        $eventos = Event::join('users','users.id','=','events.idusuario')
                        ->join('personas','personas.id','=','events.idcliente')
                        ->select('users.usuario as user','events.id','events.start',
                        'events.end','events.title','events.content','events.title','events.class',
                        'events.estado','events.area','personas.nombre as cliente')
                        ->where([
                            ['events.area',$area],
                            ['events.'.$criterio, 'like', '%'. $buscar . '%']])
                        ->orderBy('events.start','desc')->paginate(12);
                    }
                }else{
                    $eventos = Event::join('users','users.id','=','events.idusuario')
                    ->join('personas','personas.id','=','events.idcliente')
                    ->select('users.usuario as user','events.id','events.start',
                    'events.end','events.title','events.content','events.title','events.class',
                    'events.estado','events.area','personas.nombre as cliente')
                    ->where('events.area',$area)
                    ->orderBy('events.start','desc')->paginate(12);
                }
            }
        }else{
            if($estado != ''){
                if($buscar!=''){
                    if($criterio == 'cliente'){
                        $eventos = Event::join('users','users.id','=','events.idusuario')
                        ->join('personas','personas.id','=','events.idcliente')
                        ->select('users.usuario as user','events.id','events.start',
                        'events.end','events.title','events.content','events.title','events.class',
                        'events.estado','events.area','personas.nombre as cliente')
                        ->where([
                            ['events.estado',$estado],
                            ['personas.nombre', 'like', '%'. $buscar . '%']])
                        ->orderBy('events.start','desc')->paginate(12);
                    }else{
                        $eventos = Event::join('users','users.id','=','events.idusuario')
                        ->join('personas','personas.id','=','events.idcliente')
                        ->select('users.usuario as user','events.id','events.start',
                        'events.end','events.title','events.content','events.title','events.class',
                        'events.estado','events.area','personas.nombre as cliente')
                        ->where([['events.estado',$estado],['events.'.$criterio, 'like', '%'. $buscar . '%']])
                        ->orderBy('events.start','desc')->paginate(12);
                    }
                }else{
                    $eventos = Event::join('users','users.id','=','events.idusuario')
                    ->join('personas','personas.id','=','events.idcliente')
                    ->select('users.usuario as user','events.id','events.start',
                    'events.end','events.title','events.content','events.title','events.class',
                    'events.estado','events.area','personas.nombre as cliente')
                    ->where('events.estado',$estado)
                    ->orderBy('events.start','desc')->paginate(12);
                }
            }else{
                if($buscar!=''){
                    if($criterio == 'cliente'){
                        $eventos = Event::join('users','users.id','=','events.idusuario')
                        ->join('personas','personas.id','=','events.idcliente')
                        ->select('users.usuario as user','events.id','events.start',
                        'events.end','events.title','events.content','events.title','events.class',
                        'events.estado','events.area','personas.nombre as cliente')
                        ->where('personas.nombre', 'like', '%'. $buscar . '%')
                        ->orderBy('events.start','desc')->paginate(12);
                    }else{
                        $eventos = Event::join('users','users.id','=','events.idusuario')
                        ->join('personas','personas.id','=','events.idcliente')
                        ->select('users.usuario as user','events.id','events.start',
                        'events.end','events.title','events.content','events.title','events.class',
                        'events.estado','events.area','personas.nombre as cliente')
                        ->where('events.'.$criterio, 'like', '%'. $buscar . '%')
                        ->orderBy('events.start','desc')->paginate(12);
                    }
                }else{
                    $eventos = Event::join('users','users.id','=','events.idusuario')
                    ->join('personas','personas.id','=','events.idcliente')
                    ->select('users.usuario as user','events.id','events.start',
                    'events.end','events.title','events.content','events.title','events.class',
                    'events.estado','events.area','personas.nombre as cliente')
                    ->orderBy('events.start','desc')->paginate(12);
                }
            }
        }

        return [
            'pagination' => [
                'total'         => $eventos->total(),
                'current_page'  => $eventos->currentPage(),
                'per_page'      => $eventos->perPage(),
                'last_page'     => $eventos->lastPage(),
                'from'          => $eventos->firstItem(),
                'to'            => $eventos->lastItem(),
            ],
            'actividades' => $eventos
        ];

    }
    public function ListarExcel(Request $request){
        $inicio = $request->inicio;
        $fin = $request->fin;
        return Excel::download(new ActividadesExport($inicio,$fin), 'actividades-'.$inicio.'-'.$fin.'.xlsx');
    }
    public function listarProyectos(Request $request){


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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
                        'projects.flete','projects.instalacion','projects.area','projects.tipo_facturacion',
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
}
