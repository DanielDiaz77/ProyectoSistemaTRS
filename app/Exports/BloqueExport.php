<?php

namespace App\Exports;

use App\Bloque;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BloqueExport implements FromCollection, WithHeadings
{
    protected $bodega;

    public function __construct($bodega)
    {
        $this->bodega = $bodega;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){

        return Bloque::join('categorias','bloques.idcategoria','=','categorias.id')
            ->select(
                'bloques.id','bloques.codigo','bloques.sku','categorias.nombre',
                'bloques.terminado','bloques.largo','bloques.alto','bloques.metros_cubicos',
                'bloques.ancho','bloques.precio_venta','bloques.ubicacion','bloques.stock',
                'bloques.descripcion','bloques.observacion','bloques.origen','bloques.contenedor',
                'bloques.fecha_llegada','bloques.comprometido','bloques.condicion')
            ->where([['bloques.ubicacion',$this->bodega],['bloques.stock','>=',1],['bloques.condicion',1]])
            ->orderBy('bloques.id', 'asc')->get();
    }

    public function headings(): array{
        return [
            '#',
            'No° Placa',
            'Codigo',
            'Material',
            'Terminado',
            'Largo',
            'Alto',
            'Metros Cubicos',
            'Ancho',
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
