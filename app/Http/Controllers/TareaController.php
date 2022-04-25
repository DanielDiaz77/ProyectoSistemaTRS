<?php

namespace App\Http\Controllers;

use App\Persona;
use App\Tarea;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TareaController extends Controller
{
    public function index(Request $request){
        if (!$request->ajax()) return redirect('/');

        $usrol = \Auth::user()->idrol;
        $usvend = \Auth::user()->id;
        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $estadoT =  $request->estado;
        $filtro = $request->buscador;

        if($filtro == ''){
            if($usrol == 2){
                if($estadoT == ''){
                    if($buscar == ''){
                        $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                        ->join('users','users.id','=','tareas.idusuario')
                        ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                        'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company',
                        'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                        'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                        ->where([['tareas.idusuario',$usvend],['tareas.tipo','!=','Comentario']])
                        ->orderBy('tareas.fecha', 'asc')->paginate(12);
                    }else{
                        if($criterio == 'cliente'){
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.idusuario',$usvend],
                                ['tareas.tipo','!=','Comentario'],
                                ['personas.nombre','like', '%'. $buscar . '%']
                            ])
                            ->orderBy('tareas.fecha', 'asc')->paginate(12);
                        }else if($criterio == 'tipocliente'){
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.idusuario',$usvend],
                                ['tareas.tipo','!=','Comentario'],
                                ['personas.tipo','like', '%'. $buscar . '%']
                            ])
                            ->orderBy('tareas.fecha', 'asc')->paginate(12);
                        }else{
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.idusuario',$usvend],
                                ['tareas.tipo','!=','Comentario'],
                                ['tareas.'.$criterio, 'like', '%'. $buscar . '%']
                            ])
                            ->orderBy('tareas.fecha', 'asc')->paginate(12);
                        }
                    }
                }else{
                    if($buscar == ''){
                        $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                        ->join('users','users.id','=','tareas.idusuario')
                        ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                        'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company',
                        'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                        'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                        ->where([
                            ['tareas.idusuario',$usvend],
                            ['tareas.tipo','!=','Comentario'],
                            ['tareas.estado',$estadoT]
                        ])
                        ->orderBy('tareas.fecha', 'asc')->paginate(12);
                    }else{
                        if($criterio == 'cliente'){
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.idusuario',$usvend],
                                ['tareas.tipo','!=','Comentario'],
                                ['personas.nombre','like', '%'. $buscar . '%'],
                                ['tareas.estado',$estadoT]
                            ])
                            ->orderBy('tareas.fecha', 'asc')->paginate(12);
                        }else if($criterio == 'tipocliente'){
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.idusuario',$usvend],
                                ['tareas.tipo','!=','Comentario'],
                                ['personas.tipo','like', '%'. $buscar . '%'],
                                ['tareas.estado',$estadoT]
                            ])
                            ->orderBy('tareas.fecha', 'asc')->paginate(12);
                        }else{
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.idusuario',$usvend],
                                ['tareas.tipo','!=','Comentario'],
                                ['tareas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['tareas.estado',$estadoT]
                            ])
                            ->orderBy('tareas.fecha', 'asc')->paginate(12);
                        }
                    }
                }
            }else{
                if($estadoT == ''){
                    if($buscar == ''){
                        $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                        ->join('users','users.id','=','tareas.idusuario')
                        ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                        'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company',
                        'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                        'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                        ->where([['tareas.tipo','!=','Comentario']])
                        ->orderBy('tareas.fecha', 'desc')->paginate(12);
                    }else{
                        if($criterio == 'cliente'){
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.tipo','!=','Comentario'],
                                ['personas.nombre','like', '%'. $buscar . '%']
                            ])
                            ->orderBy('tareas.fecha', 'desc')->paginate(12);
                        }else if($criterio == 'tipocliente'){
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.tipo','!=','Comentario'],
                                ['personas.tipo','like', '%'. $buscar . '%']
                            ])
                            ->orderBy('tareas.fecha', 'asc')->paginate(12);
                        }else{
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.tipo','!=','Comentario'],
                                ['tareas.'.$criterio, 'like', '%'. $buscar . '%']
                            ])
                            ->orderBy('tareas.fecha', 'desc')->paginate(12);
                        }
                    }
                }else{
                    if($buscar == ''){
                        $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                        ->join('users','users.id','=','tareas.idusuario')
                        ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                        'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company',
                        'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                        'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                        ->where([
                            ['tareas.tipo','!=','Comentario'],
                            ['tareas.estado',$estadoT]
                        ])
                        ->orderBy('tareas.fecha', 'desc')->paginate(12);
                    }else{
                        if($criterio == 'cliente'){
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.tipo','!=','Comentario'],
                                ['personas.nombre','like', '%'. $buscar . '%'],
                                ['tareas.estado',$estadoT]
                            ])
                            ->orderBy('tareas.fecha', 'desc')->paginate(12);
                        }else if($criterio == 'tipocliente'){
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.tipo','!=','Comentario'],
                                ['personas.tipo','like', '%'. $buscar . '%'],
                                ['tareas.estado',$estadoT]
                            ])
                            ->orderBy('tareas.fecha', 'desc')->paginate(12);
                        }else{
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.tipo','!=','Comentario'],
                                ['tareas.'.$criterio, 'like', '%'. $buscar . '%'],
                                ['tareas.estado',$estadoT]
                            ])
                            ->orderBy('tareas.fecha', 'desc')->paginate(12);
                        }
                    }
                }
            }
        }else{
            if($usrol == 2){
                if($estadoT == ''){
                    if($buscar == ''){
                        $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                        ->join('users','users.id','=','tareas.idusuario')
                        ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                        'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company','personas.clave',
                        'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                        'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                        ->where([['tareas.idusuario',$usvend],['tareas.tipo','!=','Comentario'],['personas.clave','LIKE', '%'.$filtro.'%']])
                        ->orderBy('tareas.fecha', 'asc')->paginate(12);
                    }else{
                        if($criterio == 'cliente'){
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company','personas.clave',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.idusuario',$usvend],
                                ['tareas.tipo','!=','Comentario'],
                                ['personas.clave','LIKE', '%'.$filtro.'%']
                            ])
                            ->orderBy('tareas.fecha', 'asc')->paginate(12);
                        }else if($criterio == 'tipocliente'){
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company','personas.clave',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.idusuario',$usvend],
                                ['tareas.tipo','!=','Comentario'],
                                ['personas.clave','LIKE', '%'.$filtro.'%']
                            ])
                            ->orderBy('tareas.fecha', 'asc')->paginate(12);
                        }else{
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company','personas.clave',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.idusuario',$usvend],
                                ['tareas.tipo','!=','Comentario'],
                                ['personas.clave','LIKE', '%'.$filtro.'%']
                            ])
                            ->orderBy('tareas.fecha', 'asc')->paginate(12);
                        }
                    }
                }else{
                    if($buscar == ''){
                        $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                        ->join('users','users.id','=','tareas.idusuario')
                        ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                        'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company','personas.clave',
                        'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                        'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                        ->where([
                            ['tareas.idusuario',$usvend],
                            ['tareas.tipo','!=','Comentario'],
                            ['tareas.estado',$estadoT],
                            ['personas.clave','LIKE', '%'.$filtro.'%']
                        ])
                        ->orderBy('tareas.fecha', 'asc')->paginate(12);
                    }else{
                        if($criterio == 'cliente'){
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company','personas.clave',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.idusuario',$usvend],
                                ['tareas.tipo','!=','Comentario'],
                                ['personas.clave','LIKE', '%'.$filtro.'%'],
                                ['tareas.estado',$estadoT]
                            ])
                            ->orderBy('tareas.fecha', 'asc')->paginate(12);
                        }else if($criterio == 'tipocliente'){
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company','personas.clave',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.idusuario',$usvend],
                                ['tareas.tipo','!=','Comentario'],
                                ['personas.clave','LIKE', '%'.$filtro.'%'],
                                ['tareas.estado',$estadoT]
                            ])
                            ->orderBy('tareas.fecha', 'asc')->paginate(12);
                        }else{
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company','personas.clave',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.idusuario',$usvend],
                                ['tareas.tipo','!=','Comentario'],
                                ['personas.clave','LIKE', '%'.$filtro.'%'],
                                ['tareas.estado',$estadoT]
                            ])
                            ->orderBy('tareas.fecha', 'asc')->paginate(12);
                        }
                    }
                }
            }else{
                if($estadoT == ''){
                    if($buscar == ''){
                        $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                        ->join('users','users.id','=','tareas.idusuario')
                        ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                        'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company','personas.clave',
                        'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                        'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                        ->where([['tareas.tipo','!=','Comentario'],['personas.clave','LIKE', '%'.$filtro.'%']])
                        ->orderBy('tareas.fecha', 'desc')->paginate(12);
                    }else{
                        if($criterio == 'cliente'){
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company','personas.clave',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.tipo','!=','Comentario'],['personas.clave','LIKE', '%'.$filtro.'%']
                            ])
                            ->orderBy('tareas.fecha', 'desc')->paginate(12);
                        }else if($criterio == 'tipocliente'){
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company','personas.clave',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.tipo','!=','Comentario'],['personas.clave','LIKE', '%'.$filtro.'%']
                            ])
                            ->orderBy('tareas.fecha', 'asc')->paginate(12);
                        }else{
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company','personas.clave',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.tipo','!=','Comentario'],['personas.clave','LIKE', '%'.$filtro.'%']
                            ])
                            ->orderBy('tareas.fecha', 'desc')->paginate(12);
                        }
                    }
                }else{
                    if($buscar == ''){
                        $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                        ->join('users','users.id','=','tareas.idusuario')
                        ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                        'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company','personas.clave',
                        'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                        'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                        ->where([
                            ['tareas.tipo','!=','Comentario'],
                            ['tareas.estado',$estadoT],
                            ['personas.clave','LIKE', '%'.$filtro.'%']
                        ])
                        ->orderBy('tareas.fecha', 'desc')->paginate(12);
                    }else{
                        if($criterio == 'cliente'){
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company','personas.clave',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.tipo','!=','Comentario'],
                                ['personas.clave','LIKE', '%'.$filtro.'%'],
                                ['tareas.estado',$estadoT]
                            ])
                            ->orderBy('tareas.fecha', 'desc')->paginate(12);
                        }else if($criterio == 'tipocliente'){
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company','personas.clave',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.tipo','!=','Comentario'],
                                ['personas.clave','LIKE', '%'.$filtro.'%'],
                                ['tareas.estado',$estadoT]
                            ])
                            ->orderBy('tareas.fecha', 'desc')->paginate(12);
                        }else{
                            $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
                            ->join('users','users.id','=','tareas.idusuario')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
                            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company','personas.clave',
                            'personas.tel_company','users.idrol','users.area','tareas.id','tareas.nombre','tareas.descripcion',
                            'tareas.fecha','tareas.estado', 'tareas.tipo as clase')
                            ->where([
                                ['tareas.tipo','!=','Comentario'],
                                ['personas.clave','LIKE', '%'.$filtro.'%'],
                                ['tareas.estado',$estadoT]
                            ])
                            ->orderBy('tareas.fecha', 'desc')->paginate(12);
                        }
                    }
                }
            }
        }

        return [
            'pagination' => [
                'total'        => $tareas->total(),
                'current_page' => $tareas->currentPage(),
                'per_page'     => $tareas->perPage(),
                'last_page'    => $tareas->lastPage(),
                'from'         => $tareas->firstItem(),
                'to'           => $tareas->lastItem(),
            ],
            'tareas' => $tareas,
            'userid' => $usvend
        ];
    }
    public function store(Request $request){

        if(!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $tarea = new Tarea();
            $tarea->nombre = $request->nombre;
            $tarea->descripcion = $request->descripcion;
            $tarea->tipo = $request->tipo;
            $tarea->fecha = $request->fecha;
            $tarea->estado = '0';
            $tarea->idusuario = \Auth::user()->id;
            $tarea->idcliente = $request->idcliente;
            $tarea->save();

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function update(Request $request){
        if(!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();
            $tarea = Tarea::findOrFail($request->id);
            $tarea->nombre = $request->nombre;
            $tarea->descripcion = $request->descripcion;
            $tarea->tipo = $request->tipo;
            $tarea->fecha = $request->fecha;
            $tarea->estado = '0';
            $tarea->idusuario = \Auth::user()->id;
            $tarea->idcliente = $request->idcliente;
            $tarea->save();

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function desactivar(Request $request){
        if(!$request->ajax()) return redirect('/');
        $tarea = Tarea::findOrFail($request->id);
        $tarea->estado = '2';
        $tarea->save();

    }
    public function completar(Request $request){
        if(!$request->ajax()) return redirect('/');
        $tarea = Tarea::findOrFail($request->id);
        $tarea->estado = $request->estado;
        $tarea->save();
    }
    public function obtenerTareasCliente(Request $request){

        if (!$request->ajax()) return redirect('/');

        $idcliente = $request->idcliente;

        $tareas = Tarea::join('personas','personas.id','=','tareas.idcliente')
        ->join('users','users.id','=','tareas.idusuario')
        ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company',
            'personas.tel_company','personas.observacion','users.idrol','users.area','tareas.id','tareas.nombre',
            'tareas.descripcion','tareas.fecha','tareas.estado', 'tareas.tipo as clase','personas.num_documento')
        ->where([
            ['tareas.idcliente',$idcliente],
            ['tareas.tipo','!=','Comentario']
        ])
        ->orderBy('tareas.fecha', 'desc')->paginate(7);

        $siguiente = Tarea::join('personas','personas.id','=','tareas.idcliente')
        ->join('users','users.id','=','tareas.idusuario')
        ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company',
            'personas.tel_company','personas.observacion','users.idrol','users.area','tareas.id','tareas.nombre',
            'tareas.descripcion','tareas.fecha','tareas.estado', 'tareas.tipo as clase','personas.num_documento')
        ->where([
            ['tareas.idcliente',$idcliente],
            ['tareas.tipo','!=','Comentario'],
            ['tareas.estado',0]
        ])
        ->orderBy('tareas.fecha','asc')->paginate(1);

        $comentarios = Tarea::join('personas','personas.id','=','tareas.idcliente')
        ->join('users','users.id','=','tareas.idusuario')
        ->select('personas.id as idcliente','personas.nombre as cliente','personas.domicilio','personas.telefono',
            'personas.ciudad','personas.rfc','personas.email','personas.tipo','users.usuario','personas.company',
            'personas.tel_company','personas.observacion','users.idrol','users.area','tareas.id','tareas.nombre',
            'tareas.descripcion','tareas.fecha','tareas.estado', 'tareas.tipo as clase','personas.num_documento')
        ->where([
            ['tareas.idcliente',$idcliente],
            ['tareas.tipo','Comentario'],
            ['tareas.estado',0]
        ])
        ->orderBy('tareas.fecha', 'desc')->paginate(4);

        return [
            'tareas' => $tareas,
            'siguiente' => $siguiente,
            'comentarios' => $comentarios
        ];

    }
}
