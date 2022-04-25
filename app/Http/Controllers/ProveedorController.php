<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Proveedor;
use App\Persona;

class ProveedorController extends Controller
{
    public function index(Request $request){
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar==''){
            $personas = Proveedor::join('personas','proveedores.id','=','personas.id')
            ->select('personas.id','personas.nombre','personas.tipo_documento',
            'personas.num_documento','personas.domicilio','personas.telefono',
            'personas.ciudad','personas.rfc','personas.email','proveedores.contacto',
            'proveedores.telefono_contacto')
            ->orderBy('personas.id', 'desc')->paginate(12);
        }
        else{
            $personas = Proveedor::join('personas','proveedores.id','=','personas.id')
            ->select('personas.id','personas.nombre','personas.tipo_documento',
            'personas.num_documento','personas.domicilio','personas.telefono',
            'personas.ciudad','personas.rfc','personas.email','proveedores.contacto',
            'proveedores.telefono_contacto')
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
            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->ciudad = $request->ciudad;
            $persona->domicilio = $request->domicilio;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->rfc = $request->rfc;
            $persona->save();

            $proveedor = new Proveedor();
            $proveedor->contacto = $request->contacto;
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->id = $persona->id;

            $proveedor->save();

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

            $proveedor = Proveedor::findOrFail($request->id);

            $persona = Persona::findOrFail($proveedor->id);

            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->ciudad = $request->ciudad;
            $persona->domicilio = $request->domicilio;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->rfc = $request->rfc;
            $persona->save();

            $proveedor->contacto = $request->contacto;
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->save();

            DB::commit();

        }catch(Exception $e){

            DB::rollBack();

        }
    }
    public function selectProveedor(Request $request){

        if (!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;

        $proveedores = Proveedor::join('personas','proveedores.id','=','personas.id')
        ->where('personas.nombre','like','%'.$filtro.'%')
        ->orWhere('personas.rfc','like','%'.$filtro.'%')
        ->select('personas.id','personas.nombre','personas.rfc','proveedores.contacto',
        'proveedores.telefono_contacto')
        ->orderBy('personas.nombre','asc')->get();

        return ['proveedores' => $proveedores];
    }
}
