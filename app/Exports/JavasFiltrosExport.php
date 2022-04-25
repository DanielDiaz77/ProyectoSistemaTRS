<?php

namespace App\Exports;

use App\Java;
use Maatwebsite\Excel\Concerns\FromCollection;

class JavasFiltrosExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){

        if($this->zona == 'GDL'){
            if($this->buscar != null){
                if($this->acabado != null){
                    if($this->bodega != null){
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['javas.stock','>=',1],
                                ['javas.terminado','like', '%'. $this->acabado . '%'],
                                ['javas.ubicacion',$this->bodega]
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }else{
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['javas.stock','>=',1],
                                ['javas.terminado','like', '%'. $this->acabado . '%'],
                                ['javas.ubicacion','!=','San Luis']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }
                }else{
                    if($this->bodega != null){
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['javas.stock','>=',1],
                                ['javas.ubicacion',$this->bodega]

                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }else{
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['javas.stock','>=',1],
                                ['javas.ubicacion','!=','San Luis']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }
                }
            }else{
                if($this->acabado != null){
                    if($this->bodega != null){
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.stock','>=',1],
                                ['javas.terminado','like', '%'. $this->acabado . '%'],
                                ['javas.ubicacion',$this->bodega]
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }else{
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.stock','>=',1],
                                ['javas.terminado','like', '%'. $this->acabado . '%'],
                                ['javas.ubicacion','!=','San Luis']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }
                }else{
                    if($this->bodega != null){
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.stock','>=',1],
                                ['javas.ubicacion',$this->bodega]
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }else{
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.stock','>=',1],
                                ['javas.ubicacion','!=','San Luis']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }
                }
            }
        }elseif ($this->zona == 'SLP') {
            if($this->buscar != null){
                if($this->acabado != null){
                    if($this->bodega != null){
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['javas.stock','>=',1],
                                ['javas.terminado','like', '%'. $this->acabado . '%'],
                                ['javas.ubicacion','San Luis']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }else{
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['javas.stock','>=',1],
                                ['javas.terminado','like', '%'. $this->acabado . '%'],
                                ['javas.ubicacion','San Luis']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }
                }else{
                    if($this->bodega != null){
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['javas.stock','>=',1],
                                ['javas.ubicacion','San Luis']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }else{
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['javas.stock','>=',1],
                                ['javas.ubicacion','San Luis']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }
                }
            }else{
                if($this->acabado != null){
                    if($this->bodega != null){
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.stock','>=',1],
                                ['javas.terminado','like', '%'. $this->acabado . '%'],
                                ['javas.ubicacion','San Luis']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }else{
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.stock','>=',1],
                                ['javas.terminado','like', '%'. $this->acabado . '%'],
                                ['javas.ubicacion','San Luis']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }
                }else{
                    if($this->bodega != null){
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.stock','>=',1],
                                ['javas.ubicacion','San Luis']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }else{
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.stock','>=',1],['javas.ubicacion','San Luis']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }
                }
            }
        }elseif ($this->zona == 'AGS') {
            if($this->buscar != null){
                if($this->acabado != null){
                    if($this->bodega != null){
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['javas.stock','>=',1],
                                ['javas.terminado','like', '%'. $this->acabado . '%'],
                                ['javas.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }else{
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['javas.stock','>=',1],
                                ['javas.terminado','like', '%'. $this->acabado . '%'],
                                ['javas.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }
                }else{
                    if($this->bodega != null){
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['javas.stock','>=',1],
                                ['javas.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }else{
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['javas.stock','>=',1],
                                ['javas.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }
                }
            }else{
                if($this->acabado != null){
                    if($this->bodega != null){
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.stock','>=',1],
                                ['javas.terminado','like', '%'. $this->acabado . '%'],
                                ['javas.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }else{
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.stock','>=',1],
                                ['javas.terminado','like', '%'. $this->acabado . '%'],
                                ['javas.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }
                }else{
                    if($this->bodega != null){
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.stock','>=',1],
                                ['javas.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }else{
                        return Java::join('categorias','javas.idcategoria','=','categorias.id')
                        ->select(
                            'javas.id','javas.codigo','javas.sku','categorias.nombre',
                            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
                            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
                            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
                            'javas.fecha_llegada','javas.comprometido')
                            ->where([
                                ['javas.stock','>=',1],['javas.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('javas.id', 'asc')->get();
                    }
                }
            }
        }
    }

    public function headings(): array{
        return [
            '#',
            'No° Java',
            'Codigo',
            'Material',
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
