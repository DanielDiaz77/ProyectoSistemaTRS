<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ArticulosVentasExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Exports\ArticulosExport;
use App\Exports\JavasFiltrosExport;
use App\Exports\ArticulosOcultosExport;
use App\Exports\JavasExport;
use App\Exports\JavaVentasExport;
use Exception;
use App\Java;
use App\User;
use App\Link;
use Carbon\Carbon;

class JavaController extends Controller
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
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->leftjoin('users','javas.idusuario','=','users.id')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'javas.comprometido','users.usuario')
                ->where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.condicion',1],['javas.ubicacion','!=','Aguascalientes']])
                ->orderBy('javas.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Java::where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.condicion',1],['javas.ubicacion','!=','Aguascalientes']])
                ->orderBy('javas.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.condicion',1],['javas.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

            }elseif($bodega == 'nol'){
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->leftjoin('users','javas.idusuario','=','users.id')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'javas.comprometido','users.usuario')
                ->where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes'],['javas.condicion',1]])
                ->orderBy('javas.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Java::where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes'],['javas.condicion',1]])
                ->orderBy('javas.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts = Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes'],['javas.condicion',1]])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

            }else{
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->leftjoin('users','javas.idusuario','=','users.id')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'javas.comprometido','users.usuario')
                ->where([['javas.stock','>',0],['javas.condicion',1]])
                ->orderBy('javas.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                ->Categoria($idcategoria)->paginate(12);

                $total = Java::where([['javas.stock','>',0],['javas.condicion',1]])
                ->orderBy('javas.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                ->Categoria($idcategoria)->count();

                $sumaMts = Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['javas.stock','>',0],['javas.condicion',1]])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
            }
        }elseif($estado == 2){
            if($bodega == ''){
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->leftjoin('users','javas.idusuario','=','users.id')
                ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','javas.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->rightjoin('personas','ventas.idcliente','=','personas.id')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion','personas.nombre as cliente',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'ventas.num_comprobante as venta','users.usuario')
                ->where([['javas.stock','<=',0],['javas.ubicacion','!=','San Luis'],['ventas.estado','Registrado'],['javas.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->orderBy('javas.id', 'desc')
                ->paginate(12);

                $total = Java::join('detalle_ventas','detalle_ventas.idarticulo','javas.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->where([['javas.stock','<=',0],['javas.ubicacion','!=','San Luis'],['ventas.estado','Registrado'],['javas.ubicacion','!=','Aguascalientes']])
                ->orderBy('javas.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Java::join('detalle_ventas','detalle_ventas.idarticulo','javas.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['javas.stock','<=',0],['javas.ubicacion','!=','San Luis'],['ventas.estado','Registrado'],['javas.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
            }elseif($bodega == 'nol'){

                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->leftjoin('users','javas.idusuario','=','users.id')
                ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','javas.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->rightjoin('personas','ventas.idcliente','=','personas.id')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion','personas.nombre as cliente',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'ventas.num_comprobante as venta','users.usuario')
                ->where([['javas.stock','<=',0],['javas.ubicacion','!=','San Luis'],
                    ['javas.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->orderBy('javas.id', 'desc')
                ->paginate(12);

                $total = Java::join('detalle_ventas','detalle_ventas.idarticulo','javas.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->where([['javas.stock','<=',0],['javas.ubicacion','!=','San Luis'],
                    ['javas.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado']])
                ->orderBy('javas.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Java::join('detalle_ventas','detalle_ventas.idarticulo','javas.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['javas.stock','<=',0],['javas.ubicacion','!=','San Luis'],
                    ['javas.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
            }else{
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->leftjoin('users','javas.idusuario','=','users.id')
                ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','javas.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->rightjoin('personas','ventas.idcliente','=','personas.id')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion','personas.nombre as cliente',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'ventas.num_comprobante as venta','users.usuario')
                ->where([['javas.stock','<=',0],['ventas.estado','Registrado']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)
                ->orderBy('javas.id', 'desc')
                ->paginate(12);

                $total = Java::join('detalle_ventas','detalle_ventas.idarticulo','javas.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->where([['javas.stock','<=',0],['ventas.estado','Registrado']])
                ->orderBy('javas.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Ubicacion($bodega)->Categoria($idcategoria)
                ->count();

                $sumaMts =  Java::join('detalle_ventas','detalle_ventas.idarticulo','javas.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['javas.stock','<=',0],['ventas.estado','Registrado']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Ubicacion($bodega)->Categoria($idcategoria)
                ->get();
            }
        }elseif($estado == 3){
            if($bodega == ''){
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->leftjoin('users','javas.idusuario','=','users.id')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'javas.comprometido','users.usuario' )
                ->where([['javas.condicion',3],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes']])
                ->orderBy('javas.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Java::where([['javas.condicion',3],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes']])
                ->orderBy('javas.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['javas.condicion',3],['javas.ubicacion','!=','San Luis']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)
                ->Categoria($idcategoria)->get();

            }elseif($bodega == 'nol'){
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->leftjoin('users','javas.idusuario','=','users.id')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'javas.comprometido','users.usuario')
                ->where([['javas.condicion',3],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes']])
                ->orderBy('javas.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Java::where([['javas.condicion',3],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes']])
                ->orderBy('javas.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts = Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['javas.condicion',3],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

            }else{
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->leftjoin('users','javas.idusuario','=','users.id')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'javas.comprometido','users.usuario')
                ->where([['javas.condicion',3]])
                ->orderBy('javas.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                ->Categoria($idcategoria)->paginate(12);

                $total = Java::where([['javas.condicion',3]])
                ->orderBy('javas.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                ->Categoria($idcategoria)->count();

                $sumaMts = Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['javas.condicion',3]])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
            }
        }elseif($estado == 4){
            if($bodega == ''){
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->leftjoin('users','javas.idusuario','=','users.id')
                ->rightJoin('detalle_traslados','detalle_traslados.idarticulo','javas.id')
                ->leftjoin('traslados','detalle_traslados.idtraslado','traslados.id')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'javas.comprometido','users.usuario','traslados.num_comprobante	 as traslado')
                ->where([['javas.condicion',4],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes']])
                ->orderBy('javas.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Java::where([['javas.condicion',4],['javas.ubicacion','!=','San Luis']])
                ->orderBy('javas.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['javas.condicion',4],['javas.ubicacion','!=','San Luis']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)
                ->Categoria($idcategoria)->get();

            }elseif($bodega == 'nol'){
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->leftjoin('users','javas.idusuario','=','users.id')
                ->rightJoin('detalle_traslados','detalle_traslados.idarticulo','javas.id')
                ->leftjoin('traslados','detalle_traslados.idtraslado','traslados.id')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'javas.comprometido','users.usuario','traslados.num_comprobante	 as traslado')
                ->where([['javas.condicion',4],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes']])
                ->orderBy('javas.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Java::where([['javas.condicion',4],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes']])
                ->orderBy('javas.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts = Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['javas.condicion',4],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

            }else{
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->leftjoin('users','javas.idusuario','=','users.id')
                ->rightJoin('detalle_traslados','detalle_traslados.idarticulo','javas.id')
                ->leftjoin('traslados','detalle_traslados.idtraslado','traslados.id')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'javas.comprometido','users.usuario','traslados.num_comprobante	 as traslado')
                ->where([['javas.condicion',4]])
                ->orderBy('javas.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                ->Categoria($idcategoria)->paginate(12);

                $total = Java::where([['javas.condicion',4]])
                ->orderBy('javas.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                ->Categoria($idcategoria)->count();

                $sumaMts = Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['javas.condicion',4]])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
            }
        }elseif($estado == 0){
            if($bodega == ''){
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->leftjoin('users','javas.idusuario','=','users.id')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'javas.comprometido','users.usuario')
                ->where([['javas.condicion',0],['javas.ubicacion','!=','San Luis']])
                ->orderBy('javas.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Java::where([['javas.condicion',0],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes']])
                ->orderBy('javas.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['javas.condicion',0],['javas.ubicacion','!=','San Luis']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)
                ->Categoria($idcategoria)->get();

            }elseif($bodega == 'nol'){
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->leftjoin('users','javas.idusuario','=','users.id')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'javas.comprometido','users.usuario')
                ->where([['javas.condicion',0],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes']])
                ->orderBy('javas.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Java::where([['javas.condicion',0],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes']])
                ->orderBy('javas.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts = Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['javas.condicion',0],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

            }else{
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->leftjoin('users','javas.idusuario','=','users.id')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'javas.comprometido','users.usuario')
                ->where([['javas.condicion',0]])
                ->orderBy('javas.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                ->Categoria($idcategoria)->paginate(12);

                $total = Java::where([['javas.condicion',0]])
                ->orderBy('javas.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                ->Categoria($idcategoria)->count();

                $sumaMts = Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['javas.condicion',0]])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
            }
        }elseif($estado == 6){
            if($bodega == ''){
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->leftjoin('users','javas.idusuario','=','users.id')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'javas.comprometido','users.usuario')
                ->where([['javas.condicion',6],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes']])
                ->orderBy('javas.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Java::where([['javas.condicion',6],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes']])
                ->orderBy('javas.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['javas.condicion',6],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)
                ->Categoria($idcategoria)->get();

            }elseif($bodega == 'nol'){
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->leftjoin('users','javas.idusuario','=','users.id')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'javas.comprometido','users.usuario')
                ->where([['javas.condicion',6],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes']])
                ->orderBy('javas.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Java::where([['javas.condicion',6],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes']])
                ->orderBy('javas.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts = Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['javas.condicion',6],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

            }else{
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->leftjoin('users','javas.idusuario','=','users.id')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'javas.comprometido','users.usuario')
                ->where([['javas.condicion',6]])
                ->orderBy('javas.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                ->Categoria($idcategoria)->paginate(12);

                $total = Java::where([['javas.condicion',6]])
                ->orderBy('javas.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                ->Categoria($idcategoria)->count();

                $sumaMts = Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['javas.condicion',6]])
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

        $articulo = new Java();
        $articulo->idcategoria      =   $request->idcategoria;
        $articulo->codigo           =   $request->codigo;
        $articulo->sku              =   $request->sku;
        $articulo->nombre           =   $request->nombre;
        $articulo->terminado        =   $request->terminado;
        $articulo->largo            =   $request->largo;
        $articulo->alto             =   $request->alto;
        $articulo->metros_cuadrados =   $request->metros_cuadrados;
        $articulo->espesor          =   $request->espesor;
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

                $articulo = new Java();
                $articulo->idcategoria      =   $art['idcategoria'];
                $articulo->codigo           =   $art['codigo'];
                $articulo->sku              =   $art['sku'];
                $articulo->terminado        =   $art['terminado'];
                $articulo->largo            =   $art['largo'];
                $articulo->alto             =   $art['alto'];
                $articulo->metros_cuadrados =   $art['metros_cuadrados'];
                $articulo->espesor          =   $art['espesor'];
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
                $articulo->idusuario = \Auth::user()->id;
                $articulo->save();
            }
            DB::commit();

        }catch(Exception $e){

            DB::rollBack();

        }
    }
    public function buscarArticulo(Request $request){

        if(!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;

        $articulos = Java::where('codigo',$filtro)
        ->select('id','nombre','sku','codigo','origen','contenedor','ubicacion','fecha_llegada',
        'idcategoria','terminado','espesor','file','largo','alto','metros_cuadrados','precio_venta')->orderBy('sku','asc')->take(1)->get();
        return ['articulos' => $articulos];

    }
    public function buscarArticuloVenta(Request $request){

        if(!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;

        $articulos = java::join('categorias','javas.idcategoria','=','categorias.id')
        ->select('javas..id','javas.nombre','javas.sku','javas.codigo','javas.origen',
        'javas.contenedor','javas.ubicacion','javas.fecha_llegada','javas.idcategoria',
        'javas.terminado','javas.espesor','javas.largo','javas.alto','javas.metros_cuadrados',
        'javas.precio_venta','javas.stock','categorias.nombre as nombre_categoria','categorias.id as idcategoria',
        'javas.descripcion','javas.observacion','javas.file','javas.comprometido')
        ->where([
            ['codigo',$filtro],
            ['javas.stock','>',0],
            ['javas.condicion',1]
        ])
        ->orderBy('javas.sku','asc')->take(1)->get();
        return ['javas.' => $articulos];

    }
    public function update(Request $request){
        if(!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $articulo = Java::findOrFail($request->id);
            $articulo->idcategoria      =   $request->idcategoria;
            $articulo->codigo           =   $request->codigo;
            $articulo->sku              =   $request->sku;
            $articulo->nombre           =   $request->nombre;
            $articulo->terminado        =   $request->terminado;
            $articulo->largo            =   $request->largo;
            $articulo->alto             =   $request->alto;
            $articulo->metros_cuadrados =   $request->metros_cuadrados;
            $articulo->espesor          =   $request->espesor;
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
    public function desactivar(Request $request){
        if(!$request->ajax()) return redirect('/');
        $articulo = Java::findOrFail($request->id);
        $articulo->condicion = '0';
        $articulo->save();

    }
    public function activar(Request $request){
        if(!$request->ajax()) return redirect('/');
        $articulo = java::findOrFail($request->id);
        $articulo->condicion = '1';
        $articulo->save();
    }
    public function ocultar(Request $request){
        if(!$request->ajax()) return redirect('/');
        $articulo = Java::findOrFail($request->id);
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
                    $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                    ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku',
                        'javas.nombre','categorias.nombre as nombre_categoria','javas.terminado',
                        'javas.largo','javas.alto','javas.metros_cuadrados','javas.espesor',
                        'javas.precio_venta','javas.ubicacion','javas.contenedor','javas.stock',
                        'javas.descripcion','javas.observacion','javas.origen','javas.fecha_llegada',
                        'javas.file','javas.comprometido','javas.condicion')
                    ->where([
                        ['javas.stock','>',0],
                        ['javas.condicion',1]
                    ])
                    ->orderBy('javas.id', 'desc')->paginate(12);
                }else{
                    $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                    ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku',
                        'javas.nombre','categorias.nombre as nombre_categoria','javas.terminado',
                        'javas.largo','javas.alto','javas.metros_cuadrados','javas.espesor',
                        'javas.precio_venta','javas.ubicacion','javas.contenedor','javas.stock',
                        'javas.descripcion','javas.observacion','javas.origen','javas.fecha_llegada',
                        'javas.file','javas.comprometido','javas.condicion')
                    ->where([
                        ['javas.stock','>',0],
                        ['javas.condicion',1],
                        ['javas.terminado','like', '%'. $acabado . '%']
                    ])
                    ->orderBy('javas.id', 'desc')->paginate(12);
                }
            }else{
                if($acabado == ''){
                    $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                    ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku',
                        'javas.nombre','categorias.nombre as nombre_categoria','javas.terminado',
                        'javas.largo','javas.alto','javas.metros_cuadrados','javas.espesor',
                        'javas.precio_venta','javas.ubicacion','javas.contenedor','javas.stock',
                        'javas.descripcion','javas.observacion','javas.origen','javas.fecha_llegada',
                        'javas.file','javas.comprometido','javas.condicion')
                    ->where([
                        ['javas.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['javas.condicion',1],
                        ['javas.stock','>',0]
                    ])
                    ->orderBy('javas.id', 'desc')->paginate(12);
                }else{
                    $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                    ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku',
                        'javas.nombre','categorias.nombre as nombre_categoria','javas.terminado',
                        'javas.largo','javas.alto','javas.metros_cuadrados','javas.espesor',
                        'javas.precio_venta','javas.ubicacion','javas.contenedor','javas.stock',
                        'javas.descripcion','javas.observacion','javas.origen','javas.fecha_llegada',
                        'javas.file','javas.comprometido','javas.condicion')
                    ->where([
                        ['javas.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['javas.condicion',1],
                        ['javas.stock','>',0],
                        ['javas.terminado','like', '%'. $acabado . '%']
                    ])
                    ->orderBy('javas.id', 'desc')->paginate(12);
                }
            }
        }else{
            if($buscar==''){
                if($acabado == ''){
                    $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                    ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku',
                        'javas.nombre','categorias.nombre as nombre_categoria','javas.terminado',
                        'javas.largo','javas.alto','javas.metros_cuadrados','javas.espesor',
                        'javas.precio_venta','javas.ubicacion','javas.contenedor','javas.stock',
                        'javas.descripcion','javas.observacion','javas.origen','javas.fecha_llegada',
                        'javas.file','javas.comprometido','javas.condicion')
                    ->where([
                        ['javas.stock','>',0],
                        ['javas.condicion',1],
                        ['javas.ubicacion',$bodega]
                    ])
                    ->orderBy('javas.id', 'desc')->paginate(12);
                }else{
                    $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                    ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku',
                        'javas.nombre','categorias.nombre as nombre_categoria','javas.terminado',
                        'javas.largo','javas.alto','javas.metros_cuadrados','javas.espesor',
                        'javas.precio_venta','javas.ubicacion','javas.contenedor','javas.stock',
                        'javas.descripcion','javas.observacion','javas.origen','javas.fecha_llegada',
                        'javas.file','javas.comprometido','javas.condicion')
                    ->where([
                        ['javas.stock','>',0],
                        ['javas.condicion',1],
                        ['javas.ubicacion',$bodega],
                        ['javas.terminado','like', '%'. $acabado . '%']
                    ])
                    ->orderBy('javas.id', 'desc')->paginate(12);
                }
            }else{
                if($acabado == ''){
                    $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                    ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku',
                        'javas.nombre','categorias.nombre as nombre_categoria','javas.terminado',
                        'javas.largo','javas.alto','javas.metros_cuadrados','javas.espesor',
                        'javas.precio_venta','javas.ubicacion','javas.contenedor','javas.stock',
                        'javas.descripcion','javas.observacion','javas.origen','javas.fecha_llegada',
                        'javas.file','javas.comprometido','javas.condicion')
                    ->where([
                        ['javas.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['javas.condicion',1],
                        ['javas.stock','>',0],
                        ['javas.ubicacion',$bodega]
                    ])
                    ->orderBy('javas.id', 'desc')->paginate(12);
                }else{
                    $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                    ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku',
                        'javas.nombre','categorias.nombre as nombre_categoria','javas.terminado',
                        'javas.largo','javas.alto','javas.metros_cuadrados','javas.espesor',
                        'javas.precio_venta','javas.ubicacion','javas.contenedor','javas.stock',
                        'javas.descripcion','javas.observacion','javas.origen','javas.fecha_llegada',
                        'javas.file','javas.comprometido','javas.condicion')
                    ->where([
                        ['javas.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['javas.condicion',1],
                        ['javas.stock','>',0],
                        ['javas.ubicacion',$bodega],
                        ['javas.terminado','like', '%'. $acabado . '%']
                    ])
                    ->orderBy('javas.id', 'desc')->paginate(12);
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
            $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
            ->leftjoin('users','javas.idusuario','=','users.id')
            ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                'javas.comprometido','users.usuario')
            ->where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes'],
                ['javas.condicion',1]])
            ->orderBy('javas.id', 'desc')
            ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

            $total = Java::where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes'],
                ['javas.condicion',1]])
            ->orderBy('javas.id', 'desc')
            ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

        }elseif($bodega == 'nol'){
            $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
            ->leftjoin('users','javas.idusuario','=','users.id')
            ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                'javas.comprometido','users.usuario')
            ->where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.ubicacion','!=','Aguascalientes'],
                ['javas.condicion',1]])
            ->orderBy('javas.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

            $total = Java::where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],
            ['javas.ubicacion','!=','Aguascalientes'],['javas.condicion',1]])
            ->orderBy('javas.id', 'desc')
            ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

        }else{
            $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
            ->leftjoin('users','javas.idusuario','=','users.id')
            ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                'javas.comprometido','users.usuario')
            ->where([['javas.stock','>',0],['javas.condicion',1]])
            ->orderBy('javas.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
            ->Categoria($idcategoria)->paginate(12);

            $total = Java::where([['javas.stock','>',0],['javas.condicion',1]])
            ->orderBy('javas.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
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
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->join('detalle_cotizaciones','javas.id','=','detalle_cotizaciones.idarticulo')
                ->join('cotizaciones','cotizaciones.id','=','detalle_cotizaciones.idcotizacion')
                ->join('personas','personas.id','cotizaciones.idcliente')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'javas.comprometido','cotizaciones.id as idcotizacion',
                    'cotizaciones.num_comprobante as cotizacion','cotizaciones.estado as estado_cotizacion' ,
                    'personas.nombre as cliente')
                ->where([
                    ['javas.condicion',1]
                ])
                ->orderBy('javas.id', 'desc')->paginate(10);
            }else{
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->join('detalle_cotizaciones','javas.id','=','detalle_cotizaciones.idarticulo')
                ->join('cotizaciones','cotizaciones.id','=','detalle_cotizaciones.idcotizacion')
                ->join('personas','personas.id','cotizaciones.idcliente')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'javas.comprometido','cotizaciones.id as idcotizacion',
                    'cotizaciones.num_comprobante as cotizacion','cotizaciones.estado as estado_cotizacion',
                    'personas.nombre as cliente')
                ->where([
                    ['javas.'.$criterio, 'like', '%'. $buscar . '%'],
                    ['javas.condicion',1],
                    ['cotizaciones.estado','Registrado']
                ])
                ->orderBy('javas.id', 'desc')->paginate(10);
            }
        }elseif($area== 'SLP'){

            if($buscar==''){
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->join('detalle_cotizaciones','javas.id','=','detalle_cotizaciones.idarticulo')
                ->join('cotizaciones','cotizaciones.id','=','detalle_cotizaciones.idcotizacion')
                ->join('personas','personas.id','cotizaciones.idcliente')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'javas.comprometido','cotizaciones.id as idcotizacion',
                    'cotizaciones.num_comprobante as cotizacion','cotizaciones.estado as estado_cotizacion' ,
                    'personas.nombre as cliente')
                ->where([
                    ['cotizaciones.estado','Registrado'],
                    ['javas.condicion',1],
                    ['javas.ubicacion','San Luis']
                ])
                ->orderBy('javas.id', 'desc')->paginate(10);
            }else{
                $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                ->join('detalle_cotizaciones','javas.id','=','detalle_cotizaciones.idarticulo')
                ->join('cotizaciones','cotizaciones.id','=','detalle_cotizaciones.idcotizacion')
                ->join('personas','personas.id','cotizaciones.idcliente')
                ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                    'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                    'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                    'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                    'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                    'javas.comprometido','cotizaciones.id as idcotizacion',
                    'cotizaciones.num_comprobante as cotizacion','cotizaciones.estado as estado_cotizacion' ,
                    'personas.nombre as cliente')
                ->where([
                    ['javas.'.$criterio, 'like', '%'. $buscar . '%'],
                    ['javas.condicion',1],
                    ['cotizaciones.estado','Registrado'],
                    ['javas.ubicacion','San Luis']
                ])
                ->orderBy('javas.id', 'desc')->paginate(10);
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

        //if(!$request->ajax()) return redirect('/');
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
                    $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                    ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                        'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                        'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                        'javas.contenedor','javas.stock','javas.descripcion','javas.observacion','javas.relice',
                        'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                        'javas.comprometido','javas.salida')
                    ->where([
                        ['javas.condicion',6]
                    ])
                    ->orderBy('javas.id', 'desc')->paginate(10);

                    $total = Java::where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.condicion',1]])
                    ->orderBy('javas.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

                }else{
                    $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                    ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                        'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                        'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                        'javas.contenedor','javas.stock','javas.descripcion','javas.observacion','javas.relice',
                        'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                        'javas.comprometido','javas.salida')
                    ->where([
                        ['javas.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['javas.condicion',6],
                    ])
                    ->orderBy('javas.id', 'desc')->paginate(10);


                    $total = Java::where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.condicion',1]])
                    ->orderBy('javas.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }
            }elseif($area== 'SLP'){

                if($buscar==''){
                    $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                    ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                        'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                        'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                        'javas.contenedor','javas.stock','javas.descripcion','javas.observacion','javas.relice',
                        'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                        'javas.comprometido','javas.salida')
                    ->where([

                        ['javas.condicion',6],
                        ['javas.ubicacion','San Luis']
                    ])
                    ->orderBy('javas.id', 'desc')->paginate(10);

                    $total = Java::where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.condicion',1]])
                    ->orderBy('javas.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }else{
                    $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                    ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                        'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                        'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                        'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                        'javas.origen','javas.fecha_llegada','javas.file','javas.condicion','javas.relice',
                        'javas.comprometido','javas.salida')
                    ->where([
                        ['javas.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['javas.condicion',6],
                        ['javas.ubicacion','San Luis']
                    ])
                    ->orderBy('javas.id', 'desc')->paginate(10);

                    $total = Java::where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.condicion',1]])
                    ->orderBy('javas.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }
            }
        }else{
            if($area == 'GDL'){
                if($buscar==''){
                    $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                    ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                        'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                        'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                        'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                        'javas.origen','javas.fecha_llegada','javas.file','javas.condicion','javas.relice',
                        'javas.comprometido','javas.salida')
                    ->where([
                        ['javas.condicion',6]
                    ])
                    ->orderBy('javas.id', 'desc')->paginate(10);


                    $total = Java::where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.condicion',1]])
                    ->orderBy('javas.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Java::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }else{
                    $articulos = Articulo::join('categorias','javas.idcategoria','=','categorias.id')
                    ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                        'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                        'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                        'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                        'javas.origen','javas.fecha_llegada','javas.file','javas.condicion','javas.relice',
                        'javas.comprometido','javas.salida')
                    ->where([
                        ['javas.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['javas.condicion',6],
                        ['cotizaciones.estado','Registrado']
                    ])
                    ->orderBy('javas.id', 'desc')->paginate(10);


                    $total = Articulo::where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.condicion',1]])
                    ->orderBy('javas.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }
            }elseif($area== 'SLP'){

                if($buscar==''){
                    $articulos = Articulo::join('categorias','javas.idcategoria','=','categorias.id')
                    ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                        'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                        'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                        'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                        'javas.origen','javas.fecha_llegada','javas.file','javas.condicion','javas.relice',
                        'javas.comprometido','javas.salida')
                    ->where([
                        ['javas.condicion',6],
                        ['javas.ubicacion','San Luis']
                    ])
                    ->orderBy('javas.id', 'desc')->paginate(10);
                }else{
                    $articulos = Articulo::join('categorias','javas.idcategoria','=','categorias.id')
                    ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                        'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                        'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                        'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                        'javas.origen','javas.fecha_llegada','javas.file','javas.condicion','javas.relice',
                        'javas.comprometido','javas.salida')
                    ->where([
                        ['javas.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['javas.condicion',6],
                        ['javas.ubicacion','San Luis']
                    ])
                    ->orderBy('javas.id', 'desc')->paginate(10);


                    $total = Articulo::where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.condicion',1]])
                    ->orderBy('javas.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['javas.stock','>',0],['javas.ubicacion','!=','San Luis'],['javas.condicion',6]])
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

        $bodegas = Java::where('condicion',1)
        ->select('ubicacion')->groupBy('ubicacion')->get();
        return ['bodegas' => $bodegas];
    }
    public function listarExcel(Request $request){
        $bodega = $request->bodega;
        $mytime = Carbon::now('America/Mexico_City')->format('d-m-Y');
        return Excel::download(new JavasExport($bodega), 'inventario-'.$bodega.'-'.$mytime.'.xlsx');
    }
    public function listarExcelVenta(Request $request){
        $bodega = $request->bodega;
        $mytime = Carbon::now('America/Mexico_City')->format('d-m-Y');
        return Excel::download(new JavaVentasExport($bodega), 'javasNoEntregados-'.$bodega.'-'.$mytime.'.xlsx');
    }
    public function listarExcelOculto(Request $request){
        $bodega = $request->bodega;
        $mytime = Carbon::now('America/Mexico_City')->format('d-m-Y');
        return Excel::download(new ArticulosOcultosExport($bodega), 'inventario-'.$bodega.'-'.$mytime.'.xlsx');
    }
    public function cambiarComprometido(Request $request){

        if (!$request->ajax()) return redirect('/');

        $articulo = Java::findOrFail($request->id);
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

        $art= Java::findOrFail($request->id);
        $img = $art->file;

        if($img != null){
            $image_path = public_path($directoryName).'/'.$img;
            if(file_exists($image_path)){
                unlink($image_path);
                $fileName = null;
            }
        }

        $articulo = Java::findOrFail($request->id);
        $articulo->file = $fileName;
        $articulo->save();
    }
    public function getCodesSku(Request $request){
        //if(!$request->ajax()) return redirect('/');
        $codigos = Java::select('codigo')->get();

        return ['codigos' => $codigos];
    }
    public function listByCategory(Request $request){

        if(!$request->ajax()) return redirect('/');

        $category_id = $request->id;

        $buscar = $request->buscar;

        if($buscar != ''){
            $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
            ->select('javas.sku')
            ->addSelect(DB::raw('COUNT(javas.sku) as total'))
            ->where([['categorias.id',$category_id],['javas.sku','like', '%'. $buscar . '%']])
            ->groupBy('javas.sku')
            ->paginate(12);
        }else{
            $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
            ->select('javas.sku')
            ->addSelect(DB::raw('COUNT(javs.sku) as total'))
            ->where('categorias.id',$category_id)
            ->groupBy('javas.sku')
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
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([['javas.sku',$sku],['javas.stock','>',0]])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.stock','>',0],
                            ['javas.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }
                }else{
                    if($buscar == ''){
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.stock','>',0],
                            ['javas.terminado',$acabado]
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.stock','>',0],
                            ['javas.terminado',$acabado],
                            ['javas.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if($acabado == ''){
                    if($buscar == ''){
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.stock','>',0],
                            ['javas.ubicacion',$bodega]
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.stock','>',0],
                            ['javas.ubicacion',$bodega],
                            ['javas.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }
                }else{
                    if($buscar == ''){
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.stock','>',0],
                            ['javas.ubicacion',$bodega],
                            ['javas.terminado',$acabado]
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.stock','>',0],
                            ['javas.ubicacion',$bodega],
                            ['javas.terminado',$acabado],
                            ['javas.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }
                }
            }
        }elseif($estado == 2){
            if($bodega == ''){
                if($acabado == ''){
                    if($buscar == ''){
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.stock','<=',0],
                            ['javas.condicion','!=',3]
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.stock','<=',0],
                            ['javas.condicion','!=',3],
                            ['javas.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }
                }else{
                    if($buscar == ''){
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.terminado',$acabado],
                            ['javas.stock','<=',0],
                            ['javas.condicion','!=',3]
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.terminado',$acabado],
                            ['javas.stock','<=',0],
                            ['javas.condicion','!=',3],
                            ['javas.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if($acabado == ''){
                    if($buscar == ''){
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.stock','<=',0],
                            ['javas.condicion','!=',3],
                            ['javas.ubicacion',$bodega]
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.stock','<=',0],
                            ['javas.condicion','!=',3],
                            ['javas.ubicacion',$bodega],
                            ['javas.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }
                }else{
                    if($buscar == ''){
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.stock','<=',0],
                            ['javas.condicion','!=',3],
                            ['javas.ubicacion',$bodega]
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.stock','<=',0],
                            ['javas.condicion','!=',3],
                            ['javas.ubicacion',$bodega],
                            ['javas.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }
                }
            }
        }elseif($estado == 3){
            if($bodega == ''){
                if($acabado == ''){
                    if($buscar == ''){
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.condicion',3]
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.condicion',3],
                            ['javas.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }
                }else{
                    if($buscar == ''){
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.condicion',3],
                            ['javas.terminado',$acabado]
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.condicion',3],
                            ['javas.terminado',$acabado],
                            ['javas.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if($acabado == ''){
                    if($buscar == ''){
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.condicion',3],
                            ['javas.ubicacion',$bodega]
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.condicion',3],
                            ['javas.ubicacion',$bodega],
                            ['javas.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }
                }else{
                    if($buscar == ''){
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.condicion',3],
                            ['javas.ubicacion',$bodega],
                            ['javas.terminado',$acabado]
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.condicion',3],
                            ['javas.ubicacion',$bodega],
                            ['javas.terminado',$acabado],
                            ['javas.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }
                }
            }
        }elseif($estado == 4){
            if($bodega == ''){
                if($acabado == ''){
                    if($buscar == ''){
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.condicion',4]
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.condicion',4],
                            ['javas.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }
                }else{
                    if($buscar == ''){
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.condicion',4],
                            ['javas.terminado',$acabado]
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.condicion',4],
                            ['javas.terminado',$acabado],
                            ['javas.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }
                }
            }else{
                if($acabado == ''){
                    if($buscar == ''){
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.condicion',4],
                            ['javas.ubicacion',$bodega]
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.condicion',4],
                            ['javas.ubicacion',$bodega],
                            ['javas.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }
                }else{
                    if($buscar == ''){
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.condicion',4],
                            ['javas.ubicacion',$bodega],
                            ['javas.terminado',$acabado]
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }else{
                        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->leftjoin('users','javas.idusuario','=','users.id')
                        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
                            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
                            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
                            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
                            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion',
                            'javas.comprometido','users.usuario')
                        ->where([
                            ['javas.sku',$sku],
                            ['javas.condicion',4],
                            ['javas.ubicacion',$bodega],
                            ['javas.terminado',$acabado],
                            ['javas.'.$criterio, 'like', '%'. $buscar . '%']
                        ])
                        ->orderBy('javas.id', 'desc')->paginate(12);
                    }
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

        return Excel::download(new JavasFiltrosExport($criterio,$buscar,$acabado,$bodega,$zona), $name);
    }
    public function getLinks(Request $request){

        if (!$request->ajax()) return redirect('/');

        $articulo = Java::findOrFail($request->id);

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
                $articulo = Java::where('codigo',$art)->first();
                array_push($articulosEncontrados, $articulo);
                $articulo->stock >= 1;
                $articulo->condicion = 1;
                $articulo->save();
            }

            DB::commit();

            return [
                'detalles' => $detalles,
                'articulos' => $articulosEncontrados
            ];

        } catch(Exception $e){
            DB::rollBack();
        }
    }
    public function listarOculto(Request $request){

        $filtro = $request->buscador;

        $articulos = Java::join('categorias','javas.idcategoria','=','categorias.id')
        ->leftjoin('users','javas.idusuario','=','users.id')
        ->select('javas.id','javas.idcategoria','javas.codigo','javas.sku','javas.nombre',
            'categorias.nombre as nombre_categoria','javas.terminado','javas.largo','javas.alto',
            'javas.metros_cuadrados','javas.espesor','javas.precio_venta','javas.ubicacion',
            'javas.contenedor','javas.stock','javas.descripcion','javas.observacion',
            'javas.origen','javas.fecha_llegada','javas.file','javas.condicion','javas.salida',
            'javas.comprometido','users.usuario')
        ->where([['javas.sku','LIKE', '%'.$filtro.'%'],['javas.condicion','==',6]])->get();

        return [
            'articulos' => $articulos,
            'filtro' => $filtro,
        ];
    }
}
