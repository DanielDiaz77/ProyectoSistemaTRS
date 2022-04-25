<?php

namespace App\Exports;

use App\DetalleVentaAbrasivo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbrasivosVentasExport implements FromCollection,WithHeadings
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
        DetalleVentaAbrasivo::join('venta_abrasivos','venta_abrasivos.id','detalle_venta_abrasivos.idventaabrasivo')
        ->join('abrasivos','detalle_venta_abrasivos.idaabrasivo','=','abrasivos.id')
        ->select('abrasivos.id','venta_abrasivos.num_comprobante','abrasivos.codigo','abrasivos.sku',
            'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
            'abrasivos.descripcion','abrasivos.comprometido')
        ->where([
            ['abrasivos.ubicacion',$this->bodega],
            ['venta_abrasivos.estado','Registrado'],
            ['venta_abrasivos.entregado',0],
            ['venta_abrasivos.entrega_parcial',0]
        ])
        ->orderBy('venta_abrasivos.id', 'asc')->get();
    }
    public function headings(): array{
        return [
            '#',
            'Venta',
            'Codigo',
            'SKU',
            'Precio',
            'Ubicacion',
            'Stock',
            'Descripcion',
            'Comprometido'
        ];
    }
}
