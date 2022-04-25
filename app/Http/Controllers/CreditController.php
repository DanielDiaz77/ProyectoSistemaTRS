<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Credit;
use App\Persona;
use App\Deposit;

class CreditController extends Controller
{
    public function index(Request $request){

        if(!$request->ajax()) return redirect('/');

        $estado = $request->estado;
        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if($criterio == 'cliente'){
            if($estado == 'Abonada'){
                $creditos = Credit::join('personas','credits.creditable_id','=','personas.id')
                ->leftjoin('users','users.id','=','credits.idusuario')
                ->select('credits.id','credits.num_documento','credits.total','credits.forma_pago',
                    'credits.updated_at as fecha_hora','credits.observacion as nota','credits.estado','personas.nombre',
                    'personas.area','personas.telefono','users.usuario as vendedor','personas.tipo',
                    'personas.id as idcliente')
                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['credits.estado','Abonada']])
                ->orderBy('credits.id', 'desc')->paginate(10);
            }elseif($estado == 'Vigente'){
                $creditos = Credit::join('personas','credits.creditable_id','=','personas.id')
                ->leftjoin('users','users.id','=','credits.idusuario')
                ->select('credits.id','credits.num_documento','credits.total','credits.forma_pago',
                    'credits.updated_at as fecha_hora','credits.observacion as nota','credits.estado','personas.nombre',
                    'personas.area','personas.telefono','users.usuario as vendedor','personas.tipo',
                    'personas.id as idcliente')
                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['credits.estado','Vigente']])
                ->orderBy('credits.id', 'desc')->paginate(10);
            }elseif($estado == 'Por Validar'){
                $creditos = Credit::join('personas','credits.creditable_id','=','personas.id')
                ->leftjoin('users','users.id','=','credits.idusuario')
                ->select('credits.id','credits.num_documento','credits.total','credits.forma_pago',
                    'credits.updated_at as fecha_hora','credits.observacion as nota','credits.estado','personas.nombre',
                    'personas.area','personas.telefono','users.usuario as vendedor','personas.tipo',
                    'personas.id as idcliente')
                ->where([['personas.nombre', 'like', '%'. $buscar . '%'],['credits.estado','Por Validar']])
                ->orderBy('credits.id', 'desc')->paginate(10);
            }else{
                $creditos = Credit::join('personas','credits.creditable_id','=','personas.id')
                ->leftjoin('users','users.id','=','credits.idusuario')
                ->select('credits.id','credits.num_documento','credits.total','credits.forma_pago',
                    'credits.updated_at as fecha_hora','credits.observacion as nota','credits.estado','personas.nombre',
                    'personas.area','personas.telefono','users.usuario as vendedor','personas.tipo',
                    'personas.id as idcliente')
                ->where([['personas.nombre', 'like', '%'. $buscar . '%']])
                ->orderBy('credits.id', 'desc')->paginate(10);
            }
        }elseif($buscar == ''){
            if($estado == 'Abonada'){
                $creditos = Credit::join('personas','credits.creditable_id','=','personas.id')
                ->leftjoin('users','users.id','=','credits.idusuario')
                ->select('credits.id','credits.num_documento','credits.total','credits.forma_pago',
                    'credits.updated_at as fecha_hora','credits.observacion as nota','credits.estado','personas.nombre',
                    'personas.area','personas.telefono','users.usuario as vendedor','personas.tipo',
                    'personas.id as idcliente')
                ->where([['credits.estado','Abonada']])
                ->orderBy('credits.id', 'desc')->paginate(10);
            }elseif($estado == 'Vigente'){
                $creditos = Credit::join('personas','credits.creditable_id','=','personas.id')
                ->leftjoin('users','users.id','=','credits.idusuario')
                ->select('credits.id','credits.num_documento','credits.total','credits.forma_pago',
                    'credits.updated_at as fecha_hora','credits.observacion as nota','credits.estado','personas.nombre',
                    'personas.area','personas.telefono','users.usuario as vendedor','personas.tipo',
                    'personas.id as idcliente')
                ->where([['credits.estado','Vigente']])
                ->orderBy('credits.id', 'desc')->paginate(10);
            }elseif($estado == 'Por Validar'){
                $creditos = Credit::join('personas','credits.creditable_id','=','personas.id')
                ->leftjoin('users','users.id','=','credits.idusuario')
                ->select('credits.id','credits.num_documento','credits.total','credits.forma_pago',
                    'credits.updated_at as fecha_hora','credits.observacion as nota','credits.estado','personas.nombre',
                    'personas.area','personas.telefono','users.usuario as vendedor','personas.tipo',
                    'personas.id as idcliente')
                ->where([['credits.estado','Por Validar']])
                ->orderBy('credits.id', 'desc')->paginate(10);
            }else{
                $creditos = Credit::join('personas','credits.creditable_id','=','personas.id')
                ->leftjoin('users','users.id','=','credits.idusuario')
                ->select('credits.id','credits.num_documento','credits.total','credits.forma_pago',
                    'credits.updated_at as fecha_hora','credits.observacion as nota','credits.estado','personas.nombre',
                    'personas.area','personas.telefono','users.usuario as vendedor','personas.tipo',
                    'personas.id as idcliente')
                ->orderBy('credits.id', 'desc')->paginate(10);
            }
        }else{
            if($estado == 'Abonada'){
                $creditos = Credit::join('personas','credits.creditable_id','=','personas.id')
                ->leftjoin('users','users.id','=','credits.idusuario')
                ->select('credits.id','credits.num_documento','credits.total','credits.forma_pago',
                    'credits.updated_at as fecha_hora','credits.observacion as nota','credits.estado','personas.nombre',
                    'personas.area','personas.telefono','users.usuario as vendedor','personas.tipo',
                    'personas.id as idcliente')
                ->where([['credits.'.$criterio, 'like', '%'. $buscar . '%'],['credits.estado','Abonada']])
                ->orderBy('credits.id', 'desc')->paginate(10);
            }elseif($estado == 'Vigente'){
                $creditos = Credit::join('personas','credits.creditable_id','=','personas.id')
                ->leftjoin('users','users.id','=','credits.idusuario')
                ->select('credits.id','credits.num_documento','credits.total','credits.forma_pago',
                    'credits.updated_at as fecha_hora','credits.observacion as nota','credits.estado','personas.nombre',
                    'personas.area','personas.telefono','users.usuario as vendedor','personas.tipo',
                    'personas.id as idcliente')
                ->where([['credits.'.$criterio, 'like', '%'. $buscar . '%'],['credits.estado','Vigente']])
                ->orderBy('credits.id', 'desc')->paginate(10);
            }elseif($estado == 'Por Validar'){
                $creditos = Credit::join('personas','credits.creditable_id','=','personas.id')
                ->leftjoin('users','users.id','=','credits.idusuario')
                ->select('credits.id','credits.num_documento','credits.total','credits.forma_pago',
                    'credits.updated_at as fecha_hora','credits.observacion as nota','credits.estado','personas.nombre',
                    'personas.area','personas.telefono','users.usuario as vendedor','personas.tipo',
                    'personas.id as idcliente')
                ->where([['credits.'.$criterio, 'like', '%'. $buscar . '%'],['credits.estado','Por Validar']])
                ->orderBy('credits.id', 'desc')->paginate(10);
            }else{
                $creditos = Credit::join('personas','credits.creditable_id','=','personas.id')
                ->leftjoin('users','users.id','=','credits.idusuario')
                ->select('credits.id','credits.num_documento','credits.total','credits.forma_pago',
                'credits.updated_at as fecha_hora','credits.observacion as nota','credits.estado','personas.nombre',
                'personas.area','personas.telefono','users.usuario as vendedor','personas.tipo',
                'personas.id as idcliente')
                ->where([['credits.'.$criterio, 'like', '%'. $buscar . '%']])
                ->orderBy('credits.id', 'desc')->paginate(10);
            }
        }

        return [
            'pagination' => [
                'total'         => $creditos->total(),
                'current_page'  => $creditos->currentPage(),
                'per_page'      => $creditos->perPage(),
                'last_page'     => $creditos->lastPage(),
                'from'          => $creditos->firstItem(),
                'to'            => $creditos->lastItem(),
            ],
            'creditos' => $creditos
        ];
    }

    public function cambiarEstado(Request $request){

        if (!$request->ajax()) return redirect('/');

        $credit = Credit::findOrFail($request->id);
        $credit->idusuario = \Auth::user()->id;
        $credit->estado = $request->estado;
        $credit->save();
    }
}
