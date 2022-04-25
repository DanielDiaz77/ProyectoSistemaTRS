<?php

namespace App\Http\Controllers;

use App\Abrasivo;
use App\DetalleTrasladoAbrasivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TrasladosExport;
use App\TrasladoAbrasivo;
use Carbon\Carbon;
use Exception;

class TrasladoAbrasivoController extends Controller
{
    public function index(Request $request){

        //if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $estado = $request->estado;

        $usrol = \Auth::user()->idrol;

        if($estado == ''){
            if ($buscar==''){
                $traslados = TrasladoAbrasivo::join('users','traslado_abrasivos.idusuario','=','users.id')
                ->join('personas','traslado_abrasivos.idproveedor','=','personas.id')
                ->select('traslado_abrasivos.id','traslado_abrasivos.tipo_comprobante','traslado_abrasivos.num_comprobante',
                'personas.nombre','traslado_abrasivos.fecha_hora','traslado_abrasivos.nueva_ubicacion','traslado_abrasivos.entregado',
                'traslado_abrasivos.estado','traslado_abrasivos.file','users.usuario','traslado_abrasivos.ubicacionant',
                'traslado_abrasivos.impuesto','traslado_abrasivos.total')
                ->where('traslado_abrasivos.estado','Registrado')
                ->orderBy('traslado_abrasivos.id', 'desc')->paginate(12);
            }
            else{
                $traslados = TrasladoAbrasivo::join('users','traslado_abrasivos.idusuario','=','users.id')
                ->join('personas','traslado_abrasivos.idproveedor','=','personas.id')
                ->select('traslado_abrasivos.id','traslado_abrasivos.tipo_comprobante','traslado_abrasivos.num_comprobante','personas.nombre',
                'traslado_abrasivos.fecha_hora','traslado_abrasivos.nueva_ubicacion','traslado_abrasivos.entregado','traslado_abrasivos.estado',
                'traslado_abrasivos.file','users.usuario','traslado_abrasivos.impuesto','traslado_abrasivos.total','traslado_abrasivos.ubicacionant')
                ->where([['traslado_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['traslado_abrasivos.estado','Registrado']])
                ->orderBy('traslado_abrasivos.id', 'desc')->paginate(12);
            }
        }else{
            if ($buscar==''){
                $traslados = TrasladoAbrasivo::join('users','traslado_abrasivos.idusuario','=','users.id')
                ->join('personas','traslado_abrasivos.idproveedor','=','personas.id')
                ->select('traslado_abrasivos.id','traslado_abrasivos.tipo_comprobante','traslado_abrasivos.num_comprobante','personas.nombre',
                'traslado_abrasivos.fecha_hora','traslado_abrasivos.nueva_ubicacion','traslado_abrasivos.entregado','traslado_abrasivos.estado',
                'traslado_abrasivos.file','users.usuario','traslado_abrasivos.impuesto','traslado_abrasivos.total','traslado_abrasivos.ubicacionant')
                ->where('traslado_abrasivos.estado',$estado)
                ->orderBy('traslado_abrasivos.id', 'desc')->paginate(12);
            }
            else{
                $traslados = TrasladoAbrasivo::join('users','traslado_abrasivos.idusuario','=','users.id')
                ->join('personas','traslado_abrasivos.idproveedor','=','personas.id')
                ->select('traslado_abrasivos.id','traslado_abrasivos.tipo_comprobante','traslado_abrasivos.num_comprobante','personas.nombre',
                'traslado_abrasivos.fecha_hora','traslado_abrasivos.nueva_ubicacion','traslado_abrasivos.entregado','traslado_abrasivos.estado',
                'traslado_abrasivos.file','users.usuario','traslado_abrasivos.impuesto','traslado_abrasivos.total','traslado_abrasivos.ubicacionant')
                ->where([['traslado_abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],['traslado_abrasivos.estado',$estado]])
                ->orderBy('traslado_abrasivos.id', 'desc')->paginate(12);
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

        $lastNum = TrasladoAbrasivo::select('num_comprobante')->get()->last();
        if($lastNum != null){
            $noComp = explode('"',$lastNum);
            $SigNum = explode("-",$noComp[3]);
            return  $SigNum[2] + 1;
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

            $traslado = new TrasladoAbrasivo();
            $traslado->idproveedor = $request->idproveedor;
            $traslado->idusuario = \Auth::user()->id;
            $traslado->tipo_comprobante = $request->tipo_comprobante;
            $traslado->num_comprobante = $request->num_comprobante;
            $traslado->fecha_hora = $mytime;
            $traslado->impuesto = $request->impuesto;
            $traslado->total = $request->total;
            $traslado->nueva_ubicacion = $request->nueva_ubicacion;
            $traslado->ubicacionant = $request->ubicacionant;
            $traslado->estado = 'Registrado';
            $traslado->entregado = 0;
            $traslado->observacion = $request->observacion;
            $traslado->file = $fileName;
            $traslado->save();

            $detalles = $request->data;//Array de detalles

            //Recorro todos los elementos
            foreach($detalles as $ep=>$det)
            {
                $articulos = Abrasivo::where('codigo','=',$det['codigo'])->select('id')->first();
                $detalle = new DetalleTrasladoAbrasivo();
                $detalle->idtrasladoabrasivo = $traslado->id;
                $detalle->idabrasivo = $articulos->id;
                $detalle->cantidad = $det['cantidad'] ;
                $detalle->ubicacion = $det['ubicacion'];
                $detalle->precio_compra = $det['precio'];
                $detalle->save();

                $articulo = Abrasivo::findOrFail($det['idabrasivo']);
                $articulo->condicion = '4';
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

            $traslado = TrasladoAbrasivo::findOrFail($request->id);
            $traslado->estado = 'Anulado';
            $traslado->entregado = 0;
            $traslado->save();

            $detalles = DetalleTrasladoAbrasivo::select('idabrasivo','ubicacion')
                ->where('idtrasladoabrasivo',$request->id)->get();

            foreach($detalles as $ep=>$det){

                $articulo = Abrasivo::findOrFail($det['idabrasivo']);
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

        $traslado = TrasladoAbrasivo::join('users','traslado_abrasivos.idusuario','=','users.id')
        ->join('personas','traslado_abrasivos.idproveedor','=','personas.id')
        ->select('traslado_abrasivos.id','traslado_abrasivos.tipo_comprobante','traslado_abrasivos.num_comprobante','traslado_abrasivos.ubicacionant',
            'traslado_abrasivos.fecha_hora','traslado_abrasivos.nueva_ubicacion','traslado_abrasivos.entregado','traslado_abrasivos.estado',
            'traslado_abrasivos.file','users.usuario','traslado_abrasivos.observacion as obstraslado','personas.nombre')
        ->where('traslado_abrasivos.id',$id)
        ->orderBy('traslado_abrasivos.id', 'desc')->take(1)->get();

        return ['traslado' => $traslado];
    }
    public function obtenerDetalles(Request $request){

        if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $detalles = DetalleTrasladoAbrasivo::join('abrasivos','detalle_traslado_abrasivos.idabrasivo','=','abrasivos.id')
        ->select('detalle_traslado_abrasivos.cantidad','detalle_traslado_abrasivos.ubicacion as ubicacionAntes','detalle_traslado_abrasivos.id',
            'abrasivos.sku as articulo','abrasivos.codigo','abrasivos.descripcion','abrasivos.ubicacion','abrasivos.file',
            'abrasivos.id as idabrasivo','abrasivos.condicion','abrasivos.stock')
        ->where('detalle_traslado_abrasivos.idtrasladoabrasivo',$id)
        ->orderBy('detalle_traslado_abrasivos.id','desc')->get();

        return ['detalles' => $detalles];
    }
    public function pdf(Request $request,$id){

        $traslado = TrasladoAbrasivo::join('users','traslado_abrasivos.idusuario','=','users.id')
        ->leftjoin('personas','users.id','=','personas.id')
        ->select('traslado_abrasivos.id','traslado_abrasivos.tipo_comprobante','traslado_abrasivos.num_comprobante','traslado_abrasivos.ubicacionant',
        'traslado_abrasivos.fecha_hora','traslado_abrasivos.nueva_ubicacion','traslado_abrasivos.entregado','traslado_abrasivos.estado',
        'traslado_abrasivos.file','traslado_abrasivos.observacion as comentario','personas.nombre as usuario')
        ->where('traslado_abrasivos.id',$id)->take(1)->get();

        $detalles = DetalleTrasladoAbrasivo::join('abrasivos','detalle_traslado_abrasivos.idabrasivo','=','abrasivos.id')
            ->select('detalle_traslado_abrasivos.cantidad','detalle_traslado_abrasivos.ubicacion',
            'abrasivos.sku as articulo','abrasivos.codigo','abrasivos.ubicacion as artubicacion')
            ->where('detalle_traslado_abrasivos.idtrasladoabrasivo',$id)
            ->orderBy('detalle_traslado_abrasivos.id','desc')->get();

        $numtraslado = TrasladoAbrasivo::select('num_comprobante')->where('id',$id)->get();

        $pdf = \PDF::loadView('pdf.trasladoAbrasivo',['traslado' => $traslado,'detalles'=>$detalles]);

        return $pdf->stream('traslado-'.$numtraslado[0]->num_comprobante.'.pdf');

    }
    public function cambiarEntrega(Request $request){
        if (!$request->ajax()) return redirect('/');
        $entStatus = $request->entregado;

        if($entStatus == 1){
            try{
                DB::beginTransaction();

                $traslado = TrasladoAbrasivo::findOrFail($request->id);
                $traslado->entregado = 1;
                $traslado->save();

                $detalles = DetalleTrasladoAbrasivo::select('idabrasivo')
                    ->where('idtrasladoabrasivo',$request->id)->get();

                foreach($detalles as $ep=>$det){

                    $articulo = Abrasivo::findOrFail($det['idabrasivo']);
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

                $traslado = TrasladoAbrasivo::findOrFail($request->id);
                $traslado->entregado = 0;
                $traslado->save();

                $detalles = DetalleTrasladoAbrasivo::select('idabrasivo')
                    ->where('idtrasladoabrasivo',$request->id)->get();

                foreach($detalles as $ep=>$det){
                    $articulo = Abrasivo::findOrFail($det['idabrasivo']);
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
        $traslado = TrasladoAbrasivo::findOrFail($request->id);
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

                $trasl = TrasladoAbrasivo::findOrFail($request->id);
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

            $traslado = TrasladoAbrasivo::findOrFail($request->id);
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

        $trasl= TrasladoAbrasivo::findOrFail($request->id);
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

        $traslado = TrasladoAbrasivo::findOrFail($request->id);
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

            $Oldtraslado = TrasladoAbrasivo::findOrFail($request->id);
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

            $traslado = new TrasladoAbrasivo();
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
                $detalle = new DetalleTrasladoAbrasivo();
                $detalle->idtrasladoabrasivo = $traslado->id;
                $detalle->idabrasivo = $det['idbrasivo'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->ubicacion = $det['ubicacion'];
                $detalle->save();

                $articulo = Abrasivo::findOrFail($det['idabrasivo']);
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
