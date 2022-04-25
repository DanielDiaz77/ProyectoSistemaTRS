<?php

namespace App\Http\Controllers;

use App\DetalleIngreso;
use App\Ingreso;
use App\Articulo;
use App\Notifications\NotifyAdmin;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\New_;

class IngresoController extends Controller
{
    public function index(Request $request){
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $estado = $request->estado;

        if($estado == ''){
            if ($buscar==''){
                $ingresos = Ingreso::join('personas','ingresos.idproveedor','=','personas.id')
                ->join('users','ingresos.idusuario','=','users.id')
                ->select('ingresos.id','ingresos.tipo_comprobante','ingresos.num_comprobante',
                'ingresos.fecha_hora','ingresos.impuesto','ingresos.total','ingresos.estado',
                'personas.nombre','users.usuario', 'ingresos.active' )
                ->orderBy('ingresos.id', 'desc')->paginate(12);
            }
            else{
                $ingresos = Ingreso::join('personas','ingresos.idproveedor','=','personas.id')
                ->join('users','ingresos.idusuario','=','users.id')
                ->select('ingresos.id','ingresos.tipo_comprobante','ingresos.num_comprobante',
                'ingresos.fecha_hora','ingresos.impuesto','ingresos.total','ingresos.estado',
                'personas.nombre','users.usuario', 'ingresos.active')
                ->where('ingresos.'.$criterio, 'like', '%'. $buscar . '%')
                ->orderBy('ingresos.id', 'desc')->paginate(12);
            }
        }else{
            if ($buscar==''){
                $ingresos = Ingreso::join('personas','ingresos.idproveedor','=','personas.id')
                ->join('users','ingresos.idusuario','=','users.id')
                ->select('ingresos.id','ingresos.tipo_comprobante','ingresos.num_comprobante',
                'ingresos.fecha_hora','ingresos.impuesto','ingresos.total','ingresos.estado',
                'personas.nombre','users.usuario', 'ingresos.active')
                ->where('ingresos.estado',$estado)
                ->orderBy('ingresos.id', 'desc')->paginate(12);
            }
            else{
                $ingresos = Ingreso::join('personas','ingresos.idproveedor','=','personas.id')
                ->join('users','ingresos.idusuario','=','users.id')
                ->select('ingresos.id','ingresos.tipo_comprobante','ingresos.num_comprobante',
                'ingresos.fecha_hora','ingresos.impuesto','ingresos.total','ingresos.estado',
                'personas.nombre','users.usuario', 'ingresos.active')
                ->where([['ingresos.'.$criterio, 'like', '%'. $buscar . '%'],['ingresos.estado',$estado]])
                ->orderBy('ingresos.id', 'desc')->paginate(12);
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

            $ingreso = new Ingreso();
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
                $articulos = Articulo::where('codigo','=',$det['codigo'])->select('id')->first();
                $detalle = new DetalleIngreso();
                $detalle->idingreso = $ingreso->id;
                $detalle->idarticulo = $articulos->id;
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
        $ingreso = Ingreso::findOrFail($request->id);
        $ingreso->estado = 'Anulado';
        $ingreso->save();
    }
    public function obtenerCabecera(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $ingreso = Ingreso::join('personas','ingresos.idproveedor','=','personas.id')
        ->join('users','ingresos.idusuario','=','users.id')
        ->select('ingresos.id','ingresos.tipo_comprobante','ingresos.num_comprobante',
        'ingresos.fecha_hora','ingresos.impuesto','ingresos.total','ingresos.estado',
        'personas.nombre','users.usuario', 'ingresos.active')
        ->where('ingresos.id','=',$id)
        ->orderBy('ingresos.id', 'desc')->take(1)->get();

        return ['ingreso' => $ingreso];
    }
    public function obtenerDetalles(Request $request){

        if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $detalles = DetalleIngreso::join('articulos','detalle_ingresos.idarticulo','=','articulos.id')
        ->leftJoin('categorias','categorias.id','articulos.idcategoria')
        ->select('detalle_ingresos.cantidad','detalle_ingresos.precio_compra','articulos.sku','articulos.codigo',
            'articulos.espesor','articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.descripcion',
            'articulos.idcategoria','articulos.terminado','articulos.ubicacion','articulos.file','articulos.origen',
            'articulos.contenedor','articulos.fecha_llegada','articulos.observacion','articulos.condicion',
            'categorias.nombre as material')
        ->where('detalle_ingresos.idingreso',$id)->get();
        return ['detalles' => $detalles];
    }
    public function getLastNum(Request $request){
        //if (!$request->ajax()) return redirect('/');

        $lastNum = Ingreso::select('num_comprobante')->get()->last();

        if($lastNum != null){
            $noComp = explode('"',$lastNum);
            $SigNum = explode("-",$noComp[3]);
            return  $SigNum[1] + 1;
        }else{
            return 1;
        }
    }
    public function pdf(Request $request,$id){

        $ingreso = Ingreso::join('users','ingresos.idusuario','=','users.id')
        ->leftjoin('personas','users.id','=','personas.id')
        ->select('ingresos.id','ingresos.tipo_comprobante','ingresos.num_comprobante',
        'ingresos.fecha_hora','ingresos.impuesto','ingresos.total','ingresos.estado',
        'personas.nombre','personas.nombre as usuario')
        ->where('ingresos.id',$id)->take(1)->get();

        $detalles = DetalleIngreso::join('articulos','detalle_ingresos.idarticulo','=','articulos.id')
        ->leftJoin('categorias','articulos.idcategoria','=','categorias.id')
        ->select('detalle_ingresos.cantidad','detalle_ingresos.precio_compra','articulos.sku','articulos.codigo',
            'articulos.espesor','articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.descripcion',
            'articulos.idcategoria','articulos.terminado','articulos.ubicacion','articulos.file','articulos.origen',
            'articulos.contenedor','articulos.fecha_llegada','articulos.observacion','articulos.condicion',
            'categorias.nombre as categoria')
        ->where('detalle_ingresos.idingreso',$id)
        ->orderBy('detalle_ingresos.id','desc')->get();

        $numIngreso = Ingreso::select('num_comprobante')->where('id',$id)->get();

        $sumaMts = DB::table('articulos')
        ->select(DB::raw('SUM(metros_cuadrados) as metros'))
        ->leftJoin('detalle_ingresos','detalle_ingresos.idarticulo','articulos.id')
        ->where('detalle_ingresos.idingreso',$id)
        ->get();

        $pdf = \PDF::loadView('pdf.ingreso',['ingreso' => $ingreso,'detalles'=>$detalles,'sumaMts' => $sumaMts[0]->metros]);

        return $pdf->stream('Ingreso-'.$numIngreso[0]->num_comprobante.'.pdf');

    }
    public function pdfContenedor(Request $request,$id){

        $ingreso = Ingreso::join('users','ingresos.idusuario','=','users.id')
        ->leftjoin('personas','users.id','=','personas.id')
        ->select('ingresos.id','ingresos.tipo_comprobante','ingresos.num_comprobante',
        'ingresos.fecha_hora','ingresos.impuesto','ingresos.total','ingresos.estado',
        'personas.nombre','personas.nombre as usuario')
        ->where('ingresos.id',$id)->take(1)->get();

        $detalles = DetalleIngreso::join('articulos','detalle_ingresos.idarticulo','=','articulos.id')
        ->leftJoin('categorias','articulos.idcategoria','=','categorias.id')
        ->select('detalle_ingresos.cantidad','detalle_ingresos.precio_compra','articulos.sku','articulos.codigo',
            'articulos.espesor','articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.descripcion',
            'articulos.idcategoria','articulos.terminado','articulos.ubicacion','articulos.file','articulos.origen',
            'articulos.contenedor','articulos.fecha_llegada','articulos.observacion','articulos.condicion',
            'categorias.nombre as categoria')
        ->where('detalle_ingresos.idingreso',$id)
        ->orderBy('detalle_ingresos.id','desc')->get();

        $numIngreso = Ingreso::select('contenedor')->where('id',$id)->get();

        $sumaMts = DB::table('articulos')
        ->select(DB::raw('SUM(metros_cuadrados) as metros'))
        ->leftJoin('detalle_ingresos','detalle_ingresos.idarticulo','articulos.id')
        ->where('detalle_ingresos.idingreso',$id)
        ->get();

        $pdf = \PDF::loadView('pdf.ingreso',['ingreso' => $ingreso,'detalles'=>$detalles,'sumaMts' => $sumaMts[0]->metros]);

        return $pdf->stream('Ingreso-'.$numIngreso[0]->contenedor.'.pdf');

    }
    public function cambiarEstadoIngreso(Request $request) {
        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $ingreso = Ingreso::findOrFail($request->id);
            $ingreso->active = 1;
            $ingreso->save();

            DB::commit();

        } catch(Exception $e){
            DB::rollBack();
        }
    }
}
