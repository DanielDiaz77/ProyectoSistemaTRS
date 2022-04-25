<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Bloque;
use Illuminate\Http\Request;
use App\Link;
use Exception;
use App\Categoria;
use App\User;
use Carbon\Carbon;

class BloqueController extends Controller
{
    public function index(Request $request){
        if(!$request->ajax()) return redirect('/');

        $usarea = \Auth::user()->area;

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $bodega = $request->bodega;
        $acabado = $request->acabado;
        $estado = $request->estado;
        $idcategoria = $request->idcategoria;
        $usrol = \Auth::user()->idrol;

        if($estado == 1){
            if($bodega == ''){
                $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
                ->leftjoin('users','bloques.idusuario','=','users.id')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.precio_venta','bloques.ubicacion',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.ancho',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                    'bloques.comprometido','users.usuario','users.autoing')
                ->where([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.condicion',1],['bloques.ubicacion','!=','Aguascalientes']])
                ->orderBy('bloques.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Bloque::where([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.condicion',1],['bloques.ubicacion','!=','Aguascalientes']])
                ->orderBy('bloques.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Bloque::select(DB::raw('SUM(metros_cubicos) as metros'))
                ->where([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.condicion',1],['bloques.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

            }elseif($bodega == 'nol'){
                $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
                ->leftjoin('users','bloques.idusuario','=','users.id')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.precio_venta','bloques.ubicacion',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.ancho',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                    'bloques.comprometido','users.usuario','users.autoing')
                ->where([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes'],['bloques.condicion',1]])
                ->orderBy('bloques.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Bloque::where([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes'],['bloques.condicion',1]])
                ->orderBy('bloques.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts = Bloque::select(DB::raw('SUM(metros_cubicos) as metros'))
                ->where([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes'],['bloques.condicion',1]])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

            }else{
                $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
                ->leftjoin('users','bloques.idusuario','=','users.id')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.precio_venta','bloques.ubicacion',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.ancho',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                    'bloques.comprometido','users.usuario','users.autoing')
                ->where([['bloques.stock','>',0],['bloques.condicion',1]])
                ->orderBy('bloques.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                ->Categoria($idcategoria)->paginate(12);

                $total = Bloque::where([['bloques.stock','>',0],['bloques.condicion',1]])
                ->orderBy('bloques.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                ->Categoria($idcategoria)->count();

                $sumaMts = Bloque::select(DB::raw('SUM(metros_cubicos) as metros'))
                ->where([['bloques.stock','>',0],['bloques.condicion',1]])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
            }
        }elseif($estado == 2){
            if($bodega == ''){
                $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
                ->leftjoin('users','bloques.idusuario','=','users.id')
                ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','bloques.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->rightjoin('personas','ventas.idcliente','=','personas.id')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.precio_venta','bloques.ubicacion','bloques.ancho',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','personas.nombre as cliente',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                    'ventas.num_comprobante as venta','users.usuario','users.autoing')
                ->where([['bloques.stock','<=',0],['bloques.ubicacion','!=','San Luis'],['ventas.estado','Registrado'],['bloques.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->orderBy('bloques.id', 'desc')
                ->paginate(12);

                $total = Bloque::join('detalle_ventas','detalle_ventas.idarticulo','bloques.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->where([['bloques.stock','<=',0],['bloques.ubicacion','!=','San Luis'],['ventas.estado','Registrado'],['bloques.ubicacion','!=','Aguascalientes']])
                ->orderBy('bloques.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Bloque::join('detalle_ventas','detalle_ventas.idarticulo','bloques.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->select(DB::raw('SUM(metros_cubicos) as metros'))
                ->where([['bloques.stock','<=',0],['bloques.ubicacion','!=','San Luis'],['ventas.estado','Registrado'],['bloques.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
            }elseif($bodega == 'nol'){

                $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
                ->leftjoin('users','bloques.idusuario','=','users.id')
                ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','bloques.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->rightjoin('personas','ventas.idcliente','=','personas.id')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.precio_venta','bloques.ubicacion','bloques.ancho',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','personas.nombre as cliente',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                    'ventas.num_comprobante as venta','users.usuario','users.autoing')
                ->where([['bloques.stock','<=',0],['bloques.ubicacion','!=','San Luis'],
                    ['bloques.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->orderBy('bloques.id', 'desc')
                ->paginate(12);

                $total = Bloque::join('detalle_ventas','detalle_ventas.idarticulo','bloques.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->where([['bloques.stock','<=',0],['bloques.ubicacion','!=','San Luis'],
                    ['bloques.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado']])
                ->orderBy('bloques.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Bloque::join('detalle_ventas','detalle_ventas.idarticulo','bloques.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->select(DB::raw('SUM(metros_cubicos) as metros'))
                ->where([['bloques.stock','<=',0],['bloques.ubicacion','!=','San Luis'],
                    ['bloques.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
            }else{
                $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
                ->leftjoin('users','bloques.idusuario','=','users.id')
                ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','bloques.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->rightjoin('personas','ventas.idcliente','=','personas.id')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.precio_venta','bloques.ubicacion','bloques.ancho',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','personas.nombre as cliente',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                    'ventas.num_comprobante as venta','users.usuario','users.autoing')
                ->where([['bloques.stock','<=',0],['ventas.estado','Registrado']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)
                ->orderBy('bloques.id', 'desc')
                ->paginate(12);

                $total = Bloque::join('detalle_ventas','detalle_ventas.idarticulo','bloques.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->where([['bloques.stock','<=',0],['ventas.estado','Registrado']])
                ->orderBy('bloques.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Ubicacion($bodega)->Categoria($idcategoria)
                ->count();

                $sumaMts =  Bloque::join('detalle_ventas','detalle_ventas.idarticulo','bloques.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->select(DB::raw('SUM(metros_cubicos) as metros'))
                ->where([['bloques.stock','<=',0],['ventas.estado','Registrado']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Ubicacion($bodega)->Categoria($idcategoria)
                ->get();
            }
        }elseif($estado == 3){
            if($bodega == ''){
                $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
                ->leftjoin('users','bloques.idusuario','=','users.id')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.precio_venta','bloques.ubicacion',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.ancho',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                    'bloques.comprometido','users.usuario','users.autoing' )
                ->where([['bloques.condicion',3],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes']])
                ->orderBy('bloques.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Bloque::where([['bloques.condicion',3],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes']])
                ->orderBy('bloques.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Bloque::select(DB::raw('SUM(metros_cubicos) as metros'))
                ->where([['bloques.condicion',3],['bloques.ubicacion','!=','San Luis']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)
                ->Categoria($idcategoria)->get();

            }elseif($bodega == 'nol'){
                $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
                ->leftjoin('users','bloques.idusuario','=','users.id')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.precio_venta','bloques.ubicacion',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.ancho',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                    'bloques.comprometido','users.usuario','users.autoing')
                ->where([['bloques.condicion',3],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes']])
                ->orderBy('bloques.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Bloque::where([['bloques.condicion',3],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes']])
                ->orderBy('bloques.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts = Bloque::select(DB::raw('SUM(metros_cubicos) as metros'))
                ->where([['bloques.condicion',3],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

            }else{
                $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
                ->leftjoin('users','bloques.idusuario','=','users.id')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.precio_venta','bloques.ubicacion',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.ancho',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                    'bloques.comprometido','users.usuario','users.autoing')
                ->where([['bloques.condicion',3]])
                ->orderBy('bloques.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                ->Categoria($idcategoria)->paginate(12);

                $total = Bloque::where([['bloques.condicion',3]])
                ->orderBy('bloques.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                ->Categoria($idcategoria)->count();

                $sumaMts = Bloque::select(DB::raw('SUM(metros_cubicos) as metros'))
                ->where([['bloques.condicion',3]])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
            }
        }elseif($estado == 4){
            if($bodega == ''){
                $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
                ->leftjoin('users','bloques.idusuario','=','users.id')
                ->rightJoin('detalle_traslados','detalle_traslados.idarticulo','bloques.id')
                ->leftjoin('traslados','detalle_traslados.idtraslado','traslados.id')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.precio_venta','bloques.ubicacion',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.ancho',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                    'bloques.comprometido','users.usuario','traslados.num_comprobante	 as traslado','users.autoing')
                ->where([['bloques.condicion',4],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes']])
                ->orderBy('bloques.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Bloque::where([['bloques.condicion',4],['bloques.ubicacion','!=','San Luis']])
                ->orderBy('bloques.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Bloque::select(DB::raw('SUM(metros_cubicos) as metros'))
                ->where([['bloques.condicion',4],['bloques.ubicacion','!=','San Luis']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)
                ->Categoria($idcategoria)->get();

            }elseif($bodega == 'nol'){
                $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
                ->leftjoin('users','bloques.idusuario','=','users.id')
                ->rightJoin('detalle_traslados','detalle_traslados.idarticulo','bloques.id')
                ->leftjoin('traslados','detalle_traslados.idtraslado','traslados.id')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.precio_venta','bloques.ubicacion',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.ancho',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                    'bloques.comprometido','users.usuario','traslados.num_comprobante	 as traslado','users.autoing')
                ->where([['bloques.condicion',4],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes']])
                ->orderBy('bloques.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Bloque::where([['bloques.condicion',4],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes']])
                ->orderBy('bloques.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts = Bloque::select(DB::raw('SUM(metros_cubicos) as metros'))
                ->where([['bloques.condicion',4],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

            }else{
                $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
                ->leftjoin('users','bloques.idusuario','=','users.id')
                ->rightJoin('detalle_traslados','detalle_traslados.idarticulo','bloques.id')
                ->leftjoin('traslados','detalle_traslados.idtraslado','traslados.id')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.precio_venta','bloques.ubicacion',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.ancho',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                    'bloques.comprometido','users.usuario','traslados.num_comprobante	 as traslado','users.autoing')
                ->where([['bloques.condicion',4]])
                ->orderBy('bloques.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                ->Categoria($idcategoria)->paginate(12);

                $total = Bloque::where([['bloques.condicion',4]])
                ->orderBy('bloques.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                ->Categoria($idcategoria)->count();

                $sumaMts = Bloque::select(DB::raw('SUM(metros_cubicos) as metros'))
                ->where([['bloques.condicion',4]])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
            }
        }elseif($estado == 0){
            if($bodega == ''){
                $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
                ->leftjoin('users','bloques.idusuario','=','users.id')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.precio_venta','bloques.ubicacion',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.ancho',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                    'bloques.comprometido','users.usuario','users.autoing')
                ->where([['bloques.condicion',0],['bloques.ubicacion','!=','San Luis']])
                ->orderBy('bloques.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Bloque::where([['bloques.condicion',0],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes']])
                ->orderBy('bloques.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Bloque::select(DB::raw('SUM(metros_cubicos) as metros'))
                ->where([['bloques.condicion',0],['bloques.ubicacion','!=','San Luis']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)
                ->Categoria($idcategoria)->get();

            }elseif($bodega == 'nol'){
                $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
                ->leftjoin('users','bloques.idusuario','=','users.id')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.precio_venta','bloques.ubicacion',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.ancho',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                    'bloques.comprometido','users.usuario','users.autoing')
                ->where([['bloques.condicion',0],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes']])
                ->orderBy('bloques.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Bloque::where([['bloques.condicion',0],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes']])
                ->orderBy('bloques.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts = Bloque::select(DB::raw('SUM(metros_cubicos) as metros'))
                ->where([['bloques.condicion',0],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

            }else{
                $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
                ->leftjoin('users','bloques.idusuario','=','users.id')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.precio_venta','bloques.ubicacion',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.ancho',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                    'bloques.comprometido','users.usuario','users.autoing')
                ->where([['bloques.condicion',0]])
                ->orderBy('bloques.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                ->Categoria($idcategoria)->paginate(12);

                $total = Bloque::where([['bloques.condicion',0]])
                ->orderBy('bloques.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                ->Categoria($idcategoria)->count();

                $sumaMts = Bloque::select(DB::raw('SUM(metros_cubicos) as metros'))
                ->where([['bloques.condicion',0]])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
            }
        }elseif($estado == 6){
            if($bodega == ''){
                $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
                ->leftjoin('users','bloques.idusuario','=','users.id')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.precio_venta','bloques.ubicacion',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.ancho',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                    'bloques.comprometido','users.usuario','users.autoing')
                ->where([['bloques.condicion',6],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes']])
                ->orderBy('bloques.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Bloque::where([['bloques.condicion',6],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes']])
                ->orderBy('bloques.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Bloque::select(DB::raw('SUM(metros_cubicos) as metros'))
                ->where([['bloques.condicion',6],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)
                ->Categoria($idcategoria)->get();

            }elseif($bodega == 'nol'){
                $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
                ->leftjoin('users','bloques.idusuario','=','users.id')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.precio_venta','bloques.ubicacion',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.ancho',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                    'bloques.comprometido','users.usuario','users.autoing')
                ->where([['bloques.condicion',6],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes']])
                ->orderBy('bloques.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Bloque::where([['bloques.condicion',6],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes']])
                ->orderBy('bloques.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts = Bloque::select(DB::raw('SUM(metros_cubicos) as metros'))
                ->where([['bloques.condicion',6],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

            }else{
                $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
                ->leftjoin('users','bloques.idusuario','=','users.id')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.precio_venta','bloques.ubicacion',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.ancho',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                    'bloques.comprometido','users.usuario','users.autoing')
                ->where([['bloques.condicion',6]])
                ->orderBy('bloques.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                ->Categoria($idcategoria)->paginate(12);

                $total = Bloque::where([['bloques.condicion',6]])
                ->orderBy('bloques.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                ->Categoria($idcategoria)->count();

                $sumaMts = Bloque::select(DB::raw('SUM(metros_cubicos) as metros'))
                ->where([['bloques.condicion',6]])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
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
            'sumaMts' => $sumaMts,
            'usrol' => $usrol
        ];
    }
    public function buscarArticulo(Request $request){

        if(!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;

        $articulos = Bloque::where('codigo',$filtro)
        ->select('id','nombre','sku','codigo','origen','contenedor','ubicacion','fecha_llegada',
        'idcategoria','terminado','ancho','file','largo','alto','metros_cubicos','precio_venta')->orderBy('sku','asc')->take(1)->get();
        return ['articulos' => $articulos];

    }
    public function buscarArticuloVenta(Request $request){

        if(!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;

        $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
        ->select('bloques.id','bloques.nombre','bloques.sku','bloques.codigo','bloques.origen',
        'bloques.contenedor','bloques.ubicacion','bloques.fecha_llegada','bloques.idcategoria',
        'bloques.terminado','bloques.ancho','bloques.largo','bloques.alto','bloques.metros_cubicos',
        'bloques.precio_venta','bloques.stock','categorias.nombre as nombre_categoria','categorias.id as idcategoria',
        'bloques.descripcion','bloques.observacion','bloques.file','bloques.comprometido')
        ->where([
            ['codigo',$filtro],
            ['bloques.stock','>',0],
            ['bloques.condicion',1]
        ])
        ->orderBy('bloques.sku','asc')->take(1)->get();
        return ['articulos' => $articulos];

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

        $articulo = new Bloque();
        $articulo->idcategoria      =   $request->idcategoria;
        $articulo->codigo           =   $request->codigo;
        $articulo->sku              =   $request->sku;
        $articulo->nombre           =   $request->nombre;
        $articulo->terminado        =   $request->terminado;
        $articulo->largo            =   $request->largo;
        $articulo->alto             =   $request->alto;
        $articulo->metros_cubicos   =   $request->metros_cubicos;
        $articulo->ancho            =   $request->ancho;
        $articulo->precio_venta     =   $request->precio_venta;
        $articulo->ubicacion        =   $request->ubicacion;
        $articulo->contenedor       =   $request->contenedor;
        $articulo->stock            =   $request->stock;
        $articulo->descripcion      =   $request->descripcion;
        $articulo->observacion      =   $request->observacion;
        $articulo->origen           =   $request->origen;
        $articulo->fecha_llegada    =   $request->fecha_llegada;
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

                $articulo = new Bloque();
                $articulo->idcategoria      =   $art['idcategoria'];
                $articulo->codigo           =   $art['codigo'];
                $articulo->sku              =   $art['sku'];
                $articulo->terminado        =   $art['terminado'];
                $articulo->largo            =   $art['largo'];
                $articulo->alto             =   $art['alto'];
                $articulo->metros_cubicos   =   $art['metros_cubicos'];
                $articulo->ancho            =   $art['ancho'];
                $articulo->precio_venta     =   $art['precio_venta'];
                $articulo->ubicacion        =   $art['ubicacion'];
                $articulo->contenedor       =   $art['contenedor'];
                $articulo->stock            =   $art['stock'];
                $articulo->descripcion      =   $art['descripcion'];
                $articulo->observacion      =   $art['observacion'];
                $articulo->origen           =   $art['origen'];
                $articulo->fecha_llegada    =   $art['fecha_llegada'];
                $articulo->file             =   $fileName;
                $articulo->condicion        =   $request->active;
                $articulo->save();
            }
            DB::commit();

        }catch(Exception $e){

            DB::rollBack();

        }
    }
    public function update(Request $request){
        if(!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $articulo = Bloque::wherefindOrFail($request->id);
            $articulo->idcategoria      =   $request->idcategoria;
            $articulo->codigo           =   $request->codigo;
            $articulo->sku              =   $request->sku;
            $articulo->nombre           =   $request->nombre;
            $articulo->terminado        =   $request->terminado;
            $articulo->largo            =   $request->largo;
            $articulo->alto             =   $request->alto;
            $articulo->metros_cubicos   =   $request->metros_cubicos;
            $articulo->ancho            =   $request->ancho;
            $articulo->precio_venta     =   $request->precio_venta;
            $articulo->ubicacion        =   $request->ubicacion;
            $articulo->contenedor       =   $request->contenedor;
            $articulo->stock            =   $request->stock;
            $articulo->descripcion      =   $request->descripcion;
            $articulo->observacion      =   $request->observacion;
            $articulo->origen           =   $request->origen;
            $articulo->fecha_llegada    =   $request->fecha_llegada;
            $articulo->file             =   $request->file;
            $articulo->condicion        =   '1';
            $articulo->idusuario= \Auth::user()->id;
            $articulo->save();

            $enlaces = $request->enlaces;
            $idusuario = \Auth::user()->id;

            //Recorro todos los elementos
            foreach($enlaces as $ep=>$enl){
                $link = new Link(['user_id' => $idusuario, 'url' => $enl['url'], 'direction' => $enl['direction']]);
                $articulo->links()->save($link);
            }

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function edicionArticulos(Request $request){

        $id = $request->id;
        $articulo = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
        ->join('users','bloques.idusuario','=','users.id')
        ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
        'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
        'bloques.metros_cubicos','bloques.ancho','bloques.precio_venta','bloques.ubicacion','bloques.pulido',
        'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.relice','bloques.detalle',
        'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion','bloques.salida',
        'bloques.comprometido','users.usuario','users.usedit')
        ->where('bloques.id','=',$id)
        ->orderBy('bloques.id', 'desc')->take(1)->get();

        return ['articulo' => $articulo];

    }
    public function desactivar(Request $request){
        if(!$request->ajax()) return redirect('/');
        $articulo = Bloque::wherefindOrFail($request->id);
        $articulo->condicion = '0';
        $articulo->save();

    }
    public function activar(Request $request){
        if(!$request->ajax()) return redirect('/');
        $articulo = Bloque::wherefindOrFail($request->id);
        $articulo->condicion = '1';
        $articulo->save();
    }
    public function ocultar(Request $request){
        if(!$request->ajax()) return redirect('/');
        $articulo = Bloque::wherefindOrFail($request->id);
        $articulo->condicion = '6';
        $articulo->save();
    }
    public function listarArticulo(Request $request){

        if(!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $bodega = $request->bodega;
        $acabado = $request->acabado;

        if($bodega == ''){
            if($buscar==''){
                if($acabado == ''){
                    $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                    ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku',
                        'bloques.nombre','categorias.nombre as nombre_categoria','bloques.terminado','bloques.pulido',
                        'bloques.largo','bloques.alto','bloques.metros_cubicos','bloques.ancho','bloques.relice',
                        'bloques.precio_venta','bloques.ubicacion','bloques.contenedor','bloques.stock','bloques.detalle',
                        'bloques.descripcion','bloques.observacion','bloques.origen','bloques.fecha_llegada','bloques.salida',
                        'bloques.file','bloques.comprometido','bloques.condicion')
                    ->where([
                        ['bloques.stock','>',0],
                        ['bloques.condicion',1]
                    ])
                    ->orderBy('bloques.id', 'desc')->paginate(12);
                }else{
                    $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                    ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku',
                        'bloques.nombre','categorias.nombre as nombre_categoria','bloques.terminado','bloques.pulido',
                        'bloques.largo','bloques.alto','bloques.metros_cubicos','bloques.ancho','bloques.relice',
                        'bloques.precio_venta','bloques.ubicacion','bloques.contenedor','bloques.stock','bloques.detalle',
                        'bloques.descripcion','bloques.observacion','bloques.origen','bloques.fecha_llegada','bloques.salida',
                        'bloques.file','bloques.comprometido','bloques.condicion')
                    ->where([
                        ['bloques.stock','>',0],
                        ['bloques.condicion',1],
                        ['bloques.terminado','like', '%'. $acabado . '%']
                    ])
                    ->orderBy('bloques.id', 'desc')->paginate(12);
                }
            }else{
                if($acabado == ''){
                    $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                    ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku',
                        'bloques.nombre','categorias.nombre as nombre_categoria','bloques.terminado','bloques.pulido',
                        'bloques.largo','bloques.alto','bloques.metros_cubicos','bloques.ancho','bloques.detalle',
                        'bloques.precio_venta','bloques.ubicacion','bloques.contenedor','bloques.stock','bloques.relice',
                        'bloques.descripcion','bloques.observacion','bloques.origen','bloques.fecha_llegada','bloques.salida',
                        'bloques.file','bloques.comprometido','bloques.condicion')
                    ->where([
                        ['bloques.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['bloques.condicion',1],
                        ['bloques.stock','>',0]
                    ])
                    ->orderBy('bloques.id', 'desc')->paginate(12);
                }else{
                    $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                    ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku',
                        'bloques.nombre','categorias.nombre as nombre_categoria','bloques.terminado','bloques.pulido',
                        'bloques.largo','bloques.alto','bloques.metros_cubicos','bloques.ancho','bloques.detalle',
                        'bloques.precio_venta','bloques.ubicacion','bloques.contenedor','bloques.stock','bloques.relice',
                        'bloques.descripcion','bloques.observacion','bloques.origen','bloques.fecha_llegada','bloques.salida',
                        'bloques.file','bloques.comprometido','bloques.condicion')
                    ->where([
                        ['bloques.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['bloques.condicion',1],
                        ['bloques.stock','>',0],
                        ['bloques.terminado','like', '%'. $acabado . '%']
                    ])
                    ->orderBy('bloques.id', 'desc')->paginate(12);
                }
            }
        }else{
            if($buscar==''){
                if($acabado == ''){
                    $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                    ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku',
                        'bloques.nombre','categorias.nombre as nombre_categoria','bloques.terminado','bloques.pulido',
                        'bloques.largo','bloques.alto','bloques.metros_cubicos','bloques.ancho','bloques.detalle',
                        'bloques.precio_venta','bloques.ubicacion','bloques.contenedor','bloques.stock','bloques.relice',
                        'bloques.descripcion','bloques.observacion','bloques.origen','bloques.fecha_llegada','bloques.salida',
                        'bloques.file','bloques.comprometido','bloques.condicion')
                    ->where([
                        ['bloques.stock','>',0],
                        ['bloques.condicion',1],
                        ['bloques.ubicacion',$bodega]
                    ])
                    ->orderBy('bloques.id', 'desc')->paginate(12);
                }else{
                    $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                    ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku',
                        'bloques.nombre','categorias.nombre as nombre_categoria','bloques.terminado','bloques.pulido',
                        'bloques.largo','bloques.alto','bloques.metros_cubicos','bloques.ancho','bloques.detalle',
                        'bloques.precio_venta','bloques.ubicacion','bloques.contenedor','bloques.stock','bloques.relice',
                        'bloques.descripcion','bloques.observacion','bloques.origen','bloques.fecha_llegada','bloques.salida',
                        'bloques.file','bloques.comprometido','bloques.condicion')
                    ->where([
                        ['bloques.stock','>',0],
                        ['bloques.condicion',1],
                        ['bloques.ubicacion',$bodega],
                        ['bloques.terminado','like', '%'. $acabado . '%']
                    ])
                    ->orderBy('bloques.id', 'desc')->paginate(12);
                }
            }else{
                if($acabado == ''){
                    $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                    ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku',
                        'bloques.nombre','categorias.nombre as nombre_categoria','bloques.terminado','bloques.pulido',
                        'bloques.largo','bloques.alto','bloques.metros_cubicos','bloques.ancho','bloques.detalle',
                        'bloques.precio_venta','bloques.ubicacion','bloques.contenedor','bloques.stock','bloques.relice',
                        'bloques.descripcion','bloques.observacion','bloques.origen','bloques.fecha_llegada','bloques.salida',
                        'bloques.file','bloques.comprometido','bloques.condicion')
                    ->where([
                        ['bloques.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['bloques.condicion',1],
                        ['bloques.stock','>',0],
                        ['bloques.ubicacion',$bodega]
                    ])
                    ->orderBy('bloques.id', 'desc')->paginate(12);
                }else{
                    $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                    ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku',
                        'bloques.nombre','categorias.nombre as nombre_categoria','bloques.terminado','bloques.pulido',
                        'bloques.largo','bloques.alto','bloques.metros_cubicos','bloques.ancho','bloques.detalle',
                        'bloques.precio_venta','bloques.ubicacion','bloques.contenedor','bloques.stock','bloques.relice',
                        'bloques.descripcion','bloques.observacion','bloques.origen','bloques.fecha_llegada','bloques.salida',
                        'bloques.file','bloques.comprometido','bloques.condicion')
                    ->where([
                        ['bloques.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['bloques.condicion',1],
                        ['bloques.stock','>',0],
                        ['bloques.ubicacion',$bodega],
                        ['bloques.terminado','like', '%'. $acabado . '%']
                    ])
                    ->orderBy('bloques.id', 'desc')->paginate(12);
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
        $acabado = $request->acabado;
        $idcategoria = $request->idcategoria;

        $area = \Auth::user()->area;

        if($bodega == ''){
            $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
            ->leftjoin('users','bloques.idusuario','=','users.id')
            ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto','bloques.detalle',
                'bloques.metros_cubicos','bloques.ancho','bloques.precio_venta','bloques.ubicacion','bloques.pulido',
                'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.relice',
                'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion','bloques.salida',
                'bloques.comprometido','users.usuario')
            ->where([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes'],
                ['bloques.condicion',1]])
            ->orderBy('bloques.id', 'desc')
            ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

            $total = Bloque::wherewhere([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes'],
                ['bloques.condicion',1]])
            ->orderBy('bloques.id', 'desc')
            ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

        }elseif($bodega == 'nol'){
            $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
            ->leftjoin('users','bloques.idusuario','=','users.id')
            ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto','bloques.detalle',
                'bloques.metros_cubicos','bloques.ancho','bloques.precio_venta','bloques.ubicacion','bloques.pulido',
                'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.relice',
                'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion','bloques.salida',
                'bloques.comprometido','users.usuario')
            ->where([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.ubicacion','!=','Aguascalientes'],
                ['bloques.condicion',1]])
            ->orderBy('bloques.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

            $total = Bloque::wherewhere([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],
            ['bloques.ubicacion','!=','Aguascalientes'],['bloques.condicion',1]])
            ->orderBy('bloques.id', 'desc')
            ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

        }else{
            $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
            ->leftjoin('users','bloques.idusuario','=','users.id')
            ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto','bloques.detalle',
                'bloques.metros_cubicos','bloques.ancho','bloques.precio_venta','bloques.ubicacion','bloques.pulido',
                'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.relice',
                'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion','bloques.salida',
                'bloques.comprometido','users.usuario')
            ->where([['bloques.stock','>',0],['bloques.condicion',1]])
            ->orderBy('bloques.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
            ->Categoria($idcategoria)->paginate(12);

            $total = Bloque::wherewhere([['bloques.stock','>',0],['bloques.condicion',1]])
            ->orderBy('bloques.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
            ->Categoria($idcategoria)->count();
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
    public function listarArticuloCotizado(Request $request){

        if(!$request->ajax()) return redirect('/');

        $area = \Auth::user()->area;

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        if($area == 'GDL'){
            if($buscar==''){
                $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                ->join('detalle_cotizaciones','bloques.id','=','detalle_cotizaciones.idarticulo')
                ->join('cotizaciones','cotizaciones.id','=','detalle_cotizaciones.idcotizacion')
                ->join('personas','personas.id','cotizaciones.idcliente')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.ancho','bloques.precio_venta','bloques.ubicacion',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion','bloques.relice',
                    'bloques.comprometido','cotizaciones.id as idcotizacion','bloques.comprometido','bloques.salida',
                    'cotizaciones.num_comprobante as cotizacion','cotizaciones.estado as estado_cotizacion' ,
                    'personas.nombre as cliente')
                ->where([
                    ['bloques.condicion',1]
                ])
                ->orderBy('bloques.id', 'desc')->paginate(10);
            }else{
                $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                ->join('detalle_cotizaciones','bloques.id','=','detalle_cotizaciones.idarticulo')
                ->join('cotizaciones','cotizaciones.id','=','detalle_cotizaciones.idcotizacion')
                ->join('personas','personas.id','cotizaciones.idcliente')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.ancho','bloques.precio_venta','bloques.ubicacion',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                    'bloques.comprometido','cotizaciones.id as idcotizacion','bloques.comprometido','bloques.relice',
                    'cotizaciones.num_comprobante as cotizacion','cotizaciones.estado as estado_cotizacion' ,'bloques.salida',
                    'personas.nombre as cliente')
                ->where([
                    ['bloques.'.$criterio, 'like', '%'. $buscar . '%'],
                    ['bloques.condicion',1],
                    ['cotizaciones.estado','Registrado']
                ])
                ->orderBy('bloques.id', 'desc')->paginate(10);
            }
        }elseif($area== 'SLP'){

            if($buscar==''){
                $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                ->join('detalle_cotizaciones','bloques.id','=','detalle_cotizaciones.idarticulo')
                ->join('cotizaciones','cotizaciones.id','=','detalle_cotizaciones.idcotizacion')
                ->join('personas','personas.id','cotizaciones.idcliente')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.ancho','bloques.precio_venta','bloques.ubicacion',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion','bloques.relice',
                    'bloques.comprometido','cotizaciones.id as idcotizacion','bloques.comprometido','bloques.salida',
                    'cotizaciones.num_comprobante as cotizacion','cotizaciones.estado as estado_cotizacion' ,
                    'personas.nombre as cliente')
                ->where([
                    ['cotizaciones.estado','Registrado'],
                    ['bloques.condicion',1],
                    ['bloques.ubicacion','San Luis']
                ])
                ->orderBy('bloques.id', 'desc')->paginate(10);
            }else{
                $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                ->join('detalle_cotizaciones','bloques.id','=','detalle_cotizaciones.idarticulo')
                ->join('cotizaciones','cotizaciones.id','=','detalle_cotizaciones.idcotizacion')
                ->join('personas','personas.id','cotizaciones.idcliente')
                ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                    'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                    'bloques.metros_cubicos','bloques.ancho','bloques.precio_venta','bloques.ubicacion',
                    'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion',
                    'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion','bloques.relice',
                    'bloques.comprometido','cotizaciones.id as idcotizacion','bloques.comprometido','bloques.salida',
                    'cotizaciones.num_comprobante as cotizacion','cotizaciones.estado as estado_cotizacion' ,
                    'personas.nombre as cliente')
                ->where([
                    ['bloques.'.$criterio, 'like', '%'. $buscar . '%'],
                    ['bloques.condicion',1],
                    ['cotizaciones.estado','Registrado'],
                    ['bloques.ubicacion','San Luis']
                ])
                ->orderBy('bloques.id', 'desc')->paginate(10);
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
                    $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                    ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                        'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                        'bloques.metros_cubicos','bloques.ancho','bloques.precio_venta','bloques.ubicacion',
                        'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.relice',
                        'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                        'bloques.comprometido','bloques.salida')
                    ->where([
                        ['bloques.condicion',6]
                    ])
                    ->orderBy('bloques.id', 'desc')->paginate(10);

                    $total = Bloque::wherewhere([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.condicion',1]])
                    ->orderBy('bloques.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Bloque::whereselect(DB::raw('SUM(metros_cubicos) as metros'))
                    ->where([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

                }else{
                    $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                    ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                        'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                        'bloques.metros_cubicos','bloques.ancho','bloques.precio_venta','bloques.ubicacion',
                        'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.relice',
                        'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                        'bloques.comprometido','bloques.salida')
                    ->where([
                        ['bloques.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['bloques.condicion',6],
                    ])
                    ->orderBy('bloques.id', 'desc')->paginate(10);


                    $total = Bloque::wherewhere([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.condicion',1]])
                    ->orderBy('bloques.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Bloque::whereselect(DB::raw('SUM(metros_cubicos) as metros'))
                    ->where([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }
            }elseif($area== 'SLP'){

                if($buscar==''){
                    $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                    ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                        'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                        'bloques.metros_cubicos','bloques.ancho','bloques.precio_venta','bloques.ubicacion',
                        'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion','bloques.relice',
                        'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion',
                        'bloques.comprometido','bloques.salida')
                    ->where([

                        ['bloques.condicion',6],
                        ['bloques.ubicacion','San Luis']
                    ])
                    ->orderBy('bloques.id', 'desc')->paginate(10);

                    $total = Bloque::wherewhere([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.condicion',1]])
                    ->orderBy('bloques.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Bloque::whereselect(DB::raw('SUM(metros_cubicos) as metros'))
                    ->where([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }else{
                    $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                    ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                        'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                        'bloques.metros_cubicos','bloques.ancho','bloques.precio_venta','bloques.ubicacion',
                        'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion',
                        'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion','bloques.relice',
                        'bloques.comprometido','bloques.salida')
                    ->where([
                        ['bloques.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['bloques.condicion',6],
                        ['bloques.ubicacion','San Luis']
                    ])
                    ->orderBy('bloques.id', 'desc')->paginate(10);

                    $total = Bloque::wherewhere([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.condicion',1]])
                    ->orderBy('bloques.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Bloque::whereselect(DB::raw('SUM(metros_cubicos) as metros'))
                    ->where([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }
            }
        }else{
            if($area == 'GDL'){
                if($buscar==''){
                    $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                    ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                        'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                        'bloques.metros_cubicos','bloques.ancho','bloques.precio_venta','bloques.ubicacion',
                        'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion',
                        'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion','bloques.relice',
                        'bloques.comprometido','bloques.salida')
                    ->where([
                        ['bloques.condicion',6]
                    ])
                    ->orderBy('bloques.id', 'desc')->paginate(10);


                    $total = Bloque::wherewhere([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.condicion',1]])
                    ->orderBy('bloques.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Bloque::whereselect(DB::raw('SUM(metros_cubicos) as metros'))
                    ->where([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }else{
                    $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                    ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                        'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                        'bloques.metros_cubicos','bloques.ancho','bloques.precio_venta','bloques.ubicacion',
                        'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion',
                        'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion','bloques.relice',
                        'bloques.comprometido','bloques.salida')
                    ->where([
                        ['bloques.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['bloques.condicion',6],
                        ['cotizaciones.estado','Registrado']
                    ])
                    ->orderBy('bloques.id', 'desc')->paginate(10);


                    $total = Bloque::wherewhere([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.condicion',1]])
                    ->orderBy('bloques.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Bloque::whereselect(DB::raw('SUM(metros_cubicos) as metros'))
                    ->where([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }
            }elseif($area== 'SLP'){

                if($buscar==''){
                    $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                    ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                        'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                        'bloques.metros_cubicos','bloques.ancho','bloques.precio_venta','bloques.ubicacion',
                        'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion',
                        'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion','bloques.relice',
                        'bloques.comprometido','bloques.salida')
                    ->where([
                        ['bloques.condicion',6],
                        ['bloques.ubicacion','San Luis']
                    ])
                    ->orderBy('bloques.id', 'desc')->paginate(10);
                }else{
                    $articulos = Bloque::wherejoin('categorias','bloques.idcategoria','=','categorias.id')
                    ->select('bloques.id','bloques.idcategoria','bloques.codigo','bloques.sku','bloques.nombre',
                        'categorias.nombre as nombre_categoria','bloques.terminado','bloques.largo','bloques.alto',
                        'bloques.metros_cubicos','bloques.ancho','bloques.precio_venta','bloques.ubicacion',
                        'bloques.contenedor','bloques.stock','bloques.descripcion','bloques.observacion',
                        'bloques.origen','bloques.fecha_llegada','bloques.file','bloques.condicion','bloques.relice',
                        'bloques.comprometido','bloques.salida')
                    ->where([
                        ['bloques.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['bloques.condicion',6],
                        ['bloques.ubicacion','San Luis']
                    ])
                    ->orderBy('bloques.id', 'desc')->paginate(10);


                    $total = Bloque::wherewhere([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.condicion',1]])
                    ->orderBy('bloques.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Bloque::whereselect(DB::raw('SUM(metros_cubicos) as metros'))
                    ->where([['bloques.stock','>',0],['bloques.ubicacion','!=','San Luis'],['bloques.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
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

        $bodegas = Bloque::wherewhere('condicion',1)
        ->select('ubicacion')->groupBy('ubicacion')->get();
        return ['bodegas' => $bodegas];
    }
    public function listarExcel(Request $request){
        $bodega = $request->bodega;
        $mytime = Carbon::now('America/Mexico_City')->format('d-m-Y');
        return Excel::download(new ArticulosExport($bodega), 'inventario-'.$bodega.'-'.$mytime.'.xlsx');
    }
    public function listarExcelVenta(Request $request){
        $bodega = $request->bodega;
        $mytime = Carbon::now('America/Mexico_City')->format('d-m-Y');
        return Excel::download(new ArticulosVentasExport($bodega), 'ArticulosNoEntregados-'.$bodega.'-'.$mytime.'.xlsx');
    }
    public function listarExcelOculto(Request $request){
        $bodega = $request->bodega;
        $mytime = Carbon::now('America/Mexico_City')->format('d-m-Y');
        return Excel::download(new ArticulosOcultosExport($bodega), 'inventario-'.$bodega.'-'.$mytime.'.xlsx');
    }
    public function cambiarComprometido(Request $request){

        if (!$request->ajax()) return redirect('/');

        $articulo = Bloque::wherefindOrFail($request->id);
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

        $art= Bloque::wherefindOrFail($request->id);
        $img = $art->file;

        if($img != null){
            $image_path = public_path($directoryName).'/'.$img;
            if(file_exists($image_path)){
                unlink($image_path);
                $fileName = null;
            }
        }

        $articulo = Bloque::wherefindOrFail($request->id);
        $articulo->file = $fileName;
        $articulo->save();
    }
    public function getCodesSku(Request $request){
        if(!$request->ajax()) return redirect('/');
        $codigos = Bloque::select('codigo')->get();

        return ['codigos' => $codigos];
    }
    public function listByCategory(Request $request){

        if(!$request->ajax()) return redirect('/');

        $category_id = $request->id;

        $buscar = $request->buscar;

        if($buscar != ''){
            $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
            ->select('bloques.sku')
            ->addSelect(DB::raw('COUNT(articulos.sku) as total'))
            ->where([['categorias.id',$category_id],['bloques.sku','like', '%'. $buscar . '%']])
            ->groupBy('bloques.sku')
            ->paginate(12);
        }else{
            $articulos = Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
            ->select('bloques.sku')
            ->addSelect(DB::raw('COUNT(articulos.sku) as total'))
            ->where('categorias.id',$category_id)
            ->groupBy('bloques.sku')
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([['articulos.sku',$sku],['articulos.stock','>',0]])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.condicion',3]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
                            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion',
                            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                            'articulos.comprometido','users.usuario')
                        ->where([
                            ['articulos.sku',$sku],
                            ['articulos.condicion',4]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
                        $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
                        ->leftjoin('users','articulos.idusuario','=','users.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
        /* $articulos = Bloque::wherejoin('categorias','articulos.idcategoria','=','categorias.id')
        ->leftjoin('users','articulos.idusuario','=','users.id')
        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
            'articulos.metros_cubicos','articulos.ancho','articulos.precio_venta','articulos.ubicacion',
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
            $name = 'articulos'.'-'.$mytime.'.xlsx';
        }


        /* $name = $buscar.'-'.$acabado.'-'.$mytime.'.xlsx'; */

        return Excel::download(new ArticulosFiltrosExport($criterio,$buscar,$acabado,$bodega,$zona), $name);


    }
    public function getLinks(Request $request){

        if (!$request->ajax()) return redirect('/');

        $articulo = Bloque::wherefindOrFail($request->id);

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
                $articulo = Bloque::wherewhere('codigo',$art)->first();
                array_push($articulosEncontrados, $articulo);
                $articulo->stock = 1;
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
}
