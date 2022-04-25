<?php

namespace App\Exports;

use App\Articulo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ArticulosFiltrosExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $criterio;
    protected $buscar;
    protected $bodega;
    protected $acabado;
    protected $zona;

    public function __construct($criterio,$buscar,$acabado,$bodega,$zona)
    {
        $this->criterio = $criterio;
        $this->buscar = $buscar;
        $this->acabado = $acabado;
        $this->bodega = $bodega;
        $this->zona = $zona;
    }

    public function collection(){

        if($this->zona == 'GDL'){
            if($this->buscar != null){
                if($this->acabado != null){
                    if($this->bodega != null){
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre','articulos.condicion',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.terminado','like', '%'. $this->acabado . '%'],
                                ['articulos.ubicacion',$this->bodega]
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }else{
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre','articulos.condicion',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.terminado','like', '%'. $this->acabado . '%'],
                                ['articulos.ubicacion','!=','San Luis']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }
                }else{
                    if($this->bodega != null){
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre','articulos.condicion',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.ubicacion',$this->bodega]

                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }else{
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre','articulos.condicion',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.ubicacion','!=','San Luis']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }
                }
            }else{
                if($this->acabado != null){
                    if($this->bodega != null){
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre','articulos.condicion',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.terminado','like', '%'. $this->acabado . '%'],
                                ['articulos.ubicacion',$this->bodega]
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }else{
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre','articulos.condicion',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.terminado','like', '%'. $this->acabado . '%'],
                                ['articulos.ubicacion','!=','San Luis']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }
                }else{
                    if($this->bodega != null){
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre','articulos.condicion',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.ubicacion',$this->bodega]
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }else{
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre','articulos.condicion',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.ubicacion','!=','San Luis']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }
                }
            }
        }elseif ($this->zona == 'SLP') {
            if($this->buscar != null){
                if($this->acabado != null){
                    if($this->bodega != null){
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre','articulos.condicion',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.terminado','like', '%'. $this->acabado . '%'],
                                ['articulos.ubicacion','San Luis']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }else{
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre','articulos.condicion',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.terminado','like', '%'. $this->acabado . '%'],
                                ['articulos.ubicacion','San Luis']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }
                }else{
                    if($this->bodega != null){
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre','articulos.condicion',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.ubicacion','San Luis']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }else{
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre','articulos.condicion',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.ubicacion','San Luis']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }
                }
            }else{
                if($this->acabado != null){
                    if($this->bodega != null){
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre','articulos.condicion',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.terminado','like', '%'. $this->acabado . '%'],
                                ['articulos.ubicacion','San Luis']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }else{
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre','articulos.condicion',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.terminado','like', '%'. $this->acabado . '%'],
                                ['articulos.ubicacion','San Luis']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }
                }else{
                    if($this->bodega != null){
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre','articulos.condicion',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.ubicacion','San Luis']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }else{
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre','articulos.condicion',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.ubicacion','San Luis']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }
                }
            }
        }elseif ($this->zona == 'AGS') {
            if($this->buscar != null){
                if($this->acabado != null){
                    if($this->bodega != null){
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.terminado','like', '%'. $this->acabado . '%'],
                                ['articulos.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }else{
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.terminado','like', '%'. $this->acabado . '%'],
                                ['articulos.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }
                }else{
                    if($this->bodega != null){
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }else{
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }
                }
            }else{
                if($this->acabado != null){
                    if($this->bodega != null){
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.terminado','like', '%'. $this->acabado . '%'],
                                ['articulos.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }else{
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.terminado','like', '%'. $this->acabado . '%'],
                                ['articulos.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }
                }else{
                    if($this->bodega != null){
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }else{
                        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
                        ->select(
                            'articulos.id','articulos.codigo','articulos.sku','categorias.nombre',
                            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                            'articulos.fecha_llegada','articulos.comprometido')
                            ->where([
                                ['articulos.stock','>=',1],
                                ['articulos.condicion',1],
                                ['articulos.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('articulos.id', 'asc')->get();
                    }
                }
            }
        }
    }

    public function headings(): array{
        return [
            '#',
            'No° Placa',
            'Codigo',
            'Material',
            'Condicion',
            'Terminado',
            'Largo',
            'Alto',
            'Metros Cuadrados',
            'Espesor',
            'Precio',
            'Ubicacion',
            'Stock',
            'Descripcion',
            'Observacion',
            'Origen',
            'Contenedor',
            'Fecha de llegada',
            'Comprometido'
        ];
    }
}
