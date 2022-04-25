<?php

namespace App\Http\Controllers;

use App\Abrasivo;
use App\Exports\AbrasivoFiltrosExport;
use App\Exports\AbrasivosExport;
use App\Exports\AbrasivosVentasExport;
use Illuminate\Http\Request;
use App\User;
use App\Link;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbrasivoController extends Controller
{
    public function index(Request $request){
        if(!$request->ajax()) return redirect('/');

        $usarea = \Auth::user()->area;

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $bodega = $request->bodega;
        $estado = $request->estado;
        $usrol = \Auth::user()->idrol;

        if($estado == 1){
            if($bodega == ''){
                $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
                ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku','abrasivos.precio_venta',
                'abrasivos.ubicacion','abrasivos.stock','abrasivos.descripcion',
                'abrasivos.file','abrasivos.condicion','abrasivos.comprometido','users.usuario')
                ->where([['abrasivos.stock','>',0],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.condicion',1],['abrasivos.ubicacion','!=','Aguascalientes']])
                ->orderBy('abrasivos.id', 'desc')
                ->Criterio($criterio,$buscar)->paginate(12);

                $total = Abrasivo::where([['abrasivos.stock','>',0],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.condicion',1],['abrasivos.ubicacion','!=','Aguascalientes']])
                ->orderBy('abrasivos.id', 'desc')
                ->Criterio($criterio,$buscar)->count();

            }elseif($bodega == 'nol'){
                $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
                ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku','abrasivos.precio_venta',
                'abrasivos.ubicacion','abrasivos.stock','abrasivos.descripcion',
                'abrasivos.file','abrasivos.condicion','abrasivos.comprometido','users.usuario')
                ->where([['abrasivos.stock','>',0],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes'],['abrasivos.condicion',1]])
                ->orderBy('abrasivos.id', 'desc')->Criterio($criterio,$buscar)->paginate(12);

                $total = Abrasivo::where([['abrasivos.stock','>',0],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes'],['abrasivos.condicion',1]])
                ->orderBy('abrasivos.id', 'desc')
                ->Criterio($criterio,$buscar)->count();

            }else{
                $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
                ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku','abrasivos.precio_venta',
                    'abrasivos.ubicacion','abrasivos.stock','abrasivos.descripcion',
                    'abrasivos.file','abrasivos.condicion','abrasivos.comprometido','users.usuario')
                ->where([['abrasivos.stock','>',0],['abrasivos.condicion',1]])
                ->orderBy('abrasivos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->paginate(12);

                $total = Abrasivo::where([['abrasivos.stock','>',0],['abrasivos.condicion',1]])
                ->orderBy('abrasivos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)
                ->count();
            }
        }elseif($estado == 2){
            if($bodega == ''){
                $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
                ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','abrasivos.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->rightjoin('personas','ventas.idcliente','=','personas.id')
                ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku','abrasivos.precio_venta',
                'abrasivos.ubicacion','abrasivos.stock','abrasivos.descripcion',
                'abrasivos.file','abrasivos.condicion','abrasivos.comprometido','users.usuario')
                ->where([['abrasivos.stock','<=',0],['abrasivos.ubicacion','!=','San Luis'],['ventas.estado','Registrado'],['abrasivos.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->orderBy('abrasivos.id', 'desc')
                ->paginate(12);

                $total = Abrasivo::join('detalle_ventas','detalle_ventas.idarticulo','abrasivos.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->where([['abrasivos.stock','<=',0],['abrasivos.ubicacion','!=','San Luis'],['ventas.estado','Registrado'],['abrasivos.ubicacion','!=','Aguascalientes']])
                ->orderBy('abrasivos.id', 'desc')
                ->Criterio($criterio,$buscar)->count();

            }elseif($bodega == 'nol'){

                $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
                ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','abrasivos.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->rightjoin('personas','ventas.idcliente','=','personas.id')
                ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku','abrasivos.precio_venta',
                'abrasivos.ubicacion','abrasivos.stock','abrasivos.descripcion',
                'abrasivos.file','abrasivos.condicion','abrasivos.comprometido','users.usuario')
                ->where([['abrasivos.stock','<=',0],['abrasivos.ubicacion','!=','San Luis'],
                    ['abrasivos.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado']])
                ->Criterio($criterio,$buscar)->orderBy('abrasivos.id', 'desc')
                ->paginate(12);

                $total = Abrasivo::join('detalle_ventas','detalle_ventas.idarticulo','abrasivos.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->where([['abrasivos.stock','<=',0],['abrasivos.ubicacion','!=','San Luis'],
                    ['abrasivos.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado']])
                ->orderBy('abrasivos.id', 'desc')
                ->Criterio($criterio,$buscar)->count();

            }else{
                $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
                ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','abrasivos.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->rightjoin('personas','ventas.idcliente','=','personas.id')
                ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku','abrasivos.precio_venta',
                'abrasivos.ubicacion','abrasivos.stock','abrasivos.descripcion',
                'abrasivos.file','abrasivos.condicion','abrasivos.comprometido','users.usuario')
                ->where([['abrasivos.stock','<=',0],['ventas.estado','Registrado']])
                ->Criterio($criterio,$buscar)->Ubicacion($bodega)
                ->orderBy('abrasivos.id', 'desc')
                ->paginate(12);

                $total = Abrasivo::join('detalle_ventas','detalle_ventas.idarticulo','abrasivos.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->where([['abrasivos.stock','<=',0],['ventas.estado','Registrado']])
                ->orderBy('abrasivos.id', 'desc')
                ->Criterio($criterio,$buscar)->Ubicacion($bodega)->Ubicacion($bodega)
                ->count();
            }
        }elseif($estado == 3){
            if($bodega == ''){
                $articulos = Abrasivo::join('categorias','abrasivos.idcategoria','=','categorias.id')
                ->leftjoin('users','abrasivos.idusuario','=','users.id')
                ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku','abrasivos.precio_venta',
                'abrasivos.ubicacion','abrasivos.stock','abrasivos.descripcion',
                'abrasivos.file','abrasivos.condicion','abrasivos.comprometido','users.usuario')
                ->where([['abrasivos.condicion',3],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes']])
                ->orderBy('abrasivos.id', 'desc')
                ->Criterio($criterio,$buscar)->paginate(12);

                $total = Abrasivo::where([['abrasivos.condicion',3],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes']])
                ->orderBy('abrasivos.id', 'desc')
                ->Criterio($criterio,$buscar)->count();

            }elseif($bodega == 'nol'){
                $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
                ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku','abrasivos.precio_venta',
                'abrasivos.ubicacion','abrasivos.stock','abrasivos.descripcion',
                'abrasivos.file','abrasivos.condicion','abrasivos.comprometido','users.usuario')
                ->where([['abrasivos.condicion',3],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes']])
                ->orderBy('abrasivos.id', 'desc')->Criterio($criterio,$buscar)->paginate(12);

                $total = Abrasivo::where([['abrasivos.condicion',3],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes']])
                ->orderBy('abrasivos.id', 'desc')
                ->Criterio($criterio,$buscar)->count();

            }else{
                $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
                ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku','abrasivos.precio_venta',
                'abrasivos.ubicacion','abrasivos.stock','abrasivos.descripcion',
                'abrasivos.file','abrasivos.condicion','abrasivos.comprometido','users.usuario')
                ->where([['abrasivos.condicion',3]])
                ->orderBy('abrasivos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)
                ->paginate(12);

                $total = Abrasivo::where([['abrasivos.condicion',3]])
                ->orderBy('abrasivos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)
                ->count();
            }
        }elseif($estado == 4){
            if($bodega == ''){
                $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
                ->rightJoin('detalle_traslados','detalle_traslados.idarticulo','abrasivos.id')
                ->leftjoin('traslados','detalle_traslados.idtraslado','traslados.id')
                ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku','abrasivos.precio_venta',
                'abrasivos.ubicacion','abrasivos.stock','abrasivos.descripcion',
                'abrasivos.file','abrasivos.condicion',
                'abrasivos.comprometido','users.usuario')
                ->where([['abrasivos.condicion',4],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes']])
                ->orderBy('abrasivos.id', 'desc')
                ->Criterio($criterio,$buscar)->paginate(12);

                $total = Abrasivo::where([['abrasivos.condicion',4],['abrasivos.ubicacion','!=','San Luis']])
                ->orderBy('abrasivos.id', 'desc')
                ->Criterio($criterio,$buscar)->count();

            }elseif($bodega == 'nol'){
                $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
                ->rightJoin('detalle_traslados','detalle_traslados.idarticulo','abrasivos.id')
                ->leftjoin('traslados','detalle_traslados.idtraslado','traslados.id')
                ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku','abrasivos.precio_venta',
                'abrasivos.ubicacion','abrasivos.stock','abrasivos.descripcion',
                'abrasivos.file','abrasivos.condicion',
                'abrasivos.comprometido','users.usuario')
                ->where([['abrasivos.condicion',4],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes']])
                ->orderBy('abrasivos.id', 'desc')->Criterio($criterio,$buscar)->paginate(12);

                $total = Abrasivo::where([['abrasivos.condicion',4],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes']])
                ->orderBy('abrasivos.id', 'desc')
                ->Criterio($criterio,$buscar)->count();

            }else{
                $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
                ->rightJoin('detalle_traslados','detalle_traslados.idarticulo','abrasivos.id')
                ->leftjoin('traslados','detalle_traslados.idtraslado','traslados.id')
                ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku','abrasivos.precio_venta',
                'abrasivos.ubicacion','abrasivos.stock','abrasivos.descripcion',
                'abrasivos.file','abrasivos.condicion',
                'abrasivos.comprometido','users.usuario')
                ->where([['abrasivos.condicion',4]])
                ->orderBy('abrasivos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)
                ->paginate(12);

                $total = Abrasivo::where([['abrasivos.condicion',4]])
                ->orderBy('abrasivos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)
                ->count();
            }
        }elseif($estado == 0){
            if($bodega == ''){
                $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
                ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku','abrasivos.precio_venta',
                'abrasivos.ubicacion','abrasivos.stock','abrasivos.descripcion',
                'abrasivos.file','abrasivos.condicion',
                'abrasivos.comprometido','users.usuario')
                ->where([['abrasivos.condicion',0],['abrasivos.ubicacion','!=','San Luis']])
                ->orderBy('abrasivos.id', 'desc')
                ->Criterio($criterio,$buscar)->paginate(12);

                $total = Abrasivo::where([['abrasivos.condicion',0],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes']])
                ->orderBy('abrasivos.id', 'desc')
                ->Criterio($criterio,$buscar)->count();

            }elseif($bodega == 'nol'){
                $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
                ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku','abrasivos.precio_venta',
                'abrasivos.ubicacion','abrasivos.stock','abrasivos.descripcion',
                'abrasivos.file','abrasivos.condicion',
                'abrasivos.comprometido','users.usuario')
                ->where([['abrasivos.condicion',0],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes']])
                ->orderBy('abrasivos.id', 'desc')->Criterio($criterio,$buscar)->paginate(12);

                $total = Abrasivo::where([['abrasivos.condicion',0],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes']])
                ->orderBy('abrasivos.id', 'desc')
                ->Criterio($criterio,$buscar)->count();

            }else{
                $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
                ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku','abrasivos.precio_venta',
                'abrasivos.ubicacion','abrasivos.stock','abrasivos.descripcion',
                'abrasivos.file','abrasivos.condicion',
                'abrasivos.comprometido','users.usuario')
                ->where([['abrasivos.condicion',0]])
                ->orderBy('abrasivos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)
                ->paginate(12);

                $total = Abrasivo::where([['abrasivos.condicion',0]])
                ->orderBy('abrasivos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)
                ->count();
            }
        }elseif($estado == 6){
            if($bodega == ''){
                $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
                ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku','abrasivos.precio_venta',
                'abrasivos.ubicacion','abrasivos.stock','abrasivos.descripcion',
                'abrasivos.file','abrasivos.condicion','abrasivos.comprometido','users.usuario')
                ->where([['abrasivos.condicion',6],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes']])
                ->orderBy('abrasivos.id', 'desc')
                ->Criterio($criterio,$buscar)->paginate(12);

                $total = Abrasivo::where([['abrasivos.condicion',6],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes']])
                ->orderBy('abrasivos.id', 'desc')
                ->Criterio($criterio,$buscar)->count();

            }elseif($bodega == 'nol'){
                $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
                ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku','abrasivos.precio_venta',
                'abrasivos.ubicacion','abrasivos.stock','abrasivos.descripcion',
                'abrasivos.file','abrasivos.condicion',
                'abrasivos.comprometido','users.usuario')
                ->where([['abrasivos.condicion',6],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes']])
                ->orderBy('abrasivos.id', 'desc')->Criterio($criterio,$buscar)->paginate(12);

                $total = Abrasivo::where([['abrasivos.condicion',6],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes']])
                ->orderBy('abrasivos.id', 'desc')
                ->Criterio($criterio,$buscar)->count();

            }else{
                $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
                ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku','abrasivos.precio_venta',
                'abrasivos.ubicacion','abrasivos.stock','abrasivos.descripcion',
                'abrasivos.file','abrasivos.condicion',
                'abrasivos.comprometido','users.usuario')
                ->where([['abrasivos.condicion',6]])
                ->orderBy('abrasivos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)
                ->paginate(12);

                $total = Abrasivo::where([['abrasivos.condicion',6]])
                ->orderBy('abrasivos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)
                ->count();
            }
        }

        return [
            'pagination' => [
                'total'         => $articulos->total(),
                'current_page'  => $articulos->currentPage(),
                'per_page'      => $articulos->perPage(),
                'last_page'     => $articulos->lastPage(),
                'from'          => $articulos->firstItem(),
                'to'            => $articulos->lastItem(),
            ],

            'articulos' => $articulos,
            'total' => $total,
            'userarea' => $usarea,
            'usrol' => $usrol
        ];
    }
    public function buscarArticulo(Request $request){

        if(!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;

        $articulos = Abrasivo::where('codigo',$filtro)
        ->select('id','nombre','sku','codigo','precio_venta','ubicacion','file','stock','condicion')->orderBy('sku','asc')->take(1)->get();
        return ['articulos' => $articulos];

    }
    public function buscarArticuloVenta(Request $request){

        if(!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;

        $articulos = Abrasivo::select('abrasivos.id','abrasivos.nombre','abrasivos.sku','abrasivos.codigo',
        'abrasivos.ubicacion','abrasivos.precio_venta','abrasivos.stock',
        'abrasivos.descripcion','abrasivos.file','abrasivos.comprometido')
        ->where([
            ['codigo',$filtro],
            ['abrasivos.stock','>',0],
            ['abrasivos.condicion',1]
        ])
        ->orderBy('abrasivos.sku','asc')->take(1)->get();
        return ['abrasivos' => $articulos];

    }
    public function store(Request $request){

        if(!$request->ajax()) return redirect('/');

        $fileName = "";

        if($request->file != ""){

            $exploded = explode(',', $request->file);

            $decoded = base64_decode($exploded[1]);

            if(str_contains($exploded[0],'jpeg'))
                $extension = 'jpg';
            else
                $extension = 'png';

            $fileName = str_random().'.'.$extension;
            //The name of the directory that we need to create.
            $directoryName = 'images';

            //Check if the directory already exists.
            if(!is_dir($directoryName)){
                //Directory does not exist, so lets create it.
                mkdir($directoryName, 0777);
            }

            $path = public_path($directoryName).'/'.$fileName;

            file_put_contents($path,$decoded);
        }

        $articulo = new Abrasivo();
        $articulo->codigo           =   $request->codigo;
        $articulo->sku              =   $request->sku;
        $articulo->nombre           =   $request->nombre;
        $articulo->precio_venta     =   $request->precio_venta;
        $articulo->ubicacion        =   $request->ubicacion;
        $articulo->stock            =   $request->stock;
        $articulo->descripcion      =   $request->descripcion;
        $articulo->file             =   $fileName;
        $articulo->condicion        =   '1';
        $articulo->save();
    }
    public function storeDetalle(Request $request){

        if(!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $detalles = $request->data; //Array detalles

            //Recorrido de todos los elementos
            foreach($detalles as $ep=>$art){
                $fileName ="";
                if($art['imagen'] != ""){
                    $exploded = explode(',', $art['imagen']);

                    $decoded = base64_decode($exploded[1]);

                    if(str_contains($exploded[0],'jpeg'))
                        $extension = 'jpg';
                    else
                        $extension = 'png';

                    $fileName = str_random().'.'.$extension;

                    //The name of the directory that we need to create.
                    $directoryName = 'images';
                    if(!is_dir($directoryName)){
                        //Directory does not exist, so lets create it.
                        mkdir($directoryName, 0777);
                    }

                    $path = public_path($directoryName).'/'.$fileName;

                    file_put_contents($path,$decoded);
                }

                $articulo = new Abrasivo();
                $articulo->codigo           =   $art['codigo'];
                $articulo->sku              =   $art['sku'];
                $articulo->precio_venta     =   $art['precio_venta'];
                $articulo->ubicacion        =   $art['ubicacion'];
                $articulo->stock            =   $art['stock'];
                $articulo->descripcion      =   $art['descripcion'];
                $articulo->file             =   $fileName;
                $articulo->condicion        =   $request->active;
                $articulo->idusuario        =   \Auth::user()->id;
                $articulo->save();
            }
            DB::commit();

        }catch(Exception $e){

            DB::rollBack();

        }
    }
    public function storeStock(Request $request){

        if(!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $detalles = $request->data; //Array detalles

            //Recorrido de todos los elementos
            foreach($detalles as $ep=>$art){
                $fileName ="";
                if($art['imagen'] != ""){
                    $exploded = explode(',', $art['imagen']);

                    $decoded = base64_decode($exploded[1]);

                    if(str_contains($exploded[0],'jpeg'))
                        $extension = 'jpg';
                    else
                        $extension = 'png';

                    $fileName = str_random().'.'.$extension;

                    //The name of the directory that we need to create.
                    $directoryName = 'images';
                    if(!is_dir($directoryName)){
                        //Directory does not exist, so lets create it.
                        mkdir($directoryName, 0777);
                    }

                    $path = public_path($directoryName).'/'.$fileName;

                    file_put_contents($path,$decoded);
                }

                $articulo = new Abrasivo();
                $articulo->codigo           =   $art['codigo'];
                $articulo->sku              =   $art['sku'];
                $articulo->precio_venta     =   $art['precio_venta'];
                $articulo->ubicacion        =   $art['ubicacion'];
                $articulo->stock            =   $art['stock'];
                $articulo->descripcion      =   $art['descripcion'];
                $articulo->file             =   $fileName;
                $articulo->condicion        =   $request->active;
                $articulo->idusuario        =   \Auth::user()->id;
                $articulo->save();
            }
            DB::commit();

        }catch(Exception $e){

            DB::rollBack();

        }
    }
    public function update(Request $request){

        if(!$request->ajax()) return redirect('/');

        if($request->file != ""){

            $directoryName = 'images/tools';

            //Check if the directory already exists.
            if(!is_dir($directoryName)){
                //Directory does not exist, so lets create it.
                mkdir($directoryName, 0777);
            }

            $art= Abrasivo::findOrFail($request->id);
            $img = $art->file;

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

            $fileName = time().'.'.$extension;
            $path = public_path($directoryName).'/'.$fileName;
            file_put_contents($path,$decoded);

            $articulo = Abrasivo::findOrFail($request->id);
            $articulo->codigo           =   $request->codigo;
            $articulo->sku              =   $request->sku;
            $articulo->precio_venta     =   $request->precio_venta;
            $articulo->stock            =   $request->stock;
            $articulo->descripcion      =   $request->descripcion;
            $articulo->ubicacion        =   $request->ubicacion;
            $articulo->file             =   $fileName;
            $articulo->condicion        =   '1';
            $articulo->save();

        }else{

            $articulo = Abrasivo::findOrFail($request->id);
            $articulo->codigo           =   $request->codigo;
            $articulo->sku              =   $request->sku;
            $articulo->precio_venta     =   $request->precio_venta;
            $articulo->stock            =   $request->stock;
            $articulo->descripcion      =   $request->descripcion;
            $articulo->ubicacion        =   $request->ubicacion;
            $articulo->condicion        =   '1';
            $articulo->save();
        }
    }
    public function edicionArticulos(Request $request){

        $id = $request->id;
        $articulo = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
        ->select('abrasivos.id','abrasivos.idcategoria','abrasivos.codigo','abrasivos.sku','abrasivos.nombre',
        'categorias.nombre as nombre_categoria','abrasivos.terminado','abrasivos.largo','abrasivos.alto',
        'abrasivos.metros_cuadrados','abrasivos.espesor','abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.pulido',
        'abrasivos.contenedor','abrasivos.stock','abrasivos.descripcion','abrasivos.relice','abrasivos.detalle',
        'abrasivos.origen','abrasivos.file','abrasivos.condicion','abrasivos.salida',
        'abrasivos.comprometido','users.usuario','users.usedit')
        ->where('abrasivos.id','=',$id)
        ->orderBy('abrasivos.id', 'desc')->take(1)->get();

        return ['articulo' => $articulo];

    }
    public function desactivar(Request $request){
        if(!$request->ajax()) return redirect('/');
        $articulo = Abrasivo::findOrFail($request->id);
        $articulo->condicion = '0';
        $articulo->save();

    }
    public function activar(Request $request){
        if(!$request->ajax()) return redirect('/');
        $articulo = Abrasivo::findOrFail($request->id);
        $articulo->condicion = '1';
        $articulo->save();
    }
    public function ocultar(Request $request){
        if(!$request->ajax()) return redirect('/');
        $articulo = Abrasivo::findOrFail($request->id);
        $articulo->condicion = '6';
        $articulo->save();
    }
    public function listarArticulo(Request $request){

        if(!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $bodega = $request->bodega;
        $acabado = $request->acabado;
        $estado = $request->estado;


            if($bodega == ''){
                if($buscar==''){
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                            'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }elseif($bodega == 'Del Musico'){
                if($buscar==''){
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }elseif($bodega == 'Sastres'){
                if($buscar==''){
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }elseif($bodega == 'Mecanicos'){
                if($buscar==''){
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Sastres'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Sastres'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Sastres'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }elseif($bodega == 'Escultores'){
                if($buscar==''){
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Sastres'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Sastres'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Sastres'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }elseif($bodega == 'Oficina'){
                if($buscar==''){
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }elseif($bodega == 'San Luis'){
                if($buscar==''){
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }elseif($bodega == 'Aguascalientes'){
                if($buscar==''){
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }


        return [
            'pagination' => [
                'total'         => $articulos->total(),
                'current_page'  => $articulos->currentPage(),
                'per_page'      => $articulos->perPage(),
                'last_page'     => $articulos->lastPage(),
                'from'          => $articulos->firstItem(),
                'to'            => $articulos->lastItem(),
            ],
            'articulos' => $articulos
        ];
    }
    public function listarArticuloVenta(Request $request){

        if(!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $bodega = $request->bodega;

        $area = \Auth::user()->area;

        if($bodega == ''){
            $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
            ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                'abrasivos.descripcion','abrasivos.file','abrasivos.condicion',
                'abrasivos.comprometido','users.usuario')
            ->where([['abrasivos.stock','>',0],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes'],
                ['abrasivos.condicion',1]])
            ->orderBy('abrasivos.id', 'desc')
            ->Criterio($criterio,$buscar)->paginate(12);

            $total = Abrasivo::where([['abrasivos.stock','>',0],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes'],
                ['abrasivos.condicion',1]])
            ->orderBy('abrasivos.id', 'desc')
            ->Criterio($criterio,$buscar)->count();

        }elseif($bodega == 'nol'){
            $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
            ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
            'abrasivos.descripcion','abrasivos.file','abrasivos.condicion',
            'abrasivos.comprometido','users.usuario')
            ->where([['abrasivos.stock','>',0],['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes'],
                ['abrasivos.condicion',1]])
            ->orderBy('abrasivos.id', 'desc')->Criterio($criterio,$buscar)->paginate(12);

            $total = Abrasivo::where([['abrasivos.stock','>',0],['abrasivos.ubicacion','!=','San Luis'],
            ['abrasivos.ubicacion','!=','Aguascalientes'],['abrasivos.condicion',1]])
            ->orderBy('abrasivos.id', 'desc')
            ->Criterio($criterio,$buscar)->count();

        }else{
            $articulos = Abrasivo::join('users','abrasivos.idusuario','=','users.id')
            ->select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
            'abrasivos.descripcion','abrasivos.file','abrasivos.condicion',
            'abrasivos.comprometido','users.usuario')
            ->where([['abrasivos.stock','>',0],['abrasivos.condicion',1]])
            ->orderBy('abrasivos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)
            ->paginate(12);

            $total = Abrasivo::where([['abrasivos.stock','>',0],['abrasivos.condicion',1]])
            ->orderBy('abrasivos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)
            ->count();
        }



        return [
            'pagination' => [
                'total'         => $articulos->total(),
                'current_page'  => $articulos->currentPage(),
                'per_page'      => $articulos->perPage(),
                'last_page'     => $articulos->lastPage(),
                'from'          => $articulos->firstItem(),
                'to'            => $articulos->lastItem(),
            ],
            'articulos' => $articulos,
            'total' => $total,
            'userarea' => $area
        ];
    }
    public function listarArticuloCotizado(Request $request){

        if(!$request->ajax()) return redirect('/');

        $area = \Auth::user()->area;

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        if($area == 'GDL'){
            if($buscar==''){
                $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->join('detalle_cotizaciones','articulos.id','=','detalle_cotizaciones.idarticulo')
                ->join('cotizaciones','cotizaciones.id','=','detalle_cotizaciones.idcotizacion')
                ->join('personas','personas.id','cotizaciones.idcliente')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.relice',
                    'articulos.comprometido','cotizaciones.id as idcotizacion','articulos.comprometido','articulos.salida',
                    'cotizaciones.num_comprobante as cotizacion','cotizaciones.estado as estado_cotizacion' ,
                    'personas.nombre as cliente')
                ->where([
                    ['articulos.condicion',1]
                ])
                ->orderBy('articulos.id', 'desc')->paginate(10);
            }else{
                $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->join('detalle_cotizaciones','articulos.id','=','detalle_cotizaciones.idarticulo')
                ->join('cotizaciones','cotizaciones.id','=','detalle_cotizaciones.idcotizacion')
                ->join('personas','personas.id','cotizaciones.idcliente')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                    'articulos.comprometido','cotizaciones.id as idcotizacion','articulos.comprometido','articulos.relice',
                    'cotizaciones.num_comprobante as cotizacion','cotizaciones.estado as estado_cotizacion' ,'articulos.salida',
                    'personas.nombre as cliente')
                ->where([
                    ['articulos.'.$criterio, 'like', '%'. $buscar . '%'],
                    ['articulos.condicion',1],
                    ['cotizaciones.estado','Registrado']
                ])
                ->orderBy('articulos.id', 'desc')->paginate(10);
            }
        }elseif($area== 'SLP'){

            if($buscar==''){
                $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->join('detalle_cotizaciones','articulos.id','=','detalle_cotizaciones.idarticulo')
                ->join('cotizaciones','cotizaciones.id','=','detalle_cotizaciones.idcotizacion')
                ->join('personas','personas.id','cotizaciones.idcliente')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.relice',
                    'articulos.comprometido','cotizaciones.id as idcotizacion','articulos.comprometido','articulos.salida',
                    'cotizaciones.num_comprobante as cotizacion','cotizaciones.estado as estado_cotizacion' ,
                    'personas.nombre as cliente')
                ->where([
                    ['cotizaciones.estado','Registrado'],
                    ['articulos.condicion',1],
                    ['articulos.ubicacion','San Luis']
                ])
                ->orderBy('articulos.id', 'desc')->paginate(10);
            }else{
                $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->join('detalle_cotizaciones','articulos.id','=','detalle_cotizaciones.idarticulo')
                ->join('cotizaciones','cotizaciones.id','=','detalle_cotizaciones.idcotizacion')
                ->join('personas','personas.id','cotizaciones.idcliente')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.relice',
                    'articulos.comprometido','cotizaciones.id as idcotizacion','articulos.comprometido','articulos.salida',
                    'cotizaciones.num_comprobante as cotizacion','cotizaciones.estado as estado_cotizacion' ,
                    'personas.nombre as cliente')
                ->where([
                    ['articulos.'.$criterio, 'like', '%'. $buscar . '%'],
                    ['articulos.condicion',1],
                    ['cotizaciones.estado','Registrado'],
                    ['articulos.ubicacion','San Luis']
                ])
                ->orderBy('articulos.id', 'desc')->paginate(10);
            }
        }
        return [
            'pagination' => [
                'total'         => $articulos->total(),
                'current_page'  => $articulos->currentPage(),
                'per_page'      => $articulos->perPage(),
                'last_page'     => $articulos->lastPage(),
                'from'          => $articulos->firstItem(),
                'to'            => $articulos->lastItem(),
            ],
            'articulos' => $articulos,
            'userarea' => $area
        ];
    }
    public function listarArticuloOcultos(Request $request){

        if(!$request->ajax()) return redirect('/');
        $area = \Auth::user()->area;

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $idcategoria = $request->idcategoria;
        $filtro = $request->buscador;
        $acabado = $request->acabado;
        $usrol = \Auth::user()->idrol;

        if($filtro == ''){
            if($area == 'GDL'){
                if($buscar==''){
                    $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                        'articulos.comprometido','articulos.salida')
                    ->where([
                        ['articulos.condicion',6]
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(10);

                    $total = Abrasivo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->count();


                    $sumaMts =  Abrasivo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',6]])
                    ->Criterio($criterio,$buscar)->get();

                }else{
                    $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                        'articulos.comprometido','articulos.salida')
                    ->where([
                        ['articulos.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['articulos.condicion',6],
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(10);


                    $total = Abrasivo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->count();


                    $sumaMts =  Abrasivo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',6]])
                    ->Criterio($criterio,$buscar)->get();
                }
            }elseif($area== 'SLP'){

                if($buscar==''){
                    $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                        'articulos.comprometido','articulos.salida')
                    ->where([

                        ['articulos.condicion',6],
                        ['articulos.ubicacion','San Luis']
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(10);

                    $total = Abrasivo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->count();


                    $sumaMts =  Abrasivo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',6]])
                    ->Criterio($criterio,$buscar)->get();
                }else{
                    $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.relice',
                        'articulos.comprometido','articulos.salida')
                    ->where([
                        ['articulos.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['articulos.condicion',6],
                        ['articulos.ubicacion','San Luis']
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(10);

                    $total = Abrasivo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->count();


                    $sumaMts =  Abrasivo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',6]])
                    ->Criterio($criterio,$buscar)->get();
                }
            }
        }else{
            if($area == 'GDL'){
                if($buscar==''){
                    $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.relice',
                        'articulos.comprometido','articulos.salida')
                    ->where([
                        ['articulos.condicion',6]
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(10);


                    $total = Abrasivo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->count();


                    $sumaMts =  Abrasivo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',6]])
                    ->Criterio($criterio,$buscar)->get();
                }else{
                    $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.relice',
                        'articulos.comprometido','articulos.salida')
                    ->where([
                        ['articulos.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['articulos.condicion',6],
                        ['cotizaciones.estado','Registrado']
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(10);


                    $total = Abrasivo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->count();


                    $sumaMts =  Abrasivo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',6]])
                    ->Criterio($criterio,$buscar)->get();
                }
            }elseif($area== 'SLP'){

                if($buscar==''){
                    $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.relice',
                        'articulos.comprometido','articulos.salida')
                    ->where([
                        ['articulos.condicion',6],
                        ['articulos.ubicacion','San Luis']
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(10);
                }else{
                    $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.relice',
                        'articulos.comprometido','articulos.salida')
                    ->where([
                        ['articulos.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['articulos.condicion',6],
                        ['articulos.ubicacion','San Luis']
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(10);


                    $total = Abrasivo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->count();


                    $sumaMts =  Abrasivo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',6]])
                    ->Criterio($criterio,$buscar)->get();
                }
            }
        }

        return [
            'pagination' => [
                'total'         => $articulos->total(),
                'current_page'  => $articulos->currentPage(),
                'per_page'      => $articulos->perPage(),
                'last_page'     => $articulos->lastPage(),
                'from'          => $articulos->firstItem(),
                'to'            => $articulos->lastItem(),
            ],
            'articulos' => $articulos,
            'userarea' => $area,
            'total' => $total,
            'sumaMts' => $sumaMts,
        ];



    }
    public function selectBodega(Request $request){
        if(!$request->ajax()) return redirect('/');

        $bodegas = Abrasivo::where('condicion',1)
        ->select('ubicacion')->groupBy('ubicacion')->get();
        return ['bodegas' => $bodegas];
    }
    public function listarExcel(Request $request){
        $bodega = $request->bodega;
        $mytime = Carbon::now('America/Mexico_City')->format('d-m-Y');
        return Excel::download(new AbrasivosExport($bodega), 'inventario-'.$bodega.'-'.$mytime.'.xlsx');
    }
    public function listarExcelVenta(Request $request){
        $bodega = $request->bodega;
        $mytime = Carbon::now('America/Mexico_City')->format('d-m-Y');
        return Excel::download(new AbrasivosVentasExport($bodega), 'ArticulosNoEntregados-'.$bodega.'-'.$mytime.'.xlsx');
    }
    public function listarExcelOculto(Request $request){
        $bodega = $request->bodega;
        $mytime = Carbon::now('America/Mexico_City')->format('d-m-Y');
        return Excel::download(new ArticulosOcultosExport($bodega), 'inventario-'.$bodega.'-'.$mytime.'.xlsx');
    }
    public function cambiarComprometido(Request $request){

        if (!$request->ajax()) return redirect('/');

        $articulo = Abrasivo::findOrFail($request->id);
        $articulo->comprometido = $request->comprometido;
        $articulo->idusuario = \Auth::user()->id;
        $articulo->save();
    }
    public function eliminarImagen(Request $request){
        if(!$request->ajax()) return redirect('/');

        $directoryName = 'images';
        //Check if the directory already exists.
        if(!is_dir($directoryName)){
            //Directory does not exist, so lets create it.
            mkdir($directoryName, 0777);
        }

        $art= Abrasivo::findOrFail($request->id);
        $img = $art->file;

        if($img != null){
            $image_path = public_path($directoryName).'/'.$img;
            if(file_exists($image_path)){
                unlink($image_path);
                $fileName = null;
            }
        }

        $articulo = Abrasivo::findOrFail($request->id);
        $articulo->file = $fileName;
        $articulo->save();
    }
    public function getCodesSku(Request $request){
        if(!$request->ajax()) return redirect('/');
        $codigos = Abrasivo::select('codigo')->get();

        return ['codigos' => $codigos];
    }
    public function listByCategory(Request $request){

        if(!$request->ajax()) return redirect('/');

        $category_id = $request->id;

        $buscar = $request->buscar;

        if($buscar != ''){
            $articulos = Abrasivo::select('abrasivos.sku')
            ->addSelect(DB::raw('COUNT(abrasivos.sku) as total'))
            ->where([['categorias.id',$category_id],['abrasivos.sku','like', '%'. $buscar . '%']])
            ->groupBy('abrasivos.sku')
            ->paginate(12);
        }else{
            $articulos = Abrasivo::select('abrasivos.sku')
            ->addSelect(DB::raw('COUNT(abrasivos.sku) as total'))
            ->where('categorias.id',$category_id)
            ->groupBy('abrasivos.sku')
            ->paginate(12);
        }

        return [
            'pagination' => [
                'total'         => $articulos->total(),
                'current_page'  => $articulos->currentPage(),
                'per_page'      => $articulos->perPage(),
                'last_page'     => $articulos->lastPage(),
                'from'          => $articulos->firstItem(),
                'to'            => $articulos->lastItem(),
            ],

            'articulos' => $articulos
        ];


    }
    public function listBySku(Request $request){

        if(!$request->ajax()) return redirect('/');

        $sku = $request->sku;

        $estado = $request->estado;
        $bodega = $request->bodega;
        $acabado = $request->acabado;

        $buscar = $request->buscar;
        $criterio = $request->criterio;



        if($estado == 1){
            if($bodega == ''){
                if($acabado == ''){
                    if($buscar == ''){
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([['articulos.sku',$sku],['articulos.stock','>',0]])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.stock','>',0],
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($buscar == ''){
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.stock','>',0],
                            ['articulos.terminado',$acabado]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.stock','>',0],
                            ['articulos.terminado',$acabado],
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if($acabado == ''){
                    if($buscar == ''){
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.stock','>',0],
                            ['articulos.ubicacion',$bodega]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.stock','>',0],
                            ['articulos.ubicacion',$bodega],
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($buscar == ''){
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.stock','>',0],
                            ['articulos.ubicacion',$bodega],
                            ['articulos.terminado',$acabado]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.stock','>',0],
                            ['articulos.ubicacion',$bodega],
                            ['articulos.terminado',$acabado],
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }
                }
            }
        }elseif($estado == 2){
            if($bodega == ''){
                if($acabado == ''){
                    if($buscar == ''){
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.stock','<=',0],
                            ['articulos.condicion','!=',3]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.stock','<=',0],
                            ['articulos.condicion','!=',3],
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($buscar == ''){
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.terminado',$acabado],
                            ['articulos.stock','<=',0],
                            ['articulos.condicion','!=',3]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.terminado',$acabado],
                            ['articulos.stock','<=',0],
                            ['articulos.condicion','!=',3],
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if($acabado == ''){
                    if($buscar == ''){
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.stock','<=',0],
                            ['articulos.condicion','!=',3],
                            ['articulos.ubicacion',$bodega]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.stock','<=',0],
                            ['articulos.condicion','!=',3],
                            ['articulos.ubicacion',$bodega],
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($buscar == ''){
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.stock','<=',0],
                            ['articulos.condicion','!=',3],
                            ['articulos.ubicacion',$bodega]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.stock','<=',0],
                            ['articulos.condicion','!=',3],
                            ['articulos.ubicacion',$bodega],
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }
                }
            }
        }elseif($estado == 3){
            if($bodega == ''){
                if($acabado == ''){
                    if($buscar == ''){
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.condicion',3]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.condicion',3],
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($buscar == ''){
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.condicion',3],
                            ['articulos.terminado',$acabado]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.condicion',3],
                            ['articulos.terminado',$acabado],
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if($acabado == ''){
                    if($buscar == ''){
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.condicion',3],
                            ['articulos.ubicacion',$bodega]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.condicion',3],
                            ['articulos.ubicacion',$bodega],
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($buscar == ''){
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.condicion',3],
                            ['articulos.ubicacion',$bodega],
                            ['articulos.terminado',$acabado]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.condicion',3],
                            ['articulos.ubicacion',$bodega],
                            ['articulos.terminado',$acabado],
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }
                }
            }
        }elseif($estado == 4){
            if($bodega == ''){
                if($acabado == ''){
                    if($buscar == ''){
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.condicion',4]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.condicion',4],
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($buscar == ''){
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.condicion',4],
                            ['articulos.terminado',$acabado]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.condicion',4],
                            ['articulos.terminado',$acabado],
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if($acabado == ''){
                    if($buscar == ''){
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.condicion',4],
                            ['articulos.ubicacion',$bodega]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.condicion',4],
                            ['articulos.ubicacion',$bodega],
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($buscar == ''){
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.condicion',4],
                            ['articulos.ubicacion',$bodega],
                            ['articulos.terminado',$acabado]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.condicion',4],
                            ['articulos.ubicacion',$bodega],
                            ['articulos.terminado',$acabado],
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }
                }
            }
        }

        /* BASE */
        /* $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
        ->leftjoin('users','articulos.idusuario','=','users.id')
        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
            'articulos.comprometido','users.usuario')
        ->where([['articulos.sku',$sku]])
        ->orderBy('articulos.id', 'desc')->paginate(12); */

        return [
            'pagination' => [
                'total'         => $articulos->total(),
                'current_page'  => $articulos->currentPage(),
                'per_page'      => $articulos->perPage(),
                'last_page'     => $articulos->lastPage(),
                'from'          => $articulos->firstItem(),
                'to'            => $articulos->lastItem(),
            ],

            'articulos' => $articulos
        ];
    }
    public function listarExcelFiltros(Request $request){

        $criterio = $request->criterio;
        $buscar = $request->buscar;
        $bodega = $request->bodega;
        $acabado = $request->acabado;
        $zona = $request->zona;

        $mytime = Carbon::now('America/Mexico_City')->format('d-m-Y');

        if($buscar != null){
            if($acabado != null){
                $name = $buscar.'-'.$acabado.'-'.$mytime.'.xlsx';
            }else{
                $name = $buscar.'-'.$mytime.'.xlsx';
            }
        }else {
            $name = 'abrasivos'.'-'.$mytime.'.xlsx';
        }


        /* $name = $buscar.'-'.$acabado.'-'.$mytime.'.xlsx'; */

        return Excel::download(new AbrasivoFiltrosExport($criterio,$buscar,$acabado,$bodega,$zona), $name);


    }
    public function getLinks(Request $request){

        if (!$request->ajax()) return redirect('/');

        $articulo = Abrasivo::findOrFail($request->id);

        $links = $articulo->links()
        ->join('users','users.id','links.user_id')
        ->leftjoin('personas', 'users.id','=','personas.id')
        ->select('links.id','links.user_id as user','links.url','links.updated_at as fecha',
            'links.direction','personas.nombre')
        ->orderBy('links.updated_at','desc')
        ->get();

        return ['links' => $links];

    }
    public function deleteLink(Request $request){
        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();
            $link = Link::findOrFail($request->id);
            $link->delete();
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function cambiarEstadoIngreso(Request $request) {
        if (!$request->ajax()) return redirect('/');
        $detalles = $request->data; //Array detalles
        $articulosEncontrados = [];
        try{
            DB::beginTransaction();

            foreach($detalles as $art) {
                $articulo = Abrasivo::where('codigo',$art)->first();
                array_push($articulosEncontrados, $articulo);
                $articulo->stock >= 1;
                $articulo->condicion = 1;
                $articulo->save();
            }

            DB::commit();

            return [
                'detalles' => $detalles,
                'Articulos' => $articulosEncontrados
            ];

        } catch(Exception $e){
            DB::rollBack();
        }
    }
    public function listarOculto(Request $request){

        $filtro = $request->buscador;

        $articulos = Abrasivo::join('categorias','articulos.idcategoria','=','categorias.id')
        ->leftjoin('users','articulos.idusuario','=','users.id')
        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion',
            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
            'articulos.comprometido','users.usuario')
        ->where([['articulos.sku','LIKE', '%'.$filtro.'%'],['articulos.condicion','==',6]])->get();

        return [
            'articulos' => $articulos,
            'filtro' => $filtro,
        ];
    }
    public function listarArticuloStock(Request $request){

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $bodega = $request->bodega;
        $acabado = $request->acabado;
        $estado = $request->estado;


            if($bodega == ''){
                if($buscar==''){
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                            'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }elseif($bodega == 'Del Musico'){
                if($buscar==''){
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }elseif($bodega == 'Sastres'){
                if($buscar==''){
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }elseif($bodega == 'Mecanicos'){
                if($buscar==''){
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Sastres'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Sastres'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Sastres'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }elseif($bodega == 'Escultores'){
                if($buscar==''){
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Sastres'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Sastres'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Sastres'],
                            ['abrasivos.ubicacion','!=','Oficina'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }elseif($bodega == 'Oficina'){
                if($buscar==''){
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }elseif($bodega == 'San Luis'){
                if($buscar==''){
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }elseif($bodega == 'Aguascalientes'){
                if($buscar==''){
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.stock','>',0],
                            ['abrasivos.condicion',1],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }else{
                    if($acabado == ''){
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.ubicacion',$bodega]
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                        'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.condicion',
                        'abrasivos.stock', 'abrasivos.descripcion', 'abrasivos.file','abrasivos.comprometido')
                        ->where([
                            ['abrasivos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['abrasivos.condicion',1],
                            ['abrasivos.stock','>',0],
                            ['abrasivos.ubicacion',$bodega],
                            ['abrasivos.ubicacion','!=','Del Musico'],['abrasivos.ubicacion','!=','Mecanicos'],
                            ['abrasivos.ubicacion','!=','Sastres'],['abrasivos.ubicacion','!=','Escultores'],
                            ['abrasivos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('abrasivos.id', 'desc')->paginate(12);
                    }
                }
            }


        return [
            'pagination' => [
                'total'         => $articulos->total(),
                'current_page'  => $articulos->currentPage(),
                'per_page'      => $articulos->perPage(),
                'last_page'     => $articulos->lastPage(),
                'from'          => $articulos->firstItem(),
                'to'            => $articulos->lastItem(),
            ],
            'articulos' => $articulos
        ];
    }
}
