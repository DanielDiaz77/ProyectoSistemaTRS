<?php

namespace App\Http\Controllers;

use App\DetalleIngresoJava;
use App\IngresoJava;
use Illuminate\Http\Request;
use App\Java;
use App\Notifications\NotifyAdmin;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\New_;

class IngresoJavaController extends Controller
{
    public function index(Request $request){
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $estado = $request->estado;

        if($estado == ''){
            if ($buscar==''){
                $ingresos = IngresoJava::join('personas','ingreso_javas.idproveedor','=','personas.id')
                ->join('users','ingreso_javas.idusuario','=','users.id')
                ->select('ingreso_javas.id','ingreso_javas.tipo_comprobante','ingreso_javas.num_comprobante',
                'ingreso_javas.fecha_hora','ingreso_javas.impuesto','ingreso_javas.total','ingreso_javas.estado',
                'personas.nombre','users.usuario', 'ingreso_javas.active' )
                ->orderBy('ingreso_javas.id', 'desc')->paginate(12);
            }
            else{
                $ingresos = IngresoJava::join('personas','ingreso_javas.idproveedor','=','personas.id')
                ->join('users','ingreso_javas.idusuario','=','users.id')
                ->select('ingreso_javas.id','ingreso_javas.tipo_comprobante','ingreso_javas.num_comprobante',
                'ingreso_javas.fecha_hora','ingreso_javas.impuesto','ingreso_javas.total','ingreso_javas.estado',
                'personas.nombre','users.usuario', 'ingreso_javas.active')
                ->where('ingreso_javas.'.$criterio, 'like', '%'. $buscar . '%')
                ->orderBy('ingreso_javas.id', 'desc')->paginate(12);
            }
        }else{
            if ($buscar==''){
                $ingresos = IngresoJava::join('personas','ingreso_javas.idproveedor','=','personas.id')
                ->join('users','ingreso_javas.idusuario','=','users.id')
                ->select('ingreso_javas.id','ingreso_javas.tipo_comprobante','ingreso_javas.num_comprobante',
                'ingreso_javas.fecha_hora','ingreso_javas.impuesto','ingreso_javas.total','ingreso_javas.estado',
                'personas.nombre','users.usuario', 'ingreso_javas.active')
                ->where('ingreso_javas.estado',$estado)
                ->orderBy('ingreso_javas.id', 'desc')->paginate(12);
            }
            else{
                $ingresos = IngresoJava::join('personas','ingreso_javas.idproveedor','=','personas.id')
                ->join('users','ingreso_javas.idusuario','=','users.id')
                ->select('ingreso_javas.id','ingreso_javas.tipo_comprobante','ingreso_javas.num_comprobante',
                'ingreso_javas.fecha_hora','ingreso_javas.impuesto','ingreso_javas.total','ingreso_javas.estado',
                'personas.nombre','users.usuario', 'ingreso_javas.active')
                ->where([['ingreso_javas.'.$criterio, 'like', '%'. $buscar . '%'],['ingreso_javas.estado',$estado]])
                ->orderBy('ingreso_javas.id', 'desc')->paginate(12);
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
    public function store(Request $request){
        if(!$request->ajax()) return redirect('/');

        $mytime = Carbon::now('America/Mexico_City');

        try{
            DB::beginTransaction();

            $ingreso = new IngresoJava();
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
                $articulos = Java::where('codigo','=',$det['codigo'])->select('id')->first();
                $detalle = new DetalleIngresoJava();
                $detalle->idingresojava = $ingreso->id;
                $detalle->idjava = $articulos->id;
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
        $ingreso = IngresoJava::findOrFail($request->id);
        $ingreso->estado = 'Anulado';
        $ingreso->save();
    }
    public function obtenerCabecera(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $ingreso = IngresoJava::join('personas','ingreso_javas.idproveedor','=','personas.id')
        ->join('users','ingreso_javas.idusuario','=','users.id')
        ->select('ingreso_javas.id','ingreso_javas.tipo_comprobante','ingreso_javas.num_comprobante',
        'ingreso_javas.fecha_hora','ingreso_javas.impuesto','ingreso_javas.total','ingreso_javas.estado',
        'personas.nombre','users.usuario', 'ingreso_javas.active')
        ->where('ingreso_javas.id','=',$id)
        ->orderBy('ingreso_javas.id', 'desc')->take(1)->get();

        return ['ingreso' => $ingreso];
    }
    public function obtenerDetalles(Request $request){

        if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $detalles = DetalleIngresoJava::join('javas','detalle_ingreso_javas.idjava','=','javas.id')
        ->leftJoin('categorias','categorias.id','javas.idcategoria')
        ->select('detalle_ingreso_javas.cantidad','detalle_ingreso_javas.precio_compra','javas.sku','javas.codigo',
            'javas.espesor','javas.largo','javas.alto','javas.metros_cuadrados','javas.descripcion',
            'javas.idcategoria','javas.terminado','javas.ubicacion','javas.file','javas.origen',
            'javas.contenedor','javas.fecha_llegada','javas.observacion','javas.condicion',
            'categorias.nombre as material')
        ->where('detalle_ingreso_javas.idingresojava',$id)->get();
        return ['detalles' => $detalles];
    }
    public function getLastNum(Request $request){
        //if (!$request->ajax()) return redirect('/');

        $lastNum = IngresoJava::select('num_comprobante')->get()->last();

        if($lastNum != null){
            $noComp = explode('"',$lastNum);
            $SigNum = explode("-",$noComp[3]);
            return  $SigNum[1] + 1;
        }else{
            return 1;
        }
    }
    public function pdf(Request $request,$id){

        $ingreso = IngresoJava::join('users','ingreso_javas.idusuario','=','users.id')
        ->leftjoin('personas','users.id','=','personas.id')
        ->select('ingreso_javas.id','ingreso_javas.tipo_comprobante','ingreso_javas.num_comprobante',
        'ingreso_javas.fecha_hora','ingreso_javas.impuesto','ingreso_javas.total','ingreso_javas.estado',
        'personas.nombre','personas.nombre as usuario')
        ->where('ingreso_javas.id',$id)->take(1)->get();

        $detalles = DetalleIngresoJava::join('javas','detalle_ingreso_javas.idjava','=','javas.id')
        ->leftJoin('categorias','javas.idcategoria','=','categorias.id')
        ->select('detalle_ingreso_javas.cantidad','detalle_ingreso_javas.precio_compra','javas.sku','javas.codigo',
            'javas.espesor','javas.largo','javas.alto','javas.metros_cuadrados','javas.descripcion',
            'javas.idcategoria','javas.terminado','javas.ubicacion','javas.file','javas.origen',
            'javas.contenedor','javas.fecha_llegada','javas.observacion','javas.condicion','javas.stock',
            'categorias.nombre as categoria')
        ->where('detalle_ingreso_javas.idingresojava',$id)
        ->orderBy('detalle_ingreso_javas.id','desc')->get();

        $numIngreso = IngresoJava::select('num_comprobante')->where('id',$id)->get();

        $sumaMts = DB::table('javas')
        ->select(DB::raw('SUM(metros_cuadrados) as metros'))
        ->leftJoin('detalle_ingreso_javas','detalle_ingreso_javas.idjava','javas.id')
        ->where('detalle_ingreso_javas.idingresojava',$id)
        ->get();

        $pdf = \PDF::loadView('pdf.ingresojava',['ingreso' => $ingreso,'detalles'=>$detalles,'sumaMts' => $sumaMts[0]->metros]);

        return $pdf->stream('Ingreso-'.$numIngreso[0]->num_comprobante.'.pdf');

    }
    public function pdfContenedor(Request $request,$id){

        $ingreso = IngresoJava::join('users','ingreso_javas.idusuario','=','users.id')
        ->leftjoin('personas','users.id','=','personas.id')
        ->select('ingreso_javas.id','ingreso_javas.tipo_comprobante','ingreso_javas.num_comprobante',
        'ingreso_javas.fecha_hora','ingreso_javas.impuesto','ingreso_javas.total','ingreso_javas.estado',
        'personas.nombre','personas.nombre as usuario')
        ->where('ingreso_javas.id',$id)->take(1)->get();

        $detalles = DetalleIngresoJava::join('javas','detalle_ingreso_javas.idjava','=','javas.id')
        ->leftJoin('categorias','javas.idcategoria','=','categorias.id')
        ->select('detalle_ingreso_javas.cantidad','detalle_ingreso_javas.precio_compra','javas.sku','javas.codigo',
            'javas.espesor','javas.largo','javas.alto','javas.metros_cuadrados','javas.descripcion',
            'javas.idcategoria','javas.terminado','javas.ubicacion','javas.file','javas.origen',
            'javas.contenedor','javas.fecha_llegada','javas.observacion','javas.condicion',
            'categorias.nombre as categoria')
        ->where('detalle_ingreso_javas.idingresojava',$id)
        ->orderBy('detalle_ingreso_javas.id','desc')->get();

        $numIngreso = IngresoJava::select('contenedor')->where('id',$id)->get();

        $sumaMts = DB::table('javas')
        ->select(DB::raw('SUM(metros_cuadrados) as metros'))
        ->leftJoin('detalle_ingreso_javas','detalle_ingreso_javas.idjava','javas.id')
        ->where('detalle_ingreso_javas.idingreso',$id)
        ->get();

        $pdf = \PDF::loadView('pdf.ingreso',['ingreso' => $ingreso,'detalles'=>$detalles,'sumaMts' => $sumaMts[0]->metros]);

        return $pdf->stream('Ingreso-'.$numIngreso[0]->contenedor.'.pdf');

    }
    public function cambiarEstadoIngreso(Request $request) {
        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $ingreso = IngresoJava::findOrFail($request->id);
            $ingreso->active = 1;
            $ingreso->save();

            DB::commit();

        } catch(Exception $e){
            DB::rollBack();
        }
    }
}
