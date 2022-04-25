<?php

namespace App\Http\Controllers;

use App\Articulo;
use App\DetalleReclamo;
use App\Reclamo;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReclamoController extends Controller
{
    public function index(Request $request){

        //if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $estado = $request->estado;

        $usrol = \Auth::user()->idrol;

        if($estado == ''){
            if ($buscar==''){
                $traslados = Reclamo::join('users','reclamos.idusuario','=','users.id')
                ->select('reclamos.id','reclamos.tipo_comprobante','reclamos.num_comprobante','reclamos.condicion',
                'reclamos.fecha_hora','reclamos.estado','reclamos.file','users.usuario','reclamos.folio')
                ->where('reclamos.estado','Activo')
                ->orderBy('reclamos.id', 'desc')->paginate(12);
            }
            else{
                $traslados = Reclamo::join('users','reclamos.idusuario','=','users.id')
                ->select('reclamos.id','reclamos.tipo_comprobante','reclamos.num_comprobante','reclamos.condicion',
                'reclamos.fecha_hora','reclamos.estado','reclamos.file','users.usuario','reclamos.folio')
                ->where([['reclamos.'.$criterio, 'like', '%'. $buscar . '%'],['reclamos.estado','Activo']])
                ->orderBy('reclamos.id', 'desc')->paginate(12);
            }
        }else{
            if ($buscar==''){
                $traslados = Reclamo::join('users','reclamos.idusuario','=','users.id')
                ->select('reclamos.id','reclamos.tipo_comprobante','reclamos.num_comprobante','reclamos.condicion',
                'reclamos.fecha_hora','reclamos.estado','reclamos.file','users.usuario','reclamos.folio')
                ->where('reclamos.estado',$estado)
                ->orderBy('reclamos.id', 'desc')->paginate(12);
            }
            else{
                $traslados = Reclamo::join('users','reclamos.idusuario','=','users.id')
                ->select('reclamos.id','reclamos.tipo_comprobante','reclamos.num_comprobante','reclamos.condicion',
                'reclamos.fecha_hora','reclamos.estado','reclamos.file','users.usuario','reclamos.folio')
                ->where([['reclamos.'.$criterio, 'like', '%'. $buscar . '%'],['reclamos.estado',$estado]])
                ->orderBy('reclamos.id', 'desc')->paginate(12);
            }

        }


        return [
            'pagination' => [
                'total'        => $traslados->total(),
                'current_page' => $traslados->currentPage(),
                'per_page'     => $traslados->perPage(),
                'last_page'    => $traslados->lastPage(),
                'from'         => $traslados->firstItem(),
                'to'           => $traslados->lastItem(),
            ],
            'reclamos' => $traslados,
            'userrol' => $usrol
        ];
    }
    public function getLastNum(){

        $lastNum = Reclamo::select('num_comprobante')->get()->last();

        if($lastNum != null){
            $noComp = explode('"',$lastNum);
            $SigNum = explode("-",$noComp[3]);
            return  $SigNum[1] + 1;
        }else{
            return 1;
        }
    }
    public function store(Request $request){

        if(!$request->ajax()) return redirect('/');

        $mytime = Carbon::now('America/Mexico_City');

        try{
            DB::beginTransaction();

            $fileName =null;

            if($request->file != ""){

                //The name of the directory that we need to create.
                $directoryName = 'images/traslados';

                if(!is_dir($directoryName)){
                    //Directory does not exist, so lets create it.
                    mkdir($directoryName, 0777);
                }

                $exploded = explode(',', $request->file);
                $decoded = base64_decode($exploded[1]);

                if(str_contains($exploded[0],'jpeg'))
                    $extension = 'jpg';
                else
                    $extension = 'png';

                $fileName = str_random().'.'.$extension;

                $path = public_path($directoryName).'/'.$fileName;

                file_put_contents($path,$decoded);
            }

            $reclamo = new Reclamo();
            $reclamo->idusuario = \Auth::user()->id;
            $reclamo->tipo_comprobante = $request->tipo_comprobante;
            $reclamo->num_comprobante = $request->num_comprobante;
            $reclamo->fecha_hora = $mytime;
            $reclamo->estado = 'Activo';
            $reclamo->condicion = 1;
            $reclamo->observacion = $request->observacion;
            $reclamo->file = $fileName;
            $reclamo->save();

            $detalles = $request->data;//Array de detalles

            //Recorro todos los elementos
            foreach($detalles as $ep=>$det)
            {
                $detalle = new DetalleReclamo();
                $detalle->idreclamo= $reclamo->id;
                $detalle->idarticulo = $det['idarticulo'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->save();

                $articulo = Articulo::findOrFail($det['idarticulo']);
                $articulo->condicion = '1';
                $articulo->save();
            }

            DB::commit();

        }catch(Exception $e){

            DB::rollBack();

        }
    }
    public function desactivar(Request $request){

        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $reclamo = Reclamo::findOrFail($request->id);
            $reclamo->estado = 'Anulado';
            $reclamo->condicion= 4;
            $reclamo->save();

            $detalles = DetalleReclamo::select('idarticulo','ubicacion')
                ->where('idreclamo',$request->id)->get();

            foreach($detalles as $ep=>$det){

                $articulo = Articulo::findOrFail($det['idarticulo']);
                $articulo->ubicacion = $det['ubicacion'];
                $articulo->condicion = '1';
                $articulo->save();
            }

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function obtenerCabecera(Request $request){

        //if (!$request->ajax()) return redirect('/');

        $id = $request->id;

        $reclamo = Reclamo::join('users','reclamos.idusuario','=','users.id')
        ->select('reclamos.id','reclamos.tipo_comprobante','reclamos.num_comprobante',
            'reclamos.fecha_hora','reclamos.estado','reclamos.file','users.usuario',
            'reclamos.observacion as obsreclamo','reclamos.condicion','reclamos.folio')
        ->where('reclamos.id',$id)
        ->orderBy('reclamos.id', 'desc')->take(1)->get();

        return ['reclamo' => $reclamo];
    }
    public function obtenerDetalles(Request $request){

        //if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $detalles = DetalleReclamo::join('articulos','detalle_reclamos.idarticulo','=','articulos.id')
        ->leftJoin('categorias','articulos.idcategoria','=','categorias.id')
        ->select('detalle_reclamos.cantidad','detalle_reclamos.id','articulos.sku as articulo',
            'articulos.codigo','articulos.espesor','articulos.largo','articulos.alto','articulos.metros_cuadrados',
            'articulos.descripcion','articulos.idcategoria','articulos.terminado','articulos.ubicacion',
            'articulos.file','articulos.origen','categorias.nombre as categoria','articulos.id as idarticulo',
            'articulos.contenedor','articulos.fecha_llegada','articulos.observacion','articulos.condicion')
        ->where('detalle_reclamos.idreclamo',$id)
        ->orderBy('detalle_reclamos.id','desc')->get();

        return ['detalles' => $detalles];
    }
    public function pdf(Request $request,$id){

        $traslado = Reclamo::join('users','reclamos.idusuario','=','users.id')
        ->leftjoin('personas','users.id','=','personas.id')
        ->select('reclamos.id','reclamos.tipo_comprobante','reclamos.num_comprobante',
        'reclamos.fecha_hora','reclamos.nueva_ubicacion','reclamos.entregado','reclamos.estado',
        'reclamos.file','reclamos.observacion as comentario','personas.nombre as usuario','reclamos.folio')
        ->where('reclamos.id',$id)->take(1)->get();

        $detalles = DetalleReclamo::join('articulos','detalle_reclamos.idarticulo','=','articulos.id')
            ->leftJoin('categorias','articulos.idcategoria','=','categorias.id')
            ->select('detalle_reclamos.cantidad','detalle_reclamos.ubicacion','articulos.sku as articulo',
            'articulos.largo','articulos.alto','articulos.metros_cuadrados', 'categorias.nombre as categoria',
            'articulos.codigo','articulos.ubicacion as artubicacion','articulos.terminado','reclamos.folio')
            ->where('detalle_reclamos.idreclamo',$id)
            ->orderBy('detalle_reclamos.id','desc')->get();

        $numtraslado = Reclamo::select('num_comprobante')->where('id',$id)->get();

        $sumaMts = DB::table('articulos')
        ->select(DB::raw('SUM(metros_cuadrados) as metros'))
        ->leftJoin('detalle_reclamos','detalle_reclamos.idarticulo','articulos.id')
        ->where('detalle_reclamos.idreclamo',$id)
        ->get();

        $pdf = \PDF::loadView('pdf.traslado',['traslado' => $traslado,'detalles'=>$detalles,'sumaMts' => $sumaMts[0]->metros]);

        return $pdf->stream('traslado-'.$numtraslado[0]->num_comprobante.'.pdf');

    }
    public function actualizarObservacion(Request $request){

        if (!$request->ajax()) return redirect('/');
        $reclamo = Reclamo::findOrFail($request->id);
        $reclamo->observacion = $request->observacion;
        $reclamo->save();
    }
    public function updImage(Request $request){

        if(!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $fileName =null;

            if($request->file != ""){

                //The name of the directory that we need to create.
                $directoryName = 'images/traslados';

                if(!is_dir($directoryName)){
                    //Directory does not exist, so lets create it.
                    mkdir($directoryName, 0777);
                }

                $trasl = Reclamo::findOrFail($request->id);
                $img = $trasl->file;

                if($img != null){
                    $image_path = public_path($directoryName).'/'.$img;

                    if(file_exists($image_path)){
                        unlink($image_path);
                    }
                }

                $exploded = explode(',', $request->file);
                $decoded = base64_decode($exploded[1]);

                if(str_contains($exploded[0],'jpeg'))
                    $extension = 'jpg';
                else
                    $extension = 'png';

                $fileName = str_random().'.'.$extension;

                $path = public_path($directoryName).'/'.$fileName;

                file_put_contents($path,$decoded);
            }

            $reclamo = Reclamo::findOrFail($request->id);
            $reclamo->file = $fileName;
            $reclamo->save();

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }

    }
    public function eliminarImagen(Request $request){

        if(!$request->ajax()) return redirect('/');

        $directoryName = 'reclamosfiles';
        //Check if the directory already exists.
        if(!is_dir($directoryName)){
            //Directory does not exist, so lets create it.
            mkdir($directoryName, 0777);
        }

        $reclam= Reclamo::findOrFail($request->id);
        $img = $reclam->file;

        if($img != null){
            $image_path = public_path($directoryName).'/'.$img;
            if(file_exists($image_path)){
                unlink($image_path);
                $fileName = null;
            }
        }else {
            return;
        }

        $reclamo = Reclamo::findOrFail($request->id);
        $reclamo->file = $fileName;
        $reclamo->save();

    }
    public function update(Request $request){

        if(!$request->ajax()) return redirect('/');

        $mytime = Carbon::now('America/Mexico_City');

        try{
            DB::beginTransaction();

            $Oldreclamo = Reclamo::findOrFail($request->id);
            $Oldreclamo->delete();


            $fileName =null;

            if($request->file != ""){

                //The name of the directory that we need to create.
                $directoryName = 'images/traslados';

                if(!is_dir($directoryName)){
                    //Directory does not exist, so lets create it.
                    mkdir($directoryName, 0777);
                }

                $exploded = explode(',', $request->file);
                $decoded = base64_decode($exploded[1]);

                if(str_contains($exploded[0],'jpeg'))
                    $extension = 'jpg';
                else
                    $extension = 'png';

                $fileName = str_random().'.'.$extension;

                $path = public_path($directoryName).'/'.$fileName;

                file_put_contents($path,$decoded);
            }

            $reclamo = new Reclamo();
            $reclamo->idusuario = \Auth::user()->id;
            $reclamo->tipo_comprobante = $request->tipo_comprobante;
            $reclamo->num_comprobante = $request->num_comprobante;
            $reclamo->fecha_hora = $mytime;
            $reclamo->nueva_ubicacion = $request->nueva_ubicacion;
            $reclamo->estado = 'Activo';
            $reclamo->condicion = $request->condicion;
            $reclamo->observacion = $request->observacion;
            $reclamo->file = $fileName;
            $reclamo->save();

            $detalles = $request->data;//Array de detalles

            //Recorro todos los elementos
            foreach($detalles as $ep=>$det) {
                $detalle = new DetalleReclamo();
                $detalle->idreclamo = $reclamo->id;
                $detalle->idarticulo = $det['idarticulo'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->save();

                $articulo = Articulo::findOrFail($det['idarticulo']);
                $articulo->condicion = '4';
                /* $articulo->ubicacion = $traslado->nueva_ubicacion; */
                $articulo->save();
            }

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function updProceso(Request $request){
            //if (!$request->ajax()) return redirect('/');

            $reclamo = Reclamo::findorFail($request->id);
            $reclamo->estado = 'Activo';
            $reclamo->condicion ='2';
            $reclamo->save();
    }
    public function atendido(Request $request){
        //if (!$request->ajax()) return redirect('/');

        $reclamo = Reclamo::findorFail($request->id);
        $reclamo->estado = 'Pasivo';
        $reclamo->condicion ='3';
        $reclamo->observacion = $request-
        $reclamo->save();
    }
    public function noProcedio(Request $request){
        //if (!$request->ajax()) return redirect('/');

        $reclamo = Reclamo::findorFail($request->id);
        $reclamo->estado = 'Pasivo';
        $reclamo->condicion ='4';
        $reclamo->save();
    }
    public function storeFolio(Request $request){
        //if (!$request->ajax()) return redirect('/');

            $reclamo = Reclamo::findOrFail($request->id);
            $reclamo->estado = 'Pasivo';
            $reclamo->condicion = '3';
            $reclamo->folio= $request->folio;
            $reclamo->save();
    }
}
