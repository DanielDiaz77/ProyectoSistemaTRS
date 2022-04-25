<?php

namespace App\Http\Controllers;

use App\DetalleTrasladoJava;
use App\TrasladoJava;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TrasladosExport;
use App\Java;
use Carbon\Carbon;
use Exception;

class TrasladoJavaController extends Controller
{
    public function index(Request $request){

        //if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $estado = $request->estado;

        $usrol = \Auth::user()->idrol;

        if($estado == ''){
            if ($buscar==''){
                $traslados = TrasladoJava::join('users','traslado_javas.idusuario','=','users.id')
                ->select('traslado_javas.id','traslado_javas.tipo_comprobante','traslado_javas.num_comprobante',
                'traslado_javas.fecha_hora','traslado_javas.nueva_ubicacion','traslado_javas.entregado','traslado_javas.estado',
                'traslado_javas.file','users.usuario')
                ->where('traslado_javas.estado','Registrado')
                ->orderBy('traslado_javas.id', 'desc')->paginate(12);
            }
            else{
                $traslados = TrasladoJava::join('users','traslado_javas.idusuario','=','users.id')
                ->select('traslado_javas.id','traslado_javas.tipo_comprobante','traslado_javas.num_comprobante',
                'traslado_javas.fecha_hora','traslado_javas.nueva_ubicacion','traslado_javas.entregado','traslado_javas.estado',
                'traslado_javas.file','users.usuario')
                ->where([['traslado_javas.'.$criterio, 'like', '%'. $buscar . '%'],['traslado_javas.estado','Registrado']])
                ->orderBy('traslado_javas.id', 'desc')->paginate(12);
            }
        }else{
            if ($buscar==''){
                $traslados = TrasladoJava::join('users','traslado_javas.idusuario','=','users.id')
                ->select('traslado_javas.id','traslado_javas.tipo_comprobante','traslado_javas.num_comprobante',
                'traslado_javas.fecha_hora','traslado_javas.nueva_ubicacion','traslado_javas.entregado','traslado_javas.estado',
                'traslado_javas.file','users.usuario')
                ->where('traslado_javas.estado',$estado)
                ->orderBy('traslado_javas.id', 'desc')->paginate(12);
            }
            else{
                $traslados = TrasladoJava::join('users','traslado_javas.idusuario','=','users.id')
                ->select('traslado_javas.id','traslado_javas.tipo_comprobante','traslado_javas.num_comprobante',
                'traslado_javas.fecha_hora','traslado_javas.nueva_ubicacion','traslado_javas.entregado','traslado_javas.estado',
                'traslado_javas.file','users.usuario')
                ->where([['traslado_javas.'.$criterio, 'like', '%'. $buscar . '%'],['traslado_javas.estado',$estado]])
                ->orderBy('traslado_javas.id', 'desc')->paginate(12);
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
            'traslados' => $traslados,
            'userrol' => $usrol
        ];
    }
    public function getLastNum(){
        $lastNum = TrasladoJava::select('num_comprobante')->get()->last();

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

            $traslado = new TrasladoJava();
            $traslado->idusuario = \Auth::user()->id;
            $traslado->tipo_comprobante = $request->tipo_comprobante;
            $traslado->num_comprobante = $request->num_comprobante;
            $traslado->fecha_hora = $mytime;
            $traslado->nueva_ubicacion = $request->nueva_ubicacion;
            $traslado->estado = 'Registrado';
            $traslado->entregado = 0;
            $traslado->observacion = $request->observacion;
            $traslado->file = $fileName;
            $traslado->save();

            $detalles = $request->data;//Array de detalles

            //Recorro todos los elementos
            foreach($detalles as $ep=>$det)
            {
                $articulos = Java::where('codigo','=',$det['codigo'])->select('id')->first();
                $detalle = new DetalleTrasladoJava();
                $detalle->idtrasladojava = $traslado->id;
                $detalle->idjava = $articulos->id;
                $detalle->cantidad = $det['cantidad'];
                $detalle->ubicacion = $det['ubicacion'];
                $detalle->save();

                $articulo =Java::findOrFail($det['idjava']);
                $articulo->condicion = '4';
                /* $articulo->ubicacion = $traslado->nueva_ubicacion; */
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

            $traslado = TrasladoJava::findOrFail($request->id);
            $traslado->estado = 'Anulado';
            $traslado->entregado = 0;
            $traslado->save();

            $detalles = DetalleTrasladoJava::select('idjava','ubicacion')
                ->where('idtrasladojava',$request->id)->get();

            foreach($detalles as $ep=>$det){

                $articulo = Java::findOrFail($det['idjava']);
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

        if (!$request->ajax()) return redirect('/');

        $id = $request->id;

        $traslado = TrasladoJava::join('users','traslado_javas.idusuario','=','users.id')
        ->select('traslado_javas.id','traslado_javas.tipo_comprobante','traslado_javas.num_comprobante',
            'traslado_javas.fecha_hora','traslado_javas.nueva_ubicacion','traslado_javas.entregado','traslado_javas.estado',
            'traslado_javas.file','users.usuario','traslado_javas.observacion as obstraslado')
        ->where('traslado_javas.id',$id)
        ->orderBy('traslado_javas.id', 'desc')->take(1)->get();

        return ['traslado' => $traslado];
    }
    public function obtenerDetalles(Request $request){

        if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $detalles = DetalleTrasladoJava::join('javas','detalle_traslado_javas.idjava','=','javas.id')
        ->leftJoin('categorias','javas.idcategoria','=','categorias.id')
        ->select('detalle_traslado_javas.cantidad','detalle_traslado_javas.ubicacion as ubicacionAntes','detalle_traslado_javas.id',
            'javas.sku as articulo','javas.codigo','javas.espesor','javas.largo',
            'javas.alto','javas.metros_cuadrados','javas.descripcion','javas.idcategoria',
            'javas.terminado','javas.ubicacion','javas.file','javas.origen',
            'categorias.nombre as categoria','javas.id as idjava',
            'javas.contenedor','javas.fecha_llegada','javas.observacion','javas.condicion')
        ->where('detalle_traslado_javas.idtrasladojava',$id)
        ->orderBy('detalle_traslado_javas.id','desc')->get();

        return ['detalles' => $detalles];
    }
    public function pdf(Request $request,$id){

        $traslado = TrasladoJava::join('users','traslado_javas.idusuario','=','users.id')
        ->leftjoin('personas','users.id','=','personas.id')
        ->select('traslado_javas.id','traslado_javas.tipo_comprobante','traslado_javas.num_comprobante',
        'traslado_javas.fecha_hora','traslado_javas.nueva_ubicacion','traslado_javas.entregado','traslado_javas.estado',
        'traslado_javas.file','traslado_javas.observacion as comentario','personas.nombre as usuario')
        ->where('traslado_javas.id',$id)->take(1)->get();

        $detalles = DetalleTrasladoJava::join('javas','detalle_traslado_javas.idjava','=','javas.id')
            ->leftJoin('categorias','javas.idcategoria','=','categorias.id')
            ->select('detalle_traslado_javas.cantidad','detalle_traslado_javas.ubicacion','javas.sku as articulo',
            'javas.largo','javas.alto','javas.metros_cuadrados', 'categorias.nombre as categoria',
            'javas.codigo','javas.ubicacion as artubicacion','javas.terminado')
            ->where('detalle_traslado_javas.idtrasladojava',$id)
            ->orderBy('detalle_traslado_javas.id','desc')->get();

        $numtraslado = TrasladoJava::select('num_comprobante')->where('id',$id)->get();

        $sumaMts = DB::table('javas')
        ->select(DB::raw('SUM(metros_cuadrados) as metros'))
        ->leftJoin('detalle_traslado_javas','detalle_traslado_javas.idjava','javas.id')
        ->where('detalle_traslado_javas.idtrasladojava',$id)
        ->get();

        $pdf = \PDF::loadView('pdf.traslado',['traslado' => $traslado,'detalles'=>$detalles,'sumaMts' => $sumaMts[0]->metros]);

        return $pdf->stream('traslado-'.$numtraslado[0]->num_comprobante.'.pdf');

    }
    public function cambiarEntrega(Request $request){
        if (!$request->ajax()) return redirect('/');
        $entStatus = $request->entregado;

        if($entStatus == 1){
            try{
                DB::beginTransaction();

                $traslado = TrasladoJava::findOrFail($request->id);
                $traslado->entregado = 1;
                $traslado->save();

                $detalles = DetalleTrasladoJava::select('idjava','ubicacion')
                    ->where('idtrasladojava',$request->id)->get();

                foreach($detalles as $ep=>$det){

                    $articulo =Java::findOrFail($det['idjava']);
                    $articulo->ubicacion = $traslado->nueva_ubicacion;
                    $articulo->condicion = '1';
                    $articulo->save();
                }

                DB::commit();

            }catch(Exception $e){
                DB::rollBack();
            }
        }else{
            try{
                DB::beginTransaction();

                $traslado = TrasladoJava::findOrFail($request->id);
                $traslado->entregado = 0;
                $traslado->save();

                $detalles = DetalleTrasladoJava::select('idjava','ubicacion')
                    ->where('idtrasladojava',$request->id)->get();

                foreach($detalles as $ep=>$det){
                    $articulo =Java::findOrFail($det['idjava']);
                    $articulo->ubicacion = $det['ubicacion'];
                    $articulo->condicion = '4';
                    $articulo->save();
                }

                DB::commit();

            }catch(Exception $e){
                DB::rollBack();
            }
        }
    }
    public function actualizarObservacion(Request $request){
        if (!$request->ajax()) return redirect('/');
        $traslado = TrasladoJava::findOrFail($request->id);
        $traslado->observacion = $request->observacion;
        $traslado->save();
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

                $trasl = TrasladoJava::findOrFail($request->id);
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

            $traslado = TrasladoJava::findOrFail($request->id);
            $traslado->file = $fileName;
            $traslado->save();

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }

    }
    public function eliminarImagen(Request $request){

        if(!$request->ajax()) return redirect('/');

        $directoryName = 'images/traslados';
        //Check if the directory already exists.
        if(!is_dir($directoryName)){
            //Directory does not exist, so lets create it.
            mkdir($directoryName, 0777);
        }

        $trasl= TrasladoJava::findOrFail($request->id);
        $img = $trasl->file;

        if($img != null){
            $image_path = public_path($directoryName).'/'.$img;
            if(file_exists($image_path)){
                unlink($image_path);
                $fileName = null;
            }
        }else {
            return;
        }

        $traslado = TrasladoJava::findOrFail($request->id);
        $traslado->file = $fileName;
        $traslado->save();

    }
    public function excelTraslado($id,Request $request){
        $numtraslado = $request->num_traslado;
        return Excel::download(new TrasladosExport($id), 'traslado-'.$numtraslado.'.xlsx');
    }
    public function update(Request $request){

        if(!$request->ajax()) return redirect('/');

        $mytime = Carbon::now('America/Mexico_City');

        try{
            DB::beginTransaction();

            $Oldtraslado = TrasladoJava::findOrFail($request->id);
            $Oldtraslado->delete();


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

            $traslado = new TrasladoJava();
            $traslado->idusuario = \Auth::user()->id;
            $traslado->tipo_comprobante = $request->tipo_comprobante;
            $traslado->num_comprobante = $request->num_comprobante;
            $traslado->fecha_hora = $mytime;
            $traslado->nueva_ubicacion = $request->nueva_ubicacion;
            $traslado->estado = 'Registrado';
            $traslado->entregado = 0;
            $traslado->observacion = $request->observacion;
            $traslado->file = $fileName;
            $traslado->save();

            $detalles = $request->data;//Array de detalles

            //Recorro todos los elementos
            foreach($detalles as $ep=>$det) {
                $detalle = new DetalleTrasladoJava();
                $detalle->idtrasladojava = $traslado->id;
                $detalle->idjava = $det['idjava'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->ubicacion = $det['ubicacion'];
                $detalle->save();

                $articulo =Java::findOrFail($det['idjava']);
                $articulo->condicion = '4';
                /* $articulo->ubicacion = $traslado->nueva_ubicacion; */
                $articulo->save();
            }

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }
    }
}
