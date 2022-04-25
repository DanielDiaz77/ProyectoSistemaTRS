<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use App\Entrada;
use App\DetalleEntrada;
use App\Herramienta;


class EntradaController extends Controller
{

    public function index(Request $request){
        //if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $estado = $request->estado;
        $id =$request->id;

        if($estado == ''){
            if ($buscar==''){
                $entradas = Entrada::join('personas','entradas.idproveedor','=','personas.id')
                ->join('users','entradas.idusuario','=','users.id')
                ->select('entradas.id','entradas.tipo_comprobante','entradas.num_comprobante',
                'entradas.fecha_hora','entradas.impuesto','entradas.total','entradas.estado',
                'personas.nombre','users.usuario')
                ->orderBy('entradas.id', 'desc')->paginate(12);
            }
            else{
                $entradas = Entrada::join('personas','entradas.idproveedor','=','personas.id')
                ->join('users','entradas.idusuario','=','users.id')
                ->select('entradas.id','entradas.tipo_comprobante','entradas.num_comprobante',
                'entradas.fecha_hora','entradas.impuesto','entradas.total','entradas.estado',
                'personas.nombre','users.usuario')
                ->where('entradas.'.$criterio, 'like', '%'. $buscar . '%')
                ->orderBy('entradas.id', 'desc')->paginate(12);
            }
        }else{
            if ($buscar==''){
                $entradas = Entrada::join('personas','entradas.idproveedor','=','personas.id')
                ->join('users','entradas.idusuario','=','users.id')
                ->select('entradas.id','entradas.tipo_comprobante','entradas.num_comprobante',
                'entradas.fecha_hora','entradas.impuesto','entradas.total','entradas.estado',
                'personas.nombre','users.usuario')
                ->where('entradas.estado',$estado)
                ->orderBy('entradas.id', 'desc')->paginate(12);
            }
            else{
                $entradas = Entrada::join('personas','entradas.idproveedor','=','personas.id')
                ->join('users','entradas.idusuario','=','users.id')
                ->select('entradas.id','entradas.tipo_comprobante','entradas.num_comprobante',
                'entradas.fecha_hora','entradas.impuesto','entradas.total','entradas.estado',
                'personas.nombre','users.usuario')
                ->where([['entradas.'.$criterio, 'like', '%'. $buscar . '%'],['entradas.estado',$estado]])
                ->orderBy('entradas.id', 'desc')->paginate(12);

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

            $entrada = new Entrada();
            $entrada->idproveedor = $request->idproveedor;
            $entrada->idusuario = \Auth::user()->id;
            $entrada->tipo_comprobante = $request->tipo_comprobante;
            $entrada->num_comprobante = $request->num_comprobante;
            $entrada->fecha_hora = $mytime;
            $entrada->impuesto = $request->impuesto;
            $entrada->total = $request->total;
            $entrada->estado = 'Registrado';

            $entrada->save();

            $detalles = $request->data;//Array de detalles

            //Recorro todos los elementos
            foreach($detalles as $ep=>$det)
            {
                $articulo = Herramienta::where('codigo','=',$det['codigo'])->select('id')->first();

                $detalle = new DetalleEntrada();
                $detalle->identrada = $entrada->id;
                $detalle->idarticulo = $articulo->id;
                $detalle->cantidad = $det['cantidad'];
                $detalle->precio_compra = $det['precio'];
                $detalle->save();

            }

            DB::commit();

        }catch(Exception $e){

            DB::rollBack();

        }
    }

    public function desactivar(Request $request){
        if (!$request->ajax()) return redirect('/');
        $entrada = Entrada::findOrFail($request->id);
        $entrada->estado = 'Anulado';
        $entrada->save();
    }

    public function obtenerCabecera(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $entrada = Entrada::join('personas','entradas.idproveedor','=','personas.id')
        ->join('users','entradas.idusuario','=','users.id')
        ->select('entradas.id','entradas.tipo_comprobante','entradas.num_comprobante',
        'entradas.fecha_hora','entradas.impuesto','entradas.total','entradas.estado',
        'personas.nombre as proveedor','users.usuario')
        ->where('entradas.id',$id)
        ->orderBy('entradas.id', 'desc')->take(1)->get();

        return ['entrada' => $entrada];
    }

    public function obtenerDetalles(Request $request){

        if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $detalles = DetalleEntrada::join('herramientas','detalle_entradas.idarticulo','=','herramientas.id')
        ->select('detalle_entradas.cantidad','detalle_entradas.precio_compra','herramientas.sku','herramientas.codigo',
            'herramientas.descripcion','herramientas.ubicacion','herramientas.file')
        ->where('detalle_entradas.identrada',$id)->get();
        return ['detalles' => $detalles];
    }

    public function getLastNum(Request $request){
        if (!$request->ajax()) return redirect('/');
        $lastNum = Entrada::select('num_comprobante')->get()->last();
        if($lastNum != null){
            $noComp = explode('"',$lastNum);
            $SigNum = explode("-",$noComp[3]);
            return  $SigNum[2];
        }
        else{
            return 1;
        }

    }

    public function pdf(Request $request,$id){
        /* if (!$request->ajax()) return redirect('/'); */
        $entrada = Entrada::join('personas','entradas.idproveedor','=','personas.id')
        ->join('users','entradas.idusuario','=','users.id')
        ->select('entradas.id','entradas.tipo_comprobante','entradas.num_comprobante',
            'entradas.fecha_hora','entradas.impuesto','entradas.total','entradas.estado',
            'personas.nombre as proveedor','users.usuario')
        ->where('entradas.id',$id)
        ->orderBy('entradas.id', 'desc')->take(1)->get();

        $detalles = DetalleEntrada::join('herramientas','detalle_entradas.idarticulo','=','herramientas.id')
        ->select('detalle_entradas.cantidad','detalle_entradas.precio_compra','herramientas.sku','herramientas.codigo',
            'herramientas.descripcion','herramientas.ubicacion','herramientas.file')
        ->where('detalle_entradas.identrada',$id)->get();

        $numIngreso =Entrada::select('num_comprobante')->where('id',$id)->get();


        $pdf = \PDF::loadView('pdf.entrada',['entrada' => $entrada,'detalles'=>$detalles]);

        return $pdf->stream('Ingreso-'.$numIngreso[0]->num_comprobante.'.pdf');
    }
}
