<?php

namespace App\Http\Controllers;

use App\Bloque;
use App\DetalleIngresoBloque;
use App\IngresoBloque;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class IngresoBloqueController extends Controller
{
    public function index(Request $request){
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $estado = $request->estado;

        if($estado == ''){
            if ($buscar==''){
                $ingresos = IngresoBloque::join('personas','ingreso_bloques.idproveedor','=','personas.id')
                ->join('users','ingreso_bloques.idusuario','=','users.id')
                ->select('ingreso_bloques.id','ingreso_bloques.tipo_comprobante','ingreso_bloques.num_comprobante',
                'ingreso_bloques.fecha_hora','ingreso_bloques.impuesto','ingreso_bloques.total','ingreso_bloques.estado',
                'personas.nombre','users.usuario', 'ingreso_bloques.active' )
                ->orderBy('ingreso_bloques.id', 'desc')->paginate(12);
            }
            else{
                $ingresos = IngresoBloque::join('personas','ingreso_bloques.idproveedor','=','personas.id')
                ->join('users','ingreso_bloques.idusuario','=','users.id')
                ->select('ingreso_bloques.id','ingreso_bloques.tipo_comprobante','ingreso_bloques.num_comprobante',
                'ingreso_bloques.fecha_hora','ingreso_bloques.impuesto','ingreso_bloques.total','ingreso_bloques.estado',
                'personas.nombre','users.usuario', 'ingreso_bloques.active')
                ->where('ingreso_bloques.'.$criterio, 'like', '%'. $buscar . '%')
                ->orderBy('ingreso_bloques.id', 'desc')->paginate(12);
            }
        }else{
            if ($buscar==''){
                $ingresos = IngresoBloque::join('personas','ingreso_bloques.idproveedor','=','personas.id')
                ->join('users','ingreso_bloques.idusuario','=','users.id')
                ->select('ingreso_bloques.id','ingreso_bloques.tipo_comprobante','ingreso_bloques.num_comprobante',
                'ingreso_bloques.fecha_hora','ingreso_bloques.impuesto','ingreso_bloques.total','ingreso_bloques.estado',
                'personas.nombre','users.usuario', 'ingreso_bloques.active')
                ->where('ingreso_bloques.estado',$estado)
                ->orderBy('ingreso_bloques.id', 'desc')->paginate(12);
            }
            else{
                $ingresos = IngresoBloque::join('personas','ingreso_bloques.idproveedor','=','personas.id')
                ->join('users','ingreso_bloques.idusuario','=','users.id')
                ->select('ingreso_bloques.id','ingreso_bloques.tipo_comprobante','ingreso_bloques.num_comprobante',
                'ingreso_bloques.fecha_hora','ingreso_bloques.impuesto','ingreso_bloques.total','ingreso_bloques.estado',
                'personas.nombre','users.usuario', 'ingreso_bloques.active')
                ->where([['ingreso_bloques.'.$criterio, 'like', '%'. $buscar . '%'],['ingreso_bloques.estado',$estado]])
                ->orderBy('ingreso_bloques.id', 'desc')->paginate(12);
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

            $ingreso = new IngresoBloque();
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
                $articulos = Bloque::where('codigo','=',$det['codigo'])->select('id')->first();
                $detalle = new DetalleIngresoBloque();
                $detalle->idingresobloque = $ingreso->id;
                $detalle->idbloque = $articulos->id;
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
        $ingreso = IngresoBloque::findOrFail($request->id);
        $ingreso->estado = 'Anulado';
        $ingreso->save();
    }
    public function obtenerCabecera(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $ingreso = IngresoBloque::join('personas','ingreso_bloques.idproveedor','=','personas.id')
        ->join('users','ingreso_bloques.idusuario','=','users.id')
        ->select('ingreso_bloques.id','ingreso_bloques.tipo_comprobante','ingreso_bloques.num_comprobante',
        'ingreso_bloques.fecha_hora','ingreso_bloques.impuesto','ingreso_bloques.total','ingreso_bloques.estado',
        'personas.nombre','users.usuario', 'ingreso_bloques.active')
        ->where('ingreso_bloques.id','=',$id)
        ->orderBy('ingreso_bloques.id', 'desc')->take(1)->get();

        return ['ingreso' => $ingreso];
    }
    public function obtenerDetalles(Request $request){

        if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $detalles = DetalleIngresoBloque::join('bloques','detalle_ingreso_bloques.idbloque','=','bloques.id')
        ->leftJoin('categorias','categorias.id','bloques.idcategoria')
        ->select('detalle_ingreso_bloques.cantidad','detalle_ingreso_bloques.precio_compra','bloques.sku','bloques.codigo',
            'bloques.ancho','bloques.largo','bloques.alto','bloques.metros_cubicos','bloques.descripcion',
            'bloques.idcategoria','bloques.terminado','bloques.ubicacion','bloques.file','bloques.origen',
            'bloques.contenedor','bloques.fecha_llegada','bloques.observacion','bloques.condicion',
            'categorias.nombre as material')
        ->where('detalle_ingreso_bloques.idingresobloque',$id)->get();
        return ['detalles' => $detalles];
    }
    public function getLastNum(Request $request){
        //if (!$request->ajax()) return redirect('/');

        $lastNum = IngresoBloque::select('num_comprobante')->get()->last();

        if($lastNum != null){
            $noComp = explode('"',$lastNum);
            $SigNum = explode("-",$noComp[3]);
            return  $SigNum[1] + 1;
        }else{
            return 1;
        }
    }
    public function pdf(Request $request,$id){

        $ingreso = IngresoBloque::join('users','ingreso_bloques.idusuario','=','users.id')
        ->leftjoin('personas','users.id','=','personas.id')
        ->select('ingreso_bloques.id','ingreso_bloques.tipo_comprobante','ingreso_bloques.num_comprobante',
        'ingreso_bloques.fecha_hora','ingreso_bloques.impuesto','ingreso_bloques.total','ingreso_bloques.estado',
        'personas.nombre','personas.nombre as usuario')
        ->where('ingreso_bloques.id',$id)->take(1)->get();

        $detalles = DetalleIngresoBloque::join('bloques','detalle_ingreso_bloques.idarticulo','=','bloques.id')
        ->leftJoin('categorias','bloques.idcategoria','=','categorias.id')
        ->select('detalle_ingreso_bloques.cantidad','detalle_ingreso_bloques.precio_compra','bloques.sku','bloques.codigo',
            'bloques.espesor','bloques.largo','bloques.alto','bloques.metros_cuadrados','bloques.descripcion',
            'bloques.idcategoria','bloques.terminado','bloques.ubicacion','bloques.file','bloques.origen',
            'bloques.contenedor','bloques.fecha_llegada','bloques.observacion','bloques.condicion',
            'categorias.nombre as categoria')
        ->where('detalle_ingreso_bloques.idingreso',$id)
        ->orderBy('detalle_ingreso_bloques.id','desc')->get();

        $numIngreso = IngresoBloque::select('num_comprobante')->where('id',$id)->get();

        $sumaMts = DB::table('bloques')
        ->select(DB::raw('SUM(metros_cuadrados) as metros'))
        ->leftJoin('detalle_ingreso_bloques','detalle_ingreso_bloques.idarticulo','bloques.id')
        ->where('detalle_ingreso_bloques.idingreso',$id)
        ->get();

        $pdf = \PDF::loadView('pdf.ingreso',['ingreso' => $ingreso,'detalles'=>$detalles,'sumaMts' => $sumaMts[0]->metros]);

        return $pdf->stream('Ingreso-'.$numIngreso[0]->num_comprobante.'.pdf');

    }
    public function pdfContenedor(Request $request,$id){

        $ingreso = IngresoBloque::join('users','ingreso_bloques.idusuario','=','users.id')
        ->leftjoin('personas','users.id','=','personas.id')
        ->select('ingreso_bloques.id','ingreso_bloques.tipo_comprobante','ingreso_bloques.num_comprobante',
        'ingreso_bloques.fecha_hora','ingreso_bloques.impuesto','ingreso_bloques.total','ingreso_bloques.estado',
        'personas.nombre','personas.nombre as usuario')
        ->where('ingreso_bloques.id',$id)->take(1)->get();

        $detalles = DetalleIngresoBloque::join('bloques','detalle_ingresos.idarticulo','=','bloques.id')
        ->leftJoin('categorias','bloques.idcategoria','=','categorias.id')
        ->select('detalle_ingresos.cantidad','detalle_ingresos.precio_compra','bloques.sku','bloques.codigo',
            'bloques.espesor','bloques.largo','bloques.alto','bloques.metros_cuadrados','bloques.descripcion',
            'bloques.idcategoria','bloques.terminado','bloques.ubicacion','bloques.file','bloques.origen',
            'bloques.contenedor','bloques.fecha_llegada','bloques.observacion','bloques.condicion',
            'categorias.nombre as categoria')
        ->where('detalle_ingresos.idingreso',$id)
        ->orderBy('detalle_ingresos.id','desc')->get();

        $numIngreso = IngresoBloque::select('contenedor')->where('id',$id)->get();

        $sumaMts = DB::table('bloques')
        ->select(DB::raw('SUM(metros_cuadrados) as metros'))
        ->leftJoin('detalle_ingresos','detalle_ingresos.idarticulo','bloques.id')
        ->where('detalle_ingresos.idingreso',$id)
        ->get();

        $pdf = \PDF::loadView('pdf.ingreso',['ingreso' => $ingreso,'detalles'=>$detalles,'sumaMts' => $sumaMts[0]->metros]);

        return $pdf->stream('Ingreso-'.$numIngreso[0]->contenedor.'.pdf');

    }
    public function cambiarEstadoIngreso(Request $request) {
        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $ingreso = IngresoBloque::findOrFail($request->id);
            $ingreso->active = 1;
            $ingreso->save();

            DB::commit();

        } catch(Exception $e){
            DB::rollBack();
        }
    }
}
