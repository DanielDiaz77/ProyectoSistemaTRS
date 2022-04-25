<?php

namespace App\Exports;

use App\DetalleVentaJava;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JavaVentasExport implements FromCollection,WithHeadings
{
    protected $bodega;

    public function __construct($bodega)
    {
        $this->bodega = $bodega;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return
        DetalleVentaJava::join('venta_javas','venta_javas.id','detalle_venta_javas.idventajava')
        ->join('javas','detalle_venta_javas.idjava','=','javas.id')
        ->leftJoin('categorias','javas.idcategoria','=','categorias.id')
        ->select(
            'javas.id','venta_javas.num_comprobante','javas.codigo','javas.sku','categorias.nombre',
            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
            'javas.fecha_llegada','javas.comprometido')
        ->where([
            ['javas.ubicacion',$this->bodega],
            ['venta_javas.estado','Registrado'],
            ['venta_javas.entregado',0],
            ['venta_javas.entrega_parcial',0]
        ])
        ->orderBy('venta_javas.id', 'asc')->get();
    }

    public function headings(): array{
        return [
            '#',
            'Venta',
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
