<?php

namespace App\Exports;

use App\Java;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JavasExport implements FromCollection, WithHeadings
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
        return Java::join('categorias','javas.idcategoria','=','categorias.id')
        ->select(
            'javas.id','javas.codigo','javas.sku','categorias.nombre',
            'javas.terminado','javas.largo','javas.alto','javas.metros_cuadrados',
            'javas.espesor','javas.precio_venta','javas.ubicacion','javas.stock',
            'javas.descripcion','javas.observacion','javas.origen','javas.contenedor',
            'javas.fecha_llegada','javas.comprometido','javas.condicion')
        ->where([['javas.ubicacion',$this->bodega],['javas.stock','>=',1],['javas.condicion',1]])
        ->orderBy('javas.id', 'asc')->get();
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
            'Comprometido',
            'Condicion',
        ];
    }
}
