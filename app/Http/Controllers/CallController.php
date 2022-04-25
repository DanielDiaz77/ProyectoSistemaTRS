<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Proveedor;
use App\Comment;
use App\Persona;
use App\Call;
use App\User;

class CallController extends Controller
{
    public function index(Request $request){

        //if(!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $estado = $request->estado;
        $zona = $request->zona;
        $filtro = $request->buscador;

        $usrol = \Auth::user()->idrol;
        $usarea = \Auth::user()->area;
        $iduser = \Auth::user()->id;

            if($filtro == ''){
                if($buscar == ''){
                    if($zona == ''){
                        if($estado != ''){
                            $llamadas = Call::leftjoin('personas AS empleados', 'empleados.id','calls.idusuario')
                            ->join('personas', 'personas.id','=','calls.idcliente')
                            ->leftjoin('personas AS vendedores', 'personas.idusuario','vendedores.id')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.tipo','personas.telefono','personas.domicilio',
                                'personas.company','personas.tel_company','empleados.nombre as usuario','personas.email','personas.num_documento','personas.clave',
                                'personas.idusuario as idvendedor','personas.ciudad','calls.id','calls.body','calls.status','calls.area'
                                ,'calls.created_at as fecha','personas.active','calls.idusuario as usercall','vendedores.nombre as agente')
                            ->where([
                                    ['personas.'.$criterio, 'like', '%'. $buscar . '%'],
                                    ['calls.status',$estado],

                                ])
                            ->orderBy('calls.updated_at', 'desc')
                            ->paginate(12);
                        }else{
                            $llamadas = Call::leftjoin('personas AS empleados', 'empleados.id','calls.idusuario')
                            ->leftjoin('personas', 'personas.id','=','calls.idcliente')
                            ->leftjoin('personas AS vendedores', 'personas.idusuario','vendedores.id')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.tipo','personas.telefono','personas.domicilio',
                                'personas.company','personas.tel_company','empleados.nombre as usuario','personas.email','personas.num_documento','personas.clave',
                                'personas.idusuario as idvendedor','personas.ciudad','calls.id','calls.body','calls.status','calls.area'
                                ,'calls.created_at as fecha','personas.active','calls.idusuario as usercall','vendedores.nombre as agente')
                            ->where([['personas.'.$criterio, 'like', '%'. $buscar . '%'],])
                            ->orderBy('calls.updated_at', 'desc')
                            ->paginate(12);
                        }
                    }else{
                        if($estado != ''){
                            $llamadas = Call::leftjoin('personas AS empleados', 'empleados.id','calls.idusuario')
                            ->leftjoin('personas', 'personas.id','=','calls.idcliente')
                            ->leftjoin('personas AS vendedores', 'personas.idusuario','vendedores.id')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.tipo','personas.telefono','personas.domicilio',
                                'personas.company','personas.tel_company','empleados.nombre as usuario','personas.email','personas.num_documento','personas.clave',
                                'personas.idusuario as idvendedor','personas.ciudad','calls.id','calls.body','calls.status','calls.area'
                                ,'calls.created_at as fecha','personas.active','calls.idusuario as usercall','vendedores.nombre as agente')
                            ->where([
                                    ['personas.'.$criterio, 'like', '%'. $buscar . '%'],
                                    ['calls.status',$estado],
                                    ['calls.area',$zona],


                                ])
                            ->orderBy('calls.updated_at', 'desc')
                            ->paginate(12);
                        }else{
                            $llamadas = Call::leftjoin('personas AS empleados', 'empleados.id','calls.idusuario')
                            ->leftjoin('personas', 'personas.id','=','calls.idcliente')
                            ->leftjoin('personas AS vendedores', 'personas.idusuario','vendedores.id')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.tipo','personas.telefono','personas.domicilio',
                                'personas.company','personas.tel_company','empleados.nombre as usuario','personas.email','personas.num_documento','personas.clave',
                                'personas.idusuario as idvendedor','personas.ciudad','calls.id','calls.body','calls.status','calls.area'
                                ,'calls.created_at as fecha','personas.active','calls.idusuario as usercall','vendedores.nombre as agente')
                            ->where([
                                    ['personas.'.$criterio, 'like', '%'. $buscar . '%'],['calls.area',$zona],])
                            ->orderBy('calls.updated_at', 'desc')
                            ->paginate(12);
                        }
                    }
                }else{
                    if($zona == ''){
                        if($estado != ''){
                            $llamadas = Call::leftjoin('personas AS empleados', 'empleados.id','calls.idusuario')
                            ->leftjoin('personas', 'personas.id','=','calls.idcliente')
                            ->leftjoin('personas AS vendedores', 'personas.idusuario','vendedores.id')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.tipo','personas.telefono','personas.domicilio',
                                'personas.company','personas.tel_company','empleados.nombre as usuario','personas.email','personas.num_documento','personas.clave',
                                'personas.idusuario as idvendedor','personas.ciudad','calls.id','calls.body','calls.status','calls.area'
                                ,'calls.created_at as fecha','personas.active','calls.idusuario as usercall','vendedores.nombre as agente')
                            ->where([
                                    ['personas.'.$criterio, 'like', '%'. $buscar . '%'],
                                    ['calls.status',$estado],

                                ])
                            ->orderBy('calls.updated_at', 'desc')
                            ->paginate(12);
                        }else{
                            $llamadas = Call::leftjoin('personas AS empleados', 'empleados.id','calls.idusuario')
                            ->leftjoin('personas', 'personas.id','=','calls.idcliente')
                            ->leftjoin('personas AS vendedores', 'personas.idusuario','vendedores.id')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.tipo','personas.telefono','personas.domicilio',
                                'personas.company','personas.tel_company','empleados.nombre as usuario','personas.email','personas.num_documento','personas.clave',
                                'personas.idusuario as idvendedor','personas.ciudad','calls.id','calls.body','calls.status','calls.area'
                                ,'calls.created_at as fecha','personas.active','calls.idusuario as usercall','vendedores.nombre as agente')
                            ->where([['personas.'.$criterio, 'like', '%'. $buscar . '%'],])
                            ->orderBy('calls.updated_at', 'desc')
                            ->paginate(12);
                        }
                    }else{
                        if($estado != ''){
                            $llamadas = Call::leftjoin('personas AS empleados', 'empleados.id','calls.idusuario')
                            ->leftjoin('personas', 'personas.id','=','calls.idcliente')
                            ->leftjoin('personas AS vendedores', 'personas.idusuario','vendedores.id')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.tipo','personas.telefono','personas.domicilio',
                                'personas.company','personas.tel_company','empleados.nombre as usuario','personas.email','personas.num_documento','personas.clave',
                                'personas.idusuario as idvendedor','personas.ciudad','calls.id','calls.body','calls.status','calls.area'
                                ,'calls.created_at as fecha','personas.active','calls.idusuario as usercall','vendedores.nombre as agente')
                            ->where([
                                    ['personas.'.$criterio, 'like', '%'. $buscar . '%'],
                                    ['calls.status',$estado],
                                    ['calls.area',$zona],

                                ])
                            ->orderBy('calls.updated_at', 'desc')
                            ->paginate(12);
                        }else{
                            $llamadas = Call::leftjoin('personas AS empleados', 'empleados.id','calls.idusuario')
                            ->leftjoin('personas', 'personas.id','=','calls.idcliente')
                            ->leftjoin('personas AS vendedores', 'personas.idusuario','vendedores.id')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.tipo','personas.telefono','personas.domicilio',
                                'personas.company','personas.tel_company','empleados.nombre as usuario','personas.email','personas.num_documento','personas.clave',
                                'personas.idusuario as idvendedor','personas.ciudad','calls.id','calls.body','calls.status','calls.area'
                                ,'calls.created_at as fecha','personas.active','calls.idusuario as usercall','vendedores.nombre as agente')
                            ->where([
                                    ['personas.'.$criterio, 'like', '%'. $buscar . '%'],['calls.area',$zona],])
                            ->orderBy('calls.updated_at', 'desc')
                            ->paginate(12);
                        }
                    }
                }

            }else{
                if($buscar == ''){
                    if($zona == ''){
                        if($estado != ''){
                            $llamadas = Call::leftjoin('personas AS empleados', 'empleados.id','calls.idusuario')
                            ->leftjoin('personas', 'personas.id','=','calls.idcliente')
                            ->leftjoin('personas AS vendedores', 'personas.idusuario','vendedores.id')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.tipo','personas.telefono','personas.domicilio',
                                'personas.company','personas.tel_company','empleados.nombre as usuario','personas.email','personas.num_documento','personas.clave',
                                'personas.idusuario as idvendedor','personas.ciudad','calls.id','calls.body','calls.status','calls.area'
                                ,'calls.created_at as fecha','personas.active','calls.idusuario as usercall','vendedores.nombre as agente')
                            ->where([['personas.clave','LIKE', '%'.$filtro.'%'],['calls.status',$estado]])
                            ->orderBy('calls.updated_at', 'desc')
                            ->paginate(12);
                        }else{
                            $llamadas = Call::leftjoin('personas AS empleados', 'empleados.id','calls.idusuario')
                            ->leftjoin('personas', 'personas.id','=','calls.idcliente')
                            ->leftjoin('personas AS vendedores', 'personas.idusuario','vendedores.id')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.tipo','personas.telefono','personas.domicilio',
                                'personas.company','personas.tel_company','empleados.nombre as usuario','personas.email','personas.num_documento','personas.clave',
                                'personas.idusuario as idvendedor','personas.ciudad','calls.id','calls.body','calls.status','calls.area'
                                ,'calls.created_at as fecha','personas.active','calls.idusuario as usercall','vendedores.nombre as agente')
                            ->where([['personas.clave','LIKE', '%'.$filtro.'%']])
                            ->orderBy('calls.updated_at', 'desc')
                            ->paginate(12);
                        }
                    }else{
                        if($estado != ''){
                            $llamadas = Call::leftjoin('personas AS empleados', 'empleados.id','calls.idusuario')
                            ->leftjoin('personas', 'personas.id','=','calls.idcliente')
                            ->leftjoin('personas AS vendedores', 'personas.idusuario','vendedores.id')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.tipo','personas.telefono','personas.domicilio',
                                'personas.company','personas.tel_company','empleados.nombre as usuario','personas.email','personas.num_documento','personas.clave',
                                'personas.idusuario as idvendedor','personas.ciudad','calls.id','calls.body','calls.status','calls.area'
                                ,'calls.created_at as fecha','personas.active','calls.idusuario as usercall','vendedores.nombre as agente')
                            ->where([['personas.clave','LIKE', '%'.$filtro.'%'],['calls.status',$estado],['calls.area',$zona]
                                ])
                            ->orderBy('calls.updated_at', 'desc')
                            ->paginate(12);
                        }else{
                            $llamadas = Call::leftjoin('personas AS empleados', 'empleados.id','calls.idusuario')
                            ->leftjoin('personas', 'personas.id','=','calls.idcliente')
                            ->leftjoin('personas AS vendedores', 'personas.idusuario','vendedores.id')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.tipo','personas.telefono','personas.domicilio',
                                'personas.company','personas.tel_company','empleados.nombre as usuario','personas.email','personas.num_documento','personas.clave',
                                'personas.idusuario as idvendedor','personas.ciudad','calls.id','calls.body','calls.status','calls.area'
                                ,'calls.created_at as fecha','personas.active','calls.idusuario as usercall','vendedores.nombre as agente')
                            ->where([['personas.clave','LIKE', '%'.$filtro.'%'],['calls.area',$zona]])
                            ->orderBy('calls.updated_at', 'desc')
                            ->paginate(12);
                        }
                    }
                }else{
                    if($zona == ''){
                        if($estado != ''){
                            $llamadas = Call::leftjoin('personas AS empleados', 'empleados.id','calls.idusuario')
                            ->leftjoin('personas', 'personas.id','=','calls.idcliente')
                            ->leftjoin('personas AS vendedores', 'personas.idusuario','vendedores.id')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.tipo','personas.telefono','personas.domicilio',
                                'personas.company','personas.tel_company','empleados.nombre as usuario','personas.email','personas.num_documento','personas.clave',
                                'personas.idusuario as idvendedor','personas.ciudad','calls.id','calls.body','calls.status','calls.area'
                                ,'calls.created_at as fecha','personas.active','calls.idusuario as usercall','vendedores.nombre as agente')
                            ->where([['personas.clave','LIKE', '%'.$filtro.'%'], ['calls.status',$estado]])
                            ->orderBy('calls.updated_at', 'desc')
                            ->paginate(12);
                        }else{
                            $llamadas = Call::leftjoin('personas AS empleados', 'empleados.id','calls.idusuario')
                            ->leftjoin('personas', 'personas.id','=','calls.idcliente')
                            ->leftjoin('personas AS vendedores', 'personas.idusuario','vendedores.id')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.tipo','personas.telefono','personas.domicilio',
                                'personas.company','personas.tel_company','empleados.nombre as usuario','personas.email','personas.num_documento','personas.clave',
                                'personas.idusuario as idvendedor','personas.ciudad','calls.id','calls.body','calls.status','calls.area'
                                ,'calls.created_at as fecha','personas.active','calls.idusuario as usercall','vendedores.nombre as agente')
                            ->where([['personas.clave','LIKE', '%'.$filtro.'%']])
                            ->orderBy('calls.updated_at', 'desc')
                            ->paginate(12);
                        }
                    }else{
                        if($estado != ''){
                            $llamadas = Call::leftjoin('personas AS empleados', 'empleados.id','calls.idusuario')
                            ->leftjoin('personas', 'personas.id','=','calls.idcliente')
                            ->leftjoin('personas AS vendedores', 'personas.idusuario','vendedores.id')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.tipo','personas.telefono','personas.domicilio',
                                'personas.company','personas.tel_company','empleados.nombre as usuario','personas.email','personas.num_documento','personas.clave',
                                'personas.idusuario as idvendedor','personas.ciudad','calls.id','calls.body','calls.status','calls.area'
                                ,'calls.created_at as fecha','personas.active','calls.idusuario as usercall','vendedores.nombre as agente')
                            ->where([['personas.clave','LIKE', '%'.$filtro.'%'],['calls.status',$estado ], ['calls.area',$zona],
                                ])
                            ->orderBy('calls.updated_at', 'desc')
                            ->paginate(12);
                        }else{
                            $llamadas = Call::leftjoin('personas AS empleados', 'empleados.id','calls.idusuario')
                            ->leftjoin('personas', 'personas.id','=','calls.idcliente')
                            ->leftjoin('personas AS vendedores', 'personas.idusuario','vendedores.id')
                            ->select('personas.id as idcliente','personas.nombre as cliente','personas.tipo','personas.telefono','personas.domicilio',
                                'personas.company','personas.tel_company','empleados.nombre as usuario','personas.email','personas.num_documento','personas.clave',
                                'personas.idusuario as idvendedor','personas.ciudad','calls.id','calls.body','calls.status','calls.area'
                                ,'calls.created_at as fecha','personas.active','calls.idusuario as usercall','vendedores.nombre as agente')
                            ->where([['calls.area',$zona],['personas.clave','LIKE', '%'.$filtro.'%']])
                            ->orderBy('calls.updated_at', 'desc')
                            ->paginate(12);
                        }
                    }
                }
            }




        return [
            'pagination' => [
                'total'         => $llamadas->total(),
                'current_page'  => $llamadas->currentPage(),
                'per_page'      => $llamadas->perPage(),
                'last_page'     => $llamadas->lastPage(),
                'from'          => $llamadas->firstItem(),
                'to'            => $llamadas->lastItem(),
            ],
            'llamadas' => $llamadas,
            'userid' => $iduser,
            'userarea' => $usarea,
        ];

    }
    public function storeCliente(Request $request){

        if(!$request->ajax()) return redirect('/');

        $usarea = \Auth::user()->area;

        try{
            DB::beginTransaction();

            $persona = new Persona();
            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->ciudad = $request->ciudad;
            $persona->domicilio = $request->domicilio;
            $persona->telefono = $request->telefono;
            $persona->company = $request->company;
            $persona->tel_company = $request->tel_company;
            $persona->email = $request->email;
            $persona->tipo = $request->tipo;
            $persona->idusuario = \Auth::user()->id;
            $persona->active = 3;
            $persona->area = $usarea;
            $persona->save();

            $call = new Call();
            $call->idcliente = $persona->id;
            $call->idusuario = \Auth::user()->id;
            $call->body = $request->body;
            $call->status = 'Pendiente';
            $call->area = $request->area;
            $call->save();

            DB::commit();

        }catch(Exception $e){

            DB::rollBack();

        }
    }
    public function storeProveedor(Request $request){

        if(!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $persona = new Persona();
            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->ciudad = $request->ciudad;
            $persona->domicilio = $request->domicilio;
            $persona->telefono = $request->telefono;
            $persona->company = $request->company;
            $persona->tel_company = $request->tel_company;
            $persona->email = $request->email;
            $persona->tipo = "Proveedor";
            $persona->idusuario = \Auth::user()->id;
            $persona->active = 3;
            $persona->save();

            $proveedor = new Proveedor();
            $proveedor->contacto = $request->company;
            $proveedor->telefono_contacto = $request->tel_company;
            $proveedor->id = $persona->id;
            $proveedor->save();

            $call = new Call();
            $call->idcliente = $persona->id;
            $call->idusuario = \Auth::user()->id;
            $call->body = $request->body;
            $call->status = 'Pendiente';
            $call->area = $request->area;
            $call->save();

            DB::commit();

        }catch(Exception $e){

            DB::rollBack();

        }
    }
    public function update(Request $request){

        if(!$request->ajax()) return redirect('/');

        $persona = Persona::findOrFail($request->idcliente);
        $persona->nombre = $request->nombre;
        $persona->tipo_documento = $request->tipo_documento;
        $persona->num_documento = $request->num_documento;
        $persona->ciudad = $request->ciudad;
        $persona->domicilio = $request->domicilio;
        $persona->telefono = $request->telefono;
        $persona->company = $request->company;
        $persona->tel_company = $request->tel_company;
        $persona->email = $request->email;
        $persona->rfc = $request->rfc;
        $persona->tipo = $request->tipo;
        $persona->idusuario = $request->idvendedor;
        $persona->clave = $request->clave;
        $persona->cfdi = $request->cfdi;
        $persona->active = $request->active;
        $persona->observacion = $request->observacion;
        $persona->area = $request->area;
        $persona->save();

        $call = Call::findOrFail($request->idcall);
        $call->idcliente = $persona->id;
        $call->idusuario = $request->usercall;
        $call->body = $request->body;
        $call->status = 'Pendiente';
        $call->area = $request->area;
        $call->save();

    }
    public function desactivar(Request $request){
        if(!$request->ajax()) return redirect('/');
        $call = Call::findOrFail($request->id);
        $call->status = 'Cancelada';
        $call->save();
    }
    public function crearComment(Request $request){
        if (!$request->ajax()) return redirect('/');
        $userid = \Auth::user()->id;
        try{
            DB::beginTransaction();
            $comment = new Comment(['user_id' => $userid, 'body' => $request->body]);
            $call = Call::findOrFail($request->id); //Llamada a comentar
            $call->comments()->save($comment);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function getComments(Request $request){

        if (!$request->ajax()) return redirect('/');

        $call = Call::findOrFail($request->id); //ID llamada comentada

        $comments = $call->comments()
        ->join('users','users.id','comments.user_id')
        ->leftjoin('personas', 'users.id','=','personas.id')
        ->select('comments.id','comments.user_id as user','comments.body','comments.updated_at as fecha','personas.nombre')
        ->orderBy('comments.updated_at','desc')
        ->get();

        return [
            'comentarios' => $comments
        ];

    }
    public function editComment(Request $request){
        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();
            $comm= Comment::findOrFail($request->id);
            $comm->body = $request->body;
            $comm->save();

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function deleteComment(Request $request){
        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();
            $comm = Comment::findOrFail($request->id);
            $comm->delete();
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function cambiarEstado(Request $request){
        if (!$request->ajax()) return redirect('/');
        $call = Call::findOrFail($request->id);
        $call->status = $request->status;
        $call->save();
    }

    public function listarClave(Request $request){

        $filtro = $request->buscador;

        $llamadas = Call::join('personas', 'personas.id', '=', 'calls.idcliente')
        ->leftjoin('users', 'users.id', '=', 'calls.idusuario')
        ->select('personas.id','personas.nombre','personas.tipo','personas.telefono','personas.domicilio',
        'personas.company','personas.tel_company','users.usuario','personas.email','personas.num_documento','personas.clave',
        'personas.idusuario as idvendedor','personas.ciudad','calls.id','calls.body','calls.status','calls.area'
        ,'calls.created_at as fecha','personas.active','calls.idusuario as usercall')
        ->where([['personas.clave','LIKE', '%'.$filtro.'%'],['calls.status','==', 'Pendiente']])->get();

            return[
                'llamadas' => $llamadas,
                'filtro' => $filtro,
            ];
    }

}
