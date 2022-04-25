<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Exports\ArticulosVentasExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Exports\ArticulosExport;
use App\Exports\ArticulosFiltrosExport;
use App\Exports\ArticulosOcultosExport;
use Illuminate\Http\Request;
use App\DetalleVenta;
use App\Categoria;
use Carbon\Carbon;
use App\Articulo;
use App\Venta;
use App\User;
use App\Link;
use Exception;

class ArticuloController extends Controller
{
    public function index(Request $request){
        //if(!$request->ajax()) return redirect('/');

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
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->leftjoin('users','articulos.idusuario','=','users.id')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.pulido',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice','articulos.detalle',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
                    'articulos.comprometido','users.usuario','users.autoing')
                ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1],['articulos.ubicacion','!=','Aguascalientes']])
                ->orderBy('articulos.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Articulo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1],['articulos.ubicacion','!=','Aguascalientes']])
                ->orderBy('articulos.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1],['articulos.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

            }elseif($bodega == 'nol'){
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->leftjoin('users','articulos.idusuario','=','users.id')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
                    'articulos.comprometido','users.usuario','users.autoing')
                ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],['articulos.condicion',1]])
                ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Articulo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],['articulos.condicion',1]])
                ->orderBy('articulos.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],['articulos.condicion',1]])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

            }else{
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->leftjoin('users','articulos.idusuario','=','users.id')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
                    'articulos.comprometido','users.usuario','users.autoing')
                ->where([['articulos.stock','>',0],['articulos.condicion',1]])
                ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                ->Categoria($idcategoria)->paginate(12);

                $total = Articulo::where([['articulos.stock','>',0],['articulos.condicion',1]])
                ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                ->Categoria($idcategoria)->count();

                $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['articulos.stock','>',0],['articulos.condicion',1]])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
            }
        }elseif($estado == 2){
            if($bodega == ''){
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->leftjoin('users','articulos.idusuario','=','users.id')
                ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->rightjoin('personas','ventas.idcliente','=','personas.id')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre','articulos.pulido',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.detalle',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.relice',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','personas.nombre as cliente',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
                    'ventas.num_comprobante as venta','users.usuario','users.autoing')
                ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],['ventas.estado','Registrado'],['articulos.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->orderBy('articulos.id', 'desc')
                ->paginate(12);

                $total = Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],['ventas.estado','Registrado'],['articulos.ubicacion','!=','Aguascalientes']])
                ->orderBy('articulos.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],['ventas.estado','Registrado'],['articulos.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
            }elseif($bodega == 'nol'){

                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->leftjoin('users','articulos.idusuario','=','users.id')
                ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->rightjoin('personas','ventas.idcliente','=','personas.id')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre','articulos.pulido',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.detalle',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.relice',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','personas.nombre as cliente',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
                    'ventas.num_comprobante as venta','users.usuario','users.autoing')
                ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],
                    ['articulos.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->orderBy('articulos.id', 'desc')
                ->paginate(12);

                $total = Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],
                    ['articulos.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado']])
                ->orderBy('articulos.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],
                    ['articulos.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
            }else{
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->leftjoin('users','articulos.idusuario','=','users.id')
                ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->rightjoin('personas','ventas.idcliente','=','personas.id')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre','articulos.pulido',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.detalle',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.relice',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','personas.nombre as cliente',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
                    'ventas.num_comprobante as venta','users.usuario','users.autoing')
                ->where([['articulos.stock','<=',0],['ventas.estado','Registrado']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)
                ->orderBy('articulos.id', 'desc')
                ->paginate(12);

                $total = Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->where([['articulos.stock','<=',0],['ventas.estado','Registrado']])
                ->orderBy('articulos.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Ubicacion($bodega)->Categoria($idcategoria)
                ->count();

                $sumaMts =  Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                ->select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['articulos.stock','<=',0],['ventas.estado','Registrado']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Ubicacion($bodega)->Categoria($idcategoria)
                ->get();
            }
        }elseif($estado == 3){
            if($bodega == ''){
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->leftjoin('users','articulos.idusuario','=','users.id')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
                    'articulos.comprometido','users.usuario','users.autoing' )
                ->where([['articulos.condicion',3],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                ->orderBy('articulos.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Articulo::where([['articulos.condicion',3],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                ->orderBy('articulos.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['articulos.condicion',3],['articulos.ubicacion','!=','San Luis']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)
                ->Categoria($idcategoria)->get();

            }elseif($bodega == 'nol'){
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->leftjoin('users','articulos.idusuario','=','users.id')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
                    'articulos.comprometido','users.usuario','users.autoing')
                ->where([['articulos.condicion',3],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Articulo::where([['articulos.condicion',3],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                ->orderBy('articulos.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['articulos.condicion',3],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

            }else{
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->leftjoin('users','articulos.idusuario','=','users.id')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
                    'articulos.comprometido','users.usuario','users.autoing')
                ->where([['articulos.condicion',3]])
                ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                ->Categoria($idcategoria)->paginate(12);

                $total = Articulo::where([['articulos.condicion',3]])
                ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                ->Categoria($idcategoria)->count();

                $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['articulos.condicion',3]])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
            }
        }elseif($estado == 4){
            if($bodega == ''){
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->leftjoin('users','articulos.idusuario','=','users.id')
                ->rightJoin('detalle_traslados','detalle_traslados.idarticulo','articulos.id')
                ->leftjoin('traslados','detalle_traslados.idtraslado','traslados.id')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                    'articulos.comprometido','users.usuario','traslados.num_comprobante	 as traslado','users.autoing')
                ->where([['articulos.condicion',4],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                ->orderBy('articulos.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Articulo::where([['articulos.condicion',4],['articulos.ubicacion','!=','San Luis']])
                ->orderBy('articulos.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['articulos.condicion',4],['articulos.ubicacion','!=','San Luis']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)
                ->Categoria($idcategoria)->get();

            }elseif($bodega == 'nol'){
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->leftjoin('users','articulos.idusuario','=','users.id')
                ->rightJoin('detalle_traslados','detalle_traslados.idarticulo','articulos.id')
                ->leftjoin('traslados','detalle_traslados.idtraslado','traslados.id')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                    'articulos.comprometido','users.usuario','traslados.num_comprobante	 as traslado','users.autoing')
                ->where([['articulos.condicion',4],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Articulo::where([['articulos.condicion',4],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                ->orderBy('articulos.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['articulos.condicion',4],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

            }else{
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->leftjoin('users','articulos.idusuario','=','users.id')
                ->rightJoin('detalle_traslados','detalle_traslados.idarticulo','articulos.id')
                ->leftjoin('traslados','detalle_traslados.idtraslado','traslados.id')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion',
                    'articulos.comprometido','users.usuario','traslados.num_comprobante	 as traslado','users.autoing')
                ->where([['articulos.condicion',4]])
                ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                ->Categoria($idcategoria)->paginate(12);

                $total = Articulo::where([['articulos.condicion',4]])
                ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                ->Categoria($idcategoria)->count();

                $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['articulos.condicion',4]])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
            }
        }elseif($estado == 0){
            if($bodega == ''){
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->leftjoin('users','articulos.idusuario','=','users.id')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
                    'articulos.comprometido','users.usuario','users.autoing')
                ->where([['articulos.condicion',0],['articulos.ubicacion','!=','San Luis']])
                ->orderBy('articulos.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Articulo::where([['articulos.condicion',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                ->orderBy('articulos.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['articulos.condicion',0],['articulos.ubicacion','!=','San Luis']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)
                ->Categoria($idcategoria)->get();

            }elseif($bodega == 'nol'){
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->leftjoin('users','articulos.idusuario','=','users.id')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
                    'articulos.comprometido','users.usuario','users.autoing')
                ->where([['articulos.condicion',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Articulo::where([['articulos.condicion',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                ->orderBy('articulos.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['articulos.condicion',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

            }else{
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->leftjoin('users','articulos.idusuario','=','users.id')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
                    'articulos.comprometido','users.usuario','users.autoing')
                ->where([['articulos.condicion',0]])
                ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                ->Categoria($idcategoria)->paginate(12);

                $total = Articulo::where([['articulos.condicion',0]])
                ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                ->Categoria($idcategoria)->count();

                $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['articulos.condicion',0]])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
            }
        }elseif($estado == 6){
            if($bodega == ''){
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->leftjoin('users','articulos.idusuario','=','users.id')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
                    'articulos.comprometido','users.usuario','users.autoing')
                ->where([['articulos.condicion',6],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                ->orderBy('articulos.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Articulo::where([['articulos.condicion',6],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                ->orderBy('articulos.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['articulos.condicion',6],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)
                ->Categoria($idcategoria)->get();

            }elseif($bodega == 'nol'){
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->leftjoin('users','articulos.idusuario','=','users.id')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
                    'articulos.comprometido','users.usuario','users.autoing')
                ->where([['articulos.condicion',6],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                $total = Articulo::where([['articulos.condicion',6],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                ->orderBy('articulos.id', 'desc')
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['articulos.condicion',6],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

            }else{
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                ->leftjoin('users','articulos.idusuario','=','users.id')
                ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                    'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                    'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                    'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                    'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
                    'articulos.comprometido','users.usuario','users.autoing')
                ->where([['articulos.condicion',6]])
                ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                ->Categoria($idcategoria)->paginate(12);

                $total = Articulo::where([['articulos.condicion',6]])
                ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                ->Categoria($idcategoria)->count();

                $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                ->where([['articulos.condicion',6]])
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

        $articulos = Articulo::where('codigo',$filtro)
        ->select('id','nombre','sku','codigo','origen','contenedor','ubicacion','fecha_llegada',
        'idcategoria','terminado','espedor','file','largo','alto','metros_cuadrados','precio_venta')->orderBy('sku','asc')->take(1)->get();
        return ['articulos' => $articulos];

    }
    public function buscarArticuloVenta(Request $request){

        if(!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;

        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
        ->select('articulos.id','articulos.nombre','articulos.sku','articulos.codigo','articulos.origen',
        'articulos.contenedor','articulos.ubicacion','articulos.fecha_llegada','articulos.idcategoria',
        'articulos.terminado','articulos.espesor','articulos.largo','articulos.alto','articulos.metros_cuadrados',
        'articulos.precio_venta','articulos.stock','categorias.nombre as nombre_categoria','categorias.id as idcategoria',
        'articulos.descripcion','articulos.observacion','articulos.file','articulos.comprometido')
        ->where([
            ['codigo',$filtro],
            ['articulos.stock','>',0],
            ['articulos.condicion',1]
        ])
        ->orderBy('articulos.sku','asc')->take(1)->get();
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

        $articulo = new Articulo();
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
        $articulo->inspeccion       =   '1';
        $articulo->correccion       =   $request->correccion;
        $articulo->save();
    }
    public function updateDetalle(Request $request){
        if(!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $articulo = Articulo::findOrFail($request->id);
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
            $articulo->correccion       =   $request->correccion;
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
    public function update(Request $request){
        if(!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $articulo = Articulo::findOrFail($request->id);
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
            $articulo->correccion       =   $request->correccion;
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
        $articulo = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
        ->join('users','articulos.idusuario','=','users.id')
        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.pulido',
        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice','articulos.detalle',
        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
        'articulos.comprometido','users.usuario','users.usedit')
        ->where('articulos.id','=',$id)
        ->orderBy('articulos.id', 'desc')->take(1)->get();

        return ['articulo' => $articulo];

    }
    public function desactivar(Request $request){
        if(!$request->ajax()) return redirect('/');
        $articulo = Articulo::findOrFail($request->id);
        $articulo->condicion = '0';
        $articulo->save();

    }
    public function activar(Request $request){
        if(!$request->ajax()) return redirect('/');
        $articulo = Articulo::findOrFail($request->id);
        $articulo->condicion = '1';
        $articulo->save();
    }
    public function ocultar(Request $request){
        if(!$request->ajax()) return redirect('/');
        $articulo = Articulo::findOrFail($request->id);
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
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                        'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado','articulos.pulido',
                        'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor','articulos.relice',
                        'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock','articulos.detalle',
                        'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada','articulos.salida',
                        'articulos.file','articulos.comprometido','articulos.condicion')
                    ->where([
                        ['articulos.stock','>',0],
                        ['articulos.condicion',1]
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(12);
                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                        'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado','articulos.pulido',
                        'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor','articulos.relice',
                        'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock','articulos.detalle',
                        'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada','articulos.salida',
                        'articulos.file','articulos.comprometido','articulos.condicion')
                    ->where([
                        ['articulos.stock','>',0],
                        ['articulos.condicion',1],
                        ['articulos.terminado','like', '%'. $acabado . '%']
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(12);
                }
            }else{
                if($acabado == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                        'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado','articulos.pulido',
                        'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor','articulos.detalle',
                        'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock','articulos.relice',
                        'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada','articulos.salida',
                        'articulos.file','articulos.comprometido','articulos.condicion')
                    ->where([
                        ['articulos.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['articulos.condicion',1],
                        ['articulos.stock','>',0]
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(12);
                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                        'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado','articulos.pulido',
                        'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor','articulos.detalle',
                        'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock','articulos.relice',
                        'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada','articulos.salida',
                        'articulos.file','articulos.comprometido','articulos.condicion')
                    ->where([
                        ['articulos.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['articulos.condicion',1],
                        ['articulos.stock','>',0],
                        ['articulos.terminado','like', '%'. $acabado . '%']
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(12);
                }
            }
        }else{
            if($buscar==''){
                if($acabado == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                        'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado','articulos.pulido',
                        'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor','articulos.detalle',
                        'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock','articulos.relice',
                        'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada','articulos.salida',
                        'articulos.file','articulos.comprometido','articulos.condicion')
                    ->where([
                        ['articulos.stock','>',0],
                        ['articulos.condicion',1],
                        ['articulos.ubicacion',$bodega]
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(12);
                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                        'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado','articulos.pulido',
                        'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor','articulos.detalle',
                        'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock','articulos.relice',
                        'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada','articulos.salida',
                        'articulos.file','articulos.comprometido','articulos.condicion')
                    ->where([
                        ['articulos.stock','>',0],
                        ['articulos.condicion',1],
                        ['articulos.ubicacion',$bodega],
                        ['articulos.terminado','like', '%'. $acabado . '%']
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(12);
                }
            }else{
                if($acabado == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                        'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado','articulos.pulido',
                        'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor','articulos.detalle',
                        'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock','articulos.relice',
                        'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada','articulos.salida',
                        'articulos.file','articulos.comprometido','articulos.condicion')
                    ->where([
                        ['articulos.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['articulos.condicion',1],
                        ['articulos.stock','>',0],
                        ['articulos.ubicacion',$bodega]
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(12);
                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                        'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado','articulos.pulido',
                        'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor','articulos.detalle',
                        'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock','articulos.relice',
                        'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada','articulos.salida',
                        'articulos.file','articulos.comprometido','articulos.condicion')
                    ->where([
                        ['articulos.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['articulos.condicion',1],
                        ['articulos.stock','>',0],
                        ['articulos.ubicacion',$bodega],
                        ['articulos.terminado','like', '%'. $acabado . '%']
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(12);
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
            $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
            ->leftjoin('users','articulos.idusuario','=','users.id')
            ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.detalle',
                'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.pulido',
                'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
                'articulos.comprometido','users.usuario')
            ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],
                ['articulos.condicion',1]])
            ->orderBy('articulos.id', 'desc')
            ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

            $total = Articulo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],
                ['articulos.condicion',1]])
            ->orderBy('articulos.id', 'desc')
            ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

        }elseif($bodega == 'nol'){
            $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
            ->leftjoin('users','articulos.idusuario','=','users.id')
            ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.detalle',
                'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.pulido',
                'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
                'articulos.comprometido','users.usuario')
            ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],
                ['articulos.condicion',1]])
            ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

            $total = Articulo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],
            ['articulos.ubicacion','!=','Aguascalientes'],['articulos.condicion',1]])
            ->orderBy('articulos.id', 'desc')
            ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

        }else{
            $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
            ->leftjoin('users','articulos.idusuario','=','users.id')
            ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.detalle',
                'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.pulido',
                'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
                'articulos.comprometido','users.usuario')
            ->where([['articulos.stock','>',0],['articulos.condicion',1]])
            ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
            ->Categoria($idcategoria)->paginate(12);

            $total = Articulo::where([['articulos.stock','>',0],['articulos.condicion',1]])
            ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
            ->Categoria($idcategoria)->count();
        }

        /* if($area == 'GDL'){
            if($bodega == ''){
                if($buscar==''){
                    //
                    if($acabado == ''){
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                            'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado',
                            'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor',
                            'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada',
                            'articulos.file','articulos.comprometido','articulos.condicion')
                        ->where([
                            ['articulos.stock','>',0],
                            ['articulos.condicion',1]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(10);
                    }else{
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                            'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado',
                            'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor',
                            'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada',
                            'articulos.file','articulos.comprometido','articulos.condicion')
                        ->where([
                            ['articulos.stock','>',0],
                            ['articulos.condicion',1],
                            ['articulos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(10);
                    }
                }else{
                    //
                    if($acabado == ''){
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                            'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado',
                            'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor',
                            'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada',
                            'articulos.file','articulos.comprometido','articulos.condicion')
                        ->where([ add_
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['articulos.condicion',1],
                            ['articulos.stock','>',0]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(10);
                    }else{
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                            'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado',
                            'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor',
                            'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada',
                            'articulos.file','articulos.comprometido','articulos.condicion')
                        ->where([
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['articulos.condicion',1],
                            ['articulos.stock','>',0],
                            ['articulos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(10);
                    }
                }
            }else{
                if($buscar==''){
                    //
                    if($acabado == ''){
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                            'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado',
                            'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor',
                            'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada',
                            'articulos.file','articulos.comprometido','articulos.condicion')
                        ->where([
                            ['articulos.stock','>',0],
                            ['articulos.condicion',1],
                            ['articulos.ubicacion',$bodega]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(10);
                    }else{
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                            'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado',
                            'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor',
                            'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada',
                            'articulos.file','articulos.comprometido','articulos.condicion')
                        ->where([
                            ['articulos.stock','>',0],
                            ['articulos.condicion',1],
                            ['articulos.ubicacion',$bodega],
                            ['articulos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(10);
                    }
                }else{
                    //
                    if($acabado == ''){
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                            'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado',
                            'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor',
                            'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada',
                            'articulos.file','articulos.comprometido','articulos.condicion')
                        ->where([
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['articulos.condicion',1],
                            ['articulos.stock','>',0],
                            ['articulos.ubicacion',$bodega]
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(10);
                    }else{
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                            'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado',
                            'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor',
                            'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada',
                            'articulos.file','articulos.comprometido','articulos.condicion')
                        ->where([
                            ['articulos.'.$criterio, 'like', '%'. $buscar . '%'],
                            ['articulos.condicion',1],
                            ['articulos.stock','>',0],
                            ['articulos.ubicacion',$bodega],
                            ['articulos.terminado','like', '%'. $acabado . '%']
                        ])
                        ->orderBy('articulos.id', 'desc')->paginate(10);
                    }
                }
            }
        }elseif($area== 'SLP'){

            if($buscar==''){
                //
                if($acabado == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                        'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado',
                        'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor',
                        'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock',
                        'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada',
                        'articulos.file','articulos.comprometido','articulos.condicion')
                    ->where([
                        ['articulos.stock','>',0],
                        ['articulos.condicion',1],
                        ['articulos.ubicacion','San Luis']
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(10);
                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                        'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado',
                        'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor',
                        'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock',
                        'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada',
                        'articulos.file','articulos.comprometido','articulos.condicion')
                    ->where([
                        ['articulos.stock','>',0],
                        ['articulos.condicion',1],
                        ['articulos.ubicacion','San Luis'],
                        ['articulos.terminado','like', '%'. $acabado . '%']
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(10);
                }
            }else{
                //
                if($acabado == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                        'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado',
                        'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor',
                        'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock',
                        'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada',
                        'articulos.file','articulos.comprometido','articulos.condicion')
                    ->where([
                        ['articulos.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['articulos.condicion',1],
                        ['articulos.stock','>',0],
                        ['articulos.ubicacion','San Luis']
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(10);
                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku',
                        'articulos.nombre','categorias.nombre as nombre_categoria','articulos.terminado',
                        'articulos.largo','articulos.alto','articulos.metros_cuadrados','articulos.espesor',
                        'articulos.precio_venta','articulos.ubicacion','articulos.contenedor','articulos.stock',
                        'articulos.descripcion','articulos.observacion','articulos.origen','articulos.fecha_llegada',
                        'articulos.file','articulos.comprometido','articulos.condicion')
                    ->where([
                        ['articulos.'.$criterio, 'like', '%'. $buscar . '%'],
                        ['articulos.condicion',1],
                        ['articulos.stock','>',0],
                        ['articulos.ubicacion','San Luis'],
                        ['articulos.terminado','like', '%'. $acabado . '%']
                    ])
                    ->orderBy('articulos.id', 'desc')->paginate(10);
                }
            }
        } */

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
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
    public function updateCorte(Request $request){

        if(!$request->ajax()) return redirect('/');

        $nuevoStock = $request->stock -1 ;

        $articulo = Articulo::findOrFail($request->id);

        $articulo->stock = $nuevoStock;
        $articulo->condicion = '3';
        $articulo->observacion = $request->observacion;
        $articulo->save();
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
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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

                    $total = Articulo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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


                    $total = Articulo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }
            }elseif($area== 'SLP'){

                if($buscar==''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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

                    $total = Articulo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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

                    $total = Articulo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }
            }
        }else{
            if($area == 'GDL'){
                if($buscar==''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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


                    $total = Articulo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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


                    $total = Articulo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }
            }elseif($area== 'SLP'){

                if($buscar==''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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


                    $total = Articulo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();


                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',6]])
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

        $bodegas = Articulo::where('condicion',1)
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

        $articulo = Articulo::findOrFail($request->id);
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

        $art= Articulo::findOrFail($request->id);
        $img = $art->file;

        if($img != null){
            $image_path = public_path($directoryName).'/'.$img;
            if(file_exists($image_path)){
                unlink($image_path);
                $fileName = null;
            }
        }

        $articulo = Articulo::findOrFail($request->id);
        $articulo->file = $fileName;
        $articulo->save();
    }
    public function getCodesSku(Request $request){
        if(!$request->ajax()) return redirect('/');
        $codigos = Articulo::select('codigo')->get();

        return ['codigos' => $codigos];
    }
    public function listByCategory(Request $request){

        if(!$request->ajax()) return redirect('/');

        $category_id = $request->id;

        $buscar = $request->buscar;

        if($buscar != ''){
            $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
            ->select('articulos.sku')
            ->addSelect(DB::raw('COUNT(articulos.sku) as total'))
            ->where([['categorias.id',$category_id],['articulos.sku','like', '%'. $buscar . '%']])
            ->groupBy('articulos.sku')
            ->paginate(12);
        }else{
            $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
            ->select('articulos.sku')
            ->addSelect(DB::raw('COUNT(articulos.sku) as total'))
            ->where('categorias.id',$category_id)
            ->groupBy('articulos.sku')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
                        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
        /* $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
            $name = 'articulos'.'-'.$mytime.'.xlsx';
        }


        /* $name = $buscar.'-'.$acabado.'-'.$mytime.'.xlsx'; */

        return Excel::download(new ArticulosFiltrosExport($criterio,$buscar,$acabado,$bodega,$zona), $name);

        /* return [
            'criterio' => $criterio,
            'buscar' => $buscar,
            'bodega' => $bodega,
            'acabado' => $acabado,
            'zona' => $zona
        ]; */
    }
    public function getLinks(Request $request){

        if (!$request->ajax()) return redirect('/');

        $articulo = Articulo::findOrFail($request->id);

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
                $articulo = Articulo::where('codigo',$art)->first();
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
    public function cambiarSalida(Request $request){

        if(!$request->ajax()) return redirect('/');
        $articulo = Articulo::findOrFail($request->id);
        $articulo->salida = '1';
        $articulo->save();
    }
    public function anularSalida(Request $request){
        if(!$request->ajax()) return redirect('/');
        $articulo = Articulo::findOrFail($request->id);
        $articulo->salida = '0';
        $articulo->save();
    }
    public function listarOculto(Request $request){

        $filtro = $request->buscador;

        $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
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
    public function estadoMaterial(Request $request){
        if(!$request->ajax()) return redirect('/');

        $articulo = Articulo::findOrFail($request->id);
        $articulo->relice = '1';
        $articulo->inspeccion ='2';
        $articulo->idusuario = \Auth::user()->id;
        $articulo->save();
    }
    public function malPulido(Request $request){
        if(!$request->ajax()) return redirect('/');

        $articulo = Articulo::findOrFail($request->id);
        $articulo->pulido = '1';
        $articulo->inspeccion = '4';
        $articulo->idusuario = \Auth::user()->id;
        $articulo->save();
    }
    public function cambiarDetalle(Request $request){
        if(!$request->ajax()) return redirect('/');

        $articulo = Articulo::findOrFail($request->id);
        $articulo->detalle= '1';
        $articulo->inspeccion ='3';
        $articulo->idusuario = \Auth::user()->id;
        $articulo->save();
    }
    public function anularRelice(Request $request){
        if(!$request->ajax()) return redirect('/');

        $articulo = Articulo::findorFail($request->id);
        $articulo->relice = '0';
        $articulo->inspeccion ='1';
        $articulo->save();
    }
    public function anularDetalle(Request $request){
        if(!$request->ajax()) return redirect('/');

        $articulo = Articulo::findorFail($request->id);
        $articulo->detalle = '0';
        $articulo->inspeccion ='1';
        $articulo->save();
    }
    public function anularPulido(Request $request){
        if(!$request->ajax()) return redirect('/');

        $articulo = Articulo::findorFail($request->id);
        $articulo->pulido = '0';
        $articulo->inspeccion ='1';
        $articulo->save();
    }
    public function desocultar(Request $request){
        if(!$request->ajax()) return redirect('/');
        $articulo = Articulo::findOrFail($request->id);
        $articulo->condicion = '1';
        $articulo->save();
    }
    public function listarArticuloDetalle(Request $request){
         if(!$request->ajax()) return redirect('/');

        $usarea = \Auth::user()->area;

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $bodega = $request->bodega;
        $acabado = $request->acabado;
        $estado = $request->estado;
        $idcategoria = $request->idcategoria;
        $usrol = \Auth::user()->idrol;
        $filtro = $request->buscador;

        if($filtro == ''){
            if($estado == 1){
                if($bodega == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.pulido',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice','articulos.detalle',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

                }elseif($bodega == 'nol'){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],['articulos.condicion',1]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','>',0],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                    ->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.stock','>',0],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                    ->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.condicion',1]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
                }
            }elseif($estado == 2){
                if($bodega == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->rightjoin('personas','ventas.idcliente','=','personas.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre','articulos.pulido',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.detalle',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.relice',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','personas.nombre as cliente',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'ventas.num_comprobante as venta','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],['ventas.estado','Registrado']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->orderBy('articulos.id', 'desc')
                    ->paginate(12);

                    $total = Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],['ventas.estado','Registrado']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],['ventas.estado','Registrado']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }elseif($bodega == 'nol'){

                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->rightjoin('personas','ventas.idcliente','=','personas.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre','articulos.pulido',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.detalle',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.relice',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','personas.nombre as cliente',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'ventas.num_comprobante as venta','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],
                        ['articulos.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->orderBy('articulos.id', 'desc')
                    ->paginate(12);

                    $total = Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],
                        ['articulos.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],
                        ['articulos.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->rightjoin('personas','ventas.idcliente','=','personas.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre','articulos.pulido',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.detalle',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.relice',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','personas.nombre as cliente',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'ventas.num_comprobante as venta','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','<=',0],['ventas.estado','Registrado']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)
                    ->orderBy('articulos.id', 'desc')
                    ->paginate(12);

                    $total = Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->where([['articulos.stock','<=',0],['ventas.estado','Registrado']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Ubicacion($bodega)->Categoria($idcategoria)
                    ->count();

                    $sumaMts =  Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','<=',0],['ventas.estado','Registrado']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Ubicacion($bodega)->Categoria($idcategoria)
                    ->get();
                }
            }elseif($estado == 3){
                if($bodega == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion' )
                    ->where([['articulos.condicion',3],['articulos.ubicacion','!=','San Luis']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',3],['articulos.ubicacion','!=','San Luis']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',3],['articulos.ubicacion','!=','San Luis']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)
                    ->Categoria($idcategoria)->get();

                }elseif($bodega == 'nol'){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',3],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',3],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',3],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',3]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                    ->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',3]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                    ->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',3]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
                }
            }elseif($estado == 4){
                if($bodega == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->rightJoin('detalle_traslados','detalle_traslados.idarticulo','articulos.id')
                    ->leftjoin('traslados','detalle_traslados.idtraslado','traslados.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','traslados.num_comprobante	 as traslado','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',4],['articulos.ubicacion','!=','San Luis']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',4],['articulos.ubicacion','!=','San Luis']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',4],['articulos.ubicacion','!=','San Luis']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)
                    ->Categoria($idcategoria)->get();

                }elseif($bodega == 'nol'){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->rightJoin('detalle_traslados','detalle_traslados.idarticulo','articulos.id')
                    ->leftjoin('traslados','detalle_traslados.idtraslado','traslados.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','traslados.num_comprobante	 as traslado','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',4],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',4],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',4],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->rightJoin('detalle_traslados','detalle_traslados.idarticulo','articulos.id')
                    ->leftjoin('traslados','detalle_traslados.idtraslado','traslados.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','traslados.num_comprobante	 as traslado','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',4]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                    ->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',4]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                    ->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',4]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
                }
            }elseif($estado == 0){
                if($bodega == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',0],['articulos.ubicacion','!=','San Luis']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',0],['articulos.ubicacion','!=','San Luis']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',0],['articulos.ubicacion','!=','San Luis']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)
                    ->Categoria($idcategoria)->get();

                }elseif($bodega == 'nol'){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',0]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                    ->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',0]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                    ->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',0]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
                }
            }elseif($estado == 6){
                if($bodega == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',6],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',6],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',6],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)
                    ->Categoria($idcategoria)->get();

                }elseif($bodega == 'nol'){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',6],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',6],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',6],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',6]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                    ->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',6]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                    ->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
                }
            }
        }else{
            if($estado == 1){
                if($bodega == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.pulido',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice','articulos.detalle',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1],['articulos.inspeccion','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

                }elseif($bodega == 'nol'){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],['articulos.condicion',1],['articulos.inspeccion','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],['articulos.condicion',1]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice','articulos.inspeccion',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','>',0],['articulos.condicion',1],['articulos.inspeccion','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                    ->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.stock','>',0],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                    ->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.condicion',1]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
                }
            }elseif($estado == 2){
                if($bodega == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->rightjoin('personas','ventas.idcliente','=','personas.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre','articulos.pulido',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.detalle',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.relice',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','personas.nombre as cliente',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'ventas.num_comprobante as venta','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],['ventas.estado','Registrado'],['articulos.inspeccion','LIKE', '%'.$filtro.'%']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->orderBy('articulos.id', 'desc')
                    ->paginate(12);

                    $total = Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],['ventas.estado','Registrado']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],['ventas.estado','Registrado']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }elseif($bodega == 'nol'){

                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->rightjoin('personas','ventas.idcliente','=','personas.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre','articulos.pulido',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.detalle',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.relice',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','personas.nombre as cliente',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'ventas.num_comprobante as venta','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],
                        ['articulos.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado'],['articulos.inspeccion','LIKE', '%'.$filtro.'%']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->orderBy('articulos.id', 'desc')
                    ->paginate(12);

                    $total = Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],
                        ['articulos.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],
                        ['articulos.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->rightjoin('personas','ventas.idcliente','=','personas.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre','articulos.pulido',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.detalle',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.relice',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','personas.nombre as cliente',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'ventas.num_comprobante as venta','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','<=',0],['ventas.estado','Registrado'],['articulos.inspeccion','LIKE', '%'.$filtro.'%']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)
                    ->orderBy('articulos.id', 'desc')
                    ->paginate(12);

                    $total = Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->where([['articulos.stock','<=',0],['ventas.estado','Registrado']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Ubicacion($bodega)->Categoria($idcategoria)
                    ->count();

                    $sumaMts =  Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','<=',0],['ventas.estado','Registrado']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Ubicacion($bodega)->Categoria($idcategoria)
                    ->get();
                }
            }elseif($estado == 3){
                if($bodega == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion' )
                    ->where([['articulos.condicion',3],['articulos.ubicacion','!=','San Luis'],['articulos.inspeccion','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',3],['articulos.ubicacion','!=','San Luis']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',3],['articulos.ubicacion','!=','San Luis']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)
                    ->Categoria($idcategoria)->get();

                }elseif($bodega == 'nol'){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',3],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],['articulos.inspeccion','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',3],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',3],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',3],['articulos.inspeccion','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                    ->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',3]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                    ->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',3]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
                }
            }elseif($estado == 4){
                if($bodega == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->rightJoin('detalle_traslados','detalle_traslados.idarticulo','articulos.id')
                    ->leftjoin('traslados','detalle_traslados.idtraslado','traslados.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','traslados.num_comprobante	 as traslado','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',4],['articulos.ubicacion','!=','San Luis']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',4],['articulos.ubicacion','!=','San Luis']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',4],['articulos.ubicacion','!=','San Luis']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)
                    ->Categoria($idcategoria)->get();

                }elseif($bodega == 'nol'){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->rightJoin('detalle_traslados','detalle_traslados.idarticulo','articulos.id')
                    ->leftjoin('traslados','detalle_traslados.idtraslado','traslados.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','traslados.num_comprobante	 as traslado','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',4],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',4],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',4],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->rightJoin('detalle_traslados','detalle_traslados.idarticulo','articulos.id')
                    ->leftjoin('traslados','detalle_traslados.idtraslado','traslados.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','traslados.num_comprobante	 as traslado','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',4]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                    ->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',4]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                    ->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',4]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
                }
            }elseif($estado == 0){
                if($bodega == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',0],['articulos.ubicacion','!=','San Luis'],['articulos.inspeccion','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',0],['articulos.ubicacion','!=','San Luis']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',0],['articulos.ubicacion','!=','San Luis']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)
                    ->Categoria($idcategoria)->get();

                }elseif($bodega == 'nol'){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],['articulos.inspeccion','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',0],['articulos.inspeccion','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                    ->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',0]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                    ->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',0]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
                }
            }elseif($estado == 6){
                if($bodega == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',6],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],['articulos.inspeccion','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',6],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',6],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)
                    ->Categoria($idcategoria)->get();

                }elseif($bodega == 'nol'){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',6],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],['articulos.inspeccion','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',6],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',6],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.inspeccion',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.condicion',6],['articulos.inspeccion','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                    ->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.condicion',6]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                    ->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.condicion',6]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
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
            'total' => $total,
            'userarea' => $usarea,
            'sumaMts' => $sumaMts,
            'usrol' => $usrol
        ];
    }
    public function listarPulido(Request $request){

        $filtro = $request->buscador;

        $personas = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
        ->leftjoin('users','articulos.idusuario','=','users.id')
        ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
            'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
            'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
            'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice',
            'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida',
            'articulos.comprometido','users.usuario','users.autoing')
        ->where([['articulos.inspeccion','LIKE', '%'.$filtro.'%'],['articulos.active','!=',3]])->get();

        return [
            'personas' => $personas,
            'filtro'=> $filtro,

        ];
    }
    public function listarArticuloActualizado(Request $request){
        //if(!$request->ajax()) return redirect('/');

        $usarea = \Auth::user()->area;

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $bodega = $request->bodega;
        $acabado = $request->acabado;
        $estado = $request->estado;
        $idcategoria = $request->idcategoria;
        $usrol = \Auth::user()->idrol;
        $filtro = $request->buscador;

        if($filtro== ''){
            if($estado == 1){
                if($bodega == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.pulido','articulos.inspeccion',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice','articulos.detalle',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.updated_at as modificado',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1],['articulos.ubicacion','!=','Aguascalientes']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1],['articulos.ubicacion','!=','Aguascalientes']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1],['articulos.ubicacion','!=','Aguascalientes']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

                }elseif($bodega == 'nol'){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice','articulos.inspeccion',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.updated_at as modificado',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],['articulos.condicion',1]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice','articulos.inspeccion',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.updated_at as modificado',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','>',0],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                    ->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.stock','>',0],['articulos.condicion',1]])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                    ->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.condicion',1]])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
                }
            }elseif($estado == 2){
                if($bodega == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->rightjoin('personas','ventas.idcliente','=','personas.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre','articulos.pulido',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.detalle',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.relice',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','personas.nombre as cliente','articulos.inspeccion',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.updated_at as modificado',
                        'ventas.num_comprobante as venta','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],['ventas.estado','Registrado'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->orderBy('articulos.id', 'desc')
                    ->paginate(12);

                    $total = Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],['ventas.estado','Registrado'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],['ventas.estado','Registrado'],['articulos.ubicacion','!=','Aguascalientes']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }elseif($bodega == 'nol'){

                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->rightjoin('personas','ventas.idcliente','=','personas.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre','articulos.pulido',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.detalle',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.relice',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','personas.nombre as cliente','articulos.inspeccion',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.updated_at as modificado',
                        'ventas.num_comprobante as venta','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],
                        ['articulos.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->orderBy('articulos.id', 'desc')
                    ->paginate(12);

                    $total = Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],
                        ['articulos.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],
                        ['articulos.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->rightjoin('personas','ventas.idcliente','=','personas.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre','articulos.pulido',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.detalle',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.relice',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','personas.nombre as cliente','articulos.inspeccion',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.updated_at as modificado',
                        'ventas.num_comprobante as venta','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','<=',0],['ventas.estado','Registrado']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)
                    ->orderBy('articulos.id', 'desc')
                    ->paginate(12);

                    $total = Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->where([['articulos.stock','<=',0],['ventas.estado','Registrado']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Ubicacion($bodega)->Categoria($idcategoria)
                    ->count();

                    $sumaMts =  Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','<=',0],['ventas.estado','Registrado']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Ubicacion($bodega)->Categoria($idcategoria)
                    ->get();
                }
            }
        }else{
            if($estado == 1){
                if($bodega == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.pulido','articulos.inspeccion',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice','articulos.detalle',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.updated_at as modificado',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1],
                    ['articulos.ubicacion','!=','Aguascalientes'],['articulos.idusuario','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1],
                    ['articulos.ubicacion','!=','Aguascalientes'],['articulos.idusuario','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.condicion',1],
                    ['articulos.ubicacion','!=','Aguascalientes'],['articulos.idusuario','LIKE', '%'.$filtro.'%']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

                }elseif($bodega == 'nol'){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice','articulos.inspeccion',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.updated_at as modificado',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],
                    ['articulos.condicion',1],['articulos.idusuario','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes']
                    ,['articulos.condicion',1],['articulos.idusuario','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.ubicacion','!=','San Luis'],['articulos.ubicacion','!=','Aguascalientes'],
                    ['articulos.condicion',1],['articulos.idusuario','LIKE', '%'.$filtro.'%']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();

                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.pulido',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.detalle',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','articulos.relice','articulos.inspeccion',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.updated_at as modificado',
                        'articulos.comprometido','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','>',0],['articulos.condicion',1],['articulos.idusuario','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Ubicacion($bodega)->Terminado($acabado)
                    ->Categoria($idcategoria)->paginate(12);

                    $total = Articulo::where([['articulos.stock','>',0],['articulos.condicion',1],['articulos.idusuario','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)
                    ->Categoria($idcategoria)->count();

                    $sumaMts = Articulo::select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','>',0],['articulos.condicion',1],['articulos.idusuario','LIKE', '%'.$filtro.'%']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)->get();
                }
            }elseif($estado == 2){
                if($bodega == ''){
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->rightjoin('personas','ventas.idcliente','=','personas.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre','articulos.pulido',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.detalle',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.relice',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','personas.nombre as cliente','articulos.inspeccion',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.updated_at as modificado',
                        'ventas.num_comprobante as venta','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],['ventas.estado','Registrado'],
                    ['articulos.ubicacion','!=','Aguascalientes'],['articulos.idusuario','LIKE', '%'.$filtro.'%']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->orderBy('articulos.id', 'desc')
                    ->paginate(12);

                    $total = Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],['ventas.estado','Registrado'],
                    ['articulos.ubicacion','!=','Aguascalientes'],['articulos.idusuario','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],['ventas.estado','Registrado'],
                    ['articulos.ubicacion','!=','Aguascalientes'],['articulos.idusuario','LIKE', '%'.$filtro.'%']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }elseif($bodega == 'nol'){

                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->rightjoin('personas','ventas.idcliente','=','personas.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre','articulos.pulido',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.detalle',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.relice',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','personas.nombre as cliente','articulos.inspeccion',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.updated_at as modificado',
                        'ventas.num_comprobante as venta','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],
                        ['articulos.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado'],['articulos.idusuario','LIKE', '%'.$filtro.'%']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->orderBy('articulos.id', 'desc')
                    ->paginate(12);

                    $total = Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],
                        ['articulos.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado'],['articulos.idusuario','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->count();

                    $sumaMts =  Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','<=',0],['articulos.ubicacion','!=','San Luis'],
                        ['articulos.ubicacion','!=','Aguascalientes'],['ventas.estado','Registrado'],['articulos.idusuario','LIKE', '%'.$filtro.'%']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Categoria($idcategoria)->get();
                }else{
                    $articulos = Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                    ->leftjoin('users','articulos.idusuario','=','users.id')
                    ->rightJoin('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->rightjoin('personas','ventas.idcliente','=','personas.id')
                    ->select('articulos.id','articulos.idcategoria','articulos.codigo','articulos.sku','articulos.nombre','articulos.pulido',
                        'categorias.nombre as nombre_categoria','articulos.terminado','articulos.largo','articulos.alto','articulos.detalle',
                        'articulos.metros_cuadrados','articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.relice',
                        'articulos.contenedor','articulos.stock','articulos.descripcion','articulos.observacion','personas.nombre as cliente','articulos.inspeccion',
                        'articulos.origen','articulos.fecha_llegada','articulos.file','articulos.condicion','articulos.salida','articulos.updated_at as modificado',
                        'ventas.num_comprobante as venta','users.usuario','users.autoing','articulos.correccion')
                    ->where([['articulos.stock','<=',0],['ventas.estado','Registrado'],['articulos.idusuario','LIKE', '%'.$filtro.'%']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Categoria($idcategoria)
                    ->orderBy('articulos.id', 'desc')
                    ->paginate(12);

                    $total = Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->where([['articulos.stock','<=',0],['ventas.estado','Registrado'],['articulos.idusuario','LIKE', '%'.$filtro.'%']])
                    ->orderBy('articulos.id', 'desc')
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Ubicacion($bodega)->Categoria($idcategoria)
                    ->count();

                    $sumaMts =  Articulo::join('detalle_ventas','detalle_ventas.idarticulo','articulos.id')
                    ->leftjoin('ventas','detalle_ventas.idventa','ventas.id')
                    ->select(DB::raw('SUM(metros_cuadrados) as metros'))
                    ->where([['articulos.stock','<=',0],['ventas.estado','Registrado'],['articulos.idusuario','LIKE', '%'.$filtro.'%']])
                    ->Criterio($criterio,$buscar)->Terminado($acabado)->Ubicacion($bodega)->Ubicacion($bodega)->Categoria($idcategoria)
                    ->get();
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
            'total' => $total,
            'userarea' => $usarea,
            'sumaMts' => $sumaMts,
            'usrol' => $usrol
        ];

    }
    public function Detalletraslado(Request $request){

        $articulo = Articulo::join('articulos','detalle_traslados.idarticulo','=','articulos.id')
        ->leftJoin('categorias','articulos.idcategoria','=','categorias.id')
        ->select('detalle_traslados.cantidad','detalle_traslados.ubicacion as ubicacionAntes','detalle_traslados.id',
        'articulos.sku as articulo','articulos.codigo','articulos.espesor','articulos.largo',
        'articulos.alto','articulos.metros_cuadrados','articulos.descripcion','articulos.idcategoria',
        'articulos.terminado','articulos.ubicacion','articulos.file','articulos.origen',
        'categorias.nombre as categoria','articulos.id as idarticulo',
        'articulos.contenedor','articulos.fecha_llegada','articulos.observacion','articulos.condicion')
        ->where('articulos.id')
        ->orderBy('articulos.id','desc')->get();

        return [ 'articulo' => $articulo];
    }

}
