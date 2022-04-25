<?php

namespace App\Exports;

use App\Abrasivo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbrasivoFiltrosExport implements FromCollection,WithHeadings
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
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion',$this->bodega]
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }else{
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }
                }else{
                    if($this->bodega != null){
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion',$this->bodega]

                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }else{
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }
                }
            }else{
                if($this->acabado != null){
                    if($this->bodega != null){
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.terminado','like', '%'. $this->acabado . '%'],
                                ['abrasivos.ubicacion',$this->bodega]
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }else{
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.terminado','like', '%'. $this->acabado . '%'],
                                ['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }
                }else{
                    if($this->bodega != null){
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion',$this->bodega]
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }else{
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion','!=','San Luis'],['abrasivos.ubicacion','!=','Aguascalientes']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }
                }
            }
        }elseif ($this->zona == 'SLP') {
            if($this->buscar != null){
                if($this->acabado != null){
                    if($this->bodega != null){
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion','San Luis']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }else{
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion','San Luis']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }
                }else{
                    if($this->bodega != null){
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion','San Luis']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }else{
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion','San Luis']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }
                }
            }else{
                if($this->acabado != null){
                    if($this->bodega != null){
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion','San Luis']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }else{
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion','San Luis']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }
                }else{
                    if($this->bodega != null){
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion','San Luis']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }else{
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.stock','>=',1],['abrasivos.ubicacion','San Luis']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }
                }
            }
        }elseif ($this->zona == 'AGS') {
            if($this->buscar != null){
                if($this->acabado != null){
                    if($this->bodega != null){
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }else{
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }
                }else{
                    if($this->bodega != null){
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }else{
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.'.$this->criterio, 'like', '%'. $this->buscar . '%'],
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }
                }
            }else{
                if($this->acabado != null){
                    if($this->bodega != null){
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }else{
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }
                }else{
                    if($this->bodega != null){
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.stock','>=',1],
                                ['abrasivos.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }else{
                        return Abrasivo::select(
                            'abrasivos.id','abrasivos.codigo','abrasivos.sku',
                            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                            'abrasivos.descripcion','abrasivos.comprometido')
                            ->where([
                                ['abrasivos.stock','>=',1],['abrasivos.ubicacion','Aguascalientes']
                            ])
                        ->orderBy('abrasivos.id', 'asc')->get();
                    }
                }
            }
        }
    }

    public function headings(): array{
        return [
            '#',
            'Codigo',
            'SKU',
            'Precio',
            'Ubicacion',
            'Stock',
            'Descripcion',
            'Observacion',
            'Comprometido'
        ];
    }
}
