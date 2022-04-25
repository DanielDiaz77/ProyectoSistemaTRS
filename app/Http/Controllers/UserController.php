<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Persona;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index(Request $request){
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar==''){
            $personas = User::join('personas','users.id','=','personas.id')
            ->join('roles','users.idrol','=','roles.id')
            ->select('personas.id','personas.nombre','personas.tipo_documento',
                'personas.num_documento','personas.domicilio','personas.telefono',
                'personas.ciudad','personas.rfc','personas.email','users.usuario',
                'users.password','users.condicion','users.idrol','roles.nombre as rol',
                'users.area','users.autoing','users.usedit','users.last_act')
            ->orderBy('personas.id', 'desc')->paginate(12);
        } else{
            $personas = User::join('personas','users.id','=','personas.id')
            ->join('roles','users.idrol','=','roles.id')
            ->select('personas.id','personas.nombre','personas.tipo_documento',
                'personas.num_documento','personas.domicilio','personas.telefono',
                'personas.ciudad','personas.rfc','personas.email','users.usuario',
                'users.password','users.condicion','users.idrol','roles.nombre as rol',
                'users.area','users.autoing','users.usedit','users.last_act')
            ->where('personas.'.$criterio, 'like', '%'. $buscar . '%')
            ->orderBy('personas.id', 'desc')->paginate(12);
        }

        return [
            'pagination' => [
                'total'        => $personas->total(),
                'current_page' => $personas->currentPage(),
                'per_page'     => $personas->perPage(),
                'last_page'    => $personas->lastPage(),
                'from'         => $personas->firstItem(),
                'to'           => $personas->lastItem(),
            ],
            'personas' => $personas
        ];
    }
    public function store(Request $request){
        if(!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();
            $persona = new Persona();
            $persona->nombre         = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento  = $request->num_documento;
            $persona->ciudad         = $request->ciudad;
            $persona->domicilio      = $request->domicilio;
            $persona->telefono       = $request->telefono;
            $persona->email          = $request->email;
            $persona->rfc            = $request->rfc;
            $persona->active         = 0;
            $persona->save();

            $user = new User();
            $user->usuario   = $request->usuario;
            $user->password  = bcrypt($request->password);
            $user->condicion = '1';
            $user->idrol     = $request->idrol;
            $user->id        = $persona->id;
            $user->area      = $request->area;
            $user->autoing   = $request->autoing;
            $user->usedit    = $request->usedit;
            $user->save();
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function update(Request $request){
        if(!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();

            //Buscamos primero el proveedor a modificar

            $user = User::findOrFail($request->id);

            $persona = Persona::findOrFail($user->id);

            $persona->nombre         = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento  = $request->num_documento;
            $persona->ciudad         = $request->ciudad;
            $persona->domicilio      = $request->domicilio;
            $persona->telefono       = $request->telefono;
            $persona->email          = $request->email;
            $persona->rfc            = $request->rfc;
            $persona->active         = 0;
            $persona->save();

            $user->usuario   = $request->usuario;
            $user->condicion = '1';
            $user->idrol     = $request->idrol;
            $user->area      = $request->area;
            $user->autoing   = $request->autoing;
            $user->usedit    = $request->usedit;
            $user->save();

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function desactivar(Request $request){
        if(!$request->ajax()) return redirect('/');
        $user = User::findOrFail($request->id);
        $user->condicion = '0';
        $user->save();

    }
    public function activar(Request $request){
        if(!$request->ajax()) return redirect('/');
        $user = User::findOrFail($request->id);
        $user->condicion = '1';
        $user->save();
    }
    public function selectUsuario(Request $request){

        if(!$request->ajax()) return redirect('/');

        $usuarios = User::join('personas','users.id','=','personas.id')
        ->where([['condicion',1],['idrol','!=',3]])
        ->select('users.id','personas.nombre','users.area')->orderBy('id','asc')->get();

        return ['usuarios' => $usuarios];

    }
    public function selectUsuarioAct(Request $request){

        if(!$request->ajax()) return redirect('/');

        $actUser = \Auth::user()->id;

        $usuarios = User::join('personas','users.id','=','personas.id')
        ->where([['condicion',1],['users.id','!=',$actUser]])
        ->select('users.id','personas.nombre','users.area')->orderBy('id','asc')->get();

        return ['usuarios' => $usuarios];

    }
    public function autoIngreso(Request $request){
        if (!$request->ajax()) return redirect('/');
        try{
            $user = User::findOrFail($request->id);
            if($request->autoingreso == 0){
                $user->autoing = 0;
            }else{
                $user->autoing = 1;
            }
            $user->save();
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function autoFormato(Request $request){

        if (!$request->ajax()) return redirect('/');
        try{
            $user = User::findOrFail($request->id);
            if($request->autoFormato == 0){
                $user->autofor = 0;
            }else{
                $user->autofor= 1;
            }
            $user->save();
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function editarArticulos(Request $request){
        if(!$request->ajax()) return redirect('/');

        try{
            $user = User::findOrFail($request->id);
            if($request->usedit == 0){
                $user->usedit = 0;
            }else{
                $user->usedit= 1;
            }
            $user->save();
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function setLastConnection(Request $request){
        if (!$request->ajax()) return redirect('/');
        $mytime = Carbon::now('America/Mexico_City');
        try{
            $user = User::findOrFail($request->id);
            $user->last_act = $mytime;
            $user->save();
            DB::commit();
            return $user->usuario;
        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function getUserPerms(Request $request,$id){
        if (!$request->ajax()) return redirect('/');
        $user = User::findOrFail($id);
        return [ 'Permiso' => $user->autoing ];
    }
    public function getUserEditar(Request $request,$id){
        if (!$request->ajax()) return redirect('/');
        $user = User::findOrFail($id);
        return [ 'Permiso' => $user->usedit];
    }
    public function cambiarPassword(Request $request){
        if (!$request->ajax()) return redirect('/');
        try{
            $user = User::findOrFail($request->id);
            $user->password  = bcrypt($request->password);
            $user->save();
            DB::commit();
            return $user->usuario;
        }catch(Exception $e){
            DB::rollBack();
        }
    }
}
