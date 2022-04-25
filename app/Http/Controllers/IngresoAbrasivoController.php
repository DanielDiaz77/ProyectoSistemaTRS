<?php

namespace App\Http\Controllers;

use App\IngresoAbrasivo;
use Illuminate\Http\Request;
use App\Abrasivo;
use App\DetalleIngresoAbrasivo;
use App\Notifications\NotifyAdmin;
use App\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use PhpParser\Node\Expr\New_;

class IngresoAbrasivoController extends Controller
{
    public function index(Request $request){
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $estado = $request->estado;

        if($estado == ''){
            if ($buscar==''){
                $ingresos = IngresoAbrasivo::join('personas','ingreso_abrasivos.idproveedor','=','personas.id')
                ->join('users','ingreso_abrasivos.idusuario','=','users.id')
                ->select('ingreso_abrasivos.id','ingreso_abrasivos.tipo_comprobante','ingreso_abrasivos.num_comprobante',
                'ingreso_abrasivos.fecha_hora','ingreso_abrasivos.impuesto','ingreso_abrasivos.total','ingreso_abrasivos.estado',
                'personas.nombre','users.usuario', 'ingreso_abrasivos.active' )
                ->orderBy('ingreso_abrasivos.id', 'desc')->paginate(12);
            }
            else{
                $ingresos = IngresoAbrasivo::join('personas','ingreso_abrasivos.idproveedor','=','personas.id')
                ->join('users','ingreso_abrasivos.idusuario','=','users.id')
                ->select('ingreso_abrasivos.id','ingreso_abrasivos.tipo_comprobante','ingreso_abrasivos.num_comprobante',
                'ingreso_abrasivos.fecha_hora','ingreso_abrasivos.impuesto','ingreso_abrasivos.total','ingreso_abrasivos.estado',
                'personas.nombre','users.usuario', 'ingreso_abrasivos.active')
                ->where('ingreso_abrasivos.'.$criterio, 'like', '%'. $buscar . '%')
                ->orderBy('ingreso_abrasivos.id', 'desc')->paginate(12);
            }
        }else{
            if ($buscar==''){
                $ingresos = IngresoAbrasivo::join('personas','ingreso_abrasivos.idproveedor','=','personas.id')
                ->join('users','ingreso_abrasivos.idusuario','=','users.id')
                ->select('ingreso_abrasivos.id','ingreso_abrasivos.tipo_comprobante','ingreso_abrasivos.num_comprobante',
                'ingreso_abrasivos.fecha_hora','ingreso_abrasivos.impuesto','ingreso_abrasivos.total','ingreso_abrasivos.estado',
                'personas.nombre','users.usuario', 'ingreso_abrasivos.active')
                ->where('ingreso_abrasivos.estado',$estado)
                ->orderBy('ingreso_abrasivos.id', 'desc')->paginate(12);
            }
            else{
                $ingresos = IngresoAbrasivo::join('personas','ingreso_abrasivos.idproveedor','=','personas.id')
                ->join('users','ingreso_abrasivos.idusuario','=','users.id')
                ->select('ingreso_abrasivos.id','ingreso_abrasivos.tipo_comprobante','ingreso_abrasivos.num_comprobante',
                'ingreso_abrasivos.fecha_hora','ingreso_abrasivos.impuesto','ingreso_abrasivos.total','ingreso_abrasivos.estado',
                'personas.nombre','users.usuario', 'ingreso_abrasivos.active')
                ->where([['ingreso_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['ingreso_abrasivos.estado',$estado]])
                ->orderBy('ingreso_abrasivos.id', 'desc')->paginate(12);
            }
        }

        return [
            'pagination' => [
                'total'        => $ingresos->total(),
                'current_page' => $ingresos->currentPage(),
                'per_page'     => $ingresos->perPage(),
                'last_page'    => $ingresos->lastPage(),
                'from'         => $ingresos->firstItem(),
                'to'           => $ingresos->lastItem(),
            ],
            'ingresos' => $ingresos
        ];
    }
    public function indexEntradas(Request $request){

        //if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $estado = $request->estado;

        if($estado == ''){
            if ($buscar==''){
                $entradas = IngresoAbrasivo::join('personas','ingreso_abrasivos.idproveedor','=','personas.id')
                ->join('users','ingreso_abrasivos.idusuario','=','users.id')
                ->select('ingreso_abrasivos.id','ingreso_abrasivos.tipo_comprobante','ingreso_abrasivos.num_comprobante',
                'ingreso_abrasivos.fecha_hora','ingreso_abrasivos.impuesto','ingreso_abrasivos.total','ingreso_abrasivos.estado',
                'personas.nombre','users.usuario', 'ingreso_abrasivos.active' )
                ->orderBy('ingreso_abrasivos.id', 'desc')->paginate(12);
            }
            else{
                $entradas = IngresoAbrasivo::join('personas','ingreso_abrasivos.idproveedor','=','personas.id')
                ->join('users','ingreso_abrasivos.idusuario','=','users.id')
                ->select('ingreso_abrasivos.id','ingreso_abrasivos.tipo_comprobante','ingreso_abrasivos.num_comprobante',
                'ingreso_abrasivos.fecha_hora','ingreso_abrasivos.impuesto','ingreso_abrasivos.total','ingreso_abrasivos.estado',
                'personas.nombre','users.usuario', 'ingreso_abrasivos.active')
                ->where('ingreso_abrasivos.'.$criterio, 'like', '%'. $buscar . '%')
                ->orderBy('ingreso_abrasivos.id', 'desc')->paginate(12);
            }
        }else{
            if ($buscar==''){
                $entradas = IngresoAbrasivo::join('personas','ingreso_abrasivos.idproveedor','=','personas.id')
                ->join('users','ingreso_abrasivos.idusuario','=','users.id')
                ->select('ingreso_abrasivos.id','ingreso_abrasivos.tipo_comprobante','ingreso_abrasivos.num_comprobante',
                'ingreso_abrasivos.fecha_hora','ingreso_abrasivos.impuesto','ingreso_abrasivos.total','ingreso_abrasivos.estado',
                'personas.nombre','users.usuario', 'ingreso_abrasivos.active')
                ->where('ingreso_abrasivos.estado',$estado)
                ->orderBy('ingreso_abrasivos.id', 'desc')->paginate(12);
            }
            else{
                $entradas = IngresoAbrasivo::join('personas','ingreso_abrasivos.idproveedor','=','personas.id')
                ->join('users','ingreso_abrasivos.idusuario','=','users.id')
                ->select('ingreso_abrasivos.id','ingreso_abrasivos.tipo_comprobante','ingreso_abrasivos.num_comprobante',
                'ingreso_abrasivos.fecha_hora','ingreso_abrasivos.impuesto','ingreso_abrasivos.total','ingreso_abrasivos.estado',
                'personas.nombre','users.usuario', 'ingreso_abrasivos.active')
                ->where([['ingreso_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['ingreso_abrasivos.estado',$estado]])
                ->orderBy('ingreso_abrasivos.id', 'desc')->paginate(12);
            }
        }

        return [
            'pagination' => [
                'total'        => $entradas->total(),
                'current_page' => $entradas->currentPage(),
                'per_page'     => $entradas->perPage(),
                'last_page'    => $entradas->lastPage(),
                'from'         => $entradas->firstItem(),
                'to'           => $entradas->lastItem(),
            ],
            'entradas' => $entradas
        ];
    }
    public function store(Request $request){
        if(!$request->ajax()) return redirect('/');

        $mytime = Carbon::now('America/Mexico_City');

        try{
            DB::beginTransaction();

            $ingreso = new IngresoAbrasivo();
            $ingreso->idproveedor = $request->idproveedor;
            $ingreso->idusuario = \Auth::user()->id;
            $ingreso->tipo_comprobante = $request->tipo_comprobante;
            $ingreso->num_comprobante = $request->num_comprobante;
            $ingreso->fecha_hora = $mytime;
            $ingreso->impuesto = $request->impuesto;
            $ingreso->total = $request->total;
            $ingreso->estado = 'Registrado';
            $ingreso->active = $request->active;
            $ingreso->save();

            $detalles = $request->data;//Array de detalles

            //Recorro todos los elementos
            foreach($detalles as $ep=>$det) {
                $articulos = Abrasivo::where('codigo','=',$det['codigo'])->select('id')->first();
                $detalle = new DetalleIngresoAbrasivo();
                $detalle->idingresoabrasivo = $ingreso->id;
                $detalle->idabrasivo = $articulos->id;
                $detalle->cantidad = $det['stock'];
                $detalle->precio_compra = $det['precio_venta'];
                $detalle->save();
            }

            DB::commit();


        }catch(Exception $e){

            DB::rollBack();

        }
    }
    public function storeStock(Request $request){

        if(!$request->ajax()) return redirect('/');
        $mytime = Carbon::now('America/Mexico_City');

        try{
            DB::beginTransaction();

            $ingreso = new IngresoAbrasivo();
            $ingreso->idproveedor = $request->idproveedor;
            $ingreso->idusuario = \Auth::user()->id;
            $ingreso->tipo_comprobante = $request->tipo_comprobante;
            $ingreso->num_comprobante = $request->num_comprobante;
            $ingreso->fecha_hora = $mytime;
            $ingreso->impuesto = $request->impuesto;
            $ingreso->total = $request->total;
            $ingreso->estado = 'Registrado';
            $ingreso->active = 1;
            $ingreso->save();

            $detalles = $request->data;//Array de detalles

            //Recorro todos los elementos
            foreach($detalles as $ep=>$det){
                $articulos = Abrasivo::where('codigo','=',$det['codigo'])->select('id')->first();
                $detalle = new DetalleIngresoAbrasivo();
                $detalle->idingresoabrasivo = $ingreso->id;
                $detalle->idabrasivo= $articulos->id;
                $detalle->cantidad = $det['stock'];
                $detalle->precio_compra = $det['precio_venta'];
                $detalle->save();

            }

            DB::commit();

        }catch(Exception $e){

            DB::rollBack();

        }
    }
    public function desactivar(Request $request){
        if (!$request->ajax()) return redirect('/');
        $ingreso = IngresoAbrasivo::findOrFail($request->id);
        $ingreso->estado = 'Anulado';
        $ingreso->save();
    }
    public function obtenerCabecera(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $ingreso = IngresoAbrasivo::join('personas','ingreso_abrasivos.idproveedor','=','personas.id')
        ->join('users','ingreso_abrasivos.idusuario','=','users.id')
        ->select('ingreso_abrasivos.id','ingreso_abrasivos.tipo_comprobante','ingreso_abrasivos.num_comprobante',
        'ingreso_abrasivos.fecha_hora','ingreso_abrasivos.impuesto','ingreso_abrasivos.total','ingreso_abrasivos.estado',
        'personas.nombre','users.usuario', 'ingreso_abrasivos.active')
        ->where('ingreso_abrasivos.id','=',$id)
        ->orderBy('ingreso_abrasivos.id', 'desc')->take(1)->get();

        return ['ingreso' => $ingreso];
    }
    public function obtenerDetalles(Request $request){

        if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $detalles = DetalleIngresoAbrasivo::join('abrasivos','detalle_ingreso_abrasivos.idabrasivo','=','abrasivos.id')
        ->select('detalle_ingreso_abrasivos.cantidad','detalle_ingreso_abrasivos.precio_compra',
            'abrasivos.sku','abrasivos.codigo','abrasivos.descripcion','abrasivos.ubicacion',
            'abrasivos.file','abrasivos.condicion')
        ->where('detalle_ingreso_abrasivos.idingresoabrasivo',$id)->get();
        return ['detalles' => $detalles];
    }
    public function getLastNum(Request $request){
        if (!$request->ajax()) return redirect('/');

        $lastNum = IngresoAbrasivo::select('num_comprobante')->get()->last();

        if($lastNum != null){
            $noComp = explode('"',$lastNum);
            $SigNum = explode("-",$noComp[3]);
            return  $SigNum[1] + 1;
        }else{
            return 1;
        }
    }
    public function pdf(Request $request,$id){

        $ingreso = IngresoAbrasivo::join('users','ingreso_abrasivos.idusuario','=','users.id')
        ->leftjoin('personas','users.id','=','personas.id')
        ->select('ingreso_abrasivos.id','ingreso_abrasivos.tipo_comprobante','ingreso_abrasivos.num_comprobante',
        'ingreso_abrasivos.fecha_hora','ingreso_abrasivos.impuesto','ingreso_abrasivos.total','ingreso_abrasivos.estado',
        'personas.nombre','personas.nombre as usuario')
        ->where('ingreso_abrasivos.id',$id)->take(1)->get();

        $detalles = DetalleIngresoAbrasivo::join('abrasivos','detalle_ingreso_abrasivos.idabrasivo','=','abrasivos.id')
        ->select('detalle_ingreso_abrasivos.cantidad','detalle_ingreso_abrasivos.precio_compra','abrasivos.sku','abrasivos.codigo',
            'abrasivos.descripcion','abrasivos.ubicacion','abrasivos.file','abrasivos.condicion')
        ->where('detalle_ingreso_abrasivos.idingresoabrasivo',$id)
        ->orderBy('detalle_ingreso_abrasivos.id','desc')->get();

        $numIngreso = IngresoAbrasivo::select('num_comprobante')->where('id',$id)->get();
        $pdf = \PDF::loadView('pdf.ingresoAbrasivo',['ingreso' => $ingreso,'detalles'=>$detalles]);

        return $pdf->stream('Ingreso-'.$numIngreso[0]->num_comprobante.'.pdf');

    }
    public function cambiarEstadoIngreso(Request $request) {
        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $ingreso = IngresoAbrasivo::findOrFail($request->id);
            $ingreso->active = 1;
            $ingreso->save();

            DB::commit();

        } catch(Exception $e){
            DB::rollBack();
        }
    }
}
