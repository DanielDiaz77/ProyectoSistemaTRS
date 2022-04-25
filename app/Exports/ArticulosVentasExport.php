<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Articulo;
use App\DetalleVenta;
use App\Venta;

class ArticulosVentasExport implements FromCollection,WithHeadings
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
        DetalleVenta::join('ventas','ventas.id','detalle_ventas.idventa')
        ->join('articulos','detalle_ventas.idarticulo','=','articulos.id')
        ->leftJoin('categorias','articulos.idcategoria','=','categorias.id')
        ->select(
            'articulos.id','ventas.num_comprobante','articulos.codigo','articulos.sku','categorias.nombre',
            'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
            'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
            'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
            'articulos.fecha_llegada','articulos.comprometido')
        ->where([
            ['articulos.ubicacion',$this->bodega],
            ['ventas.estado','Registrado'],
            ['ventas.entregado',0],
            ['ventas.entrega_parcial',0]
        ])
        ->orderBy('ventas.id', 'asc')->get();
    }

    public function headings(): array{
        return [
            '#',
            'Venta',
            'No° Placa',
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
